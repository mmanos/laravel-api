<?php namespace Mmanos\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade as Authorizer;

class Authentication
{
	protected static $_instance;
	protected $client_id;
	protected $client;
	protected $user_id;
	protected $type;
	protected $check;
	protected $scopes = array();
	
	/**
	 * Retrieve the active instance.
	 *
	 * @return Authentication
	 */
	public static function instance()
	{
		if (!static::$_instance) {
			static::$_instance = new static;
		}
		
		return static::$_instance;
	}
	
	/**
	 * Return the current oauth client.
	 *
	 * @return string
	 */
	public function clientId()
	{
		if (isset($this->client_id)) {
			return $this->client_id ? $this->client_id : null;
		}
		
		if (!$this->check()) {
			return null;
		}
		
		if (Request::input('client_id')) {
			$this->client_id = Request::input('client_id');
		}
		else if ($id = Authorizer::getClientId()) {
			$this->client_id = $id;
		}
		else {
			$this->client_id = false;
		}
		
		return $this->client_id ? $this->client_id : null;
	}
	
	/**
	 * Return an array of details for the current client.
	 *
	 * @return array
	 */
	public function client()
	{
		if (isset($this->client)) {
			return $this->client ? $this->client : null;
		}
		
		$client_id = $this->clientId();
		if (!$client_id) {
			$this->client = false;
			return null;
		}
		
		$client = $this->fetchClient($client_id);
		if ($client) {
			$this->client = $client;
		}
		else {
			$this->client = false;
		}
		
		return $this->client ? $this->client : null;
	}
	
	/**
	 * Get the userID for the current request (access_token or Authorization header).
	 *
	 * @return int
	 */
	public function userId()
	{
		if (isset($this->user_id)) {
			return $this->user_id ? $this->user_id : null;
		}
		
		$this->check();
		$this->user_id = false;
		
		if ('user' == $this->type) {
			if ($user_id = Authorizer::getResourceOwnerId()) {
				return $this->user_id = $user_id;
			}
		}
		
		return null;
	}
	
	/**
	 * Return the authentication type: user, client.
	 *
	 * @return string
	 */
	public function type()
	{
		$this->check();
		return $this->type;
	}
	
	/**
	 * Validate authorization for the current request.
	 *
	 * @return bool
	 */
	public function check()
	{
		if (isset($this->check)) {
			return $this->check;
		}
		
		try {
			Authorizer::validateAccessToken();
			$this->type = 'user';
			return $this->check = true;
		} catch (Exception $e) {}
		
		$client_id     = Request::input('client_id');
		$client_secret = Request::input('client_secret');
		if (!$client_id || !$client_secret) {
			return $this->check = false;
		}
		
		$client = $this->fetchClient($client_id, $client_secret);
		if (!$client) {
			return $this->check = false;
		}
		
		if (!in_array('client_id_secret', $client->grants())) {
			return $this->check = false;
		}
		
		$this->client = $client;
		$this->type = 'client';
		return $this->check = true;
	}
	
	/**
	 * Ensure the current authentication has access to the requested scope.
	 *
	 * @param string $scope
	 *
	 * @return bool
	 */
	public function checkScope($scope)
	{
		if (isset($this->scopes[$scope])) {
			return $this->scopes[$scope];
		}
		
		if (!empty($this->scopes['*'])) {
			return true;
		}
		
		if (!$this->check()) {
			return false;
		}
		
		if ('user' == $this->type) {
			$this->scopes[$scope] = Authorizer::hasScope($scope);
		}
		else {
			$client = $this->client();
			$this->scopes[$scope] = in_array($scope, $client->scopes());
		}
		
		if (!$this->scopes[$scope] && '*' != $scope) {
			$this->scopes[$scope] = $this->checkScope('*');
		}
		
		return $this->scopes[$scope];
	}
	
	/**
	 * Fetch a Authentication\Client instance for the requested client id.
	 *
	 * @param string $client_id
	 * @param string $client_secret
	 * 
	 * @return Authentication\Client
	 */
	public static function fetchClient($client_id, $client_secret = null)
	{
		$query = DB::table('oauth_clients')->where('id', $client_id);
		if (null !== $client_secret) {
			$query->where('secret', $client_secret);
		}
		$client = $query->first();
		
		if (!$client) {
			return null;
		}
		
		return new Authentication\Client($client_id, (array) $client);
	}
}
