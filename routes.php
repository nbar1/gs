<?php

/**
 * Base object
 */
$base = new Base();

/**
 * Paths
 */
/**
 * Base
 */
$app->get('/', function () use ($base) {
	echo $base->templateEngine->draw('base', $return_string = false);
});

/**
 * Queue
 */
$app->get('/queue/', function () use ($base) {
	echo $base->getQueue()->renderView();
});

$app->post('/queue/add/', function () use ($base) {
	$base->getUser()->authenticate();
	$base->getSong()->setSongInformation(
		$_POST['songID'],
		$_POST['songTitle'],
		$_POST['songArtist'],
		$_REQUEST['songArtistId'],
		$_REQUEST['songImage'],
		$_REQUEST['songPriority']
	);
	echo $base->getQueue()->addSongToQueue($base->getSong(), $base->getUser());
});

/**
 * Song
 */
$app->get('/song/:token/', function ($token) {
	$song = new Song($token, true);
	echo json_encode($song->getSongInformation());
});

/**
 * Search
 */
$app->get('/search/artist/:query(/:count)', function ($query, $count=3) {
	$search = new Search();
	echo $search->renderView($search->getArtistSearchResults($query, $count));
});
$app->get('/search/:query(/:count(/:page))', function ($query, $count=50, $page=1) {
	$search = new Search();
	echo $search->renderView($search->getSongSearchResults($query, $count, $page));
});

/**
 * Session
 */
$app->post('/session/start/', function () use ($base){
	$host = $base->getUser()->getIdByNickname($_POST['host']);
	echo $base->getSession()->startSession($_POST['title'], $host, true, $_POST['coordinates']);
});
$app->get('/session/all/', function () use ($base) {
	var_dump($base->getSession()->getActiveSessions());
});

/**
 * User
 */
$app->get('/user/register/', function () use ($base) {
	echo $base->getUser()->renderView();
});
$app->post('/user/register/', function () use ($base){
	echo $base->getUser()->authenticate($_POST['nickname']);
});

/**
 * Player
 */
$app->get('/player/', function () {
	$player = new Player();
	echo $player->renderView();
});
$app->get('/player/stream/:token/', function ($token) {
	$player = new Player();
	echo $player->getStream($token);
});
$app->post('/player/stream/validate/', function () {
	$player = new Player();
	echo $player->markSong30Seconds($_POST['streamKey'], $_POST['streamServerID']);
});
$app->get('/player/next/', function () {
	$player = new Player();
	echo $player->playNextSong();
});

?>