<?php
require_once('../config.inc.php');


$stmt_agendamento = $pdo->query("SELECT id,token,( SELECT nome_completo FROM PACIENTES WHERE token = paciente_token ) AS paciente_nome, ( SELECT nome_completo FROM MEDICOS WHERE token = medico_token ) AS medico_nome, data_agendamento, paciente_token, modalidade, ( SELECT payment_id FROM VENDAS WHERE reference = token ) AS payment_id, status FROM AGENDA_MED WHERE STATUS IN( 'AGENDADO', 'AGUARDANDO PAGAMENTO', 'PAGAMENTO PENDENTE' ) ORDER BY data_agendamento,paciente_nome ASC");
$agendamentos = [] ;

foreach($stmt_agendamento->fetchAll(PDO::FETCH_OBJ) as $item) {
    $item->img = Modules::getUserImage($item->paciente_token);
    
    if(strtotime($item->data_agendamento) >= time()) {
      $agendamentos[$item->data_agendamento] = $item;
    }
}

ksort($agendamentos);

$stmt_pacientes = $pdo->query("SELECT DATEDIFF(CURDATE(),data_nascimento) AS age,id,nome_completo,token,creation_date,createTime,celular, (SELECT nome FROM ANAMNESE WHERE id = queixa_principal) AS queixa_principal, YEAR(CURDATE()) - YEAR(data_nascimento) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(data_nascimento, '%m%d')) AS age FROM PACIENTES WHERE creation_date >= NOW() - INTERVAL 3 DAY ORDER BY nome_completo ASC");
$pacientes = [];

foreach($stmt_pacientes->fetchAll(PDO::FETCH_OBJ) as $item) {
    $item->img = Modules::getUserImage($item->paciente_token);
    $item->isCorrect = $item->age >= 18;
    if($item->queixa_principal > 0) {

    } else {
        $item->queixa_principal = 'Não Informado.';
    }

    $pacientes[trim($item->nome_completo)] = $item;
}

ksort($pacientes);


$stmt_medicos = $pdo->query("SELECT nome_completo,token,creation_date, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade, YEAR(CURDATE()) - YEAR(data_nascimento) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(data_nascimento, '%m%d')) AS age FROM MEDICOS WHERE creation_date >= NOW() - INTERVAL 3 DAY ORDER BY creation_date, nome_completo ASC");
$medicos = [];
foreach($stmt_medicos->fetchAll(PDO::FETCH_OBJ) as $item) {
    $item->img = Modules::getUserImage($item->paciente_token);
    $medicos[trim($item->nome_completo)] = $item;
}

ksort($medicos);

$date = date('Y-m-d');

$stmt_acompanhamento = $pdo->query("SELECT *,(SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token) AS funcionario_nome,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome FROM `ACOMPANHAMENTO` WHERE proximo_acompanhamento >= '{$date}' ORDER BY proximo_acompanhamento, paciente_nome ASC");
$acompanhamentos = [];
foreach($stmt_acompanhamento->fetchAll(PDO::FETCH_OBJ) as $item) {
    $item->img = Modules::getUserImage($item->paciente_token);

    if($item->proximo_acompanhamento == date('Y-m-d')) {
        $item->proximo_acompanhamento = 'Hoje';
    } else if($item->proximo_acompanhamento == date('Y-m-d', strtotime(date('Y-m-d').' + 1 day'))) {
        $item->proximo_acompanhamento = 'Amanhã';
    }
    $acompanhamentos[] = $item;
}

sort($medicos);

$stmt_counter = $pdo->query("SELECT * FROM ((SELECT COUNT(*) AS acessos FROM ( SELECT ACCESS_LOGS.ip FROM ACCESS_LOGS WHERE `timestamp` LIKE '2024-09-23%' GROUP BY ACCESS_LOGS.ip ) AS T) AS acessos,(SELECT COUNT(*) AS medicos FROM MEDICOS) AS A, (SELECT COUNT(*) AS pacientes FROM PACIENTES) AS B, (SELECT COUNT(*) AS vendas FROM VENDAS WHERE status = 'AGUARDANDO PAGAMENTO') AS C, (SELECT COUNT(*) AS agendamentos FROM AGENDA_MED WHERE status = 'AGENDADO') AS D, (SELECT COUNT(*) AS pedidos FROM FARMACIA WHERE status != 'CANCELADO') AS E );");
$counter = $stmt_counter->fetch(PDO::FETCH_OBJ);
$counter->visitantes = 0;


$payments = $asaas->listarCobrancas();
$clientes = $asaas->listarClientes()->data;

$cobrancas = [];

foreach($payments as $payment) {
    if($payment->status == 'PENDING' && strtotime(date('Y-m-d', strtotime($payment->dueDate))) > strtotime(date('Y-m-d'))) {
        
        foreach($clientes as $cliente) {
            if($cliente->id == $payment->customer) {
                $payment->paciente_nome = $cliente->name;
                $payment->paciente_celular = $cliente->mobilePhone;
                $payment->img = Modules::getUserImage($cliente->externalReference);
                $payment->paciente_token = $cliente->externalReference;
            }
        }
 
        $cobrancas[$payment->id] = $payment;
       
    }
}

header("Content-Type: application/json");
echo json_encode([
    'agendamentos' => $agendamentos,
    'medicos' => $medicos,
    'pacientes' => $pacientes,
    'acompanhamentos' => $acompanhamentos,
    'cobrancas' => $cobrancas,
    'counter' => $counter,
    'clientes' => $clientes,
    'perms' => [
        'agendamentos' => [
            'dashboard_new_agendamentos_editar' => $user->perms->dashboard_new_agendamentos_editar,
            'dashboard_new_agendamentos_alterar' => $user->perms->dashboard_new_agendamentos_alterar,
            'dashboard_new_agendamentos_cancelar' => $user->perms->dashboard_new_agendamentos_cancelar,
            'dashboard_new_agendamentos_enviar_whatsapp' => $user->perms->dashboard_new_agendamentos_enviar_whatsapp,
        ],
        'pacientes' => [
            'dashboard_new_pacientes_carrinho' => $user->perms->dashboard_new_pacientes_carrinho,
            'dashboard_new_pacientes_ag_consulta' => $user->perms->dashboard_new_pacientes_ag_consulta,
            'dashboard_new_pacientes_perfil' => $user->perms->dashboard_new_pacientes_perfil,
            'dashboard_new_pacientes_msg_wa' => $user->perms->dashboard_new_pacientes_msg_wa,
            'dashboard_new_pacientes_prontuario' => $user->perms->dashboard_new_pacientes_prontuario
        ],
        'acompanhamento' => [
            'dashboard_proximos_acompanhamentos_prontuario' => $user->perms->dashboard_proximos_acompanhamentos_prontuario
        ],
        'medicos' => [
            'dashboard_new_medicos_perfil' => $user->perms->dashboard_new_medicos_perfil
        ]
    ]
], JSON_PRETTY_PRINT);