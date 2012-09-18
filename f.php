<?php
session_start();
require('db.php');
require('gsControl.class.php');

$gs = new gsControl();

switch($_REQUEST['f'])
{
	case 'initialize':
		echo $gs->initializeView();
		break;
	case 'queue':
		echo $gs->displayQueueView();
		break;

	case 'search':
		echo $gs->displaySearchView($gs->doSearch($_REQUEST['query']));
		break;

	case 'setname':
		$gs->setNickname($_REQUEST['nickname']);
		echo $gs->initializeView();
		break;

	case 'add':
		echo $gs->addSongToQueue($_REQUEST['songID'], $_REQUEST['songPriority'], $_REQUEST['songArtist'], $_REQUEST['songTitle']);
		break;

	default:
		echo $gs->initializeView();
		break;
}
?>
