<?php namespace Mmanos\Api;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
//use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;

trait ControllerTrait
{
	protected $response;
	
	/**
	 * Return the current response instance.
	 *
	 * @return Response
	 */
	protected function response()
	{
		if (!isset($this->response)) {
			$this->response = Response::make();
		}
		
		return $this->response;
	}
	
	/**
	 * Execute an action on the controller.
	 * Overridden to perform output transformations.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function callAction($method, $parameters)
	{
		$response = $this->response();
		$action_response = parent::callAction($method, $parameters);
		
		switch (true) {
			case $action_response instanceof Model:
				$response->setContent(Api::transform($action_response));
				break;
				
			case $action_response instanceof Collection:
				$output = array();
				foreach ($action_response as $model) {
					$output[] = Api::transform($model);
				}
				
				$response->setContent($output);
				break;
				
			case $action_response instanceof Paginator:
				$output = array();
				foreach ($action_response->getCollection() as $model) {
					$output[] = Api::transform($model);
				}
				
				$response->setContent($output)
					->header('Pagination-Page', $action_response->currentPage())
					->header('Pagination-Num', $action_response->perPage())
					->header('Pagination-Total', $action_response->total())
					->header('Pagination-Last-Page', $action_response->lastPage());
				break;
				
			case $action_response instanceof Response:
				$response = $action_response;
				break;
				
			default:
				$response->setContent($action_response);
		}
		
		return $response;
	}
}
