<?php
/**
 * Session
 *
 * Handles an individual listening session
 */

class Session extends Base
{
	/**
	 * Session ID
	 */
	public $id;

	/**
	 * Session title
	 */
	public $title;

	/**
	 * Session host
	 */
	public $host;

	/**
	 * Session coordinates
	 */
	public $coordinates;

	/**
	 * Session started timestamp
	 */
	public $started;

	/**
	 * Start session
	 *
	 * Starts a new listening session
	 *
	 * @param string $title Title of session
	 * @param int $host Host users ID
	 * @param bool $locationBased Location based session
	 * @param string|null $coordinates Coordinates of session host
	 */
	public function startSession($title, $host, $location_based=true, $latitude=null, $longitude=null)
	{
		$this->title = $title;
		$this->host = $host;
		$this->coordinates = $coordinates;

		if ($location_based && $this->matchSessionToCoordinates($coordinates) != false)
		{
			// bomb because a session is already available here
		}
		else {
			// we can create a session that is location based
			$this->getDao()->storeNewSessionData(array($title, $host, $locationBased, $latitude.",".$longitude));
		}
	}

	/**
	 * Set session
	 *
	 * @param int $session_id Session ID
	 * @return true
	 */
	public function setSession($session_id)
	{
		$_SESSION['listening_session_id'] = $session_id;
		return true;
	}

	/**
	 * Get session match based on coordinates
	 *
	 * @param int $latitude
	 * @param int $longitude
	 * @return int|bool Session ID
	 */
	public function matchSessionToCoordinates($latitude, $longitude)
	{
		$coordinates = $latitude.",".$longitude;
		return Helpers_Geolocation::getLocationMatch($coordinates, $this->getActiveSessions());
	}

	/**
	 * Get active sessions
	 *
	 * @return array Active sessions within 24 hours
	 */
	public function getActiveSessions()
	{
		return $this->getDao()->getActiveSessions();
	}

	/**
	 * Check if session is active
	 *
	 * @param int $session_id Session ID
	 * @return array Active sessions within 24 hours
	 */
	public function checkIfSessionIsActive($session_id)
	{
		return $this->getDao()->checkIfSessionIsActive($session_id);
	}
}