<?php
session_start();
require('db.php');

$sql = "SELECT q_song_id, q_song_title, q_song_artist, q_song_played_by, q_song_priority, q_song_position, q_song_status FROM thegogre_grooveshark.queue WHERE q_song_status='queued' OR q_song_status='playing' ORDER BY q_song_status ASC, q_song_priority ASC, q_song_position ASC";
$result = mysql_query($sql);

echo "<ul id=\"song_list\" class=\"queue\">";
while($row = mysql_fetch_assoc($result))
{
	$sql2 = "SELECT user_nickname FROM thegogre_grooveshark.users WHERE user_id='".$row['q_song_played_by']."' LIMIT 1";
	$result2 = mysql_query($sql2);
	$row2 = mysql_fetch_assoc($result2);
	echo "<li class='item_song ".$row['q_song_priority']." ".$row['q_song_status']."' onclick=''>";
	echo "<div class='item_status'></div>";
	echo "<div class='song_name'>" . $row['q_song_title'] . "</div>";
	echo "<div class='song_artist'>" . $row['q_song_artist'] . "</div>";
	echo "<div class='song_played_by'>Played by " . $row2['user_nickname'] . "</div>";
	if($row['q_song_status'] == "playing")
	{
		echo "<div class=\"now_playing_marker\">now playing</div>";
	}
	if($row['q_song_priority'] == "high")
	{
		echo "<div class=\"high_priority_marker\">promoted</div>";
	}
	echo "</li>";
}
echo "</ul>";
//echo "<div id=\"button_user\" class=\"footer_sticky\">options</div>";

?>
