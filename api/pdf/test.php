<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }
error_reporting(1);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT']. '/api/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$mpdf->setFooter("<small style=\"float: left\">Impresso em {DATE d/m/Y H:i} por ".$user->nome_completo." </small> - Página {nb} de {nbpg}");
$mpdf->WriteHTML('<h1>Hello World</1>');

$mpdf->Output();