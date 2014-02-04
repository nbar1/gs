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
	public function getUserByHash($hash)
	{
		// perform some mild validation on the session id
		if(preg_match('/^[a-f0-9]{32}$/', $hash) && $this->getDao()->getUserExistsByHash($hash))
		{
			$user = $this->getDao()->getUserInformationByHash($hash);
			$this->setId($user['id'])
				->setNickname($user['nickname'])
				->setActiveState($user['active']);
			return true;
		}
		return false;
	}

	/**
	 * Get user information from session
	 *
	 * @param $hash User hash [optional]
	 * @return bool
	 */
	public function isAuthenticated()
	{
		$hash = (!empty($_GET['hash'])) ? $_GET['hash'] : ((!empty($_POST['hash'])) ? $_POST['hash'] : false);
 		// perform some mild validation on the session id
		if(isset($hash) && preg_match('/^[a-f0-9]{32}$/', $hash) && $this->getDao()->getUserExistsByHash($hash))
		{
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

		return $this->getDao()->createUser(array($nickname, $hash, $created, $created));
	}

	/**
	 * Authenticate as given user
	 *
	 * @param string|null $nickname Nickname
	 * @return bool
	 */
	public function authenticate($nickname = false)
	{
		if($nickname === false)
		{
			$hash = (!empty($_GET['hash'])) ? $_GET['hash'] : ((!empty($_POST['hash'])) ? $_POST['hash'] : null);
			if($hash)
			{
				$user_info = $this->getDao()->getUserInformationByHash($hash);
				return $this->authenticate($user_info['nickname']);
			}
			ApiHandler::setStatusHeader(500);
			return array('message' => 'No credentials received');
		}

		if(strlen($nickname) > 32) $nickname = substr($nickname, 0, 32);

		if($this->getDao()->getUserExistsByNickname($nickname))
		{
			$user = $this->getDao()->getUserInformationByNickname($nickname);
			$hash = md5($user['nickname'].$user['ts_created']);
			$this->setId($user['id'])
				->setNickname($user['nickname'])
				->setActiveState($user['active']);

			// Return response
			ApiHandler::setStatusHeader(200);
			return array('hash' => $hash);
		}
		else
		{
			if($this->createUser($nickname))
			{
				return $this->authenticate($nickname);
			}
			else
			{
				ApiHandler::setStatusHeader(500);
				return array('message' => 'Could not create user');
			}
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
}
?>