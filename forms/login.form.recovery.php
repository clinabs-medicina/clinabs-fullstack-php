<?php
global $pdo, $mailer;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Regenera o ID da sessão para segurança (após o session_start)
session_regenerate_id(true);

require_once '../config.inc.php';
require_once '../libs/sendMail.php';

ini_set('display_errors', 1);
error_reporting(1);

	switch($_REQUEST['action'])
	{
		case 'resetPassword':
		{
			$sql = "SELECT
			objeto AS tipo,
			nome_completo,
			email,
			senha,
			celular,
			token
		FROM
			MEDICOS AS M
		WHERE
			M.email = :email
		UNION ALL
			(
			SELECT
				objeto AS tipo,
				nome_completo,
				email,
				senha,
				celular,
				token
			FROM
				USUARIOS AS U
			WHERE
				U.email = :email
		)
		UNION ALL
			(
			SELECT
				objeto AS tipo,
				nome_completo,
				email,
				senha,
				celular,
				token
			FROM
				PACIENTES AS P
			WHERE
				P.email = :email
		)
		UNION ALL
			(
			SELECT
				objeto AS tipo,
				nome_completo,
				email,
				senha,
				celular,
				token
			FROM
				FUNCIONARIOS AS F
			WHERE
				F.email = :email)";

			$stmt = $pdo->prepare($sql);

			$stmt->bindValue(':email', $_REQUEST['usuario']);

			$stmt->execute();

			$user = $stmt->fetch(PDO::FETCH_OBJ);   

			if($stmt->rowCount() > 0) {
					try{
						$tabela = $user->tipo.'S';
						$token = $user->token;

						try{
							$stmt2 = $pdo->prepare("UPDATE $tabela SET resetpwd = 1 WHERE token = :token");
							$stmt2->bindValue(':token', $token);
							$stmt2->execute();

							$json = json_encode([
								'type' => 'application/json',
								'status' => 'success',
								'text' => 'Verifique seu E-mail/WhatsApp com o Link de Recuperação da sua Senha.',
								'data' => $user
							], 64|128|196|256);
	
							$zwa = $wa->sendLinkMessage(
								phoneNumber: $user->celular, 
								text: 'clique no link para Redefinir a Senha de sua conta!', 
								linkUrl: 'https://'.$_SERVER['HTTP_HOST'].'/login?action=resetPassword&token='.$user->token, 
								linkTitle: 'CLINABS', 
								linkDescription: 'Redefinir Senha', 
								linkImage: 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/logo.png'
							);

							sendMail(
								mailer: $mailer,
								to: array('email' => $user->email, 'name' => $user->nome_completo), 
								subject: 'clique no link para Redefinir a Senha de sua conta!', 
								body: 'https://'.$_SERVER['HTTP_HOST'].'/login?action=resetPassword&token='.$user->token
							);
						}catch(Exception $ex) {
							$json = json_encode([
								'status' => 'danger',
								'type' => 'application/json',
								'text' => 'Erro ao Solitar Link de Redefinição.'
							], 64|128|196|256);
						}

				}catch(Exception $ex) {
					$json = json_encode([
						'status' => 'danger',
						'type' => 'application/json',
						'text' => 'Erro ao Solitar Link de Redefinição.'
					], 64|128|196|256);
				}
			}
			else {
				$json = json_encode([
					'status' => 'danger',
					'type' => 'application/json',
					'text' => 'E-mail não cadastrado.'
				], 64|128|196|256);
			}

			break;
		}
		case 'resetNewPassword': {
			
			if($_REQUEST['password'] == $_REQUEST['confirmPassword']) {
				$sql = "
                SELECT
					objeto AS tipo,
					nome_completo,
					email,
					senha,
					celular,
					token,
					resetpwd
				FROM
					USUARIOS AS U 
				WHERE
					U.token = :token 
                UNION ALL
                    SELECT
					objeto AS tipo,
					nome_completo,
					email,
					senha,
					celular,
					token,
					resetpwd
				FROM
					MEDICOS AS M 
				WHERE
					M.token = :token UNION ALL
					(
					SELECT
						objeto AS tipo,
						nome_completo,
						email,
						senha,
						celular,
						token,
						resetpwd
					FROM
						PACIENTES AS P 
					WHERE
						P.token = :token 
					) UNION ALL
					(
					SELECT
						objeto AS tipo,
						nome_completo,
						email,
						senha,
						celular,
						token,
						resetpwd
					FROM
						FUNCIONARIOS AS F 
					WHERE
						F.token = :token 
					)";


				$stmt = $pdo->prepare($sql);
				$stmt->bindValue(':token', $_REQUEST['token']);
				$stmt->execute();
				$user = $stmt->fetch(PDO::FETCH_OBJ);

				

				try
				{
					if($user->resetpwd == 1){
						$stmt2 = $pdo->prepare("UPDATE ".$user->tipo."S SET senha = :senha,resetpwd = 0 WHERE token = :token");
						$stmt2->bindValue(':senha', md5(sha1(md5($_REQUEST['password']))));
						$stmt2->bindValue(':token', $_REQUEST['token']);
						$stmt2->execute();

						if($stmt2->rowCount() > 0) {
							$json = json_encode([
								'type' => 'application/json',
								'status' => 'success',
								'text' => 'Senha Redefinida com Sucesso.'
							], 64|128|196|256);

							$wa->sendLinkMessage(
								phoneNumber: $user->celular, 
								text: 'Sua Senha foi Redefinida com Sucesso!', 
								linkUrl: 'https://'.$_SERVER['HTTP_HOST'].'/', 
								linkTitle: 'CLINABS', 
								linkDescription: 'Acessar Sistema', 
								linkImage: 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/logo.png'
							);

							$sessid = md5($user->token);
							$time = time() + (3600 * 24) * 365;
//							setcookie('sessid_clinabs', $sessid, $time, '/', $hostname, true, true, 'samesite' => 'None');
						} else {
							$json = json_encode([
								'status' => 'warning',
								'type' => 'application/json',
								'text' => 'Você Já Ulilizou esta Senha , por favor crie uma nova senha!.'
							], 64|128|196|256);
						}
					}else{
						$json = json_encode([
							'status' => 'warning',
							'type' => 'application/json',
							'text' => 'Você já solicitou a Recuperação anteriormente! Solicite um novo link.',
							'exception' => $ex
						], 64|128|196|256);
					}

				}
				catch(PDOException $ex) {
						$json = json_encode([
							'status' => 'warning',
							'type' => 'application/json',
							'text' => 'Falha ao Redefinir sua Senha!.',
							'exception' => $ex
						], 64|128|196|256);
				}
			} else {
				$json = json_encode([
					'status' => 'warning',
					'type' => 'application/json',
					'text' => 'Senhas Não Conferem!.'
				], 64|128|196|256);
			}

		break;
	} 

	case 'newPassword': {
			
		if($_REQUEST['password'] == $_REQUEST['confirmPassword']) {
			$sql = "
			SELECT
				objeto AS tipo,
				nome_completo,
				email,
				senha,
				celular,
				token,
				resetpwd
			FROM
				USUARIOS AS U 
			WHERE
				U.token = :token 
			UNION ALL
				SELECT
				objeto AS tipo,
				nome_completo,
				email,
				senha,
				celular,
				token,
				resetpwd
			FROM
				MEDICOS AS M 
			WHERE
				M.token = :token UNION ALL
				(
				SELECT
					objeto AS tipo,
					nome_completo,
					email,
					senha,
					celular,
					token,
					resetpwd
				FROM
					PACIENTES AS P 
				WHERE
					P.token = :token 
				) UNION ALL
				(
				SELECT
					objeto AS tipo,
					nome_completo,
					email,
					senha,
					celular,
					token,
					resetpwd
				FROM
					FUNCIONARIOS AS F 
				WHERE
					F.token = :token 
				)";


			$stmt = $pdo->prepare($sql);
			$stmt->bindValue(':token', $_REQUEST['token']);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_OBJ); 

			try
			{
				if($user->resetpwd == 1){
					$stmt2 = $pdo->prepare("UPDATE ".$user->tipo."S SET senha = :senha,resetpwd = 0 WHERE token = :token");
					$stmt2->bindValue(':senha', md5(sha1(md5($_REQUEST['password']))));
					$stmt2->bindValue(':token', $_REQUEST['token']);
					$stmt2->execute();

					if($stmt2->rowCount() > 0) {
						$json = json_encode([
							'type' => 'application/json',
							'status' => 'success',
							'text' => 'Senha definida com Sucesso.'
						], 64|128|196|256);

						$wa->sendLinkMessage(
							phoneNumber: $user->celular, 
							text: 'Sua Senha foi definida com Sucesso!', 
							linkUrl: 'https://'.$_SERVER['HTTP_HOST'].'/', 
							linkTitle: 'CLINABS', 
							linkDescription: 'Acessar Sistema', 
							linkImage: 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/logo.png'
						);

						$sessid = md5($user->token);
						$time = time() + (3600 * 24) * 365;
//						setcookie('sessid_clinabs', $sessid, $time, '/', $hostname, true, true, 'samesite' => 'None');
					}else {
						$json = json_encode([
							'status' => 'warning',
							'type' => 'application/json',
							'text' => 'Você Já Ulilizou esta Senha , por favor crie uma nova senha!.'
						], 64|128|196|256);
					}
				}else{
					$json = json_encode([
						'status' => 'warning',
						'type' => 'application/json',
						'text' => 'Você já solicitou a Recuperação anteriormente! Solicite um novo link.',
						'exception' => $ex
					], 64|128|196|256);
				}

			}
			catch(PDOException $ex) {
					$json = json_encode([
						'status' => 'warning',
						'type' => 'application/json',
						'text' => 'Falha ao Redefinir sua Senha!.',
						'exception' => $ex
					], 64|128|196|256);
			}
		} else {
			$json = json_encode([
				'status' => 'warning',
				'type' => 'application/json',
				'text' => 'Senhas Não Conferem!.'
			], 64|128|196|256);
		}

	break;
	} 
	
	default: {
			$json = json_encode([
				'status' => 'error',
				'icon' => 'error',
				'type' => 'application/json',
				'text' => 'E-mail não Encontrado',
				'request' => $_REQUEST
			], 64|128|196|256);

			break;
		}
	}

	header('content-Type: application/json');   
	echo $json;