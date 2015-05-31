<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Authenticate the resource owner.
	|--------------------------------------------------------------------------
	|
	| Will make the resource owner (access_token or Authorization header)
	| the authenticated user on boot, if true.
	|
	*/

	'auth_resource_owner' => true,

	/*
	|--------------------------------------------------------------------------
	| Enable CORS support.
	|--------------------------------------------------------------------------
	|
	| Attaches CORS headers to the outgoing response object, if true.
	|
	*/

	'cors_enabled' => true,

	/*
	|--------------------------------------------------------------------------
	| Allowed CORS origin.
	|--------------------------------------------------------------------------
	|
	| Determines what to use for the allowed origin CORS header:
	| - client : Will fetch the allowed list endpoints from the
	|            oauth_client_endpoints table
	| - *      : Will allow all endpoints
	|
	*/

	'cors_allowed_origin' => 'client',

	/*
	|--------------------------------------------------------------------------
	| Allowed incoming CORS headers.
	|--------------------------------------------------------------------------
	|
	| CSV of allowed incoming headers for CORS requests.
	|
	*/

	'cors_allowed_headers' => 'Origin, Content-Type, Accept, Authorization, X-Requested-With',

	/*
	|--------------------------------------------------------------------------
	| Exposed CORS outgoing headers
	|--------------------------------------------------------------------------
	|
	| CSV of headers to allow through in the response of a CORS request.
	|
	*/

	'cors_exposed_headers' => 'Pagination-Page, Pagination-Num, Pagination-Total, Pagination-Last-Page',

);
