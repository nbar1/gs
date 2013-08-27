<?php
/*
 * gsControl
 *
 * Controls the flow for viewing song queue and searching for songs
 */
class Song extends GS
{
	protected $params;

	/**
	 * __construct
	 *
	 * Initiate the database object
	 */
	public function __construct()
	{
		$this->setModel("GS_Model_Song");
	}

	/**
	 * isSongInQueue
	 *
	 * Returns whether the given songID is already queued
	 *
	 * @return bool
	 */
	function isSongInQueue()
	{
		$queue = new Queue();
		return $queue->isSongInQueue($this);
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
		if($this->isSongInQueue($songID) === FALSE)
		{
			if($this->getAvailablePromotions() < 1)
			{
				$songPriority = "low";
			}
			$data = array($songID, $songTitle, $songArtist, $songPriority, $this->getNextQueuePosition(), date("Y-m-d H:i:s"), $this->getUserID());
			$this->dbh = $this->db->prepare("INSERT INTO queue (q_song_id, q_song_title, q_song_artist, q_song_priority, q_song_position, q_song_added_ts, q_song_played_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
			if($this->dbh->execute($data))
			{
				if($songPriority === "high")
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
		$data = array("queued", "playing");
		$this->dbh = $this->db->prepare("SELECT q_id, q_song_id, q_song_status FROM queue WHERE q_song_status=? OR q_song_status=? ORDER BY q_song_status ASC, q_song_priority ASC, q_song_position ASC LIMIT 2");
		$this->dbh->execute($data);
		$this->dbh->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $this->dbh->fetchAll();
		$x=0;
		foreach($rows as $row)
		{
			if($x === 0)
			{
				if($row['q_song_status'] === "playing")
				{
					$data = array("played", $row['q_id']);
					$this->dbh = $this->db->prepare("UPDATE queue SET q_song_status=? WHERE q_id=?");
					$this->dbh->execute($data);
					$x++;
				}
				else {
					$data = array("playing", $row['q_id']);
					$this->dbh = $this->db->prepare("UPDATE queue SET q_song_status=? WHERE q_id=?");
					$this->dbh->execute($data);
					return $row['q_song_id'];
				}
			}
			else {
				$data = array("playing", $row['q_id']);
				$this->dbh = $this->db->prepare("UPDATE queue SET q_song_status=? WHERE q_id=?");
				$this->dbh->execute($data);
				return $row['q_song_id'];
			}
		}
	}

	/**
	 * getSearchResults
	 *
	 * Returns an array of search results based on a given query
	 *
	 * @param string $query
	 * @return array
	 */
	function getSearchResults($query)
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
		if($this->isAuthenticated() === FALSE)
		{
			
			$output = "<div class='row-fluid nickview_row'><div class='col-lg-12'><div class='setUser_header'>enter your name</div></div></div>";
			$output .= "<div class='row-fluid nickview_row'><div class='col-lg-12 setUser_textbox'><input type='text' class='input-lg' id='setUser_textbox' placeholder='name' maxlength='32' /></div></div>";
			$output .= "<div class='row-fluid nickview_row'><div class='col-lg-12 nickview_submit'><div id='setUser_submit' class='btn btn-lg' onclick=''>submit</div></div></div>";
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
		if($this->isAuthenticated() === TRUE)
		{
			$queue = $this->getQueue();

			$output = "<script>var t=setTimeout(\"reloadQueue()\", 15000);</script>";
			$output .= "<div id='song_list' class='queue'>";

			foreach($queue as $row)
			{
				$output .= "<div class='row-fluid item_song ".$row['q_song_priority']." ".$row['q_song_status']."' onclick=''><div class='col-lg-12'>";
				$output .= "<div class='item_status'></div>";
				$output .= "<div class='song_name'>".$row['q_song_title']."</div>";
				$output .= "<div class='song_artist'>".$row['q_song_artist']."</div>";
				$output .= "<div class='song_played_by'>".$this->getNickname($row['q_song_played_by'])."</div>";
				if($row['q_song_status'] === "playing")
				{
					$output .= "<span class='now_playing_marker label label-info'>now playing</span>";
				}
				elseif($row['q_song_priority'] === "high") {
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
