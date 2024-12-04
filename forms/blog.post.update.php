<?php
global $pdo;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$dados = json_decode(file_get_contents('php://input'), true);