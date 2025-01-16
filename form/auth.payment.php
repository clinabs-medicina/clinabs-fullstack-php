<?php
require_once '../config.inc.php';
error_reporting(1);
ini_set('display_errors', 1);

$data = $_REQUEST;

$token = $data['token'];

$useId = $data['useId'] == 'true' ? 'id' : 'payment_id';
$data['key'] = $useId;
$data['method'] = $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $confirmed = ($data['status'] == 'CONFIRMADO');

    if (!$confirmed) {
        $payload = $asaas->deleteCobranca($data['token']);
    }

    $sts = $data['status'] == 'CONFIRMADO' ? 'AGUARDANDO PAGAMENTO' : $data['status'];

    $pl = $pdo->query("SELECT id,(SELECT payment_method FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS pm FROM `VENDAS` WHERE `id` = '$token' OR `payment_id` = '$token'")->fetch(PDO::FETCH_OBJ);

    if ($pl->pm == 'ABONAR') {
        $sts = 'AGENDADO';
    }

    $stmt = $pdo->prepare('UPDATE `VENDAS` SET `status` = :status, `payload`= :dados WHERE `' . $useId . '` = :token');
    $stmt->bindValue(':status', $sts);
    $stmt->bindValue(':token', $data['token']);
    $stmt->bindValue(':dados', json_encode($data, JSON_PRETTY_PRINT));

    try {
        $stmt->execute();

        if ($data['status'] == 'CONFIRMADO') {
            $xstmt = $pdo->prepare('SELECT payment_id, asaas_payload,(SELECT modalidade FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS modalidade,(SELECT payment_method FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS payment_method, (SELECT tipo_agendamento FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS tipo_agendamento, (SELECT nome FROM ESPECIALIDADES WHERE id = (SELECT especialidade FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code))) AS medico_especialidade,(SELECT identidade_genero FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS medico_sexo,(SELECT celular FROM PACIENTES WHERE token = (SELECT paciente_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS paciente_celular, (SELECT celular FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS medico_celular, (SELECT nome_completo FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS medico,(SELECT nome_completo FROM PACIENTES WHERE token = (SELECT paciente_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS paciente, (SELECT data_agendamento FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS data_agendamento,amount,code,payment_id,status,customer FROM `VENDAS` WHERE `id` = :id OR `payment_id` = :id');
            $xstmt->bindValue(':id', $token);
            $xstmt->execute();
            $ag = $xstmt->fetch(PDO::FETCH_ASSOC);

            ksort($ag);

            $ag['has_payload'] = str_starts_with($ag['payment_id'], 'pay_');

            if ($ag['has_payload']) {
                $payload = json_decode($ag['asaas_payload'], true);

                unset($ag['asaas_payload']);

                $ag['invoice_number'] = $payload['invoiceNumber'];
                $ag['invoice_url'] = $payload['invoiceUrl'];

                $valor = number_format($ag['amount'], 2, ',', '.');
                $data_agendamento = date('d/m/Y H:i', strtotime($ag['data_agendamento']));
                $prefixo = strtolower($ag['medico_sexo']) == 'feminino' ? 'Dra.' : 'Dr.';

                // Paciente
                $msg = "
Olá {$ag['paciente']},
Sua Consulta foi Agendada com Sucesso!

para a efetivação é necessário realizar o pagamento desta Fatura para Confirmar o seu Agendamento.
Fatura: *#{$ag['invoice_number']}*
Valor: *R\$ {$valor}*
Data do Agendamento: *{$data_agendamento}*
Médico: *{$prefixo} {$ag['medico']}*
Especialidade: *{$ag['medico_especialidade']}*

clique neste link para efetivar o pagamento!

{$ag['invoice_url']}

Ajuda:

-> Se o link não está abrindo, você deve adicionar nosso contato em seu celular.
-> Você pode pagar com *PIX*, *Cartão de Crédito* ou *Débito*
-> A Validade deste pagamento é até hoje as *23:59:59*";

                $res = $wa->sendTextMessage(
                    phoneNumber: $ag['paciente_celular'],
                    text: $msg,
                );

                // Medico

                $msg = "Olá, *{$prefixo} {$ag['medico']}*,
foi realizado um agendamento *{$ag['modalidade']}* para dia *{$data_agendamento}*.
O pagamento da consulta tem o prazo de até 48 horas para ser efetuado pelo paciente.
No momento o pagamento encontra-se como pendente.

Tipo de Agendamento: {$ag['tipo_agendamento']}

Obs: Após o prazo de 48 horas, caso não seja efetuado o pagamento, a agenda estará livre para novo agendamento.";

                $res = $wa->sendTextMessage(
                    phoneNumber: $ag['medico_celular'],
                    text: $msg,
                );
            } else {
                $xstmt = $pdo->prepare('SELECT payment_id,(SELECT meet FROM `AGENDA_MED` WHERE token = VENDAS.code) AS meet, (SELECT modalidade FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS modalidade,(SELECT payment_method FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS payment_method, (SELECT tipo_agendamento FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS tipo_agendamento, (SELECT nome FROM ESPECIALIDADES WHERE id = (SELECT especialidade FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code))) AS medico_especialidade,(SELECT identidade_genero FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS medico_sexo,(SELECT celular FROM PACIENTES WHERE token = (SELECT paciente_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS paciente_celular, (SELECT celular FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS medico_celular, (SELECT nome_completo FROM MEDICOS WHERE token = (SELECT medico_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS medico,(SELECT nome_completo FROM PACIENTES WHERE token = (SELECT paciente_token FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code)) AS paciente, (SELECT data_agendamento FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.code) AS data_agendamento,amount,code,payment_id,status,customer FROM `VENDAS` WHERE `id` = :id OR `payment_id` = :id');
                $xstmt->bindValue(':id', $token);
                $xstmt->execute();
                $ag = $xstmt->fetch(PDO::FETCH_ASSOC);

                $valor = number_format($ag['amount'], 2, ',', '.');
                $data_agendamento = date('d/m/Y H:i', strtotime($ag['data_agendamento']));
                $prefixo = strtolower($ag['medico_sexo']) == 'feminino' ? 'Dra.' : 'Dr.';

                $meet = json_decode($ag['meet']);

                $paciente_link_meet = $meet->roomUrl;
                $medico_link_meet = $meet->hostRoomUrl;

                ksort($ag);
                // Paciente

                if ($ag['payment_method'] == 'ABONAR') {
                    if ($ag['modalidade'] == 'ONLINE') {
                        $linkMeetPaciente = "Link da Sala: {$paciente_link_meet}";
                        $linkMeetMedico = "Link da Sala: {$medico_link_meet}";
                    } else {
                        $linkMeetPaciente = '';
                        $linkMeetMedico = '';
                    }
                    $msg = "
Olá {$ag['paciente']},
Sua Consulta foi Agendada com Sucesso!
                                
Tipo de Agendamento: *{$ag['tipo_agendamento']}*
Data do Agendamento: *{$data_agendamento}*
Médico: *{$prefixo} {$ag['medico']}*
Especialidade: *{$ag['medico_especialidade']}*

{$linkMeetPaciente}
";
                } else {
                    $msg = "
Olá {$ag['paciente']},
Sua Consulta foi Agendada com Sucesso!
                                
para a efetivação é necessário realizar o pagamento desta Fatura para Confirmar o seu Agendamento.

Valor: *R\$ {$valor}*
Data do Agendamento: *{$data_agendamento}*
Médico: *{$prefixo} {$ag['medico']}*
Especialidade: *{$ag['medico_especialidade']}*
              
                                
Ajuda:
                                
-> Se o link não está abrindo, você deve adicionar nosso contato em seu celular.
-> Você pode pagar com *PIX*, *Cartão de Crédito* ou *Débito*
-> A Validade deste pagamento é até hoje as *23:59:59*";
                }

                if ($ag['payment_method'] == 'ABONAR') {
                    $msg1 = "Olá {$ag['paciente']},
Você tem uma Consulta Agendada com *{$prefixo} {$ag['medico']}* em *{$data_agendamento}*

{$linkMeetPaciente}";

                    $msg2 = "Olá {$prefixo} {$ag['medico']},

Viocê tem um agendamento *{$ag['modalidade']}* para dia *{$data_agendamento}*.
com  *{$ag['paciente']}*.
                                
Tipo de Agendamento: *{$ag['tipo_agendamento']}*
{$linkMeetMedico}";

                    try {
                        $dta = date('Y-m-d H:i:s', strtotime($ag['data_agendamento']) - 3600);
                        $dta2 = date('Y-m-d H:i:s', strtotime($ag['data_agendamento']) - 600);

                        $meetlink1 = end(explode(' ', $linkMeetPaciente));
                        $meetlink2 = end(explode(' ', $linkMeetMedico));

                        $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg1}', '{$ag['paciente_celular']}', 'AGENDA_MED', 'PENDENTE', '{$meetlink1}', '{$ag['code']}', '{$ag['code']}_1');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg2}', '{$ag['medico_celular']}', 'AGENDA_MED', 'PENDENTE', '{$meetlink2}', '{$ag['code']}', '{$ag['code']}_2');");
                    } catch (PDOException $ex) {
                    }
                }

                $res = $wa->sendTextMessage(
                    phoneNumber: $ag['paciente_celular'],
                    text: trim($msg),
                );

                // Medico
                if ($ag['payment_method'] == 'ABONAR') {
                    $msg = "Olá, *{$prefixo} {$ag['medico']}*,
foi realizado um agendamento *{$ag['modalidade']}* para dia *{$data_agendamento}*.
com  *{$ag['paciente']}*.
                                
Tipo de Agendamento: *{$ag['tipo_agendamento']}*

{$linkMeetMedico}";

                    $msgs = "Olá, *{$prefixo} {$ag['medico']}*,
Você tem um agendamento *{$ag['modalidade']}* para dia *{$data_agendamento}*.
com  *{$ag['paciente']}*.
                                
Tipo de Agendamento: *{$ag['tipo_agendamento']}*

{$linkMeetMedico}";

                    if ($ag['payment_method'] == 'ABONAR') {
                        $msg1 = "Olá {$ag['paciente']},
Você tem uma Consulta Agendada com *{$prefixo} {$ag['medico']}* em *{$data_agendamento}*

{$linkMeetPaciente}";

                        $msg2 = "Olá {$prefixo} {$ag['medico']},

Viocê tem um agendamento *{$ag['modalidade']}* para dia *{$data_agendamento}*.
com  *{$ag['paciente']}*.
                
Tipo de Agendamento: *{$ag['tipo_agendamento']}*
{$linkMeetMedico}";

                        try {
                            $dta = date('Y-m-d H:i:s', strtotime($ag['data_agendamento']) - 3600);
                            $dta2 = date('Y-m-d H:i:s', strtotime($ag['data_agendamento']) - 600);

                            $meetlink1 = end(explode(' ', $linkMeetPaciente));
                            $meetlink2 = end(explode(' ', $linkMeetMedico));

                            $pdo->query("INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta}', '{$msg1}', '{$ag['paciente_celular']}', 'AGENDA_MED', 'PENDENTE', '{$meetlink1}', '{$ag['code']}', '{$ag['code']}_1');INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `link`, `agenda_token`, `event`) VALUES ('LEMBRETE DE AGENDAMENTO', '{$dta2}', '{$msg2}', '{$ag['medico_celular']}', 'AGENDA_MED', 'PENDENTE', '{$meetlink2}', '{$ag['code']}', '{$ag['code']}_2');");
                        } catch (PDOException $ex) {
                        }
                    }
                } else {
                    $msg = "Olá, *{$prefixo} {$ag['medico']}*,
foi realizado um agendamento *{$ag['modalidade']}* para dia *{$data_agendamento}*.
O pagamento da consulta tem o prazo de até 48 horas para ser efetuado pelo paciente.
No momento o pagamento encontra-se como pendente.
                                
Tipo de Agendamento: {$ag['tipo_agendamento']}
                                
Obs: Após o prazo de 48 horas, caso não seja efetuado o pagamento, a agenda estará livre para novo agendamento.";
                }

                $res = $wa->sendTextMessage(
                    phoneNumber: $ag['medico_celular'],
                    text: trim($msg)
                );
            }
        }

        $json = [
            'status' => 'success',
            'text' => 'Solicitação ' . ($confirmed ? 'confirmada' : 'cancelada') . ' com sucesso!',
            'icon' => 'success',
            'allowOutSideClick' => 'false',
            'data' => $ag ?? []
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
    $stmt = $pdo->query("SELECT *,(SELECT tipo_agendamento FROM `AGENDA_MED` WHERE token = VENDAS.code) AS agendamento_tipo, DATE_FORMAT(`created_at`, '%d/%m/%Y %H:%i') AS created, (SELECT DATE_FORMAT(`data_agendamento`, '%d/%m/%Y %H:%i') FROM `AGENDA_MED` WHERE token = VENDAS.code) AS agendamento, (SELECT nome_completo FROM `PACIENTES`  WHERE token = ( SELECT paciente_token FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1)) AS paciente, (SELECT nome_completo FROM `MEDICOS`  WHERE token = ( SELECT medico_token FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1)) AS medico, (SELECT descricao FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1) AS descricao FROM `VENDAS` WHERE $useId = '$token';");

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
            'text' => 'Nenhum dado Encontrado',
            'query' => "SELECT *,(SELECT tipo_agendamento FROM `AGENDA_MED` WHERE token = VENDAS.code) AS agendamento_tipo, DATE_FORMAT(`created_at`, '%d/%m/%Y %H:%i') AS created, (SELECT DATE_FORMAT(`data_agendamento`, '%d/%m/%Y %H:%i') FROM `AGENDA_MED` WHERE token = VENDAS.code) AS agendamento, (SELECT nome_completo FROM `PACIENTES`  WHERE token = ( SELECT paciente_token FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1)) AS paciente, (SELECT nome_completo FROM `MEDICOS`  WHERE token = ( SELECT medico_token FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1)) AS medico, (SELECT descricao FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1) AS descricao FROM `VENDAS` WHERE $useId = '$token';"
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);
