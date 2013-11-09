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
	 * Get user information from session
	 *
	 * @return bool
	 */
	public function getUserBySession()
	{
		// perform some mild validation on the session id
		if(isset($_SESSION['gs_auth']) && preg_match('/^[a-f0-9]{32}$/', $_SESSION['gs_auth']) && $this->getDao()->getUserExistsByHash($_SESSION['gs_auth']))
		{
			$user = $this->getDao()->getUserInformationByHash($_SESSION['gs_auth']);
			$this->setId($user['id'])
				->setNickname($user['nickname'])
				->setActiveState($user['active']);
			return true;
		}
		return false;
	}

	/**
	 * Get Available Promotions
	 *
	 * @return int Available promotions
	 */
	public function getAvailablePromotions()
	{
		$max_promotions = 3;
		$available_promotions = $max_promotions - $this->getDao()->getRecentPromotionCount($this->getId());
		return ($available_promotions > 0) ? $available_promotions : 0;
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

		if($this->getDao()->createUser(array($nickname, $hash, $created, $created)))
		{
			$_SESSION['gs_auth'] = $hash;
			header('location: '.$_SERVER['PHP_SELF']);
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Authenticate as given user
	 *
	 * @param string|null $nickname Nickname
	 * @return bool
	 */
	public function authenticate($nickname = null)
	{
		if($nickname === null && $this->getUserBySession()) $nickname = $this->getNickname();
		if(strlen($nickname) > 32) $nickname = substr($nickname, 0, 32);

		if($this->getDao()->getUserExistsByNickname($nickname))
		{
			$user = $this->getDao()->getUserInformationByNickname($nickname);
			$hash = md5($user['nickname'].$user['ts_created']);
			$this->setId($user['id'])
				->setNickname($user['nickname'])
				->setActiveState($user['active']);
			$_SESSION['gs_auth'] = $hash;
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

		$user = $this->getDao()->getUserInformationById($id);
		return $user['nickname'];
	}

	/**
	 * Get ID by nickname
	 *
	 * @param string $nickname User nickname
	 * @return int
	 */
	public function getIdByNickname($nickname)
	{
		$user = $this->getDao()->getUserInformationByNickname($nickname);
		return $user['id'];
	}

	/**
	 * Send data to view
	 *
	 * @return string
	 */
	public function renderView()
	{
		return $this->templateEngine->draw('login');
	}
}
?>