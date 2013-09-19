<?php
session_start();
require('config.php');
require('api/gsControl.php');
require('api/gsAPI.php');

$gs = new gsControl();
$gs->api_key = $config['tinysong']['key'];
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
$country = $gsapi->getCountry();
$user = $gsapi->authenticate($config['grooveshark']['username'], $config['grooveshark']['password']);


if(!isset($_REQUEST['f']))
{
	echo $gs->initializeView();
}
else {
	switch($_REQUEST['f'])
	{
		case 'initialize':
			echo $gs->initializeView();
			break;
		case 'queue':
			echo $gs->displayQueueView();
			break;
	
		case 'search':
			echo $gs->displaySearchView($gs->getSearchResults($_REQUEST['query']));
			break;
	
		case 'setname':
			$gs->setNickname($_REQUEST['nickname']);
			echo $gs->initializeView();
			break;
	
		case 'add':
			echo $gs->addSongToQueue($_REQUEST['songID'], $_REQUEST['songPriority'], $_REQUEST['songArtist'], $_REQUEST['songTitle']);
			break;
	
		case 'markSong30Seconds':
			echo $gsapi->markStreamKeyOver30Secs($_REQUEST['streamKey'], $_REQUEST['streamServerID']);
			break;
		case 'getSongInfo':
			echo $gs->getSongInfo($_REQUEST['song']);
			break;
		case 'getNextSong':
			echo $gs->getNextSong();
			break;
		default:
			echo $gs->initializeView();
			break;
	}
}
?>
