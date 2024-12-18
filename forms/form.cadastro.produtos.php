<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 1);
$token = uniqid();

$produto = new stdClass();

$produto->nome = ucwords(strtolower($_REQUEST['nome_produto']));
$produto->codigo = $_REQUEST['codigo_produto'] ?? 'CN'.uniqid();
$produto->valor = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_venda']));
$produto->descricao = $_REQUEST['descricao'];
$produto->nacionalidade = $_REQUEST["nacionalidade"];
$produto->valor_compra = $_REQUEST["valor_compra"];
$produto->moeda = $_REQUEST["moeda"];
$produto->valor_venda = preg_replace("/[^0-9]/", "", $_REQUEST["valor_venda"]);
$produto->valor_frete_compra = $_REQUEST["valor_frete_compra"];
$produto->unidade_medida = $_REQUEST["unidade_medida"];
$produto->capacidade = $_REQUEST["capacidade"];
$produto->fornecedor = $_REQUEST["fornecedor"];
$produto->nfe = $_REQUEST["nfe"];
$produto->lote = $_REQUEST["lote"];
$produto->data_validade = $_REQUEST["data_validade"];
$produto->marca = $_REQUEST["marca"];
$produto->prazo_entrega = $_REQUEST["prazo_entrega"];
$produto->valor_frete_venda = $_REQUEST["valor_frete_venda"];
$produto->numero_frascos = $_REQUEST["numero_frascos"]; 
$produto->token = uniqid();
$produto->image = uniqid().'.png';


move_uploaded_file($_FILES['product_image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/data/images/produtos/'.$produto->image);




if($produtos->Add($produto))
{
    $json = json_encode([
        'status' => 'success', 
        'text' => 'Cadastro Realizado Com Sucesso!',
    ], JSON_PRETTY_PRINT);
}else {
    $json = json_encode([
        'status' => 'danger', 
        'text' =>  ($produtos->lastException ?? new Exception('Erro Desconhecido'))->getMessage()
    ], JSON_PRETTY_PRINT);
}

header('content-type: application/json');
echo $json;