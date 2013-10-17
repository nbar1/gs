<?php
/*
 * Base
 *
 * Base class for all gs classes
 */
class Base
{

	/**
	 * Config
	 */
	public $config;

	/**
	 * GrooveShark API
	 */
	public $gsapi;

	/**
	 * User
	 */
	public $user;

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
		$this->config = $config;
		$this->initTemplateEngine();
	}

	/**
	 * Get configuration file variables
	 */
	public function getDatabase()
	{
		$db = new PDO("mysql:host={$this->config['database']['host']};dbname={$this->config['database']['db_name']}", $this->config['database']['user'], $this->config['database']['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	}

	/**
	 * Returns the gsAPI object
	 *
	 * @returns gsAPI GrooveShark API
	 */
	protected function getGsAPI()
	{
		if (!isset($this->gsapi))
		{
			$this->gsapi = new gsAPI($this->config['api']['key'], $this->config['api']['secret']);
		}
		return $this->gsapi;
	}

	/**
	 * Returns the User object
	 *
	 * @returns User
	 */
	protected function getUser()
	{
		if (!isset($this->user))
		{
			$this->user = new User();
		}
		return $this->user;
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
