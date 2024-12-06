<?php
if(isset($_COOKIE['sessid_clinabs_uid'])) {
    setcookie('sessid_clinabs_uid', '', time() - 3600, '/'); 
} else {
    setcookie('sessid_clinabs_uid', '', time() - 3600, '/');
    setcookie('sessid_clinabs', '', time() - 3600, '/');

    header('Location: /login');
}