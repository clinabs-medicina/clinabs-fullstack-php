<?php
$hostname = "homolog.clinabs.com.br";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['userObj'])) {
    $user = (object) $_SESSION['userObj'];
    try{    
        error_log("logout user destroy\r\n" . PHP_EOL);
    } catch (PDOException $e) {
    }
}
if (session_status() === PHP_SESSION_NONE) {
    session_destroy();
}
//unset($_COOKIE['sessid_clinabs_user_data']);
//unset($_SESSION['token']);
//unset($_COOKIE['sessid_clinabs']);


setcookie("sessid_clinabs_uid", "", time() - 3600, '/', $hostname, true, false);
setcookie("sessid_clinabs_user_data", "", time() - 3600, '/', $hostname, true, false);
setcookie("sessid_clinabs", "", time() - 3600, '/', $hostname, true, false);

header('Location: /');
