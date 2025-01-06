<?php
require_once '../config.inc.php';
require_once '../libs/sendMail.php';

$data = $_REQUEST;
$birthdate = Modules::parseDate($data["data_nascimento"]);

$birthDate = new DateTime($birthdate);
$currentDate = new DateTime();
$age = $currentDate->diff($birthDate);

if($age->y < 18) {
  $keys = ",responsavel_nome,responsavel_cpf,	responsavel_rg,responsavel_contato";
  $values = ", :responsavel_nome, :responsavel_cpf,	:responsavel_rg, :responsavel_contato";
}

$stmt = $pdo->prepare("INSERT INTO PACIENTES (cpf, nome_completo, rg, nacionalidade, nome_preferencia, identidade_genero, data_nascimento, email, telefone, celular,  doc_rg_frente, doc_rg_verso, doc_cpf_frente, doc_cpf_verso, doc_comp_residencia, doc_procuracao, doc_anvisa, doc_termos, fid, senha, token $keys) 
VALUES(:cpf, :nome_completo, :rg, :nacionalidade, :nome_preferencia, :identidade_genero, :data_nascimento, :email, :telefone, :celular, :doc_rg_frente, :doc_rg_verso, :doc_cpf_frente, :doc_cpf_verso, :doc_comp_residencia, :doc_procuracao, :doc_anvisa, :doc_termos, :fid, :senha, :token $values);");

$token = md5(preg_replace("/[^0-9]/", "", $data["cpf"]).uniqid());
$passwd = uniqid();

$pwd = md5(sha1(md5($passwd)));
$data['senha'] = $passwd;

$stmt->bindValue(":cpf",  preg_replace("/[^0-9]/", "", $data["cpf"]));
$stmt->bindValue(":nome_completo", strtoupper($data["nome_completo"]));
$stmt->bindValue(":rg", preg_replace("/[^0-9]/", "", $data["rg"]));
$stmt->bindValue(":nacionalidade", $data["nacionalidade"]);
$stmt->bindValue(":nome_preferencia", strtoupper($data["nome_preferencia"]));
$stmt->bindValue(":identidade_genero", $data["identidade_genero"]);
$stmt->bindValue(":data_nascimento", Modules::parseDate($data["data_nascimento"]));
$stmt->bindValue(":email", strtolower($data["email"]));
$stmt->bindValue(":telefone", preg_replace("/[^0-9]/", "", $data["telefone"]));
$stmt->bindValue(":celular", preg_replace("/[^0-9]/", "", $data["celular"]));
$stmt->bindValue(":doc_rg_frente", $data["doc_rg_frente"]);
$stmt->bindValue(":doc_rg_verso", $data["doc_rg_verso"]);
$stmt->bindValue(":doc_cpf_frente", $data["doc_cpf_frente"]);
$stmt->bindValue(":doc_cpf_verso", $data["doc_cpf_verso"]);
$stmt->bindValue(":doc_comp_residencia", $data["doc_comp_residencia"]);
$stmt->bindValue(":doc_procuracao", $data["doc_procuracao"]);
$stmt->bindValue(":doc_anvisa", $data["doc_anvisa"]);
$stmt->bindValue(":doc_termos", $data["doc_termos"]);
$stmt->bindValue(":fid", $data["fid"]);
$stmt->bindValue(":senha", $pwd);
$stmt->bindValue(":token", $token);

if($age->y < 18) {
  $stmt->bindValue(":responsavel_nome", $_REQUEST['responsavel_nome_completo']);
  $stmt->bindValue(":responsavel_cpf", $_REQUEST['responsavel_cpf']);
  $stmt->bindValue(":responsavel_rg", $_REQUEST['responsavel_rg']);
  $stmt->bindValue(":responsavel_contato", $_REQUEST['responsavel_celular']);
}

header('Content-Type: application/json');

try {
  $stmt->execute();
  if($stmt->rowCount() > 0) {
    $stmt_street = $pdo->prepare('INSERT INTO ENDERECOS (cep, logradouro, numero, complemento, cidade, bairro, uf, tipo_endereco, user_token, isDefault, token) VALUES(:cep, :logradouro, :numero, :complemento, :cidade, :bairro, :uf, :tipo_endereco, :user_token, :isDefault, :token);');

      $stmt_street->bindValue(":cep", $data["cep"]);
      $stmt_street->bindValue(":logradouro", $data["endereco"]);
      $stmt_street->bindValue(":numero", $data["numero"]);
      $stmt_street->bindValue(":complemento", $data["complemento"]);
      $stmt_street->bindValue(":cidade", $data["cidade"]);
      $stmt_street->bindValue(":bairro", $data["bairro"]);
      $stmt_street->bindValue(":uf", $data["uf"]);
      $stmt_street->bindValue(":tipo_endereco", 'CASA');
      $stmt_street->bindValue(":user_token",  $token);
      $stmt_street->bindValue(":isDefault", 1);
      $stmt_street->bindValue(":token", uniqid());

    try {
        $stmt_street->execute();

        echo json_encode([
          'status' => 'success',
          'text' => 'Cadastro realizado com sucesso!',
          'linkUrl' => (isset($_REQUEST['fid']) && $_REQUEST['fid'] != '') ? '/':'https://'.$host.'/login?action=newPassword&token='.$token
      ]);

      $msg = " Olá *{$data["nome_completo"]}*,
      
      Sugestões para Agilizar a Validação de Sua Conta
      1º Acesse a Plataforma em https://'.$host.'//login
      2º Acesse o link Minha Conta ( abaixo de Seu nome no canto superior direito)
      3º no fim da página clique em *EDITAR*
      4º Preencha seus dados e na aba *Documentação* Envie seus documentos
      5º nossa equipe irá validar seu cadastro nas próximas horas
      ";

      //$wa->sendLinkMessage(preg_replace("/[^0-9]/", "", $data["celular"]), getStringBetween($notificacoesMsg['cadastro'], $data), 'https://'.$host.'//', 'Acesso a Sua Conta', 'Conta Clinabs', 'https://'.$host.'//assets/images/logo.png');
      $wa->sendLinkMessage(preg_replace("/[^0-9]/", "", $data["celular"]),$msg, "https://'.$host.'//login?action=newPassword&token={$token}", 'Acesso a Sua Conta', 'Conta Clinabs', 'https://'.$host.'//assets/images/logo.png');
      sendMail(
        mailer: $mailer,
        to: array('email' => $data['email'], 'name' => $data['nome_completo']), 
        subject: 'Acesso a Sua Conta',
        body: $msg.PHP_EOL.'<p><a href="https://'.$host.'//login?action=resetPassword&token='.$user->token.">Criar uma Senha</a></b>"
      );

      $sessid = md5($token);
			$time = time() + (3600 * 24) * 365;
			setcookie('sessid_clinabs', $sessid, $time, '/', hostname, true);
    } catch(PDOException $e) {
    }
  } else {
    echo json_encode([
      'status' => 'error',
      'text' => 'Ocorreu um erro ao realizar seu Cadastro',
  ]);
  }
} catch(Exception $e) {
  echo json_encode([
      'status' => 'error',
      'text' => 'Ocorreu um erro ao realizar seu Cadastro',
  ]);
}