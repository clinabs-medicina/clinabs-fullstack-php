<?php
$page = new stdClass();
$page->title = 'Faturamento (Teste)';
$page->content = 'financeiro/main.php';
$page->bc = true;
$page->name = 'link_faturamento';
$page->require_login = true;
$page->includePlugins = true;

$useDT = true;
$useSelector = true;

require_once('../config.inc.php');


$balance = $asaas->accountBalance();
$paymentsBalance = $asaas->paymentsBalance();

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';