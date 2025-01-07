<?php
require_once('config.inc.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//session_regenerate_id(true);


if(isset($_GET['session'])) {
    session_destroy();
/*    
    setcookie('sessid_clinabs_user_data', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
    setcookie('sessid_clinabs_uid', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
*/
    header('Location: /');
}else {
/*    setcookie('sessid_clinabs_user_data', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
    setcookie('sessid_clinabs_uid', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);

    setcookie('sessid_clinabs', $sessid, [
        'expires' => time() + 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
*/
    header('Location: /');
}