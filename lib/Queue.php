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
		return $this->getDao()->getQueue();
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
			$song_has_metadata = $song->hasMetadata();
			xdebug_break();
			if(!$song_has_metadata) $song->storeMetadata();

			if($song->getPriority() === 'high')
			{
				return json_encode(array('msg'=>'song promoted'));
			}
			else {
				return json_encode(array('msg'=>'song added'));
			}
		}
		else {
			return json_encode(array('msg'=>'error adding song'));
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
		// Check if song is queued
		if(!$this->isSongInQueue($song->getToken())) return json_encode(array('msg'=>'song is not queued'));
		// Check if given user has a promotion available
		if($user->getAvailablePromotions() < 1) return json_encode(array('msg'=>'no available promotions'));
		// Set the song priority and return the status
		return ($this->getDao()->setSongPriority('high', $user->getId(), $song->getId())) ? json_encode(array('msg'=>'song promoted')) : json_encode(array('msg'=>'error promoting song'));
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

	/**
	 * Send data to view
	 *
	 * @return string
	 */
	public function renderView($view = null)
	{
		if($this->getUser()->getUserBySession() === true)
		{
			$queue = $this->getQueue();
			for($x=0; $x<sizeof($queue); $x++)
			{
				$queue[$x]['played_by_name'] = $this->getUser()->getNicknameById($queue[$x]['played_by']);
			}

			$this->templateEngine->assign('queue', $queue);
			return $this->templateEngine->draw('queue');
		}
		else {
			return $this->getUser()->renderView();
		}
	}
}
?>