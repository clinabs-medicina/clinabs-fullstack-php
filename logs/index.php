<?php
header('Content-Type: text/html; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// if(isset($_SESSION['userObj'])) {
//     $user = (object) $_SESSION['userObj'];
// }


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