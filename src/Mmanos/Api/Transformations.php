<?php namespace Mmanos\Api;

class Transformations
{
	protected static $transformations = array();
	
	/**
	 * Bind a class to a transformer.
	 *
	 * @param string $class
	 * @param string $transformer
	 * 
	 * @return void
	 */
	public static function bind($class, $transformer)
	{
		static::$transformations[$class] = $transformer;
	}
	
	/**
	 * Return a transformer instance for the requested object.
	 *
	 * @param object $object
	 * 
	 * @return object|null
	 */
	public static function getTransformer($object)
	{
		$class = get_class($object);
		
		if (isset(static::$transformations[$class])) {
			return new static::$transformations[$class];
		}
		
		if (isset($object->transformer)) {
			return new $object->transformer;
		}
		
		return null;
	}
	
	/**
	 * Transform the given object.
	 *
	 * @param object  $object
	 * @param Request $request
	 * 
	 * @return mixed
	 */
	public static function transform($object, $request)
	{
		$transformer = static::getTransformer($object);
		if (!$transformer) {
			return $object;
		}
		
		return $transformer->transform($object, $request);
	}
}
