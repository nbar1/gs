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
	 * Database
	 */
	public $db;

	/**
	 * GrooveShark API
	 */
	public $gsapi;

	/**
	 * Session
	 */
	public $session;

	/**
	 * User
	 */
	public $user;

	/**
	 * Queue
	 */
	public $queue;

	/**
	 * Song
	 */
	public $song;

	/**
	 * Autoplay
	 */
	public $autoplay;

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
	 * Get database object
	 *
	 * @return PDO Database object
	 */
	public function getDatabase()
	{
		if(!isset($this->db))
		{
			$db = new PDO("mysql:host={$this->config['database']['host']};dbname={$this->config['database']['db_name']}", $this->config['database']['user'], $this->config['database']['password']);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $db;
		}
		return $this->db;
	}

	/**
	 * Returns the gsAPI object
	 *
	 * @return gsAPI GrooveShark API
	 */
	public function getGsAPI()
	{
		if (!isset($this->gsapi))
		{
			$this->gsapi = new gsAPI($this->config['api']['key'], $this->config['api']['secret']);
		}
		return $this->gsapi;
	}

	/**
	 * Returns the Session object
	 *
	 * @return Session
	 */
	public function getSession()
	{
		if (!isset($this->session))
		{
			$this->session = new Session();
		}
		return $this->session;
	}

	/**
	 * Returns the User object
	 *
	 * @return User
	 */
	public function getUser()
	{
		if (!isset($this->user))
		{
			$this->user = new User();
		}
		return $this->user;
	}

	/**
	 * Returns the Queue object
	 *
	 * @return User
	 */
	public function getQueue()
	{
		if (!isset($this->queue))
		{
			$this->queue = new Queue();
		}
		return $this->queue;
	}

	/**
	 * Returns the Song object
	 *
	 * @return Song
	 */
	public function getSong()
	{
		if (!isset($this->song))
		{
			$this->song = new Song();
		}
		return $this->song;
	}

	/**
	 * Returns the Autoplay object
	 *
	 * @return Autoplay
	 */
	public function getAutoplay()
	{
		if (!isset($this->autoplay))
		{
			$this->autoplay = new Autoplay();
		}
		return $this->autoplay;
	}

	/**
	 * Authenticate to GrooveShark
	 */
	public function authenticateToGrooveShark()
	{
		// Session caching
		if (isset($_SESSION['gsSession']) && !empty($_SESSION['gsSession']))
		{
			$this->getGsAPI()->setSession($_SESSION['gsSession']);
		}
		else {
			$_SESSION['gsSession'] = $this->getGsAPI()->startSession();
		}

		// Country caching
		if (isset($_SESSION['gsCountry']) && !empty($_SESSION['gsCountry']))
		{
			$this->getGsAPI()->setCountry($_SESSION['gsCountry']);
		}
		else {
			$_SESSION['gsCountry'] = $this->getGsAPI()->getCountry();
		}
		$this->getGsAPI()->authenticate($this->config['grooveshark']['username'], $this->config['grooveshark']['password']);
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
