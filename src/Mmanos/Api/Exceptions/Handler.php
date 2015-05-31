<?php namespace Mmanos\Api\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		if ($e instanceof HttpException) {
			$response = $e->response();
			
			if ($request->header('Origin')) {
				Cors::attachHeaders($response);
				$response->headers->set('Access-Control-Allow-Origin', $request->header('Origin'));
			}
			
			return $response;
		}
		
		return parent::render($request, $e);
	}
}
