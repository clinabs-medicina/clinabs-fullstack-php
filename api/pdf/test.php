<?php
error_reporting(1);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT']. '/api/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$mpdf->setFooter("<small style=\"float: left\">Impresso em {DATE d/m/Y H:i} por ".$user->nome_completo." </small> - Página {nb} de {nbpg}");
$mpdf->WriteHTML('<h1>Hello World</1>');

$mpdf->Output();