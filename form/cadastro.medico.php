<?php
require_once '../config.inc.php';
$data = $_REQUEST;
$token = md5(preg_replace("/[^0-9]/", "", $data["cpf"]).uniqid());
$passwd = uniqid();

$errors = [
    '23000' => 'Já Cadastrado!'
];

$pwd = md5(sha1(md5($passwd)));
$data['senha'] = $passwd;

$stmt = $pdo->prepare("INSERT INTO MEDICOS (cpf, rg, nome_completo, nome_preferencia, identidade_genero, data_nascimento, tipo_conselho, num_conselho, uf_conselho, email, telefone, celular, token, senha) 
VALUES(:cpf, :rg, :nome_completo, :nome_preferencia, :identidade_genero, :data_nascimento, :tipo_conselho, :num_conselho, :uf_conselho, :email, :telefone, :celular, :token, :senha);");

$stmt->bindValue(":cpf", $data["cpf"]);
$stmt->bindValue(":rg", $data["rg"]);
$stmt->bindValue(":nome_completo", $data["nome_completo"]);
$stmt->bindValue(":nome_preferencia", $data["nome_preferencia"]);
$stmt->bindValue(":identidade_genero", $data["identidade_genero"]);
$stmt->bindValue(":data_nascimento", Modules::parseDate($data["data_nascimento"]));
$stmt->bindValue(":tipo_conselho", $data["tipo_conselho"]);
$stmt->bindValue(":num_conselho", $data["num_conselho"]);
$stmt->bindValue(":uf_conselho", $data["uf_conselho"]);
$stmt->bindValue(":email", $data["email"]);
$stmt->bindValue(":telefone", preg_replace("/[^0-9]/", "", $data["telefone"]));
$stmt->bindValue(":celular", preg_replace("/[^0-9]/", "", $data["celular"]));
$stmt->bindValue(":senha", $pwd);
$stmt->bindValue(":token", $token);

header('Content-Type: application/json');

try {
    $stmt->execute();

    copy('user.jpg', '../data/images/profiles/'.$token.'.jpg');

    if($stmt->rowCount() > 0) {
        $stmt_street = $pdo->prepare('INSERT INTO ENDERECOS (cep, logradouro, numero, complemento, cidade, bairro, uf, tipo_endereco, user_token, isDefault, token) 
        VALUES(:cep, :logradouro, :numero, :complemento, :cidade, :bairro, :uf, :tipo_endereco, :user_token, :isDefault, :token);');

      $stmt_street->bindValue(":cep", $data["cep"]);
      $stmt_street->bindValue(":logradouro", $data["endereco"]);
      $stmt_street->bindValue(":numero", $data["numero"]);
      $stmt_street->bindValue(":complemento", $data["complemento"]);
      $stmt_street->bindValue(":cidade", $data["cidade"]);
      $stmt_street->bindValue(":bairro", $data["bairro"]);
      $stmt_street->bindValue(":uf", $data["uf"]);
      $stmt_street->bindValue(":tipo_endereco", 'ATENDIMENTO');
      $stmt_street->bindValue(":user_token",  $token);
      $stmt_street->bindValue(":isDefault", 1);
      $stmt_street->bindValue(":token", uniqid());

    try {
        $stmt_street->execute();
    } catch(PDOException $e) {
      
    } finally {
            echo json_encode([
                'status' => 'success',
                'text' => 'Cadastro realizado com sucesso!',
            ]);

            $msg = " Olá *{$data["nome_completo"]}*,

            Sugestões para Agilizar a Validação de Sua Conta
            1º Acesse a Plataforma em https://'.$host.'/login
            2º Acesse o link Minha Conta ( abaixo de Seu nome no canto superior direito)
            3º no fim da página clique em *EDITAR*
            4º Preencha seus dados e na aba *Documentação* Envie seus documentos
            5º nossa equipe irá validar seu cadastro nas próximas horas
            ";
    
            $wa->sendLinkMessage(preg_replace("/[^0-9]/", "", $data["celular"]),$msg, "https://'.$host.'//login?action=resetPassword&token={$token}", 'Acesso a Sua Conta', 'Conta Clinabs', 'https://'.$host.'//assets/images/logo.png');
    
        }
    } else {
        echo json_encode([
            'status' => 'warning',
            'text' => 'Não foi Possível realizar Seu Cadastro, vefifique se já possui cadastro conosco!.',
        ]);
    }
} catch(PDOException $e) {
    if(strpos('duplicate', $e->getMessage())) {
        echo json_encode([
            'status' => 'error',
            'text' => 'Médico já Cadastrado!',
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'text' => $errors[$e->getCode()] ?? 'Ocorreu um erro ao realizar seu Cadastro',
        ]);
    }
}