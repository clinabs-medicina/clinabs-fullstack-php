<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 1);

$token = md5(preg_replace("/[^0-9]/", "", $_REQUEST["cpf"]).uniqid());
$cpf_pwd = preg_replace('/[^0-9]/', '', $_REQUEST['cpf']);
$pwd = md5(sha1(md5('Anna@2025!')));

$cpf = preg_replace('/[^0-9]/', '', $_REQUEST["cpf"]);
$nome_completo = trim(strtoupper($_REQUEST["nome_completo"]));
$nome_preferencia = strtoupper($_REQUEST["nome_preferencia"]);
$identidade_genero = $_REQUEST["identidade_genero"];
$data_nascimento = $_REQUEST["data_nascimento"];
$email = strtolower($_REQUEST["email"]);
$telefone = preg_replace('/[^0-9]/', '', $_REQUEST["telefone"]);
$celular = preg_replace('/[^0-9]/', '', $_REQUEST["celular"]);


try
{
    if($mysql->query("INSERT INTO `PACIENTES` (
        `nome_completo`,
        `nome_preferencia`,
        `identidade_genero`,
        `cpf`,
        `data_nascimento`,
        `telefone`,
        `celular`,
        `email`,
        `senha`,
        `token`,
        `perm`
    )
    VALUES
        (
            '$nome_completo',
            '$nome_preferencia',
            '$identidade_genero',
            '$cpf',
            '$data_nascimento',
            '$telefone',
            '$celular',
            '$email',
            '$pwd',
            '$token',
            1
        )"))
        {
            $json = json_encode([
                'status' => 'success', 
                'text' => 'Paciente Cadastrado com Sucesso',
                'redirect' => $_REQUEST['redirect'] ?? 'none'
            ], JSON_PRETTY_PRINT);
        

            $wa->sendLinkMessage(
                phoneNumber: $celular, 
                text: 'clique no link para confirmar sua conta!', 
                linkUrl: 'https://'.$_SERVER['HTTP_HOST'].'/login?action=resetPassword&token='.$token, 
                linkTitle: 'CLINABS', 
                linkDescription: 'Confirmar Conta', 
                linkImage: 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/clinabs.png'
            );

            $wa->requestProfileImage($celular);
       }else {
            $json = json_encode([
                'status' => 'danger', 
                'text' => 'Erro ao Cadastrar'
            ], JSON_PRETTY_PRINT);
       }
    }catch(Exception $ex) {
        $json = json_encode([
            'status' => 'danger', 
            'text' => $mysql_errors[$ex->getCode()]
        ], JSON_PRETTY_PRINT);
    }

    header('content-type: application/json');
    echo $json;