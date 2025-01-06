<?php
require_once '../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();

$mpdf->WriteHTML(file_get_contents("../teste.html"));

$mpdf->Output();
