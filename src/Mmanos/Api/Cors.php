<?php namespace Mmanos\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class Cors
{
	/**
	 * Attach CORS headers to the given response.
	 *
	 * @param Response $response
	 * 
	 * @return void
	 */
	public static function attachHeaders($response)
	{
		$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
		$response->headers->set('Access-Control-Allow-Headers', config('api.cors_allowed_headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With'));
		$response->headers->set('Access-Control-Allow-Credentials', 'true');
		
		if ($exposed = config('api.cors_exposed_headers', 'Pagination-Page, Pagination-Num, Pagination-Total, Pagination-Last-Page')) {
			$response->headers->set('Access-Control-Expose-Headers', $exposed);
		}
	}
	
	/**
	 * Attach a CORS origin header to the given response, if allowed.
	 * Returns true if an origin header was set; false, otherwise.
	 *
	 * @param Response $response
	 * @param string   $origin
	 * 
	 * @return bool
	 */
	public static function attachOriginHeader($response, $origin)
	{
		if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
			$response->headers->set('Access-Control-Allow-Origin', $origin);
			return true;
		}
		
		if ('*' == config('api.cors_allowed_origin', 'client')) {
			$response->headers->set('Access-Control-Allow-Origin', '*');
			return true;
		}
		
		if ('client' == config('api.cors_allowed_origin', 'client')) {
			$client = Authentication::instance()->client();
			if (empty($client) || empty($client->endpoints())) {
				return false;
			}
			
			foreach ($client->endpoints() as $endpoint) {
				$parts = parse_url($endpoint);
				if (empty($parts['scheme']) || empty($parts['host'])) {
					continue;
				}
				
				$port = '';
				if (array_get($parts, 'port')) {
					$port = ':' . array_get($parts, 'port');
				}
				
				$url = $parts['scheme'] . '://' . $parts['host'] . $port;
				
				if ($origin == $url) {
					$response->headers->set('Access-Control-Allow-Origin', $url);
					return true;
				}
			}
		}
		
		return false;
	}
}
