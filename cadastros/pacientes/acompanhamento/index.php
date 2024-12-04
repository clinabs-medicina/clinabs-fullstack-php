<?php
$page = new stdClass();
$page->title = 'Pacientes';
$page->content = 'cadastros/pacientes/acompanhamento/main.php';
$page->bc = true;
$page->name = 'link_paciente';
$page->require_login = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';