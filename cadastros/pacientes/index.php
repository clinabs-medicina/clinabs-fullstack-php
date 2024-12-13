<?php
session_start();

$page = new stdClass();
$page->title = 'Pacientes';
if(isset($_REQUEST['page'])) {
    $page->content = $_REQUEST['page'] == 'presc' ? 'cadastros/pacientes/prescricao.php':'cadastros/pacientes/main.php';
} else {
    $page->content = 'cadastros/pacientes/main.php';
}
$page->bc = true;
$page->name = 'link_paciente';
$page->require_login = true;
$page->includePlugins = true;

$useDT = true;
$useSelector = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';