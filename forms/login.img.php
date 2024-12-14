<?php
ini_set('display_errors', true);
error_reporting(E_ALL);
require_once '../config.inc.php';
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if(isset($_SESSION['userObj'])) {
	$user = (object) $_SESSION['userObj'];
}


if(isset($_REQUEST['usuario']))
{
    $sql = "SELECT
	id,
	objeto AS tipo,
	token,
	nome_completo
FROM
	MEDICOS AS M 
	WHERE 
	M.email = :email
	UNION ALL
	(
	SELECT
		id,
		objeto AS tipo,
		token,
        nome_completo
	FROM
		PACIENTES AS P
		WHERE 
		P.email = :email
	) UNION ALL
	(
	SELECT
		id,
		objeto AS tipo,
		token,
		nome_completo
	FROM
	FUNCIONARIOS AS F
	WHERE 
	F.email = :email
	)";


    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':email', $_REQUEST['usuario']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if($stmt->rowCount() > 0)
    {
		header('Content-Type: image/jpeg');
		readfile($_SERVER['DOCUMENT_ROOT'].'/data/images/profiles/'.$user->token.'.jpg');
    }else{
        header('Content-Type: image/jpeg');
   		readfile($_SERVER['DOCUMENT_ROOT'].'/assets/images/user1.jpg');
    }
}else{
    header('Content-Type: image/jpeg');
    readfile($_SERVER['DOCUMENT_ROOT'].'/assets/images/user1.jpg');
}