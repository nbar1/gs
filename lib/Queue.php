<?php
/**
 * Queue
 *
 * Contains information about the current queue
 */

class Queue extends Base
{
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
	 * @return array|bool Song information
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
	 * @return array Song information
	 */
	public function getNextSong()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, token FROM queue WHERE status='queued' ORDER BY priority ASC, position ASC LIMIT 1");
		$dbh->execute();

		if($dbh->rowCount() > 1) return false;
		return $dbh->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Add Song To Queue
	 *
	 * @param Song $song Song to be added
	 * @param User $user User that added song
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
			$user->getId(),
			$promoted_user,
		);
		$dbh = $this->getDatabase()->prepare('INSERT INTO queue (token, priority, position, ts_added, played_by, promoted_by) VALUES (?, ?, ?, NOW(), ?, ?)');
		if($dbh->execute($data))
		{
			if($song->hasMetadata() === false) $song->storeMetadata();
			
			if($song->getPriority() === 'high') 
			{
				return 'song promoted';
			}
			else {
				return 'song added';
			}
		}
		else {
			return 'error adding song';
		}
	}

	/**
	 * Promote Song In Queue
	 *
	 * @param Song $song Song to be promoted
	 * @param User $user User that promoted song
	 * @return string Message sent to user
	 */
	public function promoteSongInQueue(Song $song, User $user)
	{
		if(!$this->isSongInQueue($song->getToken())) return 'song is not queued';
		if($user->getAvailablePromotions() < 1) return 'no available promotions';

		$dbh = $this->getDatabase()->prepare('UPDATE queue SET priority=?, promoted_by=? WHERE id=?');
		if($dbh->execute(array('high', $user->getId(), $song->getId()))) return 'song promoted';
		else return 'error promoting song';
	}

	/**
	 * Is Song In Queue
	 *
	 * @param int $token Token of song
	 * @return bool
	 */
	private function isSongInQueue($token)
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
	private function getNextQueuePosition()
	{
		$dbh = $this->getDatabase()->prepare("SELECT MAX(position) FROM queue");
		$dbh->execute();
		$songPosition = $dbh->fetchColumn();

		if(!$songPosition) return 1;
		else return ++$songPosition;
	}

	/**
	 * Send data to view
	 *
	 * @return string
	 */
	public function renderView($view = null)
	{
		if($this->getUser()->getUserByCookie() === true)
		{
			$queue = $this->getQueue();
			for($x=0; $x<sizeof($queue); $x++)
			{
				$queue[$x]['played_by_name'] = $this->getUser()->getNicknameById($queue[$x]['played_by']);
			}
			
			$this->templateEngine->assign("queue", $queue);
			return $this->templateEngine->draw('queue');
		}
		else {
			return $this->getUser()->renderView();
		}
	}
}
?>