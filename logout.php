<?php
require_once('config.inc.php');
session_start();

if(isset($_COOKIE['sessid_clinabs_uid']) && isset($_GET['session'])) {
    setcookie('sessid_clinabs_uid', $sessid, [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
} else {
    setcookie('sessid_clinabs', $sessid, [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);

    setcookie('sessid_clinabs_uid', $sessid, [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => true,
        'domain' => $hostname
    ]);
}

header('Location: /');