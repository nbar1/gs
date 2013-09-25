<?php
/*
 * gs
 *
 * Base class for all gs classes
 */
class gs
{

	/**
	 * database object
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * Initiate the database object
	 */
	public function __construct()
	{
		require('../config.php');
		$this->db = new PDO("mysql:host={$config['database']['host']};dbname={$config['database']['db_name']}", $config['database']['user'], $config['database']['password']);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}
?>
