<?php
session_start();
require_once('lib/autoload.php');

$app = new \Slim\Slim();
$queue = new Queue();

require_once('routes.php');

$app->run();
?>