<?php
/**
 * Base object
 */
$base = new Base();

if(isset($_SERVER["CONTENT_TYPE"]) && stripos($_SERVER["CONTENT_TYPE"], "application/json") === 0) {
	$_POST = json_decode(file_get_contents("php://input"), true);
}

/**
 * Get queue
 */
$app->get('/api/v1/queue/', function() use($base) {
	if(ApiHandler::validKey())
	{
		ApiHandler::sendResponse(200, true, array('queue' => $base->getQueue()->getQueue()));
	}
	else
	{
		ApiHandler::notAuthenticated();
	}
});

/**
 * Add song to the queue
 */
$app->post('/api/v1/queue/add/', function () use ($base) {
	if(ApiHandler::validKey())
	{
		$song_info = $base->getSong()->getSongInformationFromGrooveShark($_POST['songID']);
		var_dump($song_info);
		$base->getSong()->setSongInformation(
			$song_info['SongID'],
			$song_info['SongName'],
			$song_info['ArtistName'],
			$song_info['ArtistID'],
			$song_info['CoverArtFilename'],
			$_POST['songPriority']
		);
		$base->getUser()->getUserByApiKey($_GET['apikey']);
		if($base->getQueue()->addSongToQueue($base->getSong(), $base->getUser()))
		{
			ApiHandler::sendResponse(200, true);
		}
		else
		{
			ApiHandler::sendResponse(500, false);
		}
	}
	else
	{
		ApiHandler::notAuthenticated();
	}
});

/**
 * Register
 */
$app->post('/api/v1/register/', function() use($base) {
	$register = $base->getUser()->registerUser($_POST['username'], $_POST['password']);
	if($register === USER_ALREADY_EXISTS)
	{
		ApiHandler::sendResponse(200, false, array('message' => 'User already exists'));
	}
	elseif($register)
	{
		ApiHandler::sendResponse(200, true);
	}
	else
	{
		ApiHandler::sendResponse(500, false, array('message' => 'Error creating user'));
	}
});

/**
 * Login
 */
$app->post('/api/v1/login/', function() use($base) {
	$username = (isset($_POST['username'])) ? $_POST['username'] : null;
	$password = (isset($_POST['password'])) ? $_POST['password'] : null;
	$login = $base->getUser()->login($username, $password);
	if($login === USERNAME_REQUIRED)
	{
		ApiHandler::sendResponse(200, false, array('message' => 'Username required'));
	}
	elseif($login === PASSWORD_REQUIRED)
	{
		ApiHandler::sendResponse(200, false, array('message' => 'Password required'));
	}
	elseif($login === USERNAME_TOO_LONG)
	{
		ApiHandler::sendResponse(200, false, array('message' => 'Username too long'));
	}
	elseif($login === USER_NOT_FOUND)
	{
		ApiHandler::sendResponse(200, false, array('message' => 'User not found'));
	}
	elseif($login === BAD_PASSWORD)
	{
		ApiHandler::sendResponse(200, false, array('message' => 'Invalid password'));
	}
	elseif($login)
	{
		ApiHandler::sendResponse(200, true, $login);
	}
});

/**
 * Gets information for a given song
 */
$app->get('/api/v1/song/:token', function ($token) use ($base) {
	$song = new Song($token, true);
	echo ApiHandler::sendResponse(200, true, $song->getSongInformation());
});

/**
 * Search for a given artist
 */
$app->get('/api/v1/search/artist/:id', function ($id) use ($base) {
	if(ApiHandler::validKey())
	{
		$search = new Search();
		echo ApiHandler::sendResponse(200, true, $search->doArtistSearch($id));
	}
	else
	{
		ApiHandler::notAuthenticated();
	}
});

/**
 * Search for a given query
 */
$app->get('/api/v1/search/:query(/:count(/:page))', function ($query, $count = 30, $page = 1) use ($base) {
	if(ApiHandler::validKey())
	{
		$search = new Search();
		echo ApiHandler::sendResponse(200, true, $search->doSearch($query, $count, $page));
	}
	else
	{
		ApiHandler::notAuthenticated();
	}
});

/**
 * Player
 */
$app->get('/api/v1/player/next', function () {
	$player = new Player();
	$song = $player->playNextSong();
	if($song !== false)
	{
		ApiHandler::sendResponse(200, true, array('token' => $song));
	}
	else
	{
		ApiHandler::sendResponse(200, false);
	}
});
/**
 * Gets the stream token for a given song
 */
$app->get('/api/v1/player/stream/:token', function ($token) {
	$player = new Player();
	ApiHandler::sendResponse(200, true, $player->getStream($token));
});
/**
 * Validates the song is playing with GrooveShark after 30 seconds
 */
$app->post('/api/v1/player/stream/validate', function () {
	$player = new Player();
	$player->markSong30Seconds($_POST['streamKey'], $_POST['streamServerID']);
	ApiHandler::sendResponse(200, true);
});
/**
 * Marks a song as played
 */
$app->post('/api/v1/song/:id/played', function ($id) use ($base) {
	$player = new Player();
	echo ApiHandler::sendResponse(200, $player->markSongComplete($id));
});

?>