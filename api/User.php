<?php
/**
 * User
 *
 * Contains information about an authenticated user
 */

class User extends gs
{
	/**
	 * User ID
	 */
	private $id;

	/**
	 * User Nickname
	 */
	private $nickname;

	/**
	 * User Active
	 */
	private $active;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get user id
	 *
	 * @return int|null User ID
	 */
	public function getId()
	{
		if ($this->id !== null) return $this->id;
		return null;
	}

	/**
	 * Get nickname
	 *
	 * @return string|null User Nickname
	 */
	public function getNickname()
	{
		if ($this->nickname !== null) return $this->nickname;
		return null;
	}

	/**
	 * Get Active State
	 *
	 * @return bool User Active State
	 */
	public function isActive()
	{
		if ($this->active !== null) return $this->active;
		return null;
	}

	/**
	 * Set User ID
	 *
	 * @return User $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Set User Nickname
	 *
	 * @return User $this
	 */
	public function setNickname($nickname)
	{
		$this->nickname = $nickname;
		return $this;
	}

	/**
	 * Set User Active State
	 *
	 * @return User $this
	 */
	public function setActiveState($active)
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * Get user information from cookie
	 *
	 * @return bool
	 */
	private function getUserByCookie()
	{
		// perform some mild validation on the cookie
		if(isset($_COOKIE['auth']) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE['auth']))
		{
			$this->dbh = $this->db->prepare("SELECT id, hash, nickname, active FROM users WHERE hash=? LIMIT 1");
			$this->dbh->execute(array($_COOKIE['auth']));
			if($this->dbh->rowCount() > 0)
			{
				try
				{
					$user = $this->dbh->fetch();
					$this->setId($user['id'])
						->setNickname($user['nickname'])
						->setActiveState($user['active']);
					return true;
				}
				catch (Exception $e) {
					trigger_error($e->getMessage(), E_USER_ERROR);
				}
			}
			else {
				throw new Exception("No user matching given hash");
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Get Available Promotions
	 *
	 * @return int Available promotions
	 */
	public function getAvailablePromotions()
	{
		$maxPromotions = 3;
		$this->dbh = $this->db->prepare("SELECT * FROM queue WHERE priority IN(?, ?) AND promoted_by=? AND ts_added >= DATE_SUB(NOW(), INTERVAL 120 MINUTE)");
		if($this->dbh->execute(array("high", "med", $this->getId())))
		{
			var_dump($this->dbh->fetchAll(PDO::FETCH_ASSOC));
			$availablePromotions = $maxPromotions - $this->dbh->rowCount();
			return $availablePromotions;
		}
		else {
			return 0;
		}
	}

	/**
	 * Create User
	 *
	 * @param string Nickname
	 * @return bool
	 */
	public function createUser($nickname = null)
	{
		if(!$nickname) $nickname = "user".date("His");
		$created = date('Y-m-d H:i:s');
		$hash = md5($nickname.$created);
		$this->dbh = $this->db->prepare("INSERT INTO users (nickname, hash, ts_created, ts_lastlogin) VALUES (?, ?, ?, ?)");
		if($this->dbh->execute(array($nickname, $hash, $created, $created)))
		{
			setcookie('auth', $hash, strtotime("+5 years"));
			header('location: '.$_SERVER['PHP_SELF']);
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Is Valid User
	 *
	 * Check if given nickname belongs to a user
	 *
	 * @return bool
	 */
	public function isCurrentUser($nickname)
	{
		$this->dbh = $this->db->prepare("SELECT id FROM users WHERE nickname=? LIMIT 1");
		if($this->dbh->execute(array($nickname)) && $this->dbh->rowCount() > 0)
		{
			return true;
		}
		return false;
		
	}

	/**
	 * Authenticate as given user
	 *
	 * @param string Nickname
	 * @return bool
	 */
	public function authenticate($nickname = null)
	{
		if($nickname === null && $this->getUserByCookie()) $nickname = $this->getNickname();
		if(strlen($nickname) > 32) $nickname = substr($nickname, 0, 32);

		if($this->isCurrentUser($nickname))
		{
			$this->dbh = $this->db->prepare("SELECT id, nickname, ts_created, active FROM users WHERE nickname=?");
			$this->dbh->execute(array($nickname));
			$user = $this->dbh->fetch(PDO::FETCH_ASSOC);
			$hash = md5($user['nickname'].$user['ts_created']);
			try
			{
				$this->setId($user['id'])
					->setNickname($user['nickname'])
					->setActiveState($user['active']);
				return true;
			}
			catch (Exception $e) {
				trigger_error($e->getMessage(), E_USER_ERROR);
			}
			setcookie('auth', $hash, strtotime("+5 years"));
			return true;
			
		}
		else {
			return $this->createUser($nickname);
		}
	}
	
}
?>