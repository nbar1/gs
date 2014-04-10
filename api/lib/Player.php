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
	 * @param int $song Song token
	 * @return string JSON encoded stream information
	 */
	public function getStream($song)
	{
		$this->authenticateToGrooveShark();
		// Make request to Grooveshark and return data as JSON
		$streamInfo = $this->getGsAPI()->getSubscriberStreamKey($song, false);
		
		return $streamInfo;
	}

	/**
	 * Returns the token of the next song to play
	 *
	 * This method will mark the playing song as played, and next song as playing.
	 * If there is not a next song it will get one based on the Autoplayer
	 *
	 * @return string Song token
	 */
	public function playNextSong()
	{
		// This will mark a song as played if for some reason it wasn't already
		$currentPlayingSong = $this->getQueue()->getPlayingSong();
		if ($currentPlayingSong !== false)
		{
			$this->markSongPlayed($currentPlayingSong['id']);
		}

		// Load up an autoplayer song if no songs queued
		if ($this->getQueue()->getNextSong() === false && GS_AUTOPLAY === true)
		{
			// Get autoplay song
			$autoplay = new Autoplay();
			$autoplaySong = $autoplay->getAutoplaySong();

			$user = $this->getUser()->autoplayUser();

			$song = new Song();
			$song->setSongInformation($autoplaySong['SongID'], $autoplaySong['SongName'], $autoplaySong['ArtistName'], $autoplaySong['ArtistID'], $autoplaySong['CoverArtFilename'], 'low');
			$songID = $this->getQueue()->addSongToQueue($song, $this->getUser());
		}

		$nextSong = $this->getQueue()->getNextSong();

		if($nextSong)
		{
			$this->markSongPlaying($nextSong['id']);
			return $nextSong['token'];
		}
		return false;
	}

	/**
	 * Mark Song Played
	 *
	 * @param int $id song id
	 */
	public function markSongPlayed($id)
	{
		return $this->getDao()->markSongPlayed($id);
	}

	/**
	 * Mark Song Playing
	 *
	 * @param int $id song id
	 */
	public function markSongPlaying($id)
	{
		$this->getDao()->markSongPlaying($id);
	}
}
?>