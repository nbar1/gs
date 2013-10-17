<?php
/**
 * Song
 *
 * Contains information about a new or currently queued song
 */

class Song extends Base
{
	/**
	 * Song ID
	 */
	private $id;

	/**
	 * Song token
	 */
	private $token;

	/**
	 * Song title
	 */
	private $title;

	/**
	 * Song artist
	 */
	private $artist;

	/**
	 * Song status
	 */
	private $status;

	/**
	 * Song priority
	 */
	private $priority;

	/**
	 * Song position
	 */
	private $position;

	/**
	 * Song played by
	 */
	private $played_by;

	/**
	 * Song promoted by
	 */
	private $promoted_by;

	/**
	 * Constructor
	 *
	 * If given an ID, it will construct with a currently queued song
	 *
	 * @param int|null ID of currently queued song
	 * @param bool True if token is ID
	 */
	function __construct($id = null, $tokenAsId = false)
	{
		parent::__construct();
		if ($id !== null)
		{
			($tokenAsId) ? $this->setToken($id) : $this->setId($id);
			$this->loadSongInformation();
		}
	}

	/**
	 * Get song id
	 *
	 * @return int|null Song ID
	 */
	public function getId()
	{
		return ($this->id !== null) ? $this->id : null;
	}

	/**
	 * Get song token
	 *
	 * Token is used for interfacing with GrooveShark
	 *
	 * @return string|null Song Token
	 */
	public function getToken()
	{
		return ($this->token !== null) ? $this->token : null;
	}

	/**
	 * Get song title
	 *
	 * @return string|null Song Title
	 */
	public function getTitle()
	{
		return ($this->title !== null) ? $this->title : null;
	}

	/**
	 * Get song artist
	 *
	 * @return string|null Song Artist
	 */
	public function getArtist()
	{
		return ($this->artist !== null) ? $this->artist : null;
	}

	/**
	 * Get song status
	 *
	 * @return string|null Song Status
	 */
	public function getStatus()
	{
		return ($this->status !== null) ? $this->status : null;
	}

	/**
	 * Get song priority
	 *
	 * @return string Song Priority
	 */
	public function getPriority()
	{
		return ($this->priority !== null) ? $this->priority : null;
	}

	/**
	 * Get song position
	 *
	 * @return int|null Song Position
	 */
	public function getPosition()
	{
		return ($this->position !== null) ? $this->position : null;
	}

	/**
	 * Get song played by
	 *
	 * @return int|null ID of User
	 */
	public function getPlayedBy()
	{
		return ($this->played_by !== null) ? $this->played_by : null;
	}

	/**
	 * Get song promoted by
	 *
	 * @return int|null ID of User
	 */
	public function getPromotedBy()
	{
		return ($this->promoted_by !== null) ? $this->promoted_by : null;
	}

	/**
	 * Set song id
	 *
	 * @return Song $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Set song token
	 *
	 * Token is used for interfacing with GrooveShark
	 *
	 * @return Song $this
	 */
	public function setToken($token)
	{
		$this->token = $token;
		return $this;
	}

	/**
	 * Set song title
	 *
	 * @return Song $this
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Set song artist
	 *
	 * @return Song $this
	 */
	public function setArtist($artist)
	{
		$this->artist = $artist;
		return $this;
	}

	/**
	 * Set song status
	 *
	 * @return Song $this
	 */
	public function setStatus($status)
	{
		$allowed_statuses = array(
			'playing',
			'queued',
			'played',
			'skipped',
			'deleted',
			'error',
		);
		// Check if given status is allowed, if not set to 'error'
		if (!in_array($status, $allowed_statuses)) $status = 'error';

		$this->status = $status;
		return $this;
	}

	/**
	 * Set song priority
	 *
	 * @return Song $this
	 */
	public function setPriority($priority)
	{
		$allowed_priorities = array(
			'low',
			'med',
			'high',
		);
		// Check if given priority is allowed, if not set to default 'low'
		if (!in_array($priority, $allowed_priorities)) $priority = 'low';

		$this->priority = $priority;
		return $this;
	}

	/**
	 * Set song position
	 *
	 * @return Song $this
	 */
	public function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}

	/**
	 * Set song played by
	 *
	 * @return Song $this
	 */
	public function setPlayedBy($userid)
	{
		$this->played_by = $userid;
		return $this;
	}

	/**
	 * Set song promoted by
	 *
	 * @return Song $this
	 */
	public function setPromotedBy($userid)
	{
		$this->promoted_by = $userid;
		return $this;
	}

	/**
	 * Load Song Information
	 *
	 * Loads information from the database about the song currenly designated by $id
	 *
	 * return bool
	 */
	private function loadSongInformation()
	{
		$identifier = (isset($this->id)) ? $this->id : $this->token;
		if ($identifier)
		{
			$dbh = $this->getDatabase()->prepare("SELECT songs.id, songs.token, songs.title, songs.artist, queue.position, queue.status, queue.priority, queue.played_by, queue.promoted_by FROM queue INNER JOIN songs ON songs.token = queue.token WHERE songs.id=? OR songs.token=? ORDER BY queue.position DESC LIMIT 1");
			$dbh->execute(array($identifier, $identifier));
			if ($dbh->rowCount() > 0)
			{
				$song = $dbh->fetch(PDO::FETCH_ASSOC);
				try
				{
					$this->setId($song['id'])
						->setToken($song['token'])
						->setTitle($song['title'])
						->setArtist($song['artist'])
						->setPriority($song['priority'])
						->setPosition($song['position'])
						->setPlayedBy($song['played_by'])
						->setPromotedBy($song['promoted_by'])
						->setStatus($song['status']);

					return true;
				}
				catch (Exception $e) {
					trigger_error($e->getMessage(), E_USER_ERROR);
					return false;
				}
			}
			else {
				throw new Exception("Tried to load song information for invalid song");
			}
		}
		else {
			throw new Exception("Tried to load song information with no valid song ID");
		}
	}

	/**
	 * Set Song Information
	 *
	 * Set the song information from given data
	 *
	 * @param string $token Song token for interfaceing with Grooveshark
	 * @param string $title Song title
	 * @param string $artist Song artist
	 * @param string $priority Song priority
	 * @return bool
	 */
	public function setSongInformation($token, $title, $artist, $priority='low')
	{
		try
		{
			$this->setToken($token)
				->setTitle($title)
				->setArtist($artist)
				->setPriority($priority);
			return true;
		}
		catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}

	/**
	 * Get Song Information
	 *
	 * Get the song information
	 *
	 * @return array
	 */
	public function getSongInformation()
	{
		return array(
			'id' => $this->getId(),
			'token' => $this->getToken(),
			'title' => $this->getTitle(),
			'artist' => $this->getArtist(),
		);
	}

	/**
	 * Store Metadata
	 *
	 * Store song metadata in database
	 *
	 * @return bool
	 */
	public function storeMetadata()
	{
		$dbh = $this->getDatabase()->prepare('INSERT INTO songs (token, title, artist) VALUES (?, ?, ?)');
		try {
			return $dbh->execute(array($this->getToken(), $this->getTitle(), $this->getArtist()));
		}
		catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}

	/**
	 * Has Metadata
	 *
	 * Check the database to see if the song has metadata stored
	 *
	 * @return bool
	 */
	public function hasMetadata()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM songs WHERE token=? LIMIT 1");
		try {
			$dbh->execute(array($this->getToken()));
			return ($dbh->rowCount() > 0) ? true : false;
		}
		catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}
}
?>