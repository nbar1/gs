<?php
session_start();

// Make sure to validate and cleanse this and stuff.
$songID = $_REQUEST['song'];

// Load up API wrapper
require("gsAPI.php");
$gsapi = new gsAPI('nbar1', '1f64634987618265edb26fe236c00011'); //This is the GrooveShark PRIVATE API key
gsAPI::$headers = array("X-Client-IP: " . $_SERVER['REMOTE_ADDR']);

// Session caching stuff
if (isset($_SESSION['gsSession']) && !empty($_SESSION['gsSession'])) {
	$gsapi->setSession($_SESSION['gsSession']);
} else {
	$_SESSION['gsSession'] = $gsapi->startSession();
}
if (!$_SESSION['gsSession']) {
	die("noSession");
}
if (isset($_SESSION['gsCountry']) && !empty($_SESSION['gsCountry'])) {
	$gsapi->setCountry($_SESSION['gsCountry']);
} else {
	$_SESSION['gsCountry'] = $gsapi->getCountry();
}
if (!$_SESSION['gsCountry']) {
	die("noCountry");
}

// comment
// Make request to Grooveshark and return data as JSON
$loginHelper = $gsapi->login('nbarone', '4548bb023ad8460e0c8bd61bcd841b53'); // This is my username and MD5'd password
$streamInfo = $gsapi->getStreamKeyStreamServer($songID, false);
echo json_encode($streamInfo);
?>
