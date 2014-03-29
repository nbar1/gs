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
	private $_id;

	/**
	 * Username
	 */
	private $_username;

	/**
	 * API Key
	 */
	private $_api_key;

	/**
	 * Get user id
	 *
	 * @return int|null User ID
	 */
	public function getId()
	{
		return ($this->_id !== null) ? $this->_id : null;
	}

	/**
	 * Get Username
	 *
	 * @return string|null Username
	 */
	public function getUsername()
	{
		return ($this->_username !== null) ? $this->_username : null;
	}

	/**
	 * Get API Key
	 *
	 * @return string|null API Key
	 */
	public function getApiKey()
	{
		return ($this->_api_key !== null) ? $this->_api_key : null;
	}

	/**
	 * Set User ID
	 *
	 * @return User $this
	 */
	public function setId($id)
	{
		$this->_username = null;
		$this->_api_key = null;
		$this->_id = $id;
		return $this;
	}

	/**
	 * Set Username
	 *
	 * @return User $this
	 */
	private function _setUsername($usernane)
	{
		$this->_username = $id;
		return $this;
	}

	/**
	 * Set API Key
	 *
	 * @return User $this
	 */
	private function _setApiKey($api_key)
	{
		$this->_api_key = $api_key;
		return $this;
	}

	/**
	 * Get user information by API Key
	 *
	 * @param string $api_key
	 * @return bool
	 */
	public function getUserByApiKey($api_key)
	{
		if($this->getDao()->getUserExistsByApiKey($api_key))
		{
			$user = $this->getDao()->getUserInformationByApiKey($api_key);
			$this->setId($user['id'])
				->_setUsername($user['username'])
				->_setApiKey($user['api_key']);
			return true;
		}
		return false;
	}

	/**
	 * Get user information by username
	 *
	 * @param string $username
	 * @return bool
	 */
	public function getUserByUsername($username)
	{
		if($this->getDao()->getUserExistsByUsername($username))
		{
			$user = $this->getDao()->getUserInformationByUsername($username);
			$this->setId($user['id'])
				->_setUsername($user['username'])
				->_setApiKey($user['api_key']);
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
		$available_promotions = GS_PROMOTION_MAX - $this->getDao()->getRecentPromotionCount($this->getId());
		return ($available_promotions > 0) ? $available_promotions : 0;
	}

	/**
	 * Register User
	 *
	 * @param string $username
	 * @param string $password
	 * @return status
	 */
	public function registerUser($username, $password)
	{
		$username = trim($username);
		if($this->getDao()->getUserExistsByUsername($username))
		{
			ApiHandler::sendResponse(200, array('error' => true, 'message' => 'User already exists'));
			return USER_ALREADY_EXISTS;
		}
		$password = md5($password);
		$created = date('Y-m-d H:i:s');
		$api_key = md5($username.$created);

		return $this->getDao()->createUser(array($username, $password, $api_key, $created, $created));
	}

	/**
	 * Authenticate as given user
	 *
	 * @param string $username
	 * @param string $password
	 * @return status
	 */
	public function login($username, $password)
	{
		if(!$username) return USERNAME_REQUIRED;
		if(!$password) return PASSWORD_REQUIRED;
		if(strlen($username) > 32) return USERNAME_TOO_LONG;

		if($this->getDao()->getUserExistsByUsername($username))
		{
			$user = $this->getDao()->getUserInformationByUsername($username);
			$user_info = array(
				'username' => $user['username'],
				'api_key' => $user['api_key']
			);
			return (md5($password) === $user['password']) ? $user_info : BAD_PASSWORD;
		}
		else
		{
			return USER_NOT_FOUND;
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
	 * Get username by ID
	 *
	 * @param int $id User ID
	 * @return string
	 */
	public function getUsernameById($id)
	{
		if($id == 0) return "Autoplayer";

		$user = $this->getDao()->getUserInformationById($id);
		if($user == USER_NOT_FOUND)
		{
			return false;
		}
		return $user['username'];
	}

	/**
	 * Get ID by username
	 *
	 * @param string $username
	 * @return int
	 */
	public function getIdByUsername($username)
	{
		$user = $this->getDao()->getUserInformationByUsername($username);
		if(!$user)
		{
			return false;
		}
		return $user['id'];
	}
}
?>