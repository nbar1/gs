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
	public function startSession($title, $host, $locationBased = true, $coordinates = null)
	{
		$this->title = $title;
		$this->host = $host;
		$this->coordinates = $coordinates;
		
		if($this->getSessionMatch($coordinates) === false)
		{
			$dbh = $this->getDatabase()->prepare("INSERT INTO sessions (title, host, location_based, coordinates, started) VALUES (?, ?, ?, ?, NOW())");
			$dbh->execute(array($title, $host, $locationBased, $coordinates));
		}
	}

	/**
	 * Get session match based on coordinates
	 *
	 * @param string $coordinates lat,long
	 * @return int|bool Session ID
	 */
	public function getSessionMatch($coordinates)
	{
		return GeoLocation::getLocationMatch($coordinates, $this->getActiveSessions());
	}

	/**
	 * Get active sessions
	 *
	 * @return array Active sessions within 24 hours
	 */
	public function getActiveSessions()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, title, host, location_based, coordinates FROM sessions WHERE active = 1 AND started > timestampadd(hour, -24, now())");
		$dbh->execute();

		return $dbh->fetchAll(PDO::FETCH_ASSOC);
	}
}