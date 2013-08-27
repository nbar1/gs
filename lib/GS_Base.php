<?php
/*
 * GS
 *
 * Base class
 */
class GS
{
	private $model;

	/**
	 * getModel
	 *
	 * return Class
	 */
	public function getModel()
	{
		if(isset($this->model))
		{
			return $this->model;
		}
		else {
			return false;
		}
	}

	/**
	 * setModel
	 *
	 * return Class
	 */
	public function setModel($class)
	{
		try
		{
			$this->model = new $class;
			return true;
		}
		catch(Exception $e) {
			return false;
		}
	}
}
?>
