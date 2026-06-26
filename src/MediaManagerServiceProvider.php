<?php

namespace Sentix\MediaManager;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Sentix\MediaManager\Commands\CleanupMediaCommand;
use Sentix\MediaManager\Services\ImageProcessService;
use Sentix\MediaManager\Services\MediaService;
use Sentix\MediaManager\View\Components\MediaComponent;

class MediaManagerServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {

        $migrationPath = __DIR__.'/../database/migrations';

        // Load config
        $this->publishes([
            __DIR__.'/../config/media.php' => config_path('media.php'),
        ], 'media-config');

        $this->loadMigrationsFrom($migrationPath);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'media');

        Blade::component('media', MediaComponent::class);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/media'),
        ], 'media-views');

        $this->publishes([
            __DIR__.'/../resources/js' => public_path('vendor/media/js'),
            // __DIR__ . '/../resources/css' => public_path('vendor/media/css'),
        ], 'media-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupMediaCommand::class,
            ]);
        }

        $this->registerBladeDirectives();
    }

    public function register()
    {

        $this->mergeConfigFrom(__DIR__.'/../config/media.php', 'media');

        $this->app->singleton('media', function ($app) {
            return new MediaService;
        });

        $this->app->singleton(MediaService::class, function ($app) {
            return new MediaService($app->make(ImageProcessService::class));
        });

        $this->app->singleton(ImageProcessService::class, function ($app) {
            return new ImageProcessService;
        });
    }

    protected function registerBladeDirectives()
    {

        \Blade::directive('mediaView', function ($expression) {
            return "<?php echo Media::view({$expression}); ?>";
        });

        \Blade::directive('mediaModal', function ($expression) {
            return "<?php echo Media::modal({$expression}); ?>";
        });

        \Blade::directive('mediaScript', function ($expression) {
            return "<?php echo Media::script({$expression}); ?>";
        });

        \Blade::directive('mediaSelector', function ($expression) {
            return "<?php echo Media::selector({$expression}); ?>";
        });

    }
}
