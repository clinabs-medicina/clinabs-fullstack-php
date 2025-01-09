<?php
require_once '../config.inc.php';
require_once '../libs/sendMail.php';

$data = $_REQUEST;

$passwd = uniqid();

$pwd = md5(sha1(md5($passwd)));
$data['senha'] = $passwd;

$stmt = $pdo->prepare("INSERT INTO PACIENTES (cpf, nome_completo, nacionalidade, nome_preferencia, identidade_genero, data_nascimento, email, telefone, celular, fid, senha, token, responsavel_nome, responsavel_cpf, responsavel_rg, responsavel_contato) 
VALUES(:cpf, :nome_completo, :nacionalidade, :nome_preferencia, :identidade_genero, :data_nascimento, :email, :telefone, :celular, :fid, :senha, :token, :responsavel_nome, :responsavel_cpf, :responsavel_rg, :responsavel_contato);");

$token = md5(preg_replace("/[^0-9]/", "", $data["cpf"]).uniqid());

$stmt->bindValue(":cpf",  preg_replace("/[^0-9]/", "", $data["cpf"]));
$stmt->bindValue(":nome_completo", strtoupper($data["nome_completo"]));
$stmt->bindValue(":nacionalidade", $data["nacionalidade"]);
$stmt->bindValue(":nome_preferencia", strtoupper($data["nome_preferencia"]));
$stmt->bindValue(":identidade_genero", $data["identidade_genero"]);
$stmt->bindValue(":data_nascimento", Modules::parseDate($data["data_nascimento"]));
$stmt->bindValue(":email", strtolower($data["email"]));
$stmt->bindValue(":telefone", preg_replace("/[^0-9]/", "", $data["telefone"]));
$stmt->bindValue(":celular", preg_replace("/[^0-9]/", "", $data["celular"]));
$stmt->bindValue(":fid", $data["fid"]);
$stmt->bindValue(":senha", $pwd);
$stmt->bindValue(":token", $token);
$stmt->bindValue(":responsavel_nome", $_REQUEST['responsavel_nome_completo']);
$stmt->bindValue(":responsavel_cpf", $_REQUEST['responsavel_cpf']);
$stmt->bindValue(":responsavel_rg", $_REQUEST['responsavel_rg']);
$stmt->bindValue(":responsavel_contato", $_REQUEST['responsavel_celular']);

header('Content-Type: application/json');
$IP = getallheaders()['X-Forwarded-For'] ?? $_SERVER['REMOTE_ADDR'];

try {
   if(strpos($data["nome_completo"], ' ') !== false && strlen(preg_replace("/[^0-9]/", "", $data["cpf"])) == 11) {
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            echo json_encode([
                'status' => 'success',
                'text' => 'Cadastro realizado com sucesso!',
                'linkUrl' =>  (isset($_REQUEST['fid']) && $_REQUEST['fid'] != '') ? '/':'https://'.$host.'//login?action=newPassword&token='.$token
            ]);
    
            $msg = " Olá *{$data["nome_completo"]}*,
    
            Sugestões para Agilizar a Validação de Sua Conta
            1º Acesse a Plataforma em https://$host/login
            2º Acesse o link Minha Conta ( abaixo de Seu nome no canto superior direito)
            3º no fim da página clique em *EDITAR*
            4º Preencha seus dados e na aba *Documentação* Envie seus documentos
            5º nossa equipe irá validar seu cadastro nas próximas horas
            ";
    
            //$wa->sendLinkMessage(preg_replace("/[^0-9]/", "", $data["celular"]), getStringBetween($notificacoesMsg['cadastro'], $data), 'https://www.clinabs.com/', 'Acesso a Sua Conta', 'Conta Clinabs', 'https://clinabs.com/assets/images/logo.png');
            $wa->sendLinkMessage(preg_replace("/[^0-9]/", "", $data["celular"]),$msg, "https://$host/login?action=resetPassword&token={$token}", 'Acesso a Sua Conta', 'Conta Clinabs', 'https://'.$host.'$host/assets/images/logo.png');
        
            sendMail(
                    mailer: $mailer,
                    to: array('email' => $data['email'], 'name' => $data['nome_completo']), 
                    subject: 'Acesso a Sua Conta',
                    body: $msg.PHP_EOL.'<p><a href="https://'.$host.'/login?action=resetPassword&token='.$user->token.">Criar uma Senha</a></b>"
                );
    
            $sessid = md5($token);
            $time = time() + (3600 * 24) * 365;
            //setcookie('sessid_clinabs', $sessid, $time, '/', hostname, true);
        } 
        else {
            echo json_encode([
                'status' => 'warning',
                'text' => 'Não foi Possível realizar Seu Cadastro, vefifique se já possui cadastro conosco!.',
            ]);
        }
   } else {
    echo json_encode([
        'status' => 'error',
        'text' => 'Por favor verifique se todos os campos foram preenchidos corretamente'
    ]);
   }
   
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'text' => 'Ocorreu um erro ao realizar seu Cadastro '.$e->getMessage(),
        'exception' => trim(explode(']:', $e->getMessage())[1]).' => '.Modules::parseDate($data["data_nascimento"])
    ]);
    
    if(!isset($data['xsrf_token'])) {
            try {
                foreach(explode(',', $IP) as $ip) {
                    $ip = trim($ip);
                    $pdo->query("INSERT IGNORE INTO `IPS_BLOQUEADOS` (`ip`) VALUES ('{$ip}')");
                }
            } catch(Exception $e) {}
    }
  }