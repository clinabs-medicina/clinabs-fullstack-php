<?php
$page = new stdClass();
$page->title = 'Usuários';
$page->content = 'cadastros/usuarios/main.php';
$page->bc = true;
$page->name = 'link_usuarios';
$page->require_login = true;
$page->includePlugins = true;



require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';