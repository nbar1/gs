<?php
/**
 * Player
 *
 * Controls the music player
 */

class Player extends Base
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	public function getStream($song)
	{
		require("config.php");
		$gsapi = new gsAPI($config['api']['key'], $config['api']['secret']);

		// Session caching
		if (isset($_SESSION['gsSession']) && !empty($_SESSION['gsSession']))
		{
			$gsapi->setSession($_SESSION['gsSession']);
		}
		else {
			$_SESSION['gsSession'] = $gsapi->startSession();
		}
		
		// Country caching
		if (isset($_SESSION['gsCountry']) && !empty($_SESSION['gsCountry']))
		{
			$gsapi->setCountry($_SESSION['gsCountry']);
		}
		else {
			$_SESSION['gsCountry'] = $gsapi->getCountry();
		}

		$gsapi->authenticate($config['grooveshark']['username'], $config['grooveshark']['password']);
		// Make request to Grooveshark and return data as JSON
		$streamInfo = $gsapi->getSubscriberStreamKey($song, false);
		
		echo json_encode($streamInfo);
	}

	/**
	 * Get Next Song
	 *
	 * Gets the token of the next song
	 * 
	 * @TODO break out setting song status into a seperate method
	 * @return string Song token
	 */
	public function getNextSong()
	{
		$this->dbh = $this->db->prepare("SELECT id, token, status FROM queue WHERE status='queued' OR status='playing' ORDER BY status ASC, priority ASC, position ASC LIMIT 2");
		$this->dbh->execute();
		$rows = $this->dbh->fetchAll(PDO::FETCH_ASSOC);
		$x=0;
		foreach($rows as $row)
		{
			if($x === 0)
			{
				if($row['status'] === "playing")
				{
					$this->dbh = $this->db->prepare("UPDATE queue SET status='played' WHERE id=?");
					$this->dbh->execute(array($row['id']));
					$x++;
				}
				else {
					$this->dbh = $this->db->prepare("UPDATE queue SET status='playing' WHERE id=?");
					$this->dbh->execute(array($row['id']));
					return $row['token'];
				}
			}
			else {
				$this->dbh = $this->db->prepare("UPDATE queue SET status='playing' WHERE id=?");
				$this->dbh->execute(array($row['id']));
				return $row['token'];
			}
		}
	}

	/**
	 * Send data to view
	 *
	 * return string
	 */
	public function renderView()
	{
		return $this->templateEngine->draw('player');
	}
}
?>