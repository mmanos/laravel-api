<?php

use Illuminate\Database\Migrations\Migration;

class OauthServer extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Grant types.
		DB::table('oauth_grants')->insert(array(
			'id'         => 'password',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
		DB::table('oauth_grants')->insert(array(
			'id'         => 'refresh_token',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
		DB::table('oauth_grants')->insert(array(
			'id'         => 'authorization_code',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
		DB::table('oauth_grants')->insert(array(
			'id'         => 'client_credentials',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
		DB::table('oauth_grants')->insert(array(
			'id'         => 'client_id_secret',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		));
		
		// Scopes.
		DB::table('oauth_scopes')->insert(array(
			'id'          => '*',
			'description' => 'Allow access to all scopes.',
			'created_at'  => date('Y-m-d H:i:s'),
			'updated_at'  => date('Y-m-d H:i:s'),
		));
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		
	}
}
