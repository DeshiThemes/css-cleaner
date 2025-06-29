<?php

namespace DeshiThemes\CssCleaner;

use Illuminate\Support\ServiceProvider;
use DeshiThemes\CssCleaner\Commands\{
    PurgeCssCommand,
    MinifyCssCommand,
    OptimizeCssCommand
};

class CssCleanerServiceProvider extends ServiceProvider
{
    /**
     * Package name for assets
     */
    protected string $packageName = 'css-cleaner';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/csscleaner.php',
            $this->packageName
        );

        $this->registerFacades();
        $this->registerHelpers();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishAssets();
        $this->registerCommands();
        $this->registerRoutes();
    }

    /**
     * Register package facades.
     */
    protected function registerFacades(): void
    {
        $this->app->singleton('css-cleaner', function ($app) {
            return new \DeshiThemes\CssCleaner\CssCleaner();
        });
    }

    /**
     * Register helper functions.
     */
    protected function registerHelpers(): void
    {
        if (file_exists($helpersFile = __DIR__ . '/../src/helpers.php')) {
            require_once $helpersFile;
        }
    }

    /**
     * Publish configuration file.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/csscleaner.php' => config_path("{$this->packageName}.php"),
        ], "{$this->packageName}-config");
    }

    /**
     * Publish package assets.
     */
    protected function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path("vendor/{$this->packageName}"),
        ], "{$this->packageName}-assets");
    }

    /**
     * Register package commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PurgeCssCommand::class,
                MinifyCssCommand::class,
                OptimizeCssCommand::class,
            ]);
        }
    }

    /**
     * Register package routes.
     */
    protected function registerRoutes(): void
    {
        if (config('csscleaner.enable_routes', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
    }
}
