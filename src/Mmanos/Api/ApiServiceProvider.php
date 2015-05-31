<?php namespace Mmanos\Api;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
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
		$this->bootAuthResourceOwner();
		$this->bootFilters();
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->register('LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider');
		$this->app->register('LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider');
		
		$config_path = __DIR__.'/../../config/api.php';
		$this->mergeConfigFrom($config_path, 'api');
		$this->publishes([
			$config_path => config_path('api.php')
		], 'config');
		
		$m_from = __DIR__ . '/../../migrations/';
		$m_to = $this->app['path.database'] . '/migrations/';
		$this->publishes([
			$m_from.'2015_05_30_000000_oauth_server.php' => $m_to.'2015_05_30_000000_oauth_server.php',
		], 'migrations');
	}
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}
	
	/**
	 * Make the current resource owner (access_token or Authorization header)
	 * the current authenticated user in Laravel.
	 *
	 * @return void
	 */
	protected function bootAuthResourceOwner()
	{
		if (config('api.auth_resource_owner', true)
			&& !Auth::check()
			&& Request::input('access_token', Request::header('Authorization'))
		) {
			if ($user_id = Authentication::instance()->userId()) {
				Auth::onceUsingId($user_id);
			}
		}
	}
	
	/**
	 * Add route filters.
	 *
	 * @return void
	 */
	protected function bootFilters()
	{
		if (config('api.cors_enabled', true)) {
			$this->app['router']->before(function ($request) {
				if (Request::header('Origin') && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
					$response = Response::make(null, 204);
					Cors::attachHeaders($response);
					Cors::attachOriginHeader($response, Request::header('Origin'));
					return $response;
				}
			});
			
			$this->app['router']->after(function ($request, $response) {
				if (Request::header('Origin')) {
					Cors::attachHeaders($response);
					Cors::attachOriginHeader($response, Request::header('Origin'));
				}
			});
		}
		
		$this->app['router']->filter('protect', function ($route, $request) {
			Api::protect();
		});
		
		$this->app['router']->filter('checkscope', function ($route, $request, $scope = '') {
			// B/c Laravel uses : as a special character already.
			$scope = str_replace('.', ':', $scope);
			
			Api::checkScope($scope);
		});
	}
}
