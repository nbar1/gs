<?php
/**
 * Queue
 *
 * Contains information about the current queue
 */

class Queue extends Base
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
	 * @return array Queued songs
	 */
	public function getQueue()
	{
		$this->dbh = $this->db->prepare("SELECT songs.token, songs.title, songs.artist, queue.position, queue.status, queue.priority, queue.played_by, queue.promoted_by FROM queue INNER JOIN songs ON songs.token = queue.token WHERE queue.status IN('queued', 'playing') ORDER BY queue.status ASC, queue.priority ASC, queue.position ASC");
		$this->dbh->execute();
		$this->dbh->setFetchMode(PDO::FETCH_ASSOC);
		return $this->dbh->fetchAll();
	}

	/**
	 * Add Song To Queue
	 *
	 * @param Song Song to be added
	 * @param User User that added song
	 * @return string Message sent to user
	 */
	public function addSongToQueue(Song $song, User $user)
	{
		if($this->isSongInQueue($song->getToken())) return 'song already queued';
		if($user->getAvailablePromotions() < 1) $song->setPriority('low');
		$promoted_user = ($song->getPriority() === 'high') ? $user->getId() : null;

		$data = array(
			$song->getToken(),
			$song->getPriority(),
			$this->getNextQueuePosition(),
			date("Y-m-d H:i:s"),
			$user->getId(),
			$promoted_user,
		);
		$this->dbh = $this->db->prepare('INSERT INTO queue (token, priority, position, ts_added, played_by, promoted_by) VALUES (?, ?, ?, ?, ?, ?)');
		if($this->dbh->execute($data))
		{
			if($song->hasMetadata() === false) $song->storeMetadata();
			if($song->getPriority() === 'high') return 'song promoted';
			else return 'song added';
		}
		else {
			return 'error adding song';
		}
	}

	/**
	 * Promote Song In Queue
	 *
	 * @param Song Song to be promoted
	 * @param User User that promoted song
	 * @return string Message sent to user
	 */
	public function promoteSongInQueue(Song $song, User $user)
	{
		if(!$this->isSongInQueue($song->getToken())) return 'song is not queued';
		if($user->getAvailablePromotions() < 1) return 'no available promotions';

		$this->dbh = $this->db->prepare('UPDATE queue SET priority=?, promoted_by=? WHERE id=?');
		if($this->dbh->execute(array('high', $user->getId(), $song->getId()))) return 'song promoted';
		else return 'error promoting song';
	}

	/**
	 * Is Song In Queue
	 *
	 * @param string Token of song
	 * @return bool
	 */
	private function isSongInQueue($token)
	{
		$this->dbh = $this->db->prepare("SELECT id FROM queue WHERE status='queued' AND token=? LIMIT 1");
		$this->dbh->execute(array($token));

		return ($this->dbh->rowCount() > 0) ? true : false;
	}

	/**
	 * Get Next Queue Position
	 *
	 * Returns the next available queue slot
	 *
	 * @return int
	 */
	private function getNextQueuePosition()
	{
		$this->dbh = $this->db->prepare("SELECT MAX(position) FROM queue");
		$this->dbh->execute();
		$songPosition = $this->dbh->fetchColumn();

		if(!$songPosition) return 1;
		else return ++$songPosition;
	}
}
?>