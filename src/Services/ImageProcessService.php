<?php

namespace Sentix\MediaManager\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageProcessService
{
    protected $manager;
    protected $driver;

    public function __construct()
    {
        $this->driver = Config::get('media.image_processing.driver', 'gd');
        $this->manager = new ImageManager(new Driver());
    }

    public function process(string $path, string $disk = 'public'): void
    {
        $fullPath = Storage::disk($disk)->path($path);
        
        if (!file_exists($fullPath)) {
            return;
        }

        $this->createThumbnail($fullPath, $disk);
        
        if (Config::get('media.image_processing.optimize', true)) {
            $this->optimize($fullPath);
        }
    }

    public function createThumbnail(string $path, string $disk = 'public'): void
    {
        $config = Config::get('media.image_processing.thumbnail', [
            'width' => 150,
            'height' => 150,
            'crop' => true,
            'quality' => 80,
        ]);

        $thumbDirectory = Config::get('media.storage.thumbnails_directory', 'media/thumbnails');
        $filename = basename($path);
        $thumbPath = Storage::disk($disk)->path($thumbDirectory . '/' . $filename);
        
        $thumbDir = dirname($thumbPath);
        if (!file_exists($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        try {
            $image = $this->manager->read($path);
            
            if ($config['crop']) {
                $image->cover($config['width'], $config['height']);
            } else {
                $image->scale($config['width'], $config['height']);
            }
            
            $image->save($thumbPath, $config['quality']);
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }
    }

    public function optimize(string $path): void
    {
        $quality = Config::get('media.image_processing.optimization_quality', 85);
        
        try {
            $image = $this->manager->read($path);
            
            $originalSize = filesize($path);
            
            $image->save($path, $quality);
            
            $newSize = filesize($path);
            
            if ($newSize < $originalSize) {
                $saved = round((1 - $newSize / $originalSize) * 100, 2);
                \Log::debug("Image optimized: saved {$saved}%");
            }
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
        }
    }

    public function createPreview(string $path, string $disk = 'public'): void
    {
        $config = Config::get('media.image_processing.preview', [
            'width' => 800,
            'height' => 600,
            'quality' => 85,
        ]);

        $fullPath = Storage::disk($disk)->path($path);
        
        if (!file_exists($fullPath)) {
            return;
        }

        try {
            $image = $this->manager->read($fullPath);
            $image->scale(width: $config['width']);
            $image->save($fullPath, $config['quality']);
        } catch (\Exception $e) {
            \Log::error('Preview creation failed: ' . $e->getMessage());
        }
    }

    public function getImageInfo(string $path, string $disk = 'public'): array
    {
        $fullPath = Storage::disk($disk)->path($path);
        
        if (!file_exists($fullPath)) {
            return [];
        }

        try {
            $image = $this->manager->read($fullPath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'mime' => $image->mime(),
                'size' => filesize($fullPath),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}