<?php
/**
 * Search
 *
 * Controls the TinySong API for query searching
 */

class Search extends Base
{
	/**
	 * getSearchResults
	 *
	 * Legacy Function
	 *
	 * Returns an array of search results based on a given query
	 *
	 * @TODO: REMOVE LEGACY CODE
	 * @param string $query Search query
	 * @return array
	 */
	public function getSearchResults($query)
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
	public function getArtistSearchResults($query, $count=3)
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
	 * @return array
	 */
	public function getSongSearchResults($query, $count=30, $page=1)
	{
		$this->authenticateToGrooveShark();
		return $this->getGsAPI()->getSongSearchResults($query, null, $count, $page);
	}

	/**
	 * Send data to view
	 *
	 * @param string $query Search results
	 * @return string
	 */
	public function renderView($results)
	{
		$this->templateEngine->assign('results', $results);
		return $this->templateEngine->draw('search');
	}
}
?>