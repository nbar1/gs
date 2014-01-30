<?php

/**
 * Base object
 */
$base = new Base();

/**
 * Base
 */
/**
 * (RENDER) base
 */
$app->get('/', function () use ($base) {
	echo $base->templateEngine->draw('base', $return_string = false);
});

/**
 * Queue
 */
/**
 * (RENDER) Queue
 */
$app->get('/queue', function () use ($base) {
	echo $base->getQueue()->renderView();
});
$app->get('/my_session', function () use ($base) {
	var_dump($_SESSION);
});
/**
 * Adds a song to the queue
 */
$app->post('/queue/add/', function () use ($base) {
	$base->getUser()->authenticate();
	$base->getSong()->setSongInformation(
		$_POST['songID'],
		$_POST['songTitle'],
		$_POST['songArtist'],
		$_POST['songArtistId'],
		$_POST['songImage'],
		$_POST['songPriority']
	);
	echo $base->getQueue()->addSongToQueue($base->getSong(), $base->getUser());
});

/**
 * Song
 */
/**
 * Gets information for a given song
 */
$app->get('/song/:token/', function ($token) {
	$song = new Song($token, true);
	echo json_encode($song->getSongInformation());
});

/**
 * Search
 */
/**
 * (RENDER) A search for a given artist query
 */
$app->get('/search/artist/:artist_id', function ($artist_id) {
	$search = new Search();
	echo $search->returnArtistSearchView($artist_id);
});
/**
 * (RENDER) A search for a given song query
 */
$app->get('/search/:query(/:count(/:page))', function ($query, $count=50, $page=1) {
	xdebug_break();
	$search = new Search();
	echo $search->returnSearchView($query, $count, $page);
});

/**
 * Session
 */
$app->post('/user/geolocation/', function () use ($base){
	$session = $base->getSession()->matchSessionToCoordinates($_POST['latitude'], $_POST['longitude']);
	$base->getSession()->setSession($session['id']);
	echo json_encode($session);
});
$app->post('/session/start/', function () use ($base){
	$host = $base->getUser()->getIdByNickname($_POST['host']);
	echo $base->getSession()->startSession($_POST['title'], $host, true, $_POST['coordinates']);
});

/**
 * User
 */
/**
 * (RENDER) Register a user form
 */
$app->get('/user/register/', function () use ($base) {
	echo $base->getUser()->renderView();
});
/**
 * (RENDER) Register user to database
 */
$app->post('/user/register/', function () use ($base){
	echo $base->getUser()->authenticate($_POST['nickname']);
});

/**
 * Player
 */
/**
 * (RENDER) Base music player
 */
$app->get('/player/', function () {
	$player = new Player();
	echo $player->renderView();
});
/**
 * Gets the stream token for a given song
 */
$app->get('/player/stream/:token/', function ($token) {
	$player = new Player();
	echo $player->getStream($token);
});
/**
 * Validates the song is playing with GrooveShark after 30 seconds
 */
$app->post('/player/stream/validate/', function () {
	$player = new Player();
	echo $player->markSong30Seconds($_POST['streamKey'], $_POST['streamServerID']);
});
/**
 * Processing songs and returns the token of the next song to be played
 */
$app->get('/player/next/', function () {
	$player = new Player();
	echo $player->playNextSong();
});

?>