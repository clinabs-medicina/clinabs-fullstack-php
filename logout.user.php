<?php
$hostname = "homolog.clinabs.com.br";

session_destroy();
unset($_COOKIE['sessid_clinabs_user_data']);
unset($_COOKIE['sessid_clinabs_uid']);
unset($_COOKIE['sessid_clinabs']);


setcookie("sessid_clinabs_uid", "", time() - 3600, '/', $hostname, true, false);
setcookie("sessid_clinabs_user_data", "", time() - 3600, '/', $hostname, true, false);
setcookie("sessid_clinabs", "", time() - 3600, '/', $hostname, true, false);

header('Location: /');
