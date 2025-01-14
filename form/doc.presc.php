<?php
if(isset($_FILES['file-doc'])) {
    $extension = pathinfo($_FILES['file-doc']['name'], PATHINFO_EXTENSION);
    $file_name = '../data/docs/'.uniqid().'.'.$extension;

    header('Content-Type: application/json; charset=utf-8');
    
    if(move_uploaded_file($_FILES['file-doc']['tmp_name'], $file_name)) {
        echo json_encode([
            'status' => 'success',
            'filename' => basename($file_name)
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'filename' => ''
        ]);
    }
}