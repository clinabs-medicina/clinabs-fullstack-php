<?php
require_once('../config.inc.php');
function getMimeTypeFromBase64String($base64String) {
    // Use a regular expression to extract the MIME type from the data URI string
    preg_match('/^data:([a-zA-Z0-9\/\-\+]+);base64,/', $base64String, $matches);
    
    // Return the MIME type if it was found, otherwise return null or an error message
    return isset($matches[1]) ? $matches[1] : null;
}

function mimeToExtension($mimeType) {

    $mimeTypes = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'text/plain' => 'txt',
        'application/json' => 'json',
        'application/xml' => 'xml',
        'application/zip' => 'zip',
        'audio/mpeg' => 'mp3',
        'video/mp4' => 'mp4',
        'text/html' => 'html',
        'application/msword' => 'doc',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    ];

    return isset($mimeTypes[$mimeType]) ? $mimeTypes[$mimeType] : null;
}

$mimeType = getMimeTypeFromBase64String($_REQUEST['anexo_contents']);
$fileExtension = mimeToExtension($mimeType);
$dataContents = preg_replace('#^data:' . preg_quote($mimeType, '#') . ';base64,#', '', $_REQUEST['anexo_contents']);

$doc_path = '/data/docs/'.$_REQUEST['anexo_tipo'].'_'. uniqid() . '.' . $fileExtension;
$doc_type = $_REQUEST['anexo_tipo'];
$paciente_token = $_REQUEST['paciente_token'];
$description = $_REQUEST['details'];

$stmt = $pdo->prepare("INSERT INTO `ANEXOS_PACIENTES` (`doc_type`, `doc_path`, `paciente_token`, `descricao`) VALUES (:doc_type, :doc_path, :paciente_token, :descricao);");

$stmt->bindValue(':doc_type', $doc_type);
$stmt->bindValue(':doc_path', $doc_path);
$stmt->bindValue(':paciente_token', $paciente_token);
$stmt->bindValue(':descricao', $description);

try {
    file_put_contents($_SERVER['DOCUMENT_ROOT'].$doc_path, base64_decode($dataContents));
    $stmt->execute();

    $resp = [
        'icon' => 'success',
        'title' => 'Upload de Documentos',
        'text' => 'Anexo salvo com sucesso',
        'status' => 'success'
    ];
} catch (Exception $e) {
    $resp = [
        'icon' => 'error',
        'title' => 'Upload de Documentos',
        'text' => 'Ocorreu um erro ao salvar o anexo',
        'status' => 'error'
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resp, JSON_PRETTY_PRINT);