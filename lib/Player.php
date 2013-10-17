<?php
/**
 * Player
 *
 * Controls the music player
 */

class Player extends Base
{
	/**
	 * Returns the stream info for the flash player
	 *
	 * @param int Song token
	 * @return string JSON encoded stream information
	 */
	public function getStream($song)
	{
		// Session caching
		if (isset($_SESSION['gsSession']) && !empty($_SESSION['gsSession']))
		{
			$this->getGsAPI()->setSession($_SESSION['gsSession']);
		}
		else {
			$_SESSION['gsSession'] = $this->getGsAPI()->startSession();
		}
		
		// Country caching
		if (isset($_SESSION['gsCountry']) && !empty($_SESSION['gsCountry']))
		{
			$this->getGsAPI()->setCountry($_SESSION['gsCountry']);
		}
		else {
			$_SESSION['gsCountry'] = $this->getGsAPI()->getCountry();
		}

		$this->getGsAPI()->authenticate($this->config['grooveshark']['username'], $this->config['grooveshark']['password']);

		// Make request to Grooveshark and return data as JSON
		$streamInfo = $this->getGsAPI()->getSubscriberStreamKey($song, false);
		
		echo json_encode($streamInfo);
	}

	/**
	 * Get Next Song
	 *
	 * Gets the token of the next song
	 * 
	 * @return string Song token
	 */
	public function getNextSong()
	{
		$dbh = $this->getDatabase()->prepare("SELECT id, token, status FROM queue WHERE status='queued' OR status='playing' ORDER BY status ASC, priority ASC, position ASC LIMIT 2");
		$dbh->execute();
		$rows = $dbh->fetchAll(PDO::FETCH_ASSOC);
		$x=0;
		foreach($rows as $row)
		{
			if($x === 0)
			{
				if($row['status'] === "playing")
				{
					$this->markSongPlayed($row['id']);
					$x++;
				}
				else {
					$this->markSongPlaying($row['id']);
					return $row['token'];
				}
			}
			else {
				$this->markSongPlaying($row['id']);
				return $row['token'];
			}
		}
	}

	/**
	 * Mark Song Played
	 *
	 * @param int song id
	 */
	public function markSongPlayed($id)
	{
		$dbh = $this->getDatabase()->prepare("UPDATE queue SET status='played' WHERE id=?");
		$dbh->execute(array($id));
	}

	/**
	 * Mark Song Playing
	 *
	 * @param int song id
	 */
	public function markSongPlaying($id)
	{
		$dbh = $this->getDatabase()->prepare("UPDATE queue SET status='playing' WHERE id=?");
		$dbh->execute(array($id));
	}

	/**
	 * Send data to view
	 *
	 * return string
	 */
	public function renderView()
	{
		return $this->templateEngine->draw('player');
	}
}
?>