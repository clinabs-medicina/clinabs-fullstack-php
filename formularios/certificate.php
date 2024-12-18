<?php
require_once('../config.inc.php');


    if(is_dir($_SERVER['DOCUMENT_ROOT']."/data/certificates")) {
        mkdir($_SERVER['DOCUMENT_ROOT']."/data/certificates");
    }
    
    $token = uniqid();
    
    $path = $_SERVER['DOCUMENT_ROOT']."/data/certificates/{$token}.pfx";
    $password = $_POST['cert_pwd'];
    
    file_put_contents($path, base64_decode($_POST['cert_data']));


    $pemCertPath = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.uniqid().'.pem';
    $pemKeyPath = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.uniqid().'.pem';

    shell_exec("openssl pkcs12 -in $path -out $pemCertPath -nodes -passin pass:$password");

    $pemContent = file_get_contents($pemCertPath);
    
    if ($pemContent === false) {
        echo "Falha ao ler Certificado PKCS12";
        exit;
    }
    // Parse the certificate
    $certInfo = openssl_x509_parse($pemContent);
    file_put_contents('certificate.json', json_encode($certInfo, JSON_PRETTY_PRINT));
    
    
    
    if ($certInfo === false) {
        echo "Failed to parse the PEM file.";
        exit;
    }

    if ($json === false) {
        echo "Failed to convert to JSON.";
        exit;
    }
