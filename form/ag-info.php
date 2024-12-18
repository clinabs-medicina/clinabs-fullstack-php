<?php
require_once '../config.inc.php';
$token = $_GET['token'];

if($_GET['mode'] == 'fetch:ag') {
    $stmt = $pdo->query("SELECT *,(SELECT email FROM MEDICOS WHERE token = medico_token) as medico_email,(SELECT id FROM MEDICOS WHERE token = medico_token) as mid,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) as sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) as medico_nome,(SELECT cpf FROM MEDICOS WHERE token = medico_token) as medico_cpf FROM `AGENDA_MED` WHERE token = '{$token}';");

    $resp = $stmt->fetch(PDO::FETCH_ASSOC);
    $resp['data'] = date('Y-m-d', strtotime($resp['data_agendamento']));
    $resp['data_formated'] = date('d/m/Y', strtotime($resp['data_agendamento']));
    $resp['hora'] = date('H:i', strtotime($resp['data_agendamento']));
    $resp['medico_cpf'] = preg_replace('/[^0-9]+/', '', $resp['medico_cpf']);

    if(isset($_GET['certisign'])) {
        require_once '../api/v2/certisign/CertiSign.php';
        $certisign = new CertiSign(
            token: 'e4a3d16f31674ebfbf796c5ae9a6cbff'
        );

        $resp['signer'] = $certisign->bind('signature', [
        "file" => $_GET['file'],
        "nome" => $resp['medico_nome'],
        "email" => $resp['medico_email'],
        "cpf" => $resp['medico_cpf'],
        "receitaId" => "Receita: ".basename($_GET['file'], ".pdf"),
        "medicoNome" => "DR(a). ".$resp['medico_nome'],
        "pacienteNome" => "Paciente: ".$resp['paciente_nome']
        ]);
    }


} else {
    $stmt = $pdo->query("SELECT *,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) as sexo,data_alteracoes,(SELECT id FROM MEDICOS WHERE token = medico_token) as mid,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) as sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) as medico_nome FROM `AGENDA_MED` WHERE token = '{$token}';");

    $resp = $stmt->fetch(PDO::FETCH_ASSOC);


    $alt = json_decode($resp['data_alteracoes'], true);
    $id = $alt['medico_id'];
    $alter = $pdo->query("SELECT nome_completo,token FROM MEDICOS WHERE id = '{$id}';");
    $medico = $alter->fetch(PDO::FETCH_ASSOC);
    
    $resp['data'] = date('Y-m-d', strtotime($resp['data_agendamento']));
    $resp['data_formated'] = date('d/m/Y', strtotime($resp['data_agendamento']));//data_agendamento
    $resp['alter']['data_alterada'] = date('d/m/Y', strtotime($alt['data_agendamento']));
    $resp['hora'] = date('H:i', strtotime($resp['data_agendamento']));
    $resp['alter'] = json_decode($resp['data_alteracoes'], true);
    $resp['alter']['mid'] = $resp['mid'];
    $resp['alter']['medico_nome'] = ($medico['nome_completo'] == 'MASCULINO' ? 'Dr. ':'Dra. ').$medico['nome_completo'];
    $resp['alter']['data'] = date('d/m/Y', strtotime($resp['data_agendamento']));

    $resp['alter']['data_alterada'] = date('d/m/Y', strtotime($alt['data_agendamento']));


    $resp['alter']['hora'] = date('H:i', strtotime($resp['data_agendamento']));
    $resp['alter']['medico_token'] = $medico['token'];

    $resp = $resp['alter'];
}


header('Content-Type: application/json');
echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);