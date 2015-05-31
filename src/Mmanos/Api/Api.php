<?php namespace Mmanos\Api;

use Illuminate\Support\Facades\Request;
use Mmanos\Api\Exceptions\HttpException;

class Api
{
	/**
	 * Abort the request and return an error response object.
	 *
	 * @var int    $code
	 * @var string $message
	 * @var array  $extra
	 * 
	 * @return void
	 */
	public static function abort($code, $message = null, array $extra = array())
	{
		throw new HttpException($message, $code, $extra);
	}
	
	/**
	 * Validate authorization for this request.
	 *
	 * @return void
	 */
	public static function protect()
	{
		if (!Authentication::instance()->check()) {
			static::abort(401);
		}
	}
	
	/**
	 * Ensure the current client has access to the requested scope.
	 *
	 * @param string $scope
	 *
	 * @return void
	 */
	public static function checkScope($scope)
	{
		if (!Authentication::instance()->checkScope($scope)) {
			static::abort(403, "Access denied to scope: $scope");
		}
	}
	
	/**
	 * Initialize an internal api request.
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return InternalRequest
	 */
	public static function internal($uri, array $params = array())
	{
		return new InternalRequest($uri, $params);
	}
	
	/**
	 * Apply any available transformations to the given model and return the result.
	 *
	 * @param Model   $model
	 * @param Request $request
	 * 
	 * @return mixed
	 */
	public static function transform($object, $request = null)
	{
		return Transformations::transform($object, $request ?: Request::instance());
	}
	
	/**
	 * Bind a class to a transformer.
	 *
	 * @param string $class
	 * @param string $transformer
	 * 
	 * @return void
	 */
	public static function bindTransformer($class, $transformer)
	{
		Transformations::bind($class, $transformer);
	}
}
