<?php
/**
 * Queue
 *
 * Contains information about the current queue
 */

class Queue extends gs
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get Queue
	 *
	 * @return Array Queued songs
	 */
	public static function getQueue()
	{
		$this->dbh = $this->db->prepare("SELECT * FROM queue WHERE status=? OR status=? ORDER BY status ASC, priority ASC, position ASC");
		$this->dbh->execute(array("queued", "playing"));
		$this->dbh->setFetchMode(PDO::FETCH_ASSOC);
		return $this->dbh->fetchAll();
	}

	/**
	 * Add Song To Queue
	 *
	 * @param Song Song to be added
	 * @param User User that added song
	 */
	public static function addSongToQueue(Song $song, User $user)
	{
		if($this->isSongInQueue($song->getToken())) return 'song already queued';
		if($user->getAvailablePromotions() < 1) $song->setPriority('low');
		$promoted_user = ($song->getPriority() === 'high') ? $user->getId() : null;

		$data = array(
			$song->getToken(),
			$song->getTitle(),
			$song->getArtist(),
			$song->getPriority(),
			self::getNextQueuePosition(),
			date("Y-m-d H:i:s"),
			$user->getId(),
			$promoted_user,
		);
		$this->dbh = $this->db->prepare('INSERT INTO queue (token, title, artist, priority, position, ts_added, played_by, promoted_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
		if($this->dbh->execute($data))
		{
			if($song->getPriority() === 'high') return 'song promoted';
			else return 'song added';
		}
		else {
			return 'error adding song';
		}
	}

	/**
	 * Promite Song In Queue
	 *
	 * @param Song Song to be promoted
	 * @param User User that promoted song
	 */
	public static function promoteSongInQueue(Song $song, User $user)
	{
		if(!self::isSongInQueue($song->getToken())) return 'song is not queued';
		if($user->getAvailablePromotions() < 1) return 'no available promotions';

		$this->dbh = $this->db->prepare('UPDATE queue SET priority=?, promoted_by=? WHERE id=?');
		if($this->dbh->execute(array('high', $user->getId(), $song->getId()))) return 'song promoted';
		else return 'error promoting song';
	}

	/**
	 * Is Song In Queue
	 *
	 * @param int Song ID
	 * @return bool
	 */
	private static function isSongInQueue($token)
	{
		echo $token;
		$this->dbh = $this->db->prepare("SELECT id FROM queue WHERE status=? AND token=? LIMIT 1");
		$this->dbh->execute(array("queued", $token));

		if($this->dbh->rowCount() > 0) return true;
		else return false;
	}

	/**
	 * Get Next Queue Position
	 *
	 * Returns the next available queue slot
	 *
	 * @return int
	 */
	private static function getNextQueuePosition()
	{
		$this->dbh = $this->db->prepare("SELECT MAX(position) FROM queue");
		$this->dbh->execute();
		$songPosition = $this->dbh->fetchColumn();

		if(!$songPosition) return 1;
		else return ++$songPosition;
	}
}
?>