<?php
require_once('../config.inc.php');

$non_days = array('Sunday', 'Saturday', 'Friday');

if(in_array(date('l'), $non_days)) {
  $nextDay = date('Y-m-d', strtotime("next Monday"));
}else{
  $nextDay = date('Y-m-d', strtotime("+1 day"));
}

$stmt2 = $pdo->prepare("SELECT
	AGENDA_MED.medico_token,
	MEDICOS.nome_completo,
	MEDICOS.email,
	MEDICOS.celular
FROM
	AGENDA_MED,
	MEDICOS
	WHERE
	AGENDA_MED.medico_token = MEDICOS.token AND 
	AGENDA_MED.data_agendamento LIKE '{$nextDay}%'
GROUP BY
	AGENDA_MED.medico_token");
	
	$stmt2->execute();
	$medicos = $stmt2->fetchAll(PDO::FETCH_OBJ);
	
foreach($medicos as $medico) {
    $stmt = $pdo->prepare("SELECT
	AGENDA_MED.*, 
	PACIENTES.nome_completo AS nome_paciente,
	MEDICOS.nome_completo AS nome_medico
FROM
	AGENDA_MED,
	PACIENTES,
	MEDICOS
WHERE
	AGENDA_MED.paciente_token = PACIENTES.token AND
	AGENDA_MED.medico_token = MEDICOS.token AND
	AGENDA_MED.`status` = 'AGENDADO'
	AND
	AGENDA_MED.data_agendamento LIKE '{$nextDay}%' 
	AND
	AGENDA_MED.medico_token = '{$medico->medico_token}'

	ORDER BY data_agendamento ASC");
	
	
	$stmt->execute();
	$list = $stmt->fetchAll(PDO::FETCH_OBJ);
	
	if($stmt->rowCount() > 0) {
		foreach($list as $item) {
			$nome = '';
			if($nome == '') {
				$msg = "\n\nOlá DR(a) {$item->nome_medico} \n\nSua Agenda do Dia ".date('d/m/Y', strtotime($nextDay))."\n\n";
				$nome = $item->nome_medico;
			}
			
			$hora = date('H:i', strtotime($item->data_agendamento));
			$msg .= "\nHora: {$hora}\nPaciente: {$item->nome_paciente}\nModalidade: {$item->modalidade}\n".($item->modalidade == 'ONLINE' ? "Link da Sala: https://clinabs.com/meet/{$item->token}\n\n":"\n\n");
		}


		$response = $wa->sendLinkMessage(
						$medico->celular, 
						$msg, 
						'https://clinabs.com/agenda', 
						'CLINABS', 
						'Calendário de Agendamentos', 
						'https://clinabs.com/assets/images/logo.png'
					);

					$response = $wa->sendLinkMessage(
						'5541992319253', 
						$msg, 
						'https://clinabs.com/agenda', 
						'CLINABS', 
						'Calendário de Agendamentos', 
						'https://clinabs.com/assets/images/logo.png'
					);

					$response = $wa->sendLinkMessage(
						'5541995927699', 
						$msg, 
						'https://clinabs.com/agenda', 
						'CLINABS', 
						'Calendário de Agendamentos', 
						'https://clinabs.com/assets/images/logo.png'
					);

	} else {
		echo "Não há Agendamentos para o dia Seguinte.";
	}
}