<?php
global $pdo;
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
// Regenera o ID da sessão para segurança (após o session_start)
session_regenerate_id(true);

require_once '../config.inc.php';

$pwd = md5(sha1(md5($_REQUEST['password'])));

$usr = $_REQUEST['usuario'];
$sql = "
        SELECT
    nome_completo,
    cpf,
    celular,
    email,
    token,
    objeto
FROM
    `FUNCIONARIOS`
WHERE
    email = '{$usr}'
	AND senha = '{$pwd}'
UNION
SELECT
    nome_completo,
    cpf,
    celular,
    email,
    token,
    objeto
FROM
    `PACIENTES`
WHERE
    email = '{$usr}'
	AND senha = '{$pwd}'
UNION
SELECT
    nome_completo,
    cpf,
    celular,
    email,
    token,
    objeto
FROM
    `MEDICOS`
WHERE
    email = '{$usr}'
	AND senha = '{$pwd}'
UNION
SELECT
    nome_completo,
    cpf,
    celular,
    email,
    token,
    objeto
FROM
    `USUARIOS`
WHERE
    email = '{$usr}' 
	AND senha = '{$pwd}'";

$stmt = $pdo->query($sql);

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    
   

    $json = json_encode([
        'type' => 'application/json',
        'status' => 'success',
        'text' => 'Logado com Sucesso!',
        'data' => $user,
        'redirect' => $_REQUEST['redirect']
    ], 64 | 128 | 196 | 256);

    $sessid = md5($user['token']);
    $time = time() + (3600 * 24) * 365;

    $stmt0 = $pdo->prepare('INSERT INTO `SESSIONS` (
			`user_token`, 
			`session_id`, 
			`ip`, 
			`remember`, 
			`last_ping`,
			`startTime`) 
			VALUES(
				:user_token,
				:session_id,
				:ip,
				:remember,
				:last_ping,
				:ts
			) ON DUPLICATE KEY
			UPDATE `ip` = :ip,`last_ping` = :last_ping, startTime = :last_ping');

    $stmt0->bindValue(':user_token', $_REQUEST['usuario']);
    $stmt0->bindValue(':session_id', $sessid);
    $stmt0->bindValue(':ts', date('Y-m-d H:i:s'));
    $stmt0->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
    $stmt0->bindValue(':remember', 'on');
    $stmt0->bindValue(':last_ping', date('Y-m-d H:i:s'));

    try {
        $stmt0->execute();
    } catch (PDOException $ex) {
    }

    $datetime = date('Y-m-d H:i:s');

    $pdo->query("UPDATE {$user['objeto']}S SET session_online = 1,first_ping = '{$datetime}', last_ping = '{$datetime}' WHERE token = '{$user['token']}'");
   // setcookie('sessid_clinabs', $sessid, $time, '/', $hostname, true, false);

   setcookie('sessid_clinabs', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);

} else {
    $json = json_encode([
        'status' => 'danger',
        'type' => 'application/json',
        'text' => 'Dados inválidos'
    ], 64 | 128 | 196 | 256);
}

header('content-Type: application/json');
echo $json;