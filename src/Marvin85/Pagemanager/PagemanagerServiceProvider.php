<?php namespace Marvin85\Pagemanager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event as Event;
use Illuminate\Support\Facades\App as App;

class PagemanagerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('marvin85/pagemanager');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('pagemanager', function()
        {
            return new Pagemanager;
        });

        Event::listen('composing: *', function($view)
		{
			$pm = App::make('pagemanager');
		    $pm->addBodyClass($view->getName());
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('pagemanager');
	}

}
