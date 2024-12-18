<?php
$page = new stdClass();
$page->title = 'Agendamento';
$page->content = isset($_REQUEST['page']) && $_REQUEST['page'] == 'medico' ? 'agendamento/medico.php':'agendamento/main.php';
$page->bc = true;
$page->name = 'link_agendar_consulta';
$page->require_login = false;
$page->includePlugins = true;
require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';