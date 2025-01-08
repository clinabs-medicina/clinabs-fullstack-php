<?php
$page = new stdClass();
$page->title = 'Home';
$page->content = 'main.php';
$page->bc = true;
$page->name = 'link_home';
$page->useDT = false;
$page->useSelector = false;

$user = $_SESSION['user'];

if (!isset($_user)) {
    $_user = $user;
}
// require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pg = $_SERVER['REQUEST_URI'];
    if (isset($_SESSION['token'])) {
        $usr = $_user || $user;
    } else {
        $usr = '';
    }

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    $pdo->query("INSERT INTO `ACCESS_LOGS` (`page`, `user`, `ip`) VALUES ('{$pg}', '{$usr}', '{$ip}')");
} catch (Exception $ex) {
}

if ($user->tipo == 'MEDICO') {
    header('Location: /agenda');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/MasterPage.php';
