<?php
session_start();
require_once('lib/autoload.php');
require_once('config.php');

$app = new \Slim\Slim();
require_once('routes.php');
$app->run();
?>