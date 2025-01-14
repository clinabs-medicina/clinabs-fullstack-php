<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 1);

$produto = new stdClass();

$produto->nome = ucwords(strtolower($_REQUEST['nome_produto']));
$produto->codigo = $_REQUEST['codigo_produto'];
$produto->descricao = $_REQUEST['descricao'];
$produto->nacionalidade = $_REQUEST["nacionalidade"];
$produto->valor_compra = preg_replace('/[^0-9]/', '',$_REQUEST["valor_compra"]);
$produto->valor_venda = preg_replace('/[^0-9]/', '',$_REQUEST["valor_venda"]) /100;
$produto->valor_frete_compra = preg_replace('/[^0-9]/', '',$_REQUEST["valor_frete_compra"]);
$produto->unidade_medida = $_REQUEST["unidade_medida"];
$produto->capacidade = $_REQUEST["capacidade"];
$produto->fornecedor = $_REQUEST["fornecedor"];
$produto->nfe = $_REQUEST["nfe"];
$produto->lote = $_REQUEST["lote"];
$produto->moeda = $_REQUEST["moeda"];
$produto->data_validade = $_REQUEST["data_validade"];
$produto->marca = $_REQUEST["marca"];
$produto->prazo_entrega = $_REQUEST["prazo_entrega"];
$produto->valor_frete_venda = preg_replace('/[^0-9]/', '',$_REQUEST["valor_frete_venda"]);
$produto->numero_frascos = $_REQUEST["numero_frascos"]; 
$produto->token = $_REQUEST["token"];
$produto->catalog_file = $_REQUEST['product-catalog'];
$produto->image = $_REQUEST['product-image'];
$produto->status = $_REQUEST['status'];
$produto->excluir = $_REQUEST['excluir'] == 'on' ? 1 : 0;

if(file_exists('../tmp/'.$_REQUEST['product-image'])){
    rename('../tmp/'.$_REQUEST['product-image'], '../data/images/produtos/'.$_REQUEST['product-image']);
}

if(file_exists("../data/images/docs/{$_REQUEST['product-catalog']}")) {
    rename('../data/images/docs/'.$_REQUEST['product-catalog'], '../data/catalogs/products/'.$_REQUEST['product-catalog']);
}


function parse_sql_error($code, $exception) {
    $error = ['error' => $code, 'text' => ''];

    switch($code) {
        case '01000': {
            $error['text'] = 'Verifique os campos digitados';
            break;
        }            
    }

    $error['debug'] = $exception;

    return json_encode($error, JSON_PRETTY_PRINT);
}

    if($produtos->Update($produto))
    {
        $json = json_encode([
            'status' => 'success', 
            'text' => 'Cadastro Atualizado Com Sucesso!',
        ], JSON_PRETTY_PRINT);
    }else {
        $json = json_encode([
            'status' => 'danger', 
            'text' =>  'Erro Desconhecido'
        ], JSON_PRETTY_PRINT);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $json;