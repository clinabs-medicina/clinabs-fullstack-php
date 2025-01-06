<?php
$page = new stdClass();
$page->title = $user->tipo == 'FUNCIONARIO' ? 'Pedidos':'Meus Pedidos';
$page->content = !isset($_REQUEST['pedido_code']) ? 'pedidos/main.php':'pedidos/pedido.php';
$page->bc = true;
$page->name = 'link_pedidos';
$page->require_login = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';