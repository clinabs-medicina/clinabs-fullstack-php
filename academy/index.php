<?php
error_reporting(1);
ini_set('display_errors', 1);


$page = new stdClass();
$page->title = 'Carrinho de Compras';
$page->content = 'academy/main.php';
$page->bc = true;
$page->name = 'link_academy';
$page->require_login = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';