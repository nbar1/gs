<?php
/*
 * Docblock back in 15 minutes
 */
class gsControl
{
	public $api_key = ""; // This is the TinySong API key for searching
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
		global $db;
		if($this->isAuthenticated() == TRUE)
		{
			$data = array(date("Y-m-d H:i:s"), $this->getUserID());
			$dbh = $db->prepare("UPDATE users SET user_lastlogin=? WHERE user_id=?");
			$dbh->execute($data);
			return $this->displayQueueView();
		}
		else {
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
		global $db;
		if(isset($_COOKIE['user']))
		{
			$data = array($this->getUserID());
			$dbh = $db->prepare("SELECT user_id FROM users WHERE user_id=?");
			$dbh->execute($data);
			if($dbh->rowCount() > 0)
			{
				return true;
			} else {
				return false;
			}
		}
		else {
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
		if($_COOKIE['user'])
		{
			return $_COOKIE['user'];
		}
		else {
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
		global $db;
		if($this->isAuthenticated() == FALSE)
		{
			if(strlen($nickname) > 32)
			{
				$nickname = substr($nickname, 0, 32);
			}
			
			$data = array($nickname);
			$dbh = $db->prepare("SELECT user_id FROM users WHERE user_nickname=?");
			$dbh->execute($data);
			if($dbh->rowCount() < 1)
			{
				// Continue with creating user
				$data = array($nickname, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
				$dbh = $db->prepare("INSERT INTO users (user_nickname, user_created, user_lastlogin) VALUES (?, ?, ?)");
				if($dbh->execute($data))
				{
					setcookie('user', $dbh->lastInsertId(), strtotime("+5 years"));
					return true;
				} else {
					return false;
				}
			}
			else {
				$dbh->setFetchMode(PDO::FETCH_ASSOC);
				$row = $dbh->fetch();
				setcookie('user', $row['user_id'], strtotime("+5 years"));
				return true;
			}
		}
		else {
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
	function getNickname($userID = FALSE)
	{
		global $db;
		if($userID == FALSE)
		{
			$userID = $this->getUserID();
		}
		if($userID <> FALSE)
		{
			$data = array($userID);
			$dbh = $db->prepare("SELECT user_nickname FROM users WHERE user_id=?");
			$dbh->execute($data);
			$dbh->setFetchMode(PDO::FETCH_ASSOC);
			$row = $dbh->fetch();	
			return $row['user_nickname'];
		}
		else {
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
		global $db;
		$maxPromotions = 3;
		if($userID == FALSE)
		{
			$userID = $this->getUserID();
		}
		if($userID <> FALSE)
		{
			$data = array("high", "med", $userID);
			$dbh = $db->prepare("SELECT * FROM queue WHERE q_song_priority=? OR q_song_priority=? AND q_song_played_by=? AND q_song_added_ts >= DATE_SUB(NOW(), INTERVAL 120 MINUTE)");
			if($dbh->execute($data))
			{
				$availablePromotions = $maxPromotions - $dbh->rowCount();
				return $availablePromotions;
			}
			else {
				return 0;
			}
		}
		else {
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
		global $db;
		$dbh = $db->prepare("SELECT MAX(q_song_position) FROM queue");
		$dbh->execute();
		$songPosition = $dbh->fetchColumn();

		if(!$songPosition)
		{
			$songPosition = 1;
		}
		else {
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
		global $db;
		$data = array("queued", $songID);
		$dbh = $db->prepare("SELECT q_id FROM queue WHERE q_song_status=? AND q_song_id=? LIMIT 1");
		$dbh->execute($data);
		if($dbh->rowCount() > 0)
		{
			return true;
		}
		else {
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
		global $db;
		if($this->isSongInQueue($songID) == FALSE)
		{
			if($this->getAvailablePromotions() < 1)
			{
				$songPriority = "low";
			}
			$data = array($songID, $songTitle, $songArtist, $songPriority, $this->getNextQueuePosition(), date("Y-m-d H:i:s"), $this->getUserID());
			$dbh = $db->prepare("INSERT INTO queue (q_song_id, q_song_title, q_song_artist, q_song_priority, q_song_position, q_song_added_ts, q_song_played_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
			if($dbh->execute($data))
			{
				if($songPriority == "high")
				{
					return "song promoted";
				}
				else {
					return "song added";
				}
			}
			else {
				return "error adding song";
			}
		}
		else {
			return "song already queued";
		}
	}

	/**
	 *
	 *
	 *
	 *
	 *
	 *
	 */
	function getNextSong()
	{
		global $db;
		$data = array("queued", "playing");
		$dbh = $db->prepare("SELECT q_id, q_song_id, q_song_status FROM queue WHERE q_song_status=? OR q_song_status=? ORDER BY q_song_status ASC, q_song_priority ASC, q_song_position ASC LIMIT 2");
		$dbh->execute($data);
		$dbh->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $dbh->fetchAll();	
		$x=0;
		foreach($rows as $row)
		{
			if($x==0)
			{
				if($row['q_song_status'] == "playing")
				{
					$data = array("played", $row['q_id']);
					$dbh = $db->prepare("UPDATE queue SET q_song_status=? WHERE q_id=?");
					$dbh->execute($data);
					$x++;
				}
				else {
					$data = array("playing", $row['q_id']);
					$dbh = $db->prepare("UPDATE queue SET q_song_status=? WHERE q_id=?");
					$dbh->execute($data);
					return $row['q_song_id'];
				}
			}
			else {
				$data = array("playing", $row['q_id']);
				$dbh = $db->prepare("UPDATE queue SET q_song_status=? WHERE q_id=?");
				$dbh->execute($data);
				return $row['q_song_id'];
			}
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
		}
		else {
			$output = "<div id='song_list'>";
			foreach($results as $k=>$song)
			{
				$output .= "<div class='row-fluid item_song item_search' rel='".$song['SongID']."' onclick=''><div class='col-lg-12'>";
				$output .= "<div class='song_name'>".$song['SongName']."</div>";
				$output .= "<div class='song_artist'>".$song['ArtistName']."</div>";
				$output .= "<div class='song_id'>".$song['SongID']."</div>";
				$output .= "</div></div>";
			}
			$output .= "</div>";
		}
		$output .= '<div style="clear: both;"></div><div class="navbar navbar-fixed-bottom">
			<div class="row-fluid" id="backToQueue_options">
				<div class="col-lg-12">
					<div id="button_queue" class="btn btn-info" onclick="">back to queue</div>
				</div>
			</div>
			<div class="row" id="addToQueue_options">
				<div class="addToQueue_cont" style="margin-left: -3px;">
					<div id="addToQueue_promote" class="btn btn-warning" onclick="">promote</div>
				</div>
				<div class="addToQueue_cont">
					<div id="addToQueue_add" class="btn btn-success" onclick="">add to queue</div>
				</div>
			</div>
			<div class="row-fluid" id="addToQueue_response"></div>
		</div>';

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
		}
		else {
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
		global $db;
		if($this->isAuthenticated() == TRUE)
		{
			$output = "<script>var t=setTimeout(\"reloadQueue()\", 15000);</script>";
			//$output = "";
			$output .= "<div id='song_list' class='queue'>";

			$data = array("queued", "playing");
			$dbh = $db->prepare("SELECT q_id, q_song_id, q_song_title, q_song_artist, q_song_played_by, q_song_priority, q_song_position, q_song_status FROM queue WHERE q_song_status=? OR q_song_status=? ORDER BY q_song_status ASC, q_song_priority ASC, q_song_position ASC");
			$dbh->execute($data);
			$dbh->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $dbh->fetchAll();

			foreach($rows as $row)
			{
				$output .= "<div class='row-fluid item_song ".$row['q_song_priority']." ".$row['q_song_status']."' onclick=''><div class='col-lg-12'>";
				$output .= "<div class='item_status'></div>";
				$output .= "<div class='song_name'>".$row['q_song_title']."</div>";
				$output .= "<div class='song_artist'>".$row['q_song_artist']."</div>";
				$output .= "<div class='song_played_by'>".$this->getNickname($row['q_song_played_by'])."</div>";
				if($row['q_song_status'] == "playing")
				{
					$output .= "<span class='now_playing_marker label label-info'>now playing</span>";
				} elseif($row['q_song_priority'] == "high")
				{
					$output .= "<span class='label label-success high_priority_marker'>promoted</span>";
				}
				$output .= "<div></div></div></div>";
			}
			$output .= "</div>";
			return $output;
		}
		else {
			return $this->initializeView();
		}
	}
}
?>
