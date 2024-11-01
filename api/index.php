<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../src/Controller/UserController.php';

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING);

$controller = new UserController();
$controller->processRequest();
