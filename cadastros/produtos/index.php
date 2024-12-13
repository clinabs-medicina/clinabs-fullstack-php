<?php
session_start();


$page = new stdClass();
$page->title = 'Produtos';
$page->content = 'cadastros/produtos/main.php';
$page->bc = true;
$page->name = 'link_produtos';
$page->require_login = true;
$page->includePlugins = true;
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';