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
		$queue = $this->getDao()->getQueue();
		$queue = $this->purgeOldQueueData($queue);
		for($x=0; $x<sizeof($queue); $x++)
		{
			$queue[$x]['played_by_name'] = $this->getUser()->getUsernameById($queue[$x]['played_by']);
		}
		return $queue;
	}

	/**
	 * Purge Old Queue Data
	 *
	 * Removes an item marked as playing if it's been
	 * more than 15 minutes since it was played.
	 *
	 * @param array $queue
	 * @return array Modified queue
	 */
	public function purgeOldQueueData($queue)
	{
		if($queue[0]['status'] == 'playing' && date('YmdHi', strtotime($queue[0]['ts_played'])) < (date('YmdHi') - 15))
		{
			$player = new Player();
			$player->markSongPlayed($queue[0]['id']);
			array_shift($queue);
		}
		return $queue;
	}

	/**
	 * Get Playing Song
	 *
	 * @return array|bool Song information
	 */
	public function getPlayingSong()
	{
		return $this->getDao()->getPlayingSong();
	}

	/**
	 * Get Next Song
	 *
	 * @return array Song information
	 */
	public function getNextSong()
	{
		return $this->getDao()->getNextSong();
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
		if($this->isSongInQueue($song->getToken())) return json_encode(array('msg'=>'song already queued'));
		if($user->getAvailablePromotions() < 1) $song->setPriority('low');
		$promoted_user = ($song->getPriority() === 'high') ? $user->getId() : null;

		$data = array(
			$song->getToken(),
			$song->getPriority(),
			$this->getNextQueuePosition(),
			$user->getId(),
			$promoted_user,
		);
		if($this->getDao()->addSongToQueue($data))
		{
			if(!$song->hasMetadata()) 
			{
				$song->storeMetadata();
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Is Song In Queue
	 *
	 * @param int $token Token of song
	 * @return bool
	 */
	private function isSongInQueue($token)
	{
		return $this->getDao()->isSongInQueue($token);
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
		$song_position = $this->getDao()->getNextQueuePosition();
		return (!$song_position) ? 1 : ++$song_position;
	}
}
?>