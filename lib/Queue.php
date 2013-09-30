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
		return $this->dbh->fetchAll(PDO::FETCH_ASSOC);
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
	 * @param int Token of song
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

	/**
	 * getNextSong
	 *
	 * Returns the information for the next song in the queue
	 *
	 * @TODO this may be able to be cleaned up better - break out markSongComplete, markSongPlaying, markSongPlayed, getNextSongId
	 * @TODO move to Player class
	 *
	 * @return array Song information
	 */
	public function getNextSong()
	{
		$this->dbh = $this->db->prepare("SELECT id, token, status FROM queue WHERE status IN('queued', 'playing') ORDER BY status ASC, priority ASC, position ASC LIMIT 2");
		$this->dbh->execute();
		$songs = $this->dbh->fetchAll(PDO::FETCH_ASSOC);

		foreach($songs as $row)
		{
			if($row == $songs[0])
			{
				if($row['status'] === "playing")
				{
					$this->dbh = $this->db->prepare("UPDATE queue SET status=? WHERE id=?");
					$this->dbh->execute(array("played", $row['id']));
					continue;
				}
				else {
					$this->dbh = $this->db->prepare("UPDATE queue SET status=? WHERE id=?");
					$this->dbh->execute(array("playing", $row['id']));
					return $row['token'];
				}
			}
			else {
				$this->dbh = $this->db->prepare("UPDATE queue SET status=? WHERE id=?");
				$this->dbh->execute(array("playing", $row['id']));
				return $row['token'];
			}
		}
	}

	/**
	 * Get nickname by ID
	 *
	 * @param int User ID
	 * return string
	 */
	public function getNicknameById($id)
	{
		$this->dbh = $this->db->prepare("SELECT nickname FROM users WHERE id=? LIMIT 1");
		$this->dbh->execute(array($id));
		$row = $this->dbh->fetch(PDO::FETCH_ASSOC);
		return $row['nickname'];
	}

	/**
	 * Send data to view
	 *
	 * return string
	 */
	public function renderView($view = null)
	{
		$user = new User();
		if($user->getUserByCookie() === true)
		{
			$queue = $this->getQueue();
			for($x=0; $x<sizeof($queue); $x++)
			{
				$queue[$x]['played_by_name'] = $this->getNicknameById($queue[$x]['played_by']);
			}
			
			$this->templateEngine->assign("queue", $queue);
			return $this->templateEngine->draw('queue');
		}
		else {
			return $user->renderView();
		}
	}
}
?>