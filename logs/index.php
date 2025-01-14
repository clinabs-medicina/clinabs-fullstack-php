<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';


$page = new stdClass();
$page->title = 'Logs de Acessos';
$page->content = 'logs/main.php';
$page->bc = true;
$page->name = 'link_logs';
$page->require_login = true;

if($user->perms->table_logs == 0) {
    header('Location: /');
}

require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';