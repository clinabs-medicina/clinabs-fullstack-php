<?php
$page = new stdClass();
$page->title = 'Meus Favoritos';
$page->content = 'favoritos/main.php';
$page->includePlugins = true;
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';