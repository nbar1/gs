<?php

/**
 * Base object
 */
$base = new Base();

/**
 * Set default API headers
 */
$base->getApiHandler()->setHeaders();

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
 * Returns queue
 */
$app->get('/api/queue/', function() use($base) {
	if($base->getUser()->isAuthenticated())
	{
		echo ApiHandler::bundle($base->getQueue()->returnQueue());
	}
	else
	{
		ApiHandler::setStatusHeader(401);
		echo ApiHandler::bundle(array('message' => 'Not Authenticated'));
	}
});

/**
 * Adds a song to the queue
 */
$app->post('/api/queue/add/', function () use ($base) {

	if($base->getUser()->isAuthenticated())
	{
		$base->getSong()->setSongInformation(
			$_POST['songID'],
			$_POST['songTitle'],
			$_POST['songArtist'],
			$_POST['songArtistId'],
			$_POST['songImage'],
			$_POST['songPriority']
		);
		ApiHandler::bundle($base->getQueue()->addSongToQueue($base->getSong(), $base->getUser()));
	}
	else
	{
		ApiHandler::setStatusHeader(401);
		echo ApiHandler::bundle(array('message' => 'Not Authenticated'));
	}
});

/**
 * Authenticate user
 *
 * @TODO: Remove access via inline variable, switch to post
 */
$app->get('/api/authenticate/:nickname/', function($nickname) use($base) {
	$nickname = (!empty($nickname)) ? $nickname : false;
	echo ApiHandler::bundle($base->getUser()->authenticate($nickname));
});
$app->post('/api/authenticate/', function() use($base) {
	$nickname = (!empty($_POST['nickname'])) ? $_POST['nickname'] : false;
	echo ApiHandler::bundle($base->getUser()->authenticate($nickname));
});

















/**
 * Song
 */
/**
 * Gets information for a given song
 */
$app->get('/api/song/:token/', function ($token) use ($base) {
	if($base->getUser()->isAuthenticated())
	{
		$song = new Song($token, true);
		echo ApiHandler::bundle($song->getSongInformation());
	}
	else
	{
		ApiHandler::setStatusHeader(401);
		echo ApiHandler::bundle(array('message' => 'Not Authenticated'));
	}
	
});

/**
 * Search
 */
/**
 * (RENDER) A search for a given artist query
 */
$app->get('/api/search/artist/:artist_id', function ($artist_id) use ($base) {
	if($base->getUser()->isAuthenticated())
	{
		$search = new Search();
		echo ApiHandler::bundle($search->doArtistSearch($artist_id));
	}
	else
	{
		ApiHandler::setStatusHeader(401);
		echo ApiHandler::bundle(array('message' => 'Not Authenticated'));
	}
});
/**
 * (RENDER) A search for a given song query
 */
$app->get('/api/search/:query(/:count(/:page))', function ($query, $count=50, $page=1) use ($base) {
	if($base->getUser()->isAuthenticated())
	{
		$search = new Search();
		echo ApiHandler::bundle($search->doSearch($query, $count, $page));
	}
	else
	{
		ApiHandler::setStatusHeader(401);
		echo ApiHandler::bundle(array('message' => 'Not Authenticated'));
	}
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