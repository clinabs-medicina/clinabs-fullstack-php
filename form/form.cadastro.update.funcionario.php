<?php
require_once '../config.inc.php';
$data = $_REQUEST;

$stmt = $pdo->prepare('UPDATE FUNCIONARIOS SET nome_completo = :nome_completo, nacionalidade = :nacionalidade,nome_preferencia = :nome_preferencia,identidade_genero = :identidade_genero,data_nascimento = :data_nascimento,email = :email,telefone = :telefone,celular = :celular,situacao = :situacao WHERE token = :token');
$stmt->bindValue(':nome_completo', $data['nome_completo']);
$stmt->bindValue(':nacionalidade', $data['nacionalidade']);
$stmt->bindValue(':nome_preferencia', $data['nome_preferencia']);
$stmt->bindValue(':identidade_genero', $data['identidade_genero']);
$stmt->bindValue(':data_nascimento', $data['data_nascimento']);
$stmt->bindValue(':email', strtolower($data['email']));
$stmt->bindValue(':telefone', preg_replace('/[^0-9]+/', '', $data['telefone']));
$stmt->bindValue(':celular', preg_replace('/[^0-9]+/', '', $data['celular']));
$stmt->bindValue(':situacao', $data['situacao']);

try {
    $stmt->execute();

    $resp = [
        'status' => $stmt->rowCount() > 0 ? 'success':'warning',
        'text'  => $stmt->rowCount() > 0 ? 'Cadastro Atualizado Com Sucesso!':'Ocorreu um Erro ao Atualizar seu Cadastro.',
        'icon' => $stmt->rowCount() > 0 ? 'success':'warning'
    ];
} catch(Exception $error) {
    $resp = [
        'status' => 'error',
        'text' => 'Erro ao Atualizar Seu Cadastro!',
        'icon' => 'error'
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_PRETTY_PRINT);