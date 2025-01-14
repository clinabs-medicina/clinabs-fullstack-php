<?php
require_once '../config.inc.php';

if(isset($_GET['receita_token']) && isset($_GET['paciente_token'])) {
    $token = $_GET['paciente_token'];
    $stmt = $pdo->query("SELECT celular,nome_completo FROM PACIENTES WHERE token = '{$token}';");
    $paciente = $stmt->fetch(PDO::FETCH_OBJ);

    if($stmt->rowCount() > 0 && $paciente->celular != '') {
        $phoneNumber = $paciente->celular;
        $linkUrl = "https://clinabs.com/data/receitas/assinadas/{$_GET['receita_token']}";
        $linkTitle = "RECEITA_{$_GET['receita_token']}";
        $linkDescription = 'CLINABS CENTRO DE TELEMEDICINA INTEGRADA';

        $txt = "Olá, *{$paciente->nome_completo}*,
        Segue o Anexo da Receita referente a Sua Consulta.";

        $wa->sendDocMessage($phoneNumber,$txt, $linkUrl, $linkTitle);

        $resp = [
            'status' => 'error',
            'text' => 'Receita Enviada com Sucesso.',
            'icon' => 'success'
        ];
    } else {
        $resp = [
            'status' => 'error',
            'text' => "Paciente com Celular Inválido ou não Cadastrado. {$paciente->celular}",
            'icon' => 'error'
        ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($resp, JSON_PRETTY_PRINT);
}else {
    $resp = [
        'status' => 'error',
        'text' => 'Dados Inálidos.',
        'icon' => 'error'
    ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($resp, JSON_PRETTY_PRINT);
}