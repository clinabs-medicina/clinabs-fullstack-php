<?php
error_reporting(1);
ini_set('display_errors', 1);

use Aws\S3\S3Client;

$file = '/home/clinabs/web/dev.clinabs.com.br/public_html/api/s3/RECEITA_2003b3ec32c6c38e70e7176add6e717c6765baa71101a.pdf';
$fileName = basename(strtoupper($file));
$date = date('Y-m-d');
$bucket = 'clinabs';
$targetDir = 'receitas/' . $date;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

$s3Client = new S3Client([
    'version' => 'latest',
    'region' => 'us-west-2',
    'endpoint' => 'http://103.195.100.128:9000',
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key' => 'admin',
        'secret' => 'G6rT5eKpL8dN2aR4eK',
    ],
]);

// Cria uma Pasta com a data
$result = $s3Client->putObject([
    'Bucket' => $bucket,
    'Key' => $targetDir . '/',
    'Content-Type' => 'application/x-directory',
]);

// Enviar para o S3

$result = $s3Client->putObject([
    'Bucket' => $bucket,
    'Key' => "{$targetDir}/{$fileName}",
    'Body' => fopen($file, 'r'),
]);

echo '<pre>';
print_r($result ?? []);
echo '</pre>';
