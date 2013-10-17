<?php
/**
 * Search
 *
 * Controls the TinySong API for query searching
 *
 * @TODO: Use the GrooveShark API for searching instead of TinySong
 */

class Search extends Base
{
	/**
	 * getSearchResults
	 *
	 * Returns an array of search results based on a given query
	 *
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
	 * Send data to view
	 *
	 * @param string $query Search query
	 * @return string
	 */
	public function renderView($query)
	{
		$results = $this->getSearchResults($query);

		$this->templateEngine->assign('results', $results);
		return $this->templateEngine->draw('search');
	}
}
?>