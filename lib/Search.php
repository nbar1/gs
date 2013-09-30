<?php
/**
 * Search
 *
 * Controls the TinySong API for query searching
 */

class Search extends Base
{
	/**
	 * TinySong API Key
	 */
	private $tinysong;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		require('config.php');
		$this->tinysong = $config['tinysong']['key'];
	}

	/**
	 * getSearchResults
	 *
	 * Returns an array of search results based on a given query
	 *
	 * @param string $query
	 * @return array
	 */
	public function getSearchResults($query)
	{
		$post_url = "http://tinysong.com/s/".urlencode($query)."?format=json&limit=32&key=".$this->tinysong;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$query_results = json_decode(curl_exec($ch), TRUE);
		return $query_results;
	}

	/**
	 * Send data to view
	 *
	 * return string
	 */
	public function renderView($query)
	{
		$results = $this->getSearchResults($query);

		$this->templateEngine->assign('results', $results);
		return $this->templateEngine->draw('search');
	}
}
?>