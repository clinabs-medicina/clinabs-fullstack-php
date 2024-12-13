<?php
require_once('config.inc.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//if(isset($_GET['session'])) {
if (session_status() === PHP_SESSION_NONE) {
    try{    
        error_log("logout \: session_destroy \r\n" . PHP_EOL);
    } catch (PDOException $e) {
    }
    session_destroy();
//    unset($_COOKIE['sessid_clinabs_user_data']);
//    unset($_SESSION['token']);
//    unset($_COOKIE['sessid_clinabs']);

    // setcookie('sessid_clinabs_uid', '', -1, $hostname, true, true);
    // setcookie('sessid_clinabs_user_data', '', -1, $hostname, true, true);
    // setcookie('sessid_clinabs', '', -1, $hostname, true, true);
    header('Location: /');
}else {
    if(isset($_GET['sessid'])) {
        $stmt = $pdo->prepare('DELETE FROM `SESSIONS` WHERE session_id = :id');
		$stmt->bindValue(':id', $_GET['sessid']);

		try {
			$stmt->execute();
		} catch(PDOException $ex) {
			
		}
    } else {
        try{    
            error_log("logout destroy\r\n" . PHP_EOL);
        } catch (PDOException $e) {
        }

        if (session_status() === PHP_SESSION_ACTIVE) {     
            session_destroy();
        }
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', -1, '/', $hostname, true, true);
            setcookie($name, '', -1, '/', $hostname, true, true);
        }

        header('Location: /');
    }
}