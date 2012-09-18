<?php
/*
 * Initialize
 */
session_start();
require('db.php');

foreach($_REQUEST as $k=>$v)
{
	$$k = mysql_real_escape_string($v);
}

/*
 * Check to see if this song is already queued
 */
$sql = "SELECT q_id FROM thegogre_grooveshark.queue WHERE q_song_status='queued' AND q_song_id='{$songID}' LIMIT 1";
$result = mysql_query($sql) or die(mysql_error());

if(mysql_num_rows($result) > 0)
{
	echo "song already queued";
	exit(0);
}
unset($sql, $result);

/*
 * Get song priority
 */
switch($songPriority)
{
	case "high":
	case "med":
		// Only allow if user has played less than 3 high/med priority songs in past 120 minutes
		$sql = "SELECT q_song_played_by FROM thegogre_grooveshark.queue WHERE q_song_priority='high' OR q_song_priority='med' AND q_song_added_ts >= DATE_SUB(NOW(), INTERVAL 120 MINUTE)";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 2)
		{
			$songPriority = "low";
			echo "wait to promote";
			exit();
		}
		$response = "song promoted";
	break;
	case "low":
	default:
		$songPriority = "low";
		$response = "song added";
	break;
}

/*
 * Get song queue position
 */
$sql = "SELECT MAX(q_song_position) FROM thegogre_grooveshark.queue";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$songPosition = $row['MAX(q_song_position)'];
if(!$songPosition)
{
	$songPosition = 1;
} else {
	$songPosition++;
}

/*
 * Add song to queue
 */
$dateAdded = date("Y-m-d H:i:s");
$sql = "INSERT INTO thegogre_grooveshark.queue (q_song_id, q_song_title, q_song_artist, q_song_priority, q_song_position, q_song_added_ts, q_song_played_by) VALUES ('{$songID}','{$songTitle}','{$songArtist}','{$songPriority}','{$songPosition}','{$dateAdded}','{$_COOKIE['user']}')";
if(mysql_query($sql))
{
	echo $response;
} else {
	echo "error adding song";
}
?>
