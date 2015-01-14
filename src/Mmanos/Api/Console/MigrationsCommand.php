<?php namespace Mmanos\Api\Console;

use Illuminate\Console\Command;

class MigrationsCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'laravel-api:migrations';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create the migrations needed for the OAuth Server';
	
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->call('migrate:publish', array('package' => 'lucadegasperi/oauth2-server-laravel'));
		$this->call('migrate:publish', array('package' => 'mmanos/laravel-api'));
		$this->call('dump-autoload');
	}
}
