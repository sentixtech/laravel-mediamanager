<?php

namespace Sentix\MediaManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'original_name',
        'url',
        'path',
        'disk',
        'extension',
        'mime_type',
        'size',
        'type',
        'metadata',
        'user_type',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->morphTo();
    }

    public static function getAllowedTypes(): array
    {
        $allowedTypes = [];
        $types = Config::get('media.allowed_types', []);

        foreach ($types as $category => $config) {
            if ($config['enabled'] ?? false) {
                $allowedTypes[$category] = $config['mime_types'];
            }
        }

        return $allowedTypes;
    }

    public static function getAllowedExtensions(): array
    {
        $extensions = [];
        $types = Config::get('media.allowed_types', []);

        foreach ($types as $category => $config) {
            if ($config['enabled'] ?? false) {
                $extensions = array_merge($extensions, $config['extensions']);
            }
        }

        return array_unique($extensions);
    }

    public static function getMaxUploadSize(): int
    {
        $maxSize = 0;
        $types = Config::get('media.allowed_types', []);

        foreach ($types as $category => $config) {
            if (($config['enabled'] ?? false) && isset($config['max_size'])) {
                $maxSize = max($maxSize, $config['max_size']);
            }
        }

        return $maxSize * 1024;
    }

    public static function determineTypeCategory(string $mimeType): string
    {
        $types = Config::get('media.allowed_types', []);

        foreach ($types as $category => $config) {
            if (in_array($mimeType, $config['mime_types'] ?? [])) {
                return $category;
            }
        }

        return 'other';
    }

    public function shouldGenerateThumbnail(): bool
    {
        $config = Config::get("media.allowed_types.{$this->type}", []);

        return $config['thumbnail'] ?? false;
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return in_array($this->type, ['document', 'spreadsheet', 'presentation']);
    }

    public function getHumanSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function getFullUrl(): string
    {
        return asset($this->url);
    }

    public function getThumbnailUrl(): ?string
    {
        if (! $this->shouldGenerateThumbnail()) {
            return $this->getFullUrl();
        }

        $thumbPath = str_replace(
            Config::get('media.storage.directory'),
            Config::get('media.storage.thumbnails_directory'),
            $this->url
        );

        return asset($thumbPath);
    }

    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('type', ['document', 'spreadsheet', 'presentation']);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('original_name', 'like', "%{$search}%");
        });
    }
}
