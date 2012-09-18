<?php
if(!$_COOKIE['user'])
{
	header('location: setName.php');
}
function returnResults($query)
{
	$query = array(
		'query' => $query,
		'limit' => '32',
		'api_key' => 'a2e5ffd9cc99a2bee6207e4921def6a7',
	);

	$query['url'] = str_replace(" ", "+", "http://tinysong.com/s/" . $query['query'] . "?format=json&limit=" . $query['limit'] . "&key=" . $query['api_key']);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $query['url']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$query_results = json_decode(curl_exec($ch), TRUE);

	return $query_results;
}
$results = returnResults($_POST['q']);

if(sizeof($results) < 1)
{
	echo "<div id='error_message_generic'>No Results Found</div>";
} else {
	echo "<ul id=\"song_list\">";
	foreach ($results as $k=>$song)
	{
		/**
		 * array $song
		 *
		 * Url - Url to song
		 * SongID - Grooveshark song ID
		 * SongName - Title of song
		 * ArtistID - Grooveshark artist ID
		 * ArtistName - Name of artist
		 * AbumID - Grooveshark album ID
		 * AlbumName - Name of album
		 */
		echo "<li class='item_song item_search' rel='".$song['SongID']."' onclick=''>";
		echo "<div class='song_name'>" . $song['SongName'] . "</div>";
		echo "<div class='song_artist'>" . $song['ArtistName'] . "</div>";
		echo "<div class='song_id'>" . $song['SongID'] . "</div>";
		echo "</li>";
	}
	echo "</ul>";
	echo "<div id=\"button_queue\" class=\"footer_sticky\" onclick=''>back to queue</div>";
	echo "<div id=\"addToQueue_options\" class=\"footer_sticky\"><div id='addToQueue_promote' class=\"footer_button\" onclick=''>promote</div><div id='addToQueue_add' class=\"footer_button\" onclick=''>add to queue</div></div>";
	echo "<div id=\"addToQueue_response\" class=\"footer_sticky\"></div>";
}
?>
