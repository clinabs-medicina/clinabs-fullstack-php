<?php
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.inc.php';
$page = new stdClass();
$page->title = 'Agenda do Médico';
$page->content = isset($_GET['page']) ? 'agenda/' . $_GET['page'] . '.php' : 'agenda/main.php';
$page->bc = true;
$page->name = 'link_agenda';
$useDT = true;
$useSelector = true;
$useEditor = true;
$page->require_login = true;

$page->includePlugins = true;

if ($user->tipo == 'FUNCIONARIO') {
    $stmt2 = $pdo->prepare('SELECT * FROM PACIENTES WHERE MD5(token) = :token');
    $stmt2->bindValue(':token', $_SESSION['token']);

    $stmt2->execute();
    $_user = $stmt2->fetch(PDO::FETCH_OBJ);
    $_SESSION['_user'] = $_user;
} else {
    $stmt2 = $pdo->prepare('SELECT * FROM PACIENTES WHERE MD5(token) = :token');

    try {
        $stmt2->bindValue(':token', $_SESSION['token']);
        $stmt2->execute();
        $_user = $stmt2->fetch(PDO::FETCH_OBJ);
        $_SESSION['_user'] = $_user;
    } catch (PDOException $ex) {
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/session.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/MasterPage.php';
