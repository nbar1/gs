<?php
/*
 * Models_Queue
 *
 * Model for Queue
 */
class Models_Queue
{
	/**
	 * database object
	 */
	private $db;

	/**
	 * __construct
	 *
	 * Initiate the database object
	 */
	public function __construct()
	{
		require('config.php');
		$this->db = new PDO("mysql:host={$config['database']['host']};dbname={$config['database']['db_name']}", $config['database']['user'], $config['database']['password']);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * getQueue
	 *
	 * Return an array of the queue
	 *
	 * @return array
	 */
	function getQueue()
	{
		$data = array("queued", "playing");
		$this->dbh = $this->db->prepare("SELECT q_id, q_song_id, q_song_title, q_song_artist, q_song_played_by, q_song_priority, q_song_position, q_song_status FROM queue WHERE q_song_status=? OR q_song_status=? ORDER BY q_song_status ASC, q_song_priority ASC, q_song_position ASC");
		$this->dbh->execute($data);
		$this->dbh->setFetchMode(PDO::FETCH_ASSOC);
		return $this->dbh->fetchAll();
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
		$this->dbh = $this->db->prepare("SELECT MAX(q_song_position) FROM queue");
		$this->dbh->execute();
		$songPosition = $this->dbh->fetchColumn();

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
	 * @param Song $song
	 * @return bool
	 */
	function isSongInQueue(Song $song)
	{
		$data = array("queued", $song->getId());
		$this->dbh = $this->db->prepare("SELECT q_id FROM queue WHERE q_song_status=? AND q_song_id=? LIMIT 1");
		$this->dbh->execute($data);
		if($this->dbh->rowCount() > 0)
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
	 * @param Song $song
	 * @param string $songPriority
	 * @return string
	 */
	function addSongToQueue(Song $song)
	{
		if($this->isSongInQueue($song->getId()) === FALSE)
		{
			/*
			// Have to check for this in $song->request();
			if($this->getAvailablePromotions() < 1)
			{
				$songPriority = "low";
			}
			*/
			$data = array($song->getId(), $song->getTitle(), $song->getArtist(), $song->getPriority(), $this->getNextQueuePosition(), date("Y-m-d H:i:s"), User::getId());
			$this->dbh = $this->db->prepare("INSERT INTO queue (q_song_id, q_song_title, q_song_artist, q_song_priority, q_song_position, q_song_added_ts, q_song_played_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
			if($this->dbh->execute($data))
			{
				if($songPriority === "high")
				{
					$song->setStatus('promoted');
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
			return $song->Upvote();
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
