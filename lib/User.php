<?php
/**
 * User
 *
 * Contains information about an authenticated user
 */

class User extends Base
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
	 * Get user id
	 *
	 * @return int|null User ID
	 */
	public function getId()
	{
		return ($this->id !== null) ? $this->id : null;
	}

	/**
	 * Get nickname
	 *
	 * @return string|null User Nickname
	 */
	public function getNickname()
	{
		return ($this->nickname !== null) ? $this->nickname : null;
	}

	/**
	 * Get Active State
	 *
	 * @return bool User Active State
	 */
	public function isActive()
	{
		return ($this->active !== null) ? $this->active : null;
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
	public function getUserByCookie()
	{
		// perform some mild validation on the cookie
		if(isset($_COOKIE['gs_auth']) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE['gs_auth']))
		{
			$dbh = $this->getDatabase()->prepare("SELECT id, hash, nickname, active FROM users WHERE hash=? LIMIT 1");
			$dbh->execute(array($_COOKIE['gs_auth']));
			if($dbh->rowCount() > 0)
			{
				try
				{
					$user = $dbh->fetch();
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
		$dbh = $this->getDatabase()->prepare("SELECT id FROM queue WHERE priority IN('high', 'med') AND promoted_by=? AND ts_added >= DATE_SUB(NOW(), INTERVAL 120 MINUTE)");
		if($dbh->execute(array($this->getId())))
		{
			$availablePromotions = $maxPromotions - $dbh->rowCount();
			return $availablePromotions;
		}
		else {
			return 0;
		}
	}

	/**
	 * Create User
	 *
	 * @param string|null $nickname Nickname
	 * @return bool
	 */
	public function createUser($nickname = null)
	{
		if(!$nickname) $nickname = "user".date("His");
		$created = date('Y-m-d H:i:s');
		$hash = md5($nickname.$created);
		$dbh = $this->getDatabase()->prepare("INSERT INTO users (nickname, hash, ts_created, ts_lastlogin) VALUES (?, ?, ?, ?)");
		if($dbh->execute(array($nickname, $hash, $created, $created)))
		{
			setcookie('gs_auth', $hash, strtotime("+5 years"),'/');
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
	 * @param string $nickname Nickname
	 * @return bool
	 */
	public function isCurrentUser($nickname)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM users WHERE nickname=? LIMIT 1");
		if($dbh->execute(array($nickname)) && $dbh->rowCount() > 0)
		{
			return true;
		}
		return false;
		
	}

	/**
	 * Authenticate as given user
	 *
	 * @param string|null $nickname Nickname
	 * @return bool
	 */
	public function authenticate($nickname = null)
	{
		if($nickname === null && $this->getUserByCookie()) $nickname = $this->getNickname();
		if(strlen($nickname) > 32) $nickname = substr($nickname, 0, 32);

		if($this->isCurrentUser($nickname))
		{
			$dbh = $this->getDatabase()->prepare("SELECT id, nickname, ts_created, active FROM users WHERE nickname=?");
			$dbh->execute(array($nickname));
			$user = $dbh->fetch(PDO::FETCH_ASSOC);
			$hash = md5($user['nickname'].$user['ts_created']);
			try
			{
				$this->setId($user['id'])
					->setNickname($user['nickname'])
					->setActiveState($user['active']);
			}
			catch (Exception $e) {
				trigger_error($e->getMessage(), E_USER_ERROR);
			}
			setcookie('gs_auth', $hash, strtotime("+5 years"), '/');
			return true;
			
		}
		else {
			return $this->createUser($nickname);
		}
	}

	/**
	 * Autoplay User
	 *
	 * Creates a user for use with autoplay
	 *
	 * @return User
	 */
	public function autoplayUser()
	{
		$this->setId(0);
		return $this;
	}

	/**
	 * Get nickname by ID
	 *
	 * @param int $id User ID
	 * @return string
	 */
	public function getNicknameById($id)
	{
		if($id == 0) return "Autoplayer";

		$dbh = $this->getDatabase()->prepare("SELECT nickname FROM users WHERE id=? LIMIT 1");
		$dbh->execute(array($id));
		$row = $dbh->fetch(PDO::FETCH_ASSOC);
		return $row['nickname'];
	}

	/**
	 * Get ID by nickname
	 *
	 * @param string $nickname User nickname
	 * @return int
	 */
	public function getIdByNickname($nickname)
	{
		$dbh = $this->getDatabase()->prepare("SELECT id FROM users WHERE nickname=? LIMIT 1");
		$dbh->execute(array($nickname));
		$row = $dbh->fetch(PDO::FETCH_ASSOC);
		return $row['id'];
	}

	/**
	 * Get listening session ID
	 *
	 * @return int|bool Session ID
	 */
	public function getListeningSession()
	{
		
		$dbh = $this->getDatabase()->prepare("SELECT coordinates FROM users WHERE id=? LIMIT 1");
		$dbh->execute(array($this->getId()));
		$row = $dbh->fetch(PDO::FETCH_ASSOC);
		$coordinates = $row['coordinates'];

		return $this->getSession()->getSessionMatch($coordinates);
	}

	/**
	 * Send data to view
	 *
	 * @return string
	 */
	public function renderView()
	{
		return $this->templateEngine->draw('register');
	}
}
?>