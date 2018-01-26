<?php

namespace Brunolobo\Widgets;

use Brunolobo\Widgets\Console\WidgetMakeCommand;
use Brunolobo\Widgets\Factories\AsyncWidgetFactory;
use Brunolobo\Widgets\Factories\WidgetFactory;
use Brunolobo\Widgets\Misc\LaravelApplicationWrapper;
use Illuminate\Support\Facades\Blade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'pacto-widgets'
        );

        $this->app->bind('brunolobo.widget', function () {
            return new WidgetFactory(new LaravelApplicationWrapper());
        });

        $this->app->bind('brunolobo.async-widget', function () {
            return new AsyncWidgetFactory(new LaravelApplicationWrapper());
        });

        $this->app->singleton('brunolobo.widget-group-collection', function () {
            return new WidgetGroupCollection(new LaravelApplicationWrapper());
        });

        $this->app->singleton('command.widget.make', function ($app) {
            return new WidgetMakeCommand($app['files']);
        });

        $this->commands('command.widget.make');

        $this->app->alias('brunolobo.widget', 'Brunolobo\Widgets\Factories\WidgetFactory');
        $this->app->alias('brunolobo.async-widget', 'Brunolobo\Widgets\Factories\AsyncWidgetFactory');
        $this->app->alias('brunolobo.widget-group-collection', 'Brunolobo\Widgets\WidgetGroupCollection');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('pacto-widgets.php'),
        ]);

        $routeConfig = [
            'namespace'  => 'Brunolobo\Widgets\Controllers',
            'prefix'     => 'brunolobo',
            'middleware' => $this->app['config']->get('pacto-widgets.route_middleware', []),
        ];

        if (!$this->app->routesAreCached()) {
            $this->app['router']->group($routeConfig, function ($router) {
                $router->get('load-widget', 'WidgetController@showWidget');
            });
        }

        $omitParenthesis = version_compare($this->app->version(), '5.3', '<');

        Blade::directive('widget', function ($expression) use ($omitParenthesis) {
            $expression = $omitParenthesis ? $expression : "($expression)";

            return "<?php echo app('brunolobo.widget')->run{$expression}; ?>";
        });

        Blade::directive('asyncWidget', function ($expression) use ($omitParenthesis) {
            $expression = $omitParenthesis ? $expression : "($expression)";

            return "<?php echo app('brunolobo.async-widget')->run{$expression}; ?>";
        });

        Blade::directive('widgetGroup', function ($expression) use ($omitParenthesis) {
            $expression = $omitParenthesis ? $expression : "($expression)";

            return "<?php echo app('brunolobo.widget-group-collection')->group{$expression}->display(); ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['brunolobo.widget', 'brunolobo.async-widget'];
    }
}
