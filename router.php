<?php
session_start();
require_once('vendor/autoload.php');
require_once('lib/autoload.php');
require_once('api/gsAPI.php');
require_once('config.php');

$app = new \Slim\Slim();
$queue = new Queue();

require_once('routes.php');

$app->run();
?>