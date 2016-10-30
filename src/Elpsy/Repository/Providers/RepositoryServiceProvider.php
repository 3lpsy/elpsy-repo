<?php
namespace Elpsy\Repository\Providers;
use Illuminate\Support\ServiceProvider;
/**
 * Class RepositoryServiceProvider
 * @package Elpsy\Repository\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../resources/config/elpsy.php' => config_path('elpsy.php')
        ]);
        $this->mergeConfigFrom(__DIR__ . '/../../../resources/config/elpsy.php', 'elpsy');
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('Elpsy\Repository\Generators\Commands\RepositoryCommand');
        $this->commands('Elpsy\Repository\Generators\Commands\TransformerCommand');
        $this->commands('Elpsy\Repository\Generators\Commands\PresenterCommand');
        $this->commands('Elpsy\Repository\Generators\Commands\EntityCommand');
        $this->commands('Elpsy\Repository\Generators\Commands\BindingsCommand');
        $this->app->register('Elpsy\Repository\Providers\EventServiceProvider');
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}