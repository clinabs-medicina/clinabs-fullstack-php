<?php
session_start();


$page = new stdClass();
$page->title = 'Produtos';
$page->content = isset($_GET['id']) ? 'produtos/'.$_GET['id'].'.php':'produtos/main.php';
$page->bc = true;
$page->name = 'link_produtos';
$page->require_login = false;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';