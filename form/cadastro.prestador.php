<?php
require_once '../config.inc.php';
$data = $_REQUEST;

$stmt = $pdo->prepare("INSERT INTO USUARIOS (cpf, nome_completo, nacionalidade, nome_preferencia, identidade_genero, data_nascimento, email, telefone, celular, marcas, senha, token) 
VALUES(:cpf, :nome_completo, :nacionalidade, :nome_preferencia, :identidade_genero, :data_nascimento, :email, :telefone, :celular, :marcas, :senha, :token);");

$token = md5(preg_replace("/[^0-9]/", "", $data["cpf"]).uniqid());
$passwd = uniqid();

$pwd = md5(sha1(md5($passwd)));
$data['senha'] = $passwd;


$stmt->bindValue(":cpf",  preg_replace("/[^0-9]/", "", $data["cpf"]));
$stmt->bindValue(":nome_completo", strtoupper($data["nome_completo"]));
$stmt->bindValue(":nacionalidade", $data["nacionalidade"]);
$stmt->bindValue(":nome_preferencia", strtoupper($data["nome_preferencia"]));
$stmt->bindValue(":identidade_genero", $data["identidade_genero"]);
$stmt->bindValue(":data_nascimento", Modules::parseDate($data["data_nascimento"]));
$stmt->bindValue(":email", strtolower($data["email"]));
$stmt->bindValue(":telefone", preg_replace("/[^0-9]/", "", $data["telefone"]));
$stmt->bindValue(":celular", preg_replace("/[^0-9]/", "", $data["celular"]));
$stmt->bindValue(":marcas", $data["marcas"]);
$stmt->bindValue(":senha", $pwd);
$stmt->bindValue(":token", $token);

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'text' => 'Cadastro realizado com sucesso!',
        ]);

        //$wa->sendLinkMessage(preg_replace("/[^0-9]/", "", $data["celular"]), getStringBetween($notificacoesMsg['cadastro'], $data), 'https://www.clinabs.com/', 'Acesso a Sua Conta', 'Conta Clinabs', 'https://clinabs.com/assets/images/logo.png');
    } else {
        echo json_encode([
            'status' => 'warning',
            'text' => 'Não foi Possível realizar Seu Cadastro, vefifique se já possui cadastro conosco!.',
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'text' => 'Ocorreu um erro ao realizar seu Cadastro\n\n'.$e->getMessage(),
    ]);
    
  }