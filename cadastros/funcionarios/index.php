<?php
$page = new stdClass();
$page->title = 'Funcionários';
$page->content = 'cadastros/funcionarios/main.php';
$page->bc = true;
$page->name = 'link_funcionarios';
$page->require_login = true;
$page->includePlugins = true;

$useDT = true;
$useSelector = true;



require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';