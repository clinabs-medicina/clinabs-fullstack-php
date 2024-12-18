<?php
require_once '../config.inc.php';


$data = json_decode(file_get_contents('php://input'), true);

$file = '/data/images/unidades/'.uniqid().'.png';

if(strlen($data['unidade_image']) > 0) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'].$file, base64_decode($data['unidade_image']));
    Modules::resize_crop_image($_SERVER['DOCUMENT_ROOT'].$file, $_SERVER['DOCUMENT_ROOT'].$file, 500,500);
} else {
    file_put_contents($_SERVER['DOCUMENT_ROOT'].$file, '');
}


if(strlen($data['unidade_image']) > 0){
    $stmt = $pdo->prepare('INSERT INTO `UNIDADES` (`nome`, `contato`, `image`, `payments`, `cep`, `logradouro`, `cidade`, `bairro`, `uf`, `complemento`, `numero`, `medicos`, `token`) 
    VALUES(:nome, :contato, :image, :payments, :cep, :logradouro, :cidade, :bairro, :uf, :complemento, :numero, :medicos, :token) 
    ON DUPLICATE KEY UPDATE `nome` = :nome, `contato` = :contato, `image` = :image, `payments` = :payments, `cep` = :cep, `logradouro` = :logradouro, `cidade` = :cidade, `bairro` = :bairro, `uf` = :uf, `complemento` = :complemento, `numero` = :numero, `medicos` = :medicos;');
    $stmt->bindValue(':nome', strtoupper($data['unidade_nome'])); 
    $stmt->bindValue(':contato', $data['unidade_contato']); 
    $stmt->bindValue(':image', $file); 
    $stmt->bindValue(':payments', '[]'); 
    $stmt->bindValue(':cep', $data['unidade_cep']); 
    $stmt->bindValue(':logradouro', $data['unidade_endereco']); 
    $stmt->bindValue(':cidade', $data['unidade_cidade']); 
    $stmt->bindValue(':bairro', $data['unidade_bairro']); 
    $stmt->bindValue(':uf', $data['unidade_uf']); 
    $stmt->bindValue(':complemento', $data['unidade_complemento']); 
    $stmt->bindValue(':numero', $data['unidade_numero']);
    $stmt->bindValue(':medicos', $data['unidade_medicos'] ?? '[]');
    $stmt->bindValue(':token', uniqid());
} else {
    $stmt = $pdo->prepare('INSERT INTO `UNIDADES` (`nome`, `contato`, `payments`, `cep`, `logradouro`, `cidade`, `bairro`, `uf`, `complemento`, `numero`, `medicos`, `token`) 
    VALUES(:nome, :contato, :payments, :cep, :logradouro, :cidade, :bairro, :uf, :complemento, :numero, :medicos, :token) 
    ON DUPLICATE KEY UPDATE `nome` = :nome, `contato` = :contato, `payments` = :payments, `cep` = :cep, `logradouro` = :logradouro, `cidade` = :cidade, `bairro` = :bairro, `uf` = :uf, `complemento` = :complemento, `numero` = :numero, `medicos` = :medicos;');
    $stmt->bindValue(':nome', strtoupper($data['unidade_nome'])); 
    $stmt->bindValue(':contato', $data['unidade_contato']); 
    $stmt->bindValue(':payments', '[]'); 
    $stmt->bindValue(':cep', $data['unidade_cep']); 
    $stmt->bindValue(':logradouro', $data['unidade_endereco']); 
    $stmt->bindValue(':cidade', $data['unidade_cidade']); 
    $stmt->bindValue(':bairro', $data['unidade_bairro']); 
    $stmt->bindValue(':uf', $data['unidade_uf']); 
    $stmt->bindValue(':complemento', $data['unidade_complemento']); 
    $stmt->bindValue(':numero', $data['unidade_numero']);
    $stmt->bindValue(':medicos', $data['unidade_medicos'] ?? '[]');
    $stmt->bindValue(':token', uniqid());
}

header('Content-Type: application/json');

try {
    $stmt->execute();
    echo json_encode([
            'status' => 'success',
            'text' => 'Salvo com Sucesso!',
            'icon' => 'success',
            'allowOutsideClick' => false,
            'image' => $file
        ], JSON_PRETTY_PRINT);
} catch(PDOException $error) {
    echo json_encode([
        'title' => 'Atenção',
        'status' => 'error',
        'text' => $error->getMessage(),
        'icon' => 'error',
        'allowOutsideClick' => false
        ], JSON_PRETTY_PRINT);
}