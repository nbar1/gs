<?php
session_start();
require('db.php');

foreach($_REQUEST as $k=>$v)
{
	if($k <> "user")
	{
		$$k = mysql_real_escape_string($v);
	}
}

switch($f)
{
	/**
	 * queue
	 *
	 * Displays the current queue the the user
	 */
	case 'queue':
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
	break;

	/**
	 * addToQueue
	 *
	 * Add selected song to queue
	 *
	 * @param int $songID
	 * @param string $songTitle
	 * @param string $songArtist
	 * @param string $songPriority
	 */
	case 'addToQueue':
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
	break;

	/**
	 * search
	 *
	 * Perform a search based on user query
	 *
	 * @param string $q
	 */
	case 'search':
		if(!$_COOKIE['user'])
		{
			header('location: functions.php?f=setName');
		}

		$query = array(
			'query' => $q,
			'limit' => '32',
			'api_key' => 'a2e5ffd9cc99a2bee6207e4921def6a7',
		);

		$query['url'] = str_replace(" ", "+", "http://tinysong.com/s/" . $query['query'] . "?format=json&limit=" . $query['limit'] . "&key=" . $query['api_key']);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$results = json_decode(curl_exec($ch), TRUE);

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
	break;

	/**
	 * setName
	 *
	 * Sets user ID as a cookie, which is linked to a nickname
	 *
	 * @param bool $set
	 * @param string $nickname
	 */
	case 'setName':
		if($_POST['set'])
		{
			// Check to see if nickname exists, if TRUE take over
			$sql = "SELECT * FROM thegogre_grooveshark.users WHERE user_nickname='".mysql_real_escape_string($_POST['nickname'])."'";
			$result = mysql_query($sql);
			if(mysql_num_rows($result) < 1)
			{
				// Continue with creating user
				$sql = "INSERT INTO thegogre_grooveshark.users (user_nickname, user_created, user_lastlogin) VALUES ('".mysql_real_escape_string($_POST['nickname'])."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
				$result = mysql_query($sql);
				if($result)
				{
					$_SESSION['nickname'] = mysql_real_escape_string($_POST['nickname']);
						$_SESSION['active'] = TRUE;
					setcookie('user', mysql_insert_id(), strtotime("+5 years"));
				} else {
					// TODO Error creating user
				}
			} else {
				$row = mysql_fetch_assoc($result);
				$_SESSION['nickname'] = $row['user_nickname'];
				$_SESSION['active'] = $row['user_active'];
				setcookie('user', $row['user_id'], strtotime("+5 years"));
			}
		header('location: queue.php');
			exit();
		}
		if($_COOKIE['user'])
		{
			$sql = "SELECT * FROM thegogre_grooveshark.users WHERE user_id='".mysql_real_escape_string($_COOKIE['user'])."' LIMIT 1";
			$result = mysql_query($sql);
			if(mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_assoc($result);
				$_SESSION['nickname'] = $row['user_nickname'];
				$_SESSION['active'] = $row['user_active'];
				// Update user login time
				$sql = "UPDATE thegogre_grooveshark.users SET user_lastlogin='".date("Y-m-d H:i:s")."' WHERE user_id='".mysql_real_escape_string($_COOKIE['user'])."'";
				$result = mysql_query($sql);
				header('location: functions.php?f=queue');
				exit();
			}
		}
		echo "<div class=\"setUser_header\">enter your name</div>";
		echo "<input type=\"text\" class=\"setUser_textbox\" id=\"setUser_textbox\" placeholder=\"name\" maxlength=\"32\"/>";
		echo "<div id=\"setUser_submit\" onclick=\"\">submit</div>";
	break;

	default:
		header('location: functions.php?f=setName');
	break;
}
?>
