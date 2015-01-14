<?php namespace Mmanos\Api\Authentication;

use Illuminate\Support\Facades\DB;

class Client
{
	protected $id;
	protected $data;
	protected $scopes;
	protected $endpoints;
	protected $grants;
	
	/**
	 * Create a new Client instance.
	 *
	 * @param string $id
	 * @param array  $data
	 * 
	 * @return void
	 */
	public function __construct($id, array $data)
	{
		$this->id = $id;
		$this->data = $data;
	}
	
	/**
	 * Return the allowed scopes for this client.
	 *
	 * @return array
	 */
	public function scopes()
	{
		if (isset($this->scopes)) {
			return $this->scopes;
		}
		
		return $this->scopes = DB::table('oauth_client_scopes')
			->select('scope_id')
			->where('client_id', $this->id)
			->lists('scope_id');
	}
	
	/**
	 * Return the allowed endpoints for this client.
	 *
	 * @return array
	 */
	public function endpoints()
	{
		if (isset($this->endpoints)) {
			return $this->endpoints;
		}
		
		return $this->endpoints = DB::table('oauth_client_endpoints')
			->select('redirect_uri')
			->where('client_id', $this->id)
			->lists('redirect_uri');
	}
	
	/**
	 * Return the allowed grants for this client.
	 *
	 * @return array
	 */
	public function grants()
	{
		if (isset($this->grants)) {
			return $this->grants;
		}
		
		return $this->grants = DB::table('oauth_client_grants')
			->select('grant_id')
			->where('client_id', $this->id)
			->lists('grant_id');
	}
}
