<?php
/*
 * Base
 *
 * Base class for all gs classes
 */
class Base
{

	/**
	 * Database object
	 */
	public $db;

	/**
	 * Template engine
	 */
	public $templateEngine;

	/**
	 * Constructor
	 *
	 * Initiate the database object
	 */
	public function __construct()
	{
		require('config.php');
		$this->initTemplateEngine();
		$this->db = new PDO("mysql:host={$config['database']['host']};dbname={$config['database']['db_name']}", $config['database']['user'], $config['database']['password']);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Initiate template engine
	 */
	private function initTemplateEngine()
	{
		$this->templateEngine = new \Rain\Tpl;
		$config = array(
			"tpl_dir"	=> "assets/templates/",
			"cache_dir"	=> "tmp/",
			"tpl_ext"	=> "phtml",
		);
		$this->templateEngine->configure($config);
	}
}
?>
