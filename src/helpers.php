<?php

if (! function_exists('media_manager_asset')) {
    function media_manager_asset($path)
    {
        return asset('vendor/media-manager/'.$path);
    }
}

if (! function_exists('get_media_url')) {
    function get_media_url($media, $thumbnail = false)
    {
        if (! $media) {
            return null;
        }

        if (is_numeric($media)) {
            $media = \Sentix\MediaManager\Models\Media::find($media);
        }

        if (! $media) {
            return null;
        }

        return $thumbnail ? $media->getThumbnailUrl() : $media->getFullUrl();
    }
}
function media_type_from_url(string $url): string
{
    $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

    foreach (config('media.allowed_types') as $type => $config) {
        if (in_array($extension, $config['extensions'] ?? [])) {
            return $type;
        }
    }

    return 'file';
}
