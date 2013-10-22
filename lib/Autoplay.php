<?php
/**
 * Autoplay
 *
 * Processes information for auto play
 */

class Autoplay extends Base
{
	/**
	 * Get recent songs
	 *
	 * @return array Recent songs
	 */
	private function getRecentSongs()
	{
		$dbh = $this->getDatabase()->prepare("SELECT DISTINCT songs.artist_id, songs.token FROM queue INNER JOIN songs ON songs.token = queue.token WHERE queue.played_by <> 0 ORDER BY queue.position DESC LIMIT 10");
		$dbh->execute();
		return $dbh->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Build auto play parameters
	 *
	 * @return array Auto play parameters
	 */
	private function buildParameters()
	{
		$parameters = array(
			'artistIDs' => array(),
			'songIDs' => array(),
		);

		foreach($this->getRecentSongs() as $song)
		{
			$parameters['artistIDs'][] = intval($song['artist_id']);
			$parameters['songIDs'][] = intval($song['token']);
		}
		$parameters['artistIDs'] = array_unique($parameters['artistIDs']);
		$parameters['songIDs'] = array_unique($parameters['songIDs']);
		return $parameters;
	}

	/**
	 * Get information for next song
	 *
	 * @return array Song information
	 */
	public function getAutoplaySong()
	{
		$this->authenticateToGrooveShark();
		$params = $this->buildParameters();
		$nextSong = $this->getGsAPI()->makeCall("startAutoplay", $params, null, false, $this->getGsAPI()->sessionID);
		return $nextSong['nextSong'];
	}
}
?>