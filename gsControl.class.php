<?php
/*
 * Docblock back in 15 minutes
 */
class gsControl
{
	var $api_key = "a2e5ffd9cc99a2bee6207e4921def6a7";
	/**
	 * initializeView
	 *
	 * Displays queue or nickname setup based on user authentication state
	 * The return of this function will be displayed to the screen as HTML
	 *
	 * @return function
	 */
	function initializeView()
	{
		if($this->isAuthenticated() == TRUE)
		{
			$sql = "UPDATE thegogre_grooveshark.users SET user_lastlogin='".date("Y-m-d H:i:s")."' WHERE user_id='".mysql_real_escape_string($this->getUserID())."'";
			$result = mysql_query($sql);
			return $this->displayQueueView();
		} else {
			return $this->displayNicknameView();
		}
	}

	/**
	 * isAuthenticated
	 *
	 * Returns whether the current user is authenticated
	 *
	 * @return bool
	 */
	function isAuthenticated()
	{
		if(isset($_COOKIE['user']))
		{
			$sql = "SELECT COUNT(*) from thegogre_grooveshark.users WHERE user_id='".mysql_real_escape_string($_COOKIE['user'])."'";
			$result = mysql_query($sql);
			if(mysql_num_rows($result) > 0)
			{
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * getUserID
	 *
	 * Get the current users userID
	 *
	 * @return int
	 */
	function getUserID()
	{
		if($this->isAuthenticated() == TRUE)
		{
			return $_COOKIE['user'];
		} else {
			return false;
		}
	}

	/**
	 * setNickname
	 *
	 * Set the current users Nickname
	 *
	 * @return bool
	 */
	function setNickname($nickname)
	{
		if($this->isAuthenticated() == FALSE)
		{
			if(strlen($nickname) > 32)
			{
				$nickname = substr($nickname, 0, 32);
			}
			$sql = "SELECT user_id FROM thegogre_grooveshark.users WHERE user_nickname='".mysql_real_escape_string($nickname)."'";
			$result = mysql_query($sql);
			if(mysql_num_rows($result) < 1)
			{
				// Continue with creating user
				$sql = "INSERT INTO thegogre_grooveshark.users (user_nickname, user_created, user_lastlogin) VALUES ('".mysql_real_escape_string($nickname)."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
				$result = mysql_query($sql);
				if($result)
				{
					setcookie('user', mysql_insert_id(), strtotime("+5 years"));
					return true;
				} else {
					return false;
				}
			} else {
				$row = mysql_fetch_assoc($result);
				setcookie('user', $row['user_id'], strtotime("+5 years"));
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * getNickname
	 *
	 * Return nickname of given userID
	 *
	 * @param $userID
	 * @return string
	 */
	function getNickname($userID=FALSE)
	{
		if($userID == FALSE)
		{
			$userID = $this->getUserID();
		}
		if($userID <> FALSE)
		{
			$sql = "SELECT user_nickname FROM thegogre_grooveshark.users WHERE user_id='".mysql_real_escape_string($userID)."'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			return $row['user_nickname'];
		} else {
			return false;
		}
	}

	/**
	 * getAvailablePromotions
	 *
	 * Returns available promotions for given userID
	 *
	 * @param $userID
	 * @return int
	 */
	function getAvailablePromotions($userID=FALSE)
	{
		$maxPromotions = 3;
		if($userID == FALSE)
		{
			$userID = $this->getUserID();
		}
		if($userID <> FALSE)
		{
			$sql = "SELECT COUNT(*) FROM thegogre_grooveshark.queue WHERE q_song_priority='high' OR q_song_priority='med' AND q_song_played_by='".$userID."' AND q_song_added_ts >= DATE_SUB(NOW(), INTERVAL 120 MINUTE)";
			$result = mysql_query($sql);
			if($result)
			{
				$availablePromotions = $maxPromotions - mysql_num_rows($result);
				return $availablePromotions;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	/**
	 * getNextQueuePosition
	 *
	 * Returns the next available queue slot
	 *
	 * @return int
	 */
	function getNextQueuePosition()
	{
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
		return $songPosition;
	}

	/**
	 * isSongInQueue
	 *
	 * Returns whether the given songID is already queued
	 *
	 * @param int $songID
	 * @return bool
	 */
	function isSongInQueue($songID)
	{
		$sql = "SELECT q_id FROM thegogre_grooveshark.queue WHERE q_song_status='queued' AND q_song_id='".$songID."' LIMIT 1";
		$result = mysql_query($sql) or die(mysql_error());
		if(mysql_num_rows($result) > 0)
		{
			return true;
		} else {
			return false;
		}
	}

	/**
	 * addSongToQueue
	 *
	 * Adds a given song to the queue
	 *
	 * @param int $songID
	 * @param string $songPriority
	 * @param string $songArtist
	 * @param string $songTitle
	 * @return string
	 */
	function addSongToQueue($songID, $songPriority, $songArtist, $songTitle)
	{
		if($this->isSongInQueue($songID) == FALSE)
		{
			if($this->getAvailablePromotions() < 1)
			{
				$songPriority = "low";
			}
			$sql = "INSERT INTO thegogre_grooveshark.queue (q_song_id, q_song_title, q_song_artist, q_song_priority, q_song_position, q_song_added_ts, q_song_played_by) VALUES ('".$songID."','".$songTitle."','".$songArtist."','".$songPriority."','".$this->getNextQueuePosition()."','".date("Y-m-d H:i:s")."','".$this->getUserID()."')";
			if(mysql_query($sql))
			{
				if($songPriority == "high")
				{
					return "song promoted";
				} else {
					return "song added";
				}
			} else {
				return "error adding song";
			}
		} else {
			return "song already queued";
		}
	}

	/**
	 * doSearch
	 *
	 * Returns an array of search results based on a given query
	 *
	 * @param string $query
	 * @return array
	 */
	function doSearch($query)
	{
		$post_url = str_replace(" ", "+", "http://tinysong.com/s/".$query."?format=json&limit=32&key=".$this->api_key);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$query_results = json_decode(curl_exec($ch), TRUE);
		return $query_results;
	}

	/**
	 * displaySearchView
	 *
	 * Displays the view allowing an authorized user to view search results
	 * Returns a string value that will be output to the screen
	 *
	 * @param array $results
	 * @return string
	 */
	function displaySearchView($results)
	{
		if(sizeof($results) < 1)
		{
			$output = "<div id='error_message_generic'>No Results Found</div>";
		} else {
			$output = "<ul id='song_list'>";
			foreach($results as $k=>$song)
			{
				$output .= "<li class='item_song item_search' rel='".$song['SongID']."' onclick=''>";
				$output .= "<div class='song_name'>".$song['SongName']."</div>";
				$output .= "<div class='song_artist'>".$song['ArtistName']."</div>";
				$output .= "<div class='song_id'>".$song['SongID']."</div>";
				$output .= "</li>";
			}
			$output .= "</ul>";
		}
		$output .= "<div id='button_queue' class='footer_sticky' onclick=''>back to queue</div>";
		$output .= "<div id='addToQueue_options' class='footer_sticky'><div id='addToQueue_promote' class='footer_button' onclick=''>promote</div><div id='addToQueue_add' class='footer_button' onclick=''>add to queue</div></div>";
		$output .= "<div id='addToQueue_response' class='footer_sticky'></div>";

		return $output;
	}

	/**
	 * displayNicknameView
	 *
	 * Displays the view allowing an unauthenticated user to set their nickname
	 * Returns a string value that will be output to the screen
	 *
	 * @return string
	 */
	function displayNicknameView()
	{
		if($this->isAuthenticated() == FALSE)
		{
			$output = "<div class='setUser_header'>enter your name</div>";
			$output .= "<input type='text' class='setUser_textbox' id='setUser_textbox' placeholder='name' maxlength='32' />";
			$output .= "<div id='setUser_submit' onclick=''>submit</div>";
			return $output;
		} else {
			return $this->displayQueueView();
		}
	}

	/**
	 * displayQueueView
	 *
	 * Displays the view allowing an authorized user to view the queue
	 * Returns a string value that will be output to the screen
	 *
	 * @return string
	 */
	function displayQueueView()
	{
		if($this->isAuthenticated() == TRUE)
		{
			$output = "<ul id='song_list' class='queue'>";

			$sql = "SELECT q_id, q_song_id, q_song_title, q_song_artist, q_song_played_by, q_song_priority, q_song_position, q_song_status FROM thegogre_grooveshark.queue WHERE q_song_status='queued' OR q_song_status='playing' ORDER BY q_song_status ASC, q_song_priority ASC, q_song_position ASC";
			$result = mysql_query($sql);

			while($row = mysql_fetch_assoc($result))
			{
				$output .= "<li class='item_song ".$row['q_song_priority']." ".$row['q_song_status']."' onclick=''>";
				$output .= "<div class='item_status'></div>";
				$output .= "<div class='song_name'>".$row['q_song_title']."</div>";
				$output .= "<div class='song_artist'>".$row['q_song_artist']."</div>";
				$output .= "<div class='song_played_by'>".$this->getNickname($row['q_song_played_by'])."</div>";
				if($row['q_song_status'] == "playing")
				{
					$output .= "<div class='now_playing_marker'>now playing</div>";
				} elseif($row['q_song_priority'] == "high")
				{
					$output .= "<div class='high_priority_marker'>promoted</div>";
				}
			}
			$output .= "</ul>";
			return $output;
		} else {
			return $this->displayNicknameView();
		}
	}
}
?>
