<?php
/*
 * Base
 *
 * Base class for all gs classes
 */
class Base
{
	/**
	 * Data access object
	 */
	private $dao;

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
	 * Get data access object
	 *
	 * @return Dao Data access object
	 */
	public function getDao()
	{
		if(!isset($this->dao))
		{
			$this->dao = new Dao();
		}
		return $this->dao;
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
			$this->gsapi = new gsAPI(GSAPI_KEY, GSAPI_SECRET);
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
		$this->getGsAPI()->authenticateToken(GROOVESHARK_TOKEN);
	}
}
?>
