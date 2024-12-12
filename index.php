<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page = new stdClass();
$page->title = 'Home';
$page->content = 'main.php';
$page->bc = true;
$page->name = 'link_home';
$page->useDT = false;
$page->useSelector = false;
//require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

try {
    $pg = $_SERVER['REQUEST_URI'];
    if(isset($_COOKIE['sessid_clinabs'])) {
        $usr = $_user || $user;
    } else {
        $usr = '';
    }
    
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    $pdo->query("INSERT INTO `ACCESS_LOGS` (`page`, `user`, `ip`) VALUES ('{$pg}', '{$usr}', '{$ip}')");
} catch (Exception $ex) {

}

if($user->tipo == 'MEDICO') {
    header('Location: /agenda');
}


require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';
