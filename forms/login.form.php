<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo;
ini_set('display_errors', true);
error_reporting(E_ALL);


require_once '../config.inc.php';

$pwd = md5(sha1(md5($_REQUEST['password'])));

$usr = $_REQUEST['usuario'];
$sql = "
        SELECT
    nome_completo,
    nome_preferencia,
    data_nascimento,
    0 as disponibilizar_agenda,
    cpf,
    celular,
    email,
    token,
    objeto,
    perm,
    '[]' AS marcas,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag,
    '' AS anamnese
FROM
    `FUNCIONARIOS`
WHERE
    email = '{$usr}'
	AND senha = '{$pwd}'
UNION
SELECT
    nome_completo,
    nome_preferencia,
    data_nascimento,
    0 as disponibilizar_agenda,
    cpf,
    celular,
    email,
    token,
    objeto,
    perm,
    '[]' AS marcas,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag,
    anamnese
FROM
    `PACIENTES`
WHERE
    email = '{$usr}'
	AND senha = '{$pwd}'
UNION
SELECT
    nome_completo,
    nome_preferencia,
    data_nascimento,
    disponibilizar_agenda,
    cpf,
    celular,
    email,
    token,
    objeto,
    perm,
    '[]' AS marcas,
	prescricao_sem_receita,
	inicio_ag,
	fim_ag,
    anamnese
FROM
    `MEDICOS`
WHERE
    email = '{$usr}'
	AND senha = '{$pwd}'
UNION
SELECT
    nome_completo,
    nome_preferencia,
    data_nascimento,
    0 as disponibilizar_agenda,
    cpf,
    celular,
    email,
    token,
    objeto,
    perm,
    marcas,
	'' AS prescricao_sem_receita,
	'' AS inicio_ag,
	'' AS fim_ag,
    '' AS anamnese
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
/*
   setcookie('sessid_clinabs', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
*/   
    if (isset($user)) {
        $_SESSION['token'] = $user['token'];
        $_SESSION['token'] = $sessid;
        $_SESSION['usuario'] = $user['nome_completo'];
        $_SESSION['apelido'] = $user['nome_preferencia'];
        $_SESSION['cpf'] = $user['cpf'];
        $_SESSION['celular'] = $user['celular'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nascimento'] = $user['data_nascimento'];
        $_SESSION['objeto'] = $user['objeto'];
        $_SESSION['tipo'] = $user['objeto'];
        $_SESSION['perms_id'] = $user['perm'];
        $_SESSION['marcas'] = $user['marcas'];
        $_SESSION['prescricao_sem_receita'] = $user['prescricao_sem_receita'];
        $_SESSION['inicio_ag'] = $user['inicio_ag'];
        $_SESSION['fim_ag'] = $user['fim_ag'];
        $_SESSION['disponibilizar_agenda'] = $user['disponibilizar_agenda'];
        $_SESSION['anamnese'] = $user['anamnese'];
        if (isset($user) && is_array($user)) {        
          $_SESSION['userObj'] = $user;
        }
    } else {
        try {
            error_log("Token não carregado login.form \r\n" . PHP_EOL);
            } catch (PDOException $e) {
            }
    }

} else {
    $json = json_encode([
        'status' => 'danger',
        'type' => 'application/json',
        'text' => 'Dados inválidos'
    ], 64 | 128 | 196 | 256);
}

header('Content-Type: application/json; charset=utf-8');
echo $json;