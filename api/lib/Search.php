<?php
/**
 * Search
 *
 * Controls the TinySong API for query searching
 */

class Search extends Base
{
	/**
	 * Get search results from TinySong
	 *
	 * Returns an array of search results based on a given query
	 *
	 * @param string $query Search query
	 * @return array
	 */
	public function getSearchResultsFromTinySong($query)
	{
		$get_url = "http://tinysong.com/s/".urlencode($query)."?format=json&limit=32&key=" . TINYSONGAPI_KEY;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $get_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$query_results = json_decode(curl_exec($ch), TRUE);
		return $query_results;
	}

	/**
	 * Get Artist Search Results
	 *
	 * Returns an array of artist search results based on a given query
	 *
	 * @param string $query Search query
	 * @param int $count Number of results to return
	 * @return array
	 */
	public function getArtistSearchResults($query, $count=10)
	{
		$this->authenticateToGrooveShark();
		return $this->getGsAPI()->getArtistSearchResults($query, $count);
	}

	/**
	 * Get Song Search Results
	 *
	 * Returns an array of song search results based on a given query
	 *
	 * @param string $query Search query
	 * @param int $count Number of results to return
	 * @param int $page Page number
	 * @return array
	 */
	public function getSongSearchResults($query, $count, $page)
	{
		$this->authenticateToGrooveShark();
		return $this->getGsAPI()->getSongSearchResults($query, null, $count, $page);
	}

	/**
	 * Get Songs by Artist
	 *
	 * Returns an array of song search results based on a given query
	 *
	 * @param string $artist_id Artist ID
	 * @return array
	 */
	public function getArtistSongs($artist_id)
	{
		$this->authenticateToGrooveShark();
		return $this->getGsAPI()->getArtistPopularSongs($artist_id);
	}

	/**
	 * Get Most Popular Songs
	 *
	 * Returns an array of song search results based on popularity
	 *
	 * @param string $monthly Expand to monthly popular songs
	 * @return array
	 */
	public function getMostPopularSongs(User $user, $monthly = false)
	{
		$this->authenticateToGrooveShark();
		if($monthly) {
			$results = $this->getGsAPI()->getPopularSongsMonth(30);
		}
		else
		{
			$results = $this->getGsAPI()->getPopularSongsToday(30);
		}
		return array(
			'userPromotions' => $user->getAvailablePromotions(),
			'type' => 'tinysong',
			'artists' => null,
			'songs' => $results,
		);
	}

	/**
	 * Parse artist search results
	 *
	 * Parses the artist search results and tried to return only relevant artist
	 *
	 * @param string $query Search query
	 * @param array $results Search results
	 * @return array Parses results
	 */
	public function parseArtistResultsForRelevantArtist($query, $results)
	{
		$query = trim($query);
		foreach($results as $artist)
		{
			$artist_name = trim($artist['ArtistName']);
			if(strcasecmp($query, $artist_name) == 0) return array($artist);
		}
		return array_slice($results, 0, 3);
	}

	/**
	 * Return search
	 *
	 * If GS API fails, falls back to tinysong
	 *
	 * @param string $query Search query
	 * @param int $count Number of results to return
	 * @param int $page Page number
	 * @return array
	 */
	public function doSearch($query, $count=30, $page=1, User $user)
	{
		try
		{
			$results_artists = $this->parseArtistResultsForRelevantArtist($query, $this->getArtistSearchResults($query));
			$results_songs = $this->getSongSearchResults($query, $count, $page);
			return array(
				'userPromotions' => $user->getAvailablePromotions(),
				'type' => 'full',
				'artists' => $results_artists,
				'songs' => $results_songs,
			);
		}
		catch (Exception $e)
		{
			error_log("RATE LIMIT: Searching for \"".$query."\" via TinySong");
			// this wont call an error, just fallback to TinySong
			$results_songs = $this->getSearchResultsFromTinySong($query);
			return array(
				'userPromotions' => $user->getAvailablePromotions(),
				'type' => 'tinysong',
				'artists' => null,
				'songs' => $results_songs,
			);
		}
	}

	/**
	 * Return artist search
	 *
	 * @param string $artist_id Search query
	 * @return array
	 */
	public function doArtistSearch($artist_id, User $user)
	{
		try
		{
			$results_songs = $this->getArtistSongs($artist_id);
			return array(
				'userPromotions' => $user->getAvailablePromotions(),
				'type' => 'artist',
				'artist' => $artist_id,
				'songs' => $results_songs,
			);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

}
?>