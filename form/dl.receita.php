<?php

if(isset($_GET['file'])) {
    $file = "../data/receitas/assinadas/{$_GET['file']}";

    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary"); 
    header("Content-disposition: attachment; filename=\"" . basename($_GET['file']) . "\""); 
    readfile($file); 
}