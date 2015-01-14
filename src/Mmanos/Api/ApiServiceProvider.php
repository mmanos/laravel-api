<?php namespace Mmanos\Api;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
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
		$this->package('mmanos/laravel-api');
		
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
		
		$this->app->bind('command.laravel-api.migrations', 'Mmanos\Api\Console\MigrationsCommand');
		$this->commands('command.laravel-api.migrations');
		
		$this->registerHttpExceptionHandler();
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
		if (Config::get('laravel-api::auth_resource_owner')
			&& !Auth::check()
			&& Input::get('access_token', Request::header('Authorization'))
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
		if (Config::get('laravel-api::cors_enabled')) {
			$this->app->before(function ($request) {
				if (Request::header('Origin') && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
					$response = Response::make(null, 204);
					Cors::attachHeaders($response);
					Cors::attachOriginHeader($response, Request::header('Origin'));
					return $response;
				}
			});
			
			$this->app->after(function ($request, $response) {
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
	
	/**
	 * Register a custom HttpException shutdown handler.
	 *
	 * @return void
	 */
	protected function registerHttpExceptionHandler()
	{
		$this->app->error(function(HttpException $e) {
			$response = $e->response();
			
			if (Request::header('Origin')) {
				Cors::attachHeaders($response);
				$response->headers->set('Access-Control-Allow-Origin', Request::header('Origin'));
			}
			
			return $response;
		});
	}
}
