<?php namespace Mmanos\Api;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

class InternalRequest
{
	protected $uri;
	protected $params;
	
	/**
	 * Create a new InternalRequest instance.
	 *
	 * @param string $uri
	 * @param array  $params
	 * 
	 * @return void
	 */
	public function __construct($uri, array $params = array())
	{
		$this->uri = $uri;
		$this->params = $params;
	}
	
	/**
	 * Dispatch a GET request.
	 *
	 * @return mixed
	 */
	public function get()
	{
		return $this->dispatch('get');
	}
	
	/**
	 * Dispatch a POST request.
	 *
	 * @return mixed
	 */
	public function post()
	{
		return $this->dispatch('post');
	}
	
	/**
	 * Dispatch a PUT request.
	 *
	 * @return mixed
	 */
	public function put()
	{
		return $this->dispatch('put');
	}
	
	/**
	 * Dispatch a DELETE request.
	 *
	 * @return mixed
	 */
	public function delete()
	{
		return $this->dispatch('delete');
	}
	
	/**
	 * Dispatch this request.
	 *
	 * @param string $method
	 * 
	 * @return mixed
	 */
	public function dispatch($method)
	{
		// Save original input.
		$original_input = Request::input();
		
		// Create request.
		$request = Request::create($this->uri, $method, $this->params);
		
		// Replace input (maintain api auth parameters).
		Request::replace(array_merge($request->input(), array(
			'client_id'     => Request::input('client_id'),
			'client_secret' => Request::input('client_secret'),
			'access_token'  => Request::input('access_token'),
		)));
		
		// Dispatch request.
		$response = Route::dispatch($request);
		
		// Restore original input.
		Request::replace($original_input);
		
		$content = $response->getContent();
		return empty($content) ? null : json_decode($response->getContent(), true);
	}
}
