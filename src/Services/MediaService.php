<?php

namespace Sentix\MediaManager\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Sentix\MediaManager\Models\Media;

class MediaService
{
    protected $config;

    protected $options = [];
    // protected $imageProcess;

    public function __construct()
    {
        $this->config = Config::get('media', []);
        // $this->imageProcess = $imageProcess;
    }

    /**
     * Render full media manager view
     */
    public function view(array $options = []): string
    {
        $this->options = array_merge([
            'title' => 'Media Manager',
            'upload' => true,
            'select' => true,
            'filters' => true,
            'statistics' => true,
            'inline' => false,
            'containerId' => 'media-manager-container',
            'height' => 'auto',
            'width' => '100%',
        ], $options);

        $this->shareGlobalData();

        return View::make('media::index', [
            'options' => $this->options,
            'totalFiles' => Media::count(),
            'imageCount' => Media::where('type', 'image')->count(),
            'videoCount' => Media::where('type', 'video')->count(),
            'documentCount' => Media::whereIn('type', ['document', 'spreadsheet'])->count(),
            'storageUsed' => $this->formatBytes(Media::sum('size')),
            'filters' => config('media.filters'),
        ])->render();
    }

    /**
     * Render only modal (for inline usage)
     */
    public function modal(array $options = []): string
    {
        $this->options = array_merge([
            'modalId' => 'uploadMediaModal',
            'triggerButton' => true,
            'buttonText' => 'Select Media',
            'buttonClass' => 'btn btn-primary',
            'multiple' => false,
            'inputName' => 'media_ids',
            'preview' => true,
            'previewId' => 'media-preview',
        ], $options);

        return View::make('media::partials.modal', [
            'options' => $this->options,
            'filters' => config('media.filters'),
        ])->render();
    }

    /**
     * Render only script tag
     */
    public function script(array $options = []): string
    {
        $this->options = array_merge([
            // 'csrf' => csrf_token(),
            'uploadUrl' => route('media.upload'),
            'fetchUrl' => route('media.fetch'),
            'deleteUrl' => route('media.delete', ['id' => '']),
            'bulkDeleteUrl' => route('media.bulk-delete'),
            'autoInit' => true,
            'debug' => false,
            'config' => isset($this->config['allowed_types']) ? $this->config['allowed_types'] : null,
            'defaultTab' => $this->config['default_tab'] ?? 'select',
            'accept' => '*/*',
            'max' => 10,
        ], $options);

        $script = '
        <script>
            window.MediaManagerConfig = '.json_encode($this->options).";
        </script>
        <script src='".asset('vendor/media/js/MediaUI.js')."'></script>
        <script src='".asset('vendor/media/js/MediaUpload.js')."'></script>
        <script src='".asset('vendor/media/js/MediaSelection.js')."'></script>
        <script src='".asset('vendor/media/js/MediaList.js')."'></script>
        <script src='".asset('vendor/media/js/MediaManager.js')."'></script>
        ";
        
        if ($this->options['autoInit']) {
            $script .= "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof window.MediaManager !== 'undefined') {
                        window.mediaManager = new window.MediaManager();
                    }
                });
            </script>
            ";
        }

        return $script;
    }

    /**
     * Get media selector for forms
     */
    public function selector(string $inputName, array $options = []): string
    {
        $options = array_merge([
            'multiple' => false,
            'preview' => true,
            'value' => '',
            'placeholder' => 'Select Media',
            'buttonText' => 'Choose Media',
            'accept' => 'image/*,video/*,.pdf,.xlsx,.xls,.csv',
        ], $options);

        return View::make('media::partials.selector', [
            'inputName' => $inputName,
            'options' => $options,
            'value' => $options['value'],
        ])->render();
    }

    /**
     * Get media gallery
     */
    public function gallery(array $options = []): string
    {
        $options = array_merge([
            'limit' => 12,
            'type' => null, // image, video, document
            'columns' => 4,
            'showDelete' => false,
            'showSelect' => true,
        ], $options);

        $query = Media::query();

        if ($options['type']) {
            $query->where('type', $options['type']);
        }

        $media = $query->latest()->limit($options['limit'])->get();

        return View::make('media::partials.gallery', [
            'media' => $media,
            'options' => $options,
        ])->render();
    }

    /**
     * Initialize media manager on specific element
     */
    public function init(string $selector, array $options = []): string
    {
        $options = array_merge([
            'multiple' => false,
            'onSelect' => null,
            'previewContainer' => null,
            'inputField' => null,
        ], $options);

        $initScript = "
        <script>
            (function() {
                var element = document.querySelector('{$selector}');
                if (element && typeof MediaManager !== 'undefined') {
                    element.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (window.mediaManager) {
                            window.mediaManager.initSelector(".json_encode($options).');
                        }
                    });
                }
            })();
        </script>
        ';

        return $initScript;
    }

    protected function shareGlobalData(): void
    {
        View::share('mediaConfig', [
            'filters' => $this->getEnabledFilters(),
            'sorting' => Config::get('media.sorting', []),
            'pagination' => Config::get('media.pagination', []),
            'viewModes' => Config::get('media.view_modes', []),
        ]);
    }

    protected function getEnabledFilters(): array
    {
        $filters = Config::get('media.filters', []);

        return array_filter($filters, function ($filter) {
            return $filter['enabled'] ?? false;
        });
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    public function uploadMultiple(array $files, $user = null): array
    {
        $disk = config('media.storage.disk', 'public');
        $directory = config('media.storage.directory', 'media');

        $uploaded = [];

        foreach ($files as $file) {
            $path = $file->store($directory, $disk);
            $storageUrl = Storage::url($path);
            $media = Media::create([
                'original_name' => $file->getClientOriginalName(),
                'url' => $storageUrl,
                'path' => $storageUrl,
                'name' => $file->getClientOriginalName(),
                'type' => $this->detectType($file),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => $user?->id,
                'user_type' => get_class($user),
            ]);

            // if ($this->detectType($file) === 'image') {
            //   $this->imageProcess->process($path, $disk);
            // }

            $uploaded[] = $media;
        }

        return $uploaded;
    }

    protected function detectType($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $allowedTypes = config('media.allowed_types', []);

        foreach ($allowedTypes as $type => $config) {
            if (! ($config['enabled'] ?? false)) {
                continue;
            }

            if (in_array($extension, $config['extensions'] ?? [])) {
                return $type;
            }
        }

        return 'unknown';
    }

    public function delete($media): bool
    {
        $path = trim($media->path);
        $filePath = public_path($path);

        if (file_exists($filePath) && ! is_dir($filePath)) {
            unlink($filePath);
        }

        return $media->forceDelete();
    }

    public function bulkDelete(array $ids): int
    {
        $deletedCount = 0;

        $medias = Media::whereIn('id', $ids)->get();

        foreach ($medias as $media) {
            $path = trim($media->path);
            $filePath = public_path($path);

            if (file_exists($filePath) && ! is_dir($filePath)) {
                unlink($filePath);
            }

            if ($media->forceDelete()) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
