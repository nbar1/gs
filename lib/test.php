<?php
include('Base.php');
include('Song.php');
include('User.php');
include('Queue.php');

$user = new User();
$user->authenticate('nick');

var_dump($user);

$song = new Song();
$song->setSongInformation('1234', 'title', 'artist');
$song->setPriority('high');
var_dump($song);
$queue = new Queue();

//echo $queue->addSongToQueue($song, $user);

var_dump($queue->getNextSong());

//echo $queue->addSongToQueue($song, $user);

?>