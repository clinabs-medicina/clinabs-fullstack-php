<?php
$page = new stdClass();
$page->title = 'Médicos';
$page->content = isset($_GET['id']) ? 'medicos/perfil.php':'medicos/main.php';
$page->bc = true;
$page->name = 'link_medicos';
$page->require_login = false;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';