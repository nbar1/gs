<?php
/*
 * Dao
 *
 * Data access object
 */
class Dao extends Base
{
	/**
	 * Database
	 */
	protected $db;

	/**
	 * Get database object
	 *
	 * @return PDO Database object
	 */
	protected function getDatabase()
	{
		if (!isset($this->db))
		{
			$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $db;
		}
		return $this->db;
	}

	/**
	 * Get recent songs
	 *
	 * @return array Recent songs
	 */
	public function getRecentSongs()
	{
		$dbh = $this->getDatabase()->prepare("SELECT DISTINCT songs.artist_id, songs.token FROM queue INNER JOIN songs ON songs.token = queue.token WHERE queue.played_by <> 0 ORDER BY queue.position DESC LIMIT 10");
		$dbh->execute();
		return $dbh->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Mark Song Played
	 *
	 * @param int $id song id
	 * @return true
	 */
	public function markSongPlayed($id)
	{
		$dbh = $this->getDatabase()->prepare("UPDATE queue SET status='played' WHERE id=?");
		return $dbh->execute(array($id));
	}

	/**
	 * Mark Song Playing
	 *
	 * @param int $id song id
	 * @return true
	 */
	public function markSongPlaying($id)
	{
		$dbh = $this->getDatabase()->prepare("UPDATE queue SET status='playing', ts_played=? WHERE id=?");
		return $dbh->execute(array(date('Y-m-d H:i:s'), $id));
		return true;
	}

	/**
	 * Get Queue
	 *
	 * @return array Queued songs
	 */
	public function getQueue()
	{
		$dbh = $this->getDatabase()->prepare("SELECT songs.token, songs.title, songs.artist, queue.position, queue.status, queue.priority, queue.played_by, queue.promoted_by FROM queue INNER JOIN songs ON songs.token = queue.token WHERE queue.status IN('queued', 'playing') ORDER BY queue.status ASC, queue.priority ASC, queue.position ASC");
		$dbh->execute();
		return $dbh->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get Playing Song
	 *
	 * @return array|false Song information
	 */
	public function getPlayingSong()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, token FROM queue WHERE status='playing' LIMIT 1");
		$dbh->execute();
		if($dbh->rowCount() > 1) return false;
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get Next Song
	 *
	 * @return array|false Song information
	 */
	public function getNextSong()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, token FROM queue WHERE status='queued' ORDER BY priority ASC, position ASC LIMIT 1");
		$dbh->execute();
		if ($dbh->rowCount() > 1) return false;
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Insert song into queue
	 *
	 * @param array $data Song Information
	 * @returns bool Status
	 */
	public function addSongToQueue($data)
	{
		if (sizeof($data) !== 5) return false;

		$dbh = $this->getDatabase()->prepare('INSERT INTO queue (token, priority, position, ts_added, played_by, promoted_by) VALUES (?, ?, ?, NOW(), ?, ?)');
		return $dbh->execute($data);
	}

	/**
	 * Set song priority
	 *
	 * @param string $priority Song priority
	 * @param int $user_id User ID
	 * @param int $song_id Song ID
	 * @return bool Status
	 */
	public function setSongPriority($priority, $user_id, $song_id)
	{
		if (!in_array($priority, array('low', 'med', 'high'))) return false;
		$dbh = $this->getDatabase()->prepare('UPDATE queue SET priority=?, promoted_by=? WHERE id=?');
		return $dbh->execute(array($priority, $user, $song));
	}

	/**
	 * Is Song In Queue
	 *
	 * @param int $token Token of song
	 * @return bool
	 */
	public function isSongInQueue($token)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM queue WHERE status='queued' AND token=? LIMIT 1");
		$dbh->execute(array($token));

		return ($dbh->rowCount() > 0) ? true : false;
	}

	/**
	 * Get Next Queue Position
	 *
	 * Returns the next available queue slot
	 *
	 * @return int
	 */
	public function getNextQueuePosition()
	{
		$dbh = $this->getDatabase()->prepare("SELECT MAX(position) FROM queue");
		$dbh->execute();
		return $dbh->fetchColumn();
	}

	/**
	 * Get Song Information
	 *
	 * @param int $song_id Song ID/Token
	 * @return array|false Song information
	 */
	public function getSongInformation($song_id)
	{
		$dbh = $this->getDatabase()->prepare("SELECT songs.id, songs.token, songs.title, songs.artist, songs.artist_id, songs.image, queue.position, queue.status, queue.priority, queue.played_by, queue.promoted_by FROM queue INNER JOIN songs ON songs.token = queue.token WHERE songs.id=? OR songs.token=? ORDER BY queue.position DESC LIMIT 1");
		$dbh->execute(array($song_id, $song_id));
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Store Song Metadata
	 *
	 * @param array $song Song information
	 * @return bool Status
	 */
	public function storeSongMetadata($song)
	{
		$dbh = $this->getDatabase()->prepare('INSERT INTO songs (token, title, artist, artist_id, image) VALUES (?, ?, ?, ?, ?)');
		return $dbh->execute($song);
	}

	/**
	 * Check if song has metadata
	 *
	 * @param $song_token Song token
	 * @return bool Song has metadata
	 */
	public function songHasMetadata($song_token)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM songs WHERE token=? LIMIT 1");
		$dbh->execute(array($song_token));
		return (bool) $dbh->rowCount();
	}

	/**
	 * Check if user exists by given API key
	 *
	 * @param string $api_key
	 * @return bool User exists
	 */
	public function getUserExistsByApiKey($api_key)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM users WHERE api_key=? LIMIT 1");
		return ($dbh->execute(array($api_key)) && $dbh->rowCount() > 0) ? true : false;
	}

	/**
	 * Get user information by api key
	 *
	 * @param string $api_key
	 * @return array|false User Information
	 */
	public function getUserInformationByApiKey($api_key)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, api_key, username, active, ts_created FROM users WHERE api_key=? LIMIT 1");
		$dbh->execute(array($api_key));
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Check if user exists by given username
	 *
	 * @param string $username
	 * @return bool Status
	 */
	public function getUserExistsByUsername($username)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
		return ($dbh->execute(array($username)) && $dbh->rowCount() > 0) ? true : false;
	}

	/**
	 * Get user information by username
	 *
	 * @param string $username
	 * @return array|false User Information
	 */
	public function getUserInformationByUsername($username)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, api_key, username, password, active, ts_created FROM users WHERE username=? LIMIT 1");
		$dbh->execute(array($username));
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get user information by ID
	 *
	 * @param string $user_id User ID
	 * @return array|false User Information
	 */
	public function getUserInformationById($user_id)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, api_key, username, active, ts_created FROM users WHERE id=? LIMIT 1");
		$dbh->execute(array($user_id));
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get number of recent promotions by a given user
	 *
	 * @param int $user_d User ID
	 * @return int Number of promotions
	 */
	public function getRecentPromotionCount($user_id)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM queue WHERE priority IN('high', 'med') AND promoted_by=? AND ts_added >= DATE_SUB(NOW(), INTERVAL " . GS_PROMOTION_TIMELIMIT . " MINUTE)");
		$dbh->execute(array($user_id));
		return $dbh->rowCount();
	}

	/**
	 * Create User
	 *
	 * @param array $user User data
	 * @return bool Status
	 */
	public function createUser($user)
	{
		if (sizeof($user) !== 5) return false;
		$dbh = $this->getDatabase()->prepare("INSERT INTO users (username, password, api_key, ts_created, ts_lastlogin) VALUES (?, ?, ?, ?, ?)");
		return $dbh->execute($user);
	}


	/**
	 * Get active sessions
	 *
	 * @return array Active sessions within 24 hours
	 */
	public function getActiveSessions()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, title, host, location_based, coordinates FROM sessions WHERE active=1 AND started > timestampadd(hour, -24, now())");
		$dbh->execute();
		return $dbh->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Check if session is active
	 *
	 * @return bool
	 */
	public function checkIfSessionIsActive($session_id)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM sessions WHERE id=? AND active=1 started > timestampadd(hour, -24, now())");
		$dbh->execute();
		return (bool) $dbh->rowCount();
	}

	/**
	 * Store new session in database
	 *
	 * @param array $session_info Session information
	 * @return array Active sessions within 24 hours
	 */
	public function storeNewSessionData($session_info)
	{
		$dbh = $this->getDatabase()->prepare("INSERT INTO sessions (title, host, location_based, coordinates, started) VALUES (?, ?, ?, ?, NOW())");
		return $dbh->execute($session_info);
	}
}
?>
