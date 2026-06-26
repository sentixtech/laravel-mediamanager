<?php

namespace Sentix\MediaManager\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Sentix\MediaManager\Models\Media;
use Sentix\MediaManager\Services\ImageProcessService;
use Sentix\MediaManager\Services\MediaService;

class MediaController extends Controller
{
    protected $mediaService;

    protected $imageProcessor;

    public function __construct(MediaService $mediaService, ImageProcessService $imageProcessor)
    {
        $this->mediaService = $mediaService;
        $this->imageProcessor = $imageProcessor;
    }

    public function index()
    {
        $stats = $this->mediaService->getStatistics();

        return view('media::index', [
            'totalFiles' => $stats['total_files'],
            'imageCount' => $stats['image_count'],
            'videoCount' => $stats['video_count'],
            'documentCount' => $stats['document_count'],
            'storageUsed' => $stats['storage_used'],
            'config' => [
                'defaultTab' => Config::get('media.default_tab', 'upload'),
                'filters' => $this->getEnabledFilters(),
                'sorting' => Config::get('media.sorting'),
                'viewModes' => Config::get('media.view_modes'),
                'pagination' => Config::get('media.pagination'),
                'upload' => [
                    'maxFiles' => Config::get('media.upload.max_files', 20),
                    'maxSize' => Config::get('media.upload.max_total_size', 10240),
                    'allowedTypes' => Media::getAllowedExtensions(),
                ],
            ],
        ]);
    }

    protected function getEnabledFilters(): array
    {
        $filters = Config::get('media.filters', []);

        return array_filter($filters, function ($filter) {
            return $filter['enabled'] ?? false;
        });
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'files' => 'required|array|min:1|max:'.Config::get('media.upload.max_files', 20),
                'files.*' => 'file|max:'.(Config::get('media.upload.max_total_size', 10240) * 1024),
            ]);

            if ($hook = Config::get('media.events.before_upload')) {
                if (is_callable($hook)) {
                    $hook($request->file('files'));
                }
            }

            $uploadedFiles = $this->mediaService->uploadMultiple($request->file('files'), auth()->user());

            if ($hook = Config::get('media.events.after_upload')) {
                if (is_callable($hook)) {
                    $hook($uploadedFiles);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully',
                'data' => $uploadedFiles,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function fetch(Request $request)
    {
        try {

            $request->validate([
                'filter' => 'nullable|string',
                'sort' => 'nullable|string|in:latest,oldest,name_asc,name_desc,newest,size_desc,size_asc',
                'search' => 'nullable|string|max:100',
                'view' => 'nullable|string',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:50',
                'urls' => 'nullable',
            ]);

            $perPage = Config::get('media.pagination.per_page', 20);

            // -----------------------------
            // Normalize URLs
            // -----------------------------
            $urls = $request->get('urls', []);

            $urls = is_string($urls)
                ? (json_decode($urls, true) ?? [])
                : (is_array($urls) ? $urls : []);

            $prioritizedUrls = array_values(array_unique(array_map(function ($url) {
                return ltrim($url);
            }, $urls)));

            // dd($prioritizedUrls);
            $query = Media::query();

            if (! empty($prioritizedUrls)) {
                $placeholders = implode("','", $prioritizedUrls);
                $query->orderByRaw("
                CASE 
                    WHEN url IN ('$placeholders') THEN 0 
                    ELSE 1 
                END
            ");
            }

            if ($request->filled('filter') && $request->filter !== 'all') {
                $this->applyFilter($query, $request->filter);
            }

            if ($request->filled('search')) {
                $this->applySearch($query, $request->search);
            }

            $this->applySorting($query, $request->sort);

            $media = $query->paginate($perPage);

            $media->through(function ($item) {
                $item->saveUrl = $item->url;
                $item->url = asset($item->url);
                $item->human_size = $item->human_size ?? 0;

                return $item;
            });

            if ($request->ajax()) {
                return $this->handleAjaxResponse($media, $request);
            }

            return $this->sendResponse(true, $media, 'Media fetched successfully');

        } catch (\Throwable $th) {
            ce($th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch media',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('original_name', 'like', "%{$search}%")
                ->orWhere('url', 'like', "%{$search}%");
        });
    }

    private function handleAjaxResponse($media, Request $request)
    {
        $viewMode = $request->get(
            'view',
            Config::get('media.view_modes.default', 'grid')
        );

        $html = '';

        if ($request->has('selection_mode')) {

            $inputNameId = $request->get('inputNameId');

            $urls = $request->get('urls', []);

            $selectedUrls = is_string($urls)
                ? (json_decode($urls, true) ?? [])
                : (is_array($urls) ? $urls : []);

            $selectedPaths = array_map(function ($url) {
                return ltrim(parse_url($url, PHP_URL_PATH), '/');
            }, $selectedUrls);

            $media->through(function ($item) use ($selectedPaths) {

                $itemPath = ltrim(parse_url($item->saveUrl ?? $item->url, PHP_URL_PATH), '/');

                $item->selected = in_array($itemPath, $selectedPaths);

                return $item;
            });

            $html = view(
                "media::partials.selection-{$viewMode}",
                compact('media', 'inputNameId')
            )->render();

        } else {

            $html = view(
                "media::partials.{$viewMode}",
                compact('media')
            )->render();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'html' => $html,
                'view' => $request->has('selection_mode') ? $html : null,
                'pagination' => (string) $media->links(),
                'total' => $media->total(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'from' => $media->firstItem(),
                'to' => $media->lastItem(),
            ],
        ]);
    }

    protected function applyFilter($query, string $filter): void
    {
        $filterConfig = Config::get("media.filters.{$filter}");
        if (! $filterConfig) {
            return;
        }

        if (isset($filterConfig['type'])) {
            $query->where('type', $filterConfig['type']);
        } elseif (isset($filterConfig['extensions'])) {
            $query->whereIn('extension', $filterConfig['extensions']);
        }
    }

    protected function applySorting($query, ?string $sortKey): void
    {
        $sortKey = $sortKey ?? Config::get('media.sorting.default', 'newest');
        $sortConfig = Config::get("media.sorting.options.{$sortKey}");

        if ($sortConfig) {
            $query->orderBy($sortConfig['field'], $sortConfig['direction']);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    public function destroy($id)
    {
        try {
            $media = Media::findOrFail($id);
            if ($hook = Config::get('media.events.before_delete')) {
                if (is_callable($hook)) {
                    $hook($media);
                }
            }
            $this->mediaService->delete($media);
            if ($hook = Config::get('media.events.after_delete')) {
                if (is_callable($hook)) {
                    $hook($media);
                }
            }
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Media deleted successfully',
                ]);
            }

            return redirect()->back()->with('success', 'Media deleted successfully');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete media',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete media');
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $ids = explode(',', $request->media_ids);

            $deleted = $this->mediaService->bulkDelete($ids);

            return response()->json([
                'success' => true,
                'message' => "{$deleted} media items deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media',
            ], 500);
        }
    }

   
}
