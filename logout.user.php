<?php
$hostname = "homolog.clinabs.com.br";

session_destroy();
unset($_SESSION['sessid_clinabs_user_data']);
unset($_SESSION['sessid_clinabs_uid']);
unset($_SESSION['sessid_clinabs']);


setcookie("sessid_clinabs_uid", "", time() - 3600, '/', $hostname, true, false);
setcookie("sessid_clinabs_user_data", "", time() - 3600, '/', $hostname, true, false);
setcookie("sessid_clinabs", "", time() - 3600, '/', $hostname, true, false);

header('Location: /');
