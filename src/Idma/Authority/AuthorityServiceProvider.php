<?php namespace Idma\Authority;

use Illuminate\Support\ServiceProvider;

class AuthorityServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('idma/authority');

		$this->app->singleton('idma.authority', function($app) {
			$authority = new Authority($app['auth']->user());

			$provisioners = $this->app['config']->get('authority::provisioners', null);

			foreach ($provisioners as $provider) {
				new $provider($authority);
			}

			return $authority;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['authority'];
	}

}
