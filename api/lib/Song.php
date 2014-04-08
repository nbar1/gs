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
	 * Song artist id
	 */
	private $artist_id;

	/**
	 * Song image
	 */
	private $image;

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
	 * @param int|null $id ID of currently queued song
	 * @param bool $tokenAsId Builds a song with a token instead of an ID
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
	 * Get song artist id
	 *
	 * @return string|null Song Artist ID
	 */
	public function getArtistId()
	{
		return ($this->artist_id !== null) ? $this->artist_id : null;
	}

	/**
	 * Get song image
	 *
	 * @return string|null Song Image URL
	 */
	public function getImage()
	{
		return ($this->image !== null) ? $this->image : null;
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
	 * @param int $id Song ID
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
	 * @param int $token Song token
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
	 * @param strubg $tutke Song title
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
	 * @param string $artist Artist name
	 * @return Song $this
	 */
	public function setArtist($artist)
	{
		$this->artist = $artist;
		return $this;
	}

	/**
	 * Set song artist id
	 *
	 * @param int $artist_id Artist ID
	 * @return Song $this
	 */
	public function setArtistId($artist_id)
	{
		$this->artist_id = $artist_id;
		return $this;
	}

	/**
	 * Set song image
	 *
	 * @param string $image Image filename
	 * @return Song $this
	 */
	public function setImage($image)
	{
		$image = str_replace("/90_", "", $image);
		$this->image = $image;
		return $this;
	}

	/**
	 * Set song status
	 *
	 * @param string $status Status of song
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
	 * @param string $priority Song priority
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
	 * @param int $position Queue position
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
	 * @param int $userid User who played song
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
	 * @param int $userid User who promoted song
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
	 * @return bool
	 */
	private function loadSongInformation()
	{
		$identifier = (isset($this->id)) ? $this->id : $this->token;
		if ($identifier)
		{
			$song = $this->getDao()->getSongInformation($identifier);
			if ($song && sizeof($song) > 0)
			{
				try
				{
					$this->setId($song['id'])
						->setToken($song['token'])
						->setTitle($song['title'])
						->setArtist($song['artist'])
						->setArtistId($song['artist_id'])
						->setImage($song['image'])
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
	 * @param string $artist_id Song artist id
	 * @param string $priority Song priority
	 * @return bool
	 */
	public function setSongInformation($token, $title, $artist, $artist_id, $image, $priority='low')
	{
		try
		{
			$this->setToken($token)
				->setTitle($title)
				->setArtist($artist)
				->setArtistId($artist_id)
				->setImage($image)
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
	 * @return array Song information
	 */
	public function getSongInformation()
	{
		return array(
			'id' => $this->getId(),
			'token' => $this->getToken(),
			'title' => $this->getTitle(),
			'artist' => $this->getArtist(),
			'artist_id' => $this->getArtistId(),
			'image' => $this->getImage(),
		);
	}

	/**
	 * Get Song Information From GrooveShark
	 */
	public function getSongInformationFromGrooveShark($song_id)
	{
		$this->authenticateToGrooveShark();
		return $this->getGsAPI()->getSongInfo($song_id);
	}

	/**
	 * Store Metadata
	 *
	 * Store song metadata in database
	 *
	 * @return bool Status
	 */
	public function storeMetadata()
	{
		return $this->getDao()->storeSongMetadata(array($this->getToken(), $this->getTitle(), $this->getArtist(), $this->getArtistId(), $this->getImage()));
	}

	/**
	 * Has Metadata
	 *
	 * Check the database to see if the song has metadata stored
	 *
	 * @return bool Song has metadata
	 */
	public function hasMetadata()
	{
		return $this->getDao()->songHasMetadata($this->getToken());
	}
}
?>