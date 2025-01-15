<?php
require_once ('../config.inc.php');
$user = $_SESSION['user'];

function dueDate($data_gendamento, $data_atual)
{
    $agendamento = strtotime($data_gendamento);

    $diferenca = ($agendamento - strtotime($data_atual));

    $minutos = round($diferenca / 60);
    $horas = round($minutos / 60);
    $dias = round($horas / 24);

    $vencimento = date('Y-m-d', $agendamento);
    $week = date('D', strtotime($vencimento));

    if ($horas <= 1) {
        return date('Y-m-d H:i', strtotime('+20 minutes'));
    } else if ($horas > 1 && $horas <= 6) {
        return date('Y-m-d H:i', strtotime('+20 minutes'));
    } else if ($dias == 1 && strtotime(date('Y-m-d H:i')) <= date('Y-m-d', strtotime($vencimento . ' -1 day')) . ' 18:00') {
        return date('Y-m-d') . ' 18:00';
    } else if ($dias == 1 && strtotime(date('Y-m-d H:i')) > date('Y-m-d', strtotime($vencimento . ' -1 day')) . ' 18:00') {
        return date('Y-m-d H:i', strtotime('+20 minutes'));
    } else if ($dias >= 2) {
        $vencimento = date('Y-m-d', $agendamento);
        $week = date('D', strtotime($vencimento));
        return date('Y-m-d', strtotime($vencimento . ' -1 day')) . ' 18:00';
    }
}

$data = $_REQUEST;
$token = uniqid();
$agenda_token = uniqid();

$dados = json_encode(['token' => $_REQUEST['userId'], 'nome' => $_REQUEST['userName']]);

$meet = new WhereByMeet();
$room = $meet->createRoom(uniqid());

$medico = $pdo->query("SELECT (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,nome_completo, identidade_genero,duracao_atendimento,tempo_limite_online,tempo_limite_presencial FROM MEDICOS WHERE token = '{$_REQUEST['medicoSelect']}'");
$medico = $medico->fetch(PDO::FETCH_OBJ);

$paciente = $pdo->query("SELECT payment_id,(SELECT nome FROM ANAMNESE WHERE id = queixa_principal) AS queixa_principal,nome_completo,cpf,email,celular FROM PACIENTES WHERE token = '{$_REQUEST['pacienteSelect']}'");
$paciente = $paciente->fetch(PDO::FETCH_OBJ);

$ag = new stdClass();
$ag->token = $token;
$ag->paciente_token = $_REQUEST['pacienteSelect'];
$ag->medico_token = $_REQUEST['medicoSelect'];
$ag->anamnese = $paciente->queixa_principal;
$ag->modalidade = $_REQUEST['modalidadeSelect'];
$ag->data_agendamento = date('Y-m-d H:i:s', strtotime($_REQUEST['dataAgendamento']));
$ag->duracao_agendamento = $medico->duracao_atendimento;
$ag->valor = $_REQUEST['valorConsulta'];
$ag->meet = json_encode($room);
$ag->descricao = $_REQUEST['descricao'];
$ag->payment_method = $_REQUEST['formaPgto'];
$ag->tipo_agendamento = $_REQUEST['optAgendamento'];

$sql = 'INSERT INTO `AGENDA_MED` (`tipo_agendamento`, `descricao`, `valor`,`paciente_token`, `medico_token`, `modalidade`, `anamnese`, `data_agendamento`, `duracao_agendamento`, `meet`, `token`, `payment_method`, `status`) 
VALUES (:tipo_agendamento, :descricao, :valor, :paciente_token, :medico_token, :modalidade, :anamnese, :data_agendamento, :duracao_agendamento, :meet, :token, :payment_method, :sts);';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':tipo_agendamento', $ag->tipo_agendamento);
$stmt->bindValue(':descricao', $ag->descricao);
$stmt->bindValue(':valor', $ag->valor);
$stmt->bindValue(':paciente_token', $ag->paciente_token);
$stmt->bindValue(':medico_token', $ag->medico_token);
$stmt->bindValue(':modalidade', strtoupper($ag->modalidade));
$stmt->bindValue(':anamnese', $ag->anamnese);
$stmt->bindValue(':data_agendamento', $ag->data_agendamento);
$stmt->bindValue(':duracao_agendamento', $ag->duracao_agendamento);
$stmt->bindValue(':meet', $ag->meet);
$stmt->bindValue(':token', $ag->token);
$stmt->bindValue(':payment_method', $ag->payment_method);
$stmt->bindValue(':sts', 'AGUARDANDO CONFIRMAÇÃO');

try {
    $stmt->execute();

    if ($_REQUEST['formaPgto'] == 'ABONAR') {
        $stmt1 = $pdo->prepare('INSERT INTO `VENDAS` 
        (
            `nome`,
            `code`,
            `amount`, 
            `customer`,
            `status`, 
            `payment_method`,
            `reference`,
            `module`,
            `payment_id`,
            `dueTime`,
            `dados`
        )
        VALUES
        (
            :nome,
            :token,
            :valor,
            :customer,
            :sts,
            :payment_method,
            :reference,
            :module,
            :payment_id,
            :dueTime,
            :dados
        )');

        $stmt1->bindValue(':nome', 'VENDA DE CONSULTA MÉDICA');
        $stmt1->bindValue(':token', $token);
        $stmt1->bindValue(':valor', $ag->valor);
        $stmt1->bindValue(':customer', $paciente->payment_id);
        $stmt1->bindValue(':sts', 'AGUARDANDO CONFIRMAÇÃO');
        $stmt1->bindValue(':payment_method', $ag->payment_method);
        $stmt1->bindValue(':reference', $agenda_token);
        $stmt1->bindValue(':module', 'AGENDA_MED');
        $stmt1->bindValue(':payment_id', '');
        $stmt1->bindValue(':dueTime', dueDate($ag->data_agendamento, date('Y-m-d H:i')));
        $stmt1->bindValue(':dados', $dados);

        try {
            $stmt1->execute();

            $json = [
                'status' => 'success',
                'icon' => 'success',
                'text' => 'Consulta Agendada com Sucesso!',
            ];
        } catch (Expception $e) {
            $json = [
                'status' => 'error',
                'icon' => 'error',
                'text' => 'Erro ao agendar consulta!',
            ];
        }
    } else {
        if (in_array($_REQUEST['formaPgto'], array('PIX', 'CREDIT_CARD'))) {
            $pac = $asaas->create_or_get_client(
                token: $ag->paciente_token,
                nome: $paciente->nome_completo,
                cpf: $paciente->cpf,
                email: $paciente->email,
                celular: $paciente->celular
            );

            $link = $asaas->cobrar(
                id: $pac->id,
                tipo: $ag->payment_method,
                valor: $ag->valor,
                reference: $agenda_token,
                descricao: 'VENDA DE CONSULTA MÉDICA',
                paymentDue: date('Y-m-d', strtotime($ag->data_agendamento))
            );

            if (isset($link->invoiceUrl)) {
                if ($_REQUEST['formaPgto'] == 'ABONAR') {
                    $asaas->receber_dinheiro($link->id);
                }

                $pdo->query(
                    "UPDATE PACIENTES SET payment_id = '{$pac->id}' WHERE token = '{$_REQUEST['pacienteSelect']}';"
                );

                $stmt1 = $pdo->prepare('INSERT INTO `VENDAS` (
                        `nome`,
                        `code`,
                        `amount`, 
                        `customer`,
                        `status`, 
                        `payment_method`,
                        `reference`,
                        `module`,
                        `payment_id`,
                        `dueTime`,
                        `dados`
                            )
                    VALUES
                        (
                        :nome,
                        :token,
                        :valor,
                        :customer,
                        :sts,
                        :payment_method,
                        :reference,
                        :module,
                        :payment_id,
                        :dueTime,
                        :dados
                        )');

                $stmt1->bindValue(':nome', 'VENDA DE CONSULTA MÉDICA');
                $stmt1->bindValue(':token', $token);
                $stmt1->bindValue(':valor', $ag->valor);
                $stmt1->bindValue(':customer', $paciente->payment_id);
                $stmt1->bindValue(':sts', 'AGUARDANDO CONFIRMAÇÃO');
                $stmt1->bindValue(':payment_method', $ag->payment_method);
                $stmt1->bindValue(':reference', $agenda_token);
                $stmt1->bindValue(':module', 'AGENDA_MED');
                $stmt1->bindValue(':payment_id', $link->id);
                $stmt1->bindValue(':dueTime', dueDate($ag->data_agendamento, date('Y-m-d H:i')));
                $stmt1->bindValue(':dados', $dados);

                $stmt1->execute();

                $json = [
                    'status' => 'success',
                    'icon' => 'success',
                    'text' => 'Consulta Agendada com Sucesso!',
                ];
            } else {
                $json = [
                    'status' => 'warning',
                    'icon' => 'error',
                    'text' => 'Erro ao Agendar a Consulta',
                    'data' => ['errro1']
                ];
            }
        } else {
            $pac = $asaas->create_or_get_client(
                token: $ag->paciente_token,
                nome: $paciente->nome_completo,
                cpf: $paciente->cpf,
                email: $paciente->email,
                celular: $paciente->celular
            );

            $link = $asaas->cobrar(
                id: $pac->id,
                tipo: 'UNDEFINED',
                valor: $ag->valor,
                reference: $agenda_token,
                descricao: 'VENDA DE CONSULTA MÉDICA',
                paymentDue: date('Y-m-d', strtotime($ag->data_agendamento))
            );

            if (isset($link->invoiceUrl)) {
                $pdo->query(
                    "UPDATE PACIENTES SET payment_id = '{$pac->id}' WHERE token = '{$_REQUEST['pacienteSelect']}';"
                );

                $stmt1 = $pdo->prepare('INSERT INTO `VENDAS` (
                    `nome`,
                    `code`,
                        `amount`, 
                        `customer`,
                        `status`, 
                        `payment_method`,
                            `reference`,
                            `module`,
                            `payment_id`,
                            `dueTime`,
                            `dados`
                            )
                    VALUES
                        (
                        :nome,
                        :token,
                        :valor,
                        :customer,
                        :sts,
                        :payment_method,
                        :reference,
                        :module,
                        :payment_id,
                        :dueTime,
                        :dados
                        )');

                $stmt1->bindValue(':nome', 'VENDA DE CONSULTA MÉDICA');
                $stmt1->bindValue(':token', $token);
                $stmt1->bindValue(':valor', $ag->valor);
                $stmt1->bindValue(':customer', $pac->id);
                $stmt1->bindValue(':sts', 'AGUARDANDO CONFIRMAÇÃO');
                $stmt1->bindValue(':payment_method', 'UNDEFINED');
                $stmt1->bindValue(':reference', $agenda_token);
                $stmt1->bindValue(':module', 'AGENDA_MED');
                $stmt1->bindValue(':payment_id', $link->id);
                $stmt1->bindValue(':dueTime', dueDate($ag->data_agendamento, date('Y-m-d H:i')));
                $stmt1->bindValue(':dados', $dados);

                $stmt1->execute();

                $json = [
                    'status' => 'success',
                    'icon' => 'success',
                    'text' => 'Consulta Agendada com Sucesso!',
                ];
            } else {
                $json = [
                    'status' => 'warning',
                    'icon' => 'error',
                    'text' => 'Erro ao Agendar a Consulta',
                    'data' => ['errro2']
                ];
            }
        }
    }
} catch (Exception $error) {
    $json = [
        'status' => 'warning',
        'icon' => 'error',
        'text' => 'Erro ao Agendar a Consulta',
        'data' => $error
    ];

    file_put_contents('logs-ag.txt', $error->getMessage());
}

header('Content-Type: application/json');

echo json_encode($json, JSON_PRETTY_PRINT);
