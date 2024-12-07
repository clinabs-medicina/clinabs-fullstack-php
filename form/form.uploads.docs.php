<?php
date_default_timezone_set('America/Sao_Paulo');

$mime_type = [
    'application/pdf' => '.pdf',
    'image/png' => '.png',
    'image/jpg' => '.jpg',
    'image/jpeg' => '.jpeg'
];


$input = fopen('php://input', 'r');
// Define the file path where you want to save the file
$buffer = file_get_contents('php://input');
$finfo = new finfo(FILEINFO_MIME_TYPE);
$finfo = trim($finfo->buffer($buffer));

$filePath = '../data/images/docs/'.uniqid().$mime_type[$finfo];
    
// Create a writable file stream
$output = fopen($filePath, 'w');
    
// Copy the raw input stream to the file
stream_copy_to_stream($input, $output);
    
// Close the file streams
fclose($input);
fclose($output);

header('Content-Type: application/json');
if(file_exists($filePath) && filesize($filePath) > 1024) {
    $resp = ['status' => 'success', 'text' => 'Arquivo Enviado com Sucesso!', 'path' => basename($filePath), 'mime' => $finfo];
} else {
    $resp = ['status' => 'error', 'text' => 'Ocorreu um Erro ao Enviar o Arquivo!'];
}

echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>