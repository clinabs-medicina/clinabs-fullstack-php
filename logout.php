<?php
require_once('config.inc.php');

if(isset($_GET['session'])) {
    session_destroy();
    unset($_COOKIE['sessid_clinabs_user_data']);
    unset($_COOKIE['sessid_clinabs_uid']);
    unset($_COOKIE['sessid_clinabs']);

    setcookie('sessid_clinabs_uid', '', -1, $hostname, true, true);
    setcookie('sessid_clinabs_user_data', '', -1, $hostname, true, true);
    setcookie('sessid_clinabs', '', -1, $hostname, true, true);
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
        session_destroy();
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