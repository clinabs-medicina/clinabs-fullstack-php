<?php
$page = new stdClass();
$page->title = 'Faturamento';
$page->content = 'faturamento/main.php';
$page->bc = true;
$page->name = 'link_faturamento';
$page->require_login = true;
$page->includePlugins = true;
require_once('../config.inc.php');


/* Cobrar Cliente 
$data = $asaas->cobrarCliente(
    tipo: 'UNDEFINED', 
    id: '66911af84c193', 
    reference: '204323', 
    valor: 100, 
    descricao: 'TELECONSULTA 15/07/2024 14:00 #5265656'
);
*/

$cobrancas = $asaas->listarCobrancas();
$saldo = $asaas->accountBalance();
$balance = $asaas->paymentsBalance();
$useDT = true;
$useSelector = true;
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';