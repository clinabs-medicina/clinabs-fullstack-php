<?php
$page = new stdClass();
$page->title = 'Unidades';
$page->content = 'cadastros/unidades/main.php';
$page->bc = true;
$page->name = 'link_unidades';
$page->require_login = false;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';