<?php
session_start();

// Make sure to validate and cleanse this and stuff.
$songID = $_REQUEST['song'];

// Load up API wrapper
require("../api/gsAPI.php");
require("../config.php");
$gsapi = new gsAPI($config['api']['key'], $config['api']['secret']); //This is the GrooveShark PRIVATE API key

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

$country = $gsapi->getCountry();
$user = $gsapi->authenticate($config['grooveshark']['username'], $config['grooveshark']['password']);
// Make request to Grooveshark and return data as JSON

$streamInfo = $gsapi->getSubscriberStreamKey($songID, false);

echo json_encode($streamInfo);
?>
