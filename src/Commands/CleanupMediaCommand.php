<?php

namespace Sentix\MediaManager\Commands;

use Illuminate\Console\Command;
use Sentix\MediaManager\Models\Media;

class CleanupMediaCommand extends Command
{
    protected $signature = 'media:cleanup {--days=30 : Delete files older than X days}';

    protected $description = 'Clean up old media files';

    public function handle()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);

        $deletedCount = 0;

        Media::where('created_at', '<', $date)->chunk(100, function ($medias) use (&$deletedCount) {
            foreach ($medias as $media) {
                $path = trim($media->path);
                $filePath = public_path($path);

                if (file_exists($filePath) && ! is_dir($filePath)) {
                    unlink($filePath);
                }
                $media->forceDelete();
                $deletedCount++;
            }
        });

        $this->info("Cleaned up {$deletedCount} media files older than {$days} days.");
    }
}
