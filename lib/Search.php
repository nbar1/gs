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
	 * Legacy Function
	 *
	 * Returns an array of search results based on a given query
	 *
	 * @TODO: REMOVE LEGACY CODE
	 * @param string $query Search query
	 * @return array
	 */
	public function getSearchResultsFromTinySong($query)
	{
		$get_url = "http://tinysong.com/s/".urlencode($query)."?format=json&limit=32&key=".$this->config['tinysong']['key'];
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
	 * Return a search view
	 *
	 * If GS API fails, falls back to tinysong
	 *
	 * @param string $query Search query
	 * @param int $count Number of results to return
	 * @param int $page Page number
	 * @return string
	 */
	public function returnSearchView($query, $count=30, $page=1)
	{
		//debug
		$count = 10;
		try
		{
			$results_artists = $this->parseArtistResultsForRelevantArtist($query, $this->getArtistSearchResults($query));
			$results_songs = $this->getSongSearchResults($query, $count, $page);
			return $this->renderFullSearchView($results_artists, $results_songs);
		}
		catch (Exception $e)
		{
			error_log("RATE LIMIT: Searching for \"".$query."\" via TinySong");
			// this wont call an error, just fallback to TinySong
			if(isset($this->config['tinysong']['key']))
			{
				$results_songs = $this->getSearchResultsFromTinySong($query);
				return $this->renderSongSearchView($results_songs);
			}
		}
	}

	/**
	 * Return a search view
	 *
	 * If GS API fails, falls back to tinysong
	 *
	 * @param string $artist_id Search query
	 * @return string
	 */
	public function returnArtistSearchView($artist_id)
	{
		try
		{
			$results_songs = $this->getArtistSongs($artist_id);
			return $this->renderArtistSearchView($results_songs);
		}
		catch (Exception $e)
		{
			return $this->renderError("Rate Limit Error");
		}
	}

	/**
	 * Send full search data to view
	 *
	 * @param string $results_artists Search results
	 * @param string $results_songs Search results
	 * @return string
	 */
	public function renderFullSearchView($results_artists, $results_songs)
	{
		$this->templateEngine->assign('results_artists', $results_artists);
		$this->templateEngine->assign('results_songs', $results_songs);
		return $this->templateEngine->draw('search_full');
	}

	/**
	 * Send full search data to view
	 *
	 * @param string $results_songs Search results
	 * @return string
	 */
	public function renderSongSearchView($results_songs)
	{
		$this->templateEngine->assign('results_songs', $results_songs);
		return $this->templateEngine->draw('search_songs');
	}

	/**
	 * Send full search data to view
	 *
	 * @param string $results_songs Search results
	 * @return string
	 */
	public function renderArtistSearchView($results_songs)
	{
		$this->templateEngine->assign('artist_name', $results_songs[0]['ArtistName']);
		$this->templateEngine->assign('results_songs', $results_songs);
		return $this->templateEngine->draw('search_artist');
	}
}
?>