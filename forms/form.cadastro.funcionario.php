<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 0);

$token = md5(preg_replace("/[^0-9]/", "", $_REQUEST["cpf"]).uniqid());

$pwd = md5(sha1(md5(uniqid())));

$funcionario = new stdClass();

$funcionario->nome_completo = trim(strtoupper($_REQUEST['nome_completo'])); // JOÃO SILVA
$funcionario->nacionalidade = $_REQUEST['nacionalidade']; // brasileiro
$funcionario->nome_preferencia = $_REQUEST['nome_preferencia']; // joao
$funcionario->identidade_genero = $_REQUEST['identidade_genero']; // Masculino
$funcionario->data_nascimento = $_REQUEST['data_nascimento']; // 1995-07-01
$funcionario->email = $_REQUEST['email']; // joao@clinabs.com
$funcionario->telefone = $_REQUEST['telefone']; // (41) 9999-9999
$funcionario->celular = $_REQUEST['celular']; // (41) 9 9999-9999
$funcionario->senha = $pwd;

$funcionario->enderecos = json_encode([
    [
        "endereco_nome" => "Padrão",
        "cep" => $_REQUEST['cep'],
        "endereco" => $_REQUEST['endereco'],
        "numero" => $_REQUEST['numero'],
        "complemento" => $_REQUEST['complemento'],
        "cidade" => $_REQUEST['cidade'],
        "bairro" => $_REQUEST['bairro'],
        "uf" => $_REQUEST['uf'],
        "isDefault" => true
    ]
]);

$funcionario->cpf = $_REQUEST['cpf']; // 082.178.615-62
$funcionario->rg = $_REQUEST['rg']; // 22326256
$funcionario->doc_rg_frente = $_REQUEST['doc_rg_frente']; // DOC_65afc249c3ab2.pdf
$funcionario->doc_rg_verso = $_REQUEST['doc_rg_verso']; // DOC_65afc24dcab04.pdf
$funcionario->doc_cpf_frente = $_REQUEST['doc_cpf_frente']; // DOC_65afc25150e6c.pdf
$funcionario->doc_cpf_verso = $_REQUEST['doc_cpf_verso']; // DOC_65afc254eb853.pdf
$funcionario->doc_comp_residencia = $_REQUEST['doc_comp_residencia']; // DOC_65afc258a9c9c.pdf
$funcionario->senha = $pwd;
$funcionario->token = $token;
$funcionario->receber_emails = $_REQUEST['doc_comp_residencia'];
$funcionario->termos = $_REQUEST['doc_comp_residencia'];

try {
    $funcionarios->Add($funcionario);

    $json = json_encode([
        'status' => 'success', 
        'text' => 'Cadastro Realizado Com Sucesso!'
    ], JSON_PRETTY_PRINT);
    
    $wa->sendLinkMessage(
        phoneNumber: $funcionario->celular, 
        text: 'clique no link para confirmar sua conta!', 
        linkUrl: 'https://'.$hostname.'login?resetPassword&token='.$token, 
        linkTitle: 'CLINABS', 
        linkDescription: 'Confirmar Conta', 
        linkImage: 'https://'.$hostname.'/assets/images/clinabs.png'
    );

    sendMail($mailer,array('email' => $funcionario->email, 'name' => $funcionario->nome_completo),'Conta CLINABS', 'clique no link para confirmar sua conta!<br>https://'.$hostname.'/login?resetPassword&token='.$token);

}catch(Exception $ex) {
    $json = json_encode([
        'status' => 'warning', 
        'text' => 'Erro Desconhecido'
    ], JSON_PRETTY_PRINT);
}


header('Content-Type: application/json; charset=utf-8');
echo $json;