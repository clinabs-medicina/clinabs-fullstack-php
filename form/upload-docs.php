<?php
require_once '../config.inc.php';

$allowed = ['pdf', 'png', 'jpg', 'jpeg'];
$ext  = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    if(in_array($ext, $allowed)) {
        $file_name = md5(uniqid());
        $file_size = $_FILES['file']['size'];

        if (move_uploaded_file($_FILES['file']['tmp_name'], "../data/images/docs/{$file_name}.{$ext}")) {
            if($_POST['key'] == 'FARMACIA') {
                try {
                    $stmt = $pdo->query("UPDATE FARMACIA SET doc_receita = '{$file_name}.{$ext}' WHERE token = '{$_POST['token']}'");
            
                    $response = [
                        'status' => 'success',
                        'message' => 'File uploaded successfully',
                        'path' => "{$file_name}.{$ext}",
                        'ext' => $ext,
                        'file_size' => $file_size,
                        'request' => $_POST,
                        'affected_rows' => $stmt->rowCount(),
                        'executed_query' => "UPDATE {$_POST['tb']} SET {$_POST['key']} = '{$file_name}.{$ext}' WHERE token = '{$_POST['token']}'"
                    ];
                } catch(Exception $ex) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Error uploading file',
                        'request' => $_POST,
                        'error' => $ex->getMessage(),
                        'executed_query' => "UPDATE {$_POST['tb']} SET {$_POST['key']} = '{$file_name}.{$ext}' WHERE token = '{$_POST['token']}'"
                    ];
                }
            }else {
                
                try {
                    $stmt = $pdo->query("UPDATE {$_POST['tb']} SET {$_POST['key']} = '{$file_name}.{$ext}' WHERE token = '{$_POST['token']}'");
            
                    $response = [
                        'status' => 'success',
                        'message' => 'File uploaded successfully',
                        'path' => "{$file_name}.{$ext}",
                        'ext' => $ext,
                        'file_size' => $file_size,
                        'request' => $_POST,
                        'affected_rows' => $stmt->rowCount(),
                        'executed_query' => "UPDATE {$_POST['tb']} SET {$_POST['key']} = '{$file_name}.{$ext}' WHERE token = '{$_POST['token']}'"
                    ];
                } catch(Exception $ex) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Error uploading file',
                        'request' => $_POST,
                        'error' => $ex->getMessage(),
                        
                    ];
                }
            }

            
        } else {
            // Failed to move file
            $response = [
                'status' => 'error',
                'message' => 'Ocorreu um Erro ao Enviar o Arquivo.'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Arquivo não Permitido'
        ];
    }
} else {
    // Failed to upload file
    $response = [
        'status' => 'error',
        'message' => 'Ocorreu um Erro ao Enviar o Arquivo.'
    ];
}

// Return JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
