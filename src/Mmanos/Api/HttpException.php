<?php namespace Mmanos\Api;

use Exception;
use Illuminate\Support\Facades\Response;

class HttpException extends Exception
{
	/**
	 * Array of extra parameters to output.
	 *
	 * @var array
	 */
	protected $extra;
	
	/**
	 * Create a new error instance.
	 *
	 * @var string $message
	 * @var int    $code
	 * @var array  $extra
	 * 
	 * @return void
	 */
	public function __construct($message, $code = null, array $extra = array())
	{
		parent::__construct($message, $code);
		$this->extra = $extra;
	}
	
	/**
	 * Return the extra parameter.
	 *
	 * @return array
	 */
	public function getExtra()
	{
		return $this->extra;
	}
	
	/**
	 * Return the response body array.
	 *
	 * @return array
	 */
	public function body()
	{
		$msg = $this->getMessage();
		
		if (422 == $this->getCode() && !empty($msg)) {
			$this->extra['errors'] = json_decode($msg, true);
			$msg = null;
		}
		
		if (empty($msg)) {
			switch ($this->getCode()) {
				case 404:
					$msg = 'Not Found';
					break;
				case 405:
					$msg = 'Method Not Allowed';
					break;
				case 400:
					$msg = 'Bad Request';
					break;
				case 401:
					$msg = 'Unauthorized';
					break;
				case 402:
					$msg = 'Payment Required';
					break;
				case 403:
					$msg = 'Forbidden';
					break;
				case 422:
					$msg = 'Unprocessable Entity';
					break;
				case 410:
					$msg = 'Resource Deleted';
					break;
				case 500:
					$msg = 'Server Error';
					break;
				default:
					$msg = 'Error';
			}
		}
		
		$output = array(
			'status'  => $this->getCode(),
			'message' => $msg,
		);
		
		if (!empty($this->getExtra())) {
			$output = array_merge($output, $this->getExtra());
		}
		
		return $output;
	}
	
	/**
	 * Return the response object.
	 *
	 * @return Response
	 */
	public function response()
	{
		return Response::json($this->body(), $this->getCode());
	}
}
