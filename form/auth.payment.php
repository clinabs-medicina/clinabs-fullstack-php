<?php
require_once '../config.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_REQUEST;

    $confirmed = ($data['status'] == 'CONFIRMADO');

    if (!$confirmed && $data['delete_cobranca']) {
        $payload = $asaas->deleteCobranca($data['token']);

        file_put_contents($data['token'] . '.json', json_encode($payload, JSON_PRETTY_PRINT));
    }

    $stmt = $pdo->prepare('UPDATE `VENDAS` SET `status` = :status, `payload`= :dados WHERE `payment_id` = :token');
    $stmt->bindValue(':status', $confirmed ? 'PAGAMENTO PENDENTE' : 'CANCELADO');
    $stmt->bindValue(':token', $data['token']);
    $stmt->bindValue(':dados', json_encode($data, JSON_PRETTY_PRINT));

    try {
        $stmt->execute();
        $json = [
            'status' => 'success',
            'text' => 'Solicitação ' . ($confirmed ? 'confirmada' : 'cancelada') . ' com sucesso!',
            'icon' => 'success',
            'allowOutSideClick' => 'false',
        ];
    } catch (Exception $e) {
        $json = [
            'status' => 'error',
            'text' => 'Ocorreu um erro ao ' . ($confirmed ? 'confirmar' : 'cancelar') . ' a solicitação.',
            'icon' => 'error',
            'allowOutSideClick' => 'false',
        ];
    }
} else {
    $stmt = $pdo->query("SELECT *,(SELECT tipo_agendamento FROM `AGENDA_MED` WHERE token = VENDAS.code) AS agendamento_tipo, DATE_FORMAT(`created_at`, '%d/%m/%Y %H:%i') AS created, (SELECT DATE_FORMAT(`data_agendamento`, '%d/%m/%Y %H:%i') FROM `AGENDA_MED` WHERE token = VENDAS.code) AS agendamento, (SELECT nome_completo FROM `PACIENTES`  WHERE token = ( SELECT paciente_token FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1)) AS paciente, (SELECT nome_completo FROM `MEDICOS`  WHERE token = ( SELECT medico_token FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1)) AS medico, (SELECT descricao FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1) AS descricao FROM `VENDAS` WHERE payment_id = '{$_GET['token']}';");

    if ($stmt->rowCount() > 0) {
        $payload = $stmt->fetch(PDO::FETCH_OBJ);
        $payload->payment_method = $asaas->get_status($payload->payment_method);
        $payload->solicitante = json_decode($payload->dados)->nome ?? 'Não Informado';

        $json = [
            'status' => 'success',
            'data' => $payload
        ];
    } else {
        $json = [
            'status' => 'error',
            'text' => 'Nenhum dado Encontrado'
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);
