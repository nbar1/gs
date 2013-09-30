<?php
/**
 * Base
 */
$app->get('/', function () {
	$base = new Base;
	echo $base->templateEngine->draw('base', $return_string = false);
});

/**
 * Queue
 */
$app->get('/queue/', function () {
	$queue = new Queue;
	echo $queue->renderView();
});

$app->post('/queue/add/', function () {
	$queue = new Queue;
	$user = new User;
	$user->authenticate();
	$song = new Song;
	$song->setSongInformation($_POST['songID'], $_POST['songTitle'], $_POST['songArtist'], $_REQUEST['songPriority']);
	echo $queue->addSongToQueue($song, $user);
});

$app->get('/queue/next/', function () {
	$queue = new Queue;
	echo $queue->getNextSong();
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
$app->get('/search/:query/', function ($query) {
	$search = new Search;
	echo $search->renderView(urldecode($query));
});

/**
 * User
 */
$app->get('/user/register/', function () {
	$user = new User;
	echo $user->renderView();
});
$app->post('/user/register/', function () {
	$user = new User;
	echo $user->authenticate($_POST['nickname']);
});

/**
 * Player
 */
$app->get('/player/', function () {
	$player = new Player;
	echo $player->renderView();
});
$app->get('/player/stream/:token/', function ($token) {
	$player = new Player;
	echo $player->getStream($token);
});
$app->post('/player/stream/validate/', function () {
	$player = new Player;
	echo $player->markSong30Seconds($_POST['streamKey'], $_POST['streamServerID']);
});
$app->get('/player/next/', function () {
	$player = new Player;
	echo $player->getNextSong();
});

?>