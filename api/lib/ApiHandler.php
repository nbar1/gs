<?php
/*
 * API Handler
 *
 * Handle REST API related functions
 */
class ApiHandler
{
	/**
	 * Send JSON respnose
	 *
	 * @param $status HTTP Status Code
	 * @param $response HTTP Response Data
	 */
	public static function sendResponse($status, $success, $response = array())
	{
		$app = \Slim\Slim::getInstance();
		$app->status($status);
		$app->contentType('application/json');
		$data = array_merge(array('success' => $success), $response);
		echo json_encode($data);
		$app->stop();
	}

	/**
	 * Check if valid key was sent
	 *
	 * @returns bool
	 */
	public static function validKey()
	{
		if(!isset($_GET['apikey']))
		{
			return false;
		}
		$base = new Base();
		return $base->getDao()->getUserExistsByApiKey($_GET['apikey']);
	}

	/**
	 * Send not authenticated response
	 */
	public static function notAuthenticated()
	{
		self::sendResponse(401, false, array('message' => 'Invalid API Key'));
	}
}
?>
