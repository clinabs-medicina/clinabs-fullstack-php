<?php
$page = new stdClass();
$page->title = 'Médicos';
$page->content = 'cadastros/medicos/main.php';
$page->bc = true;
$page->name = 'link_medicos';
$page->require_login = true;
$page->includePlugins = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';