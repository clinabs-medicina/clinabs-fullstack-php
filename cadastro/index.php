<?php

if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'messages'){
    header('Location: https://clinabs.com/cadastro/');
}

$page = new stdClass();
$page->title = 'Cadastros';
$page->content = isset($_REQUEST['page']) ? 'cadastro/'.$_REQUEST['page'].'.php':'cadastro/main.php';
$page->bc = true;
$page->name = 'link_cadastro';
$page->require_login = false;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';