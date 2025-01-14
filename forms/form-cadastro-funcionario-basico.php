<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 0);
$token = md5(preg_replace("/[^0-9]/", "", $_REQUEST["cpf"]).uniqid());
$cpf_pwd = preg_replace('/[^0-9]/', '', $_REQUEST['cpf']);
$pwd = md5(sha1(md5('test@2025!')));


$cpf = preg_replace('/[^0-9]/', '', $_REQUEST["cpf"]);
$nome_completo = strtoupper($_REQUEST["nome_completo"]);
$nome_preferencia = strtoupper($_REQUEST["nome_preferencia"]);
$identidade_genero = $_REQUEST["identidade_genero"];
$data_nascimento = $_REQUEST["data_nascimento"];
$email = strtolower($_REQUEST["email"]);
$telefone = preg_replace('/[^0-9]/', '', $_REQUEST["telefone"]);
$celular = preg_replace('/[^0-9]/', '', $_REQUEST["celular"]);



try
{
    $stmt= $pdo->prepare("INSERT INTO `FUNCIONARIOS` (
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
            :nome_completo,
            :nome_preferencia,
            :identidade_genero,
            :cpf,
            :data_nascimento,
            :telefone,
            :celular,
            :email,
            :pwd,
            :token,
            :perm
        )");
        
            $stmt->bindValue(':nome_completo', $nome_completo);
            $stmt->bindValue(':nome_preferencia', $nome_preferencia);
            $stmt->bindValue(':identidade_genero', $identidade_genero);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':data_nascimento', $data_nascimento);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':celular', $celular);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':pwd', $pwd);
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':perm', 1);
        
           $stmt->execute();
        
            $json = json_encode([
                'status' => 'success', 
                'text' => 'Funcionario Cadastrado com Sucesso',
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
       
    }catch(Exception $ex) {
        $json = json_encode([
            'status' => 'danger', 
            'text' => $mysql_errors[$ex->getCode()]
        ], JSON_PRETTY_PRINT);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $json;