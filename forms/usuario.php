<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set( 'display_errors', 0);
$token = uniqid();
$cpf_pwd = preg_replace('/[^0-9]/', '', $_REQUEST['cpf']);
$pwd = md5(sha1(md5('anna@2025!')));


$cpf = preg_replace('/[^0-9]/', '', $_REQUEST["cpf"]);
$nome_completo = strtoupper($_REQUEST["nome_completo"]);
$nome_preferencia = strtoupper($_REQUEST["nome_preferencia"]);
$identidade_genero = $_REQUEST["identidade_genero"];
$data_nascimento = $_REQUEST["data_nascimento"];
$email = strtolower($_REQUEST["email"]);
$telefone = preg_replace('/[^0-9]/', '', $_REQUEST["telefone"]);
$celular = preg_replace('/[^0-9]/', '', $_REQUEST["celular"]);
$marcas = $_REQUEST["marcas"];


try
{
    $stmt= $pdo->prepare("INSERT INTO `USUARIOS` (
        `nome_completo`,
        `nome_preferencia`,
        `identidade_genero`,
        `cpf`,
        `data_nascimento`,
        `telefone`,
        `celular`,
        `email`,
        `senha`,
        `marcas`,
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
            :marcas,
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
  			$stmt->bindValue(':marcas', json_encode($marcas));
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':perm', 1);
        
           $stmt->execute();
        
            $json = json_encode([
                'status' => 'success', 
                'text' => 'Usuário Cadastrado com Sucesso',
            ], JSON_PRETTY_PRINT);
        

            $wa->sendLinkMessage(
                phoneNumber: $celular, 
                text: 'clique no link para confirmar sua conta!', 
                linkUrl: 'https://'.$hostname.'/login?action=resetPassword&token='.$token, 
                linkTitle: 'CLINABS', 
                linkDescription: 'Confirmar Conta', 
                linkImage: 'https://'.$hostname.'/assets/images/clinabs.png'
            );

            $wa->requestProfileImage($celular);
       
    }catch(Exception $ex) {
        $json = json_encode([
            'status' => 'danger', 
            'text' => $ex->getMessage()
        ], JSON_PRETTY_PRINT);
    }

    header('content-type: application/json');
    echo $json;