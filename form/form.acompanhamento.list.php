<?php
require_once '../config.inc.php';

$stmt = $pdo->prepare("SELECT
	id,
	paciente_token,
	`timestamp`,
	texto AS texto,
	last_update,
	updated_by,
	( SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token ) AS usuario_nome,
	( SELECT objeto FROM FUNCIONARIOS WHERE token = funcionario_token ) AS especialidade,
	periodo,
	semana,
	'' AS token,
	'' AS remedio,
	doc_tipo AS tipo,
    doc_file
FROM
	ACOMPANHAMENTO 
WHERE
	paciente_token = :token 
	UNION
	(
	SELECT
		id,
		paciente_token,
		`timestamp`,
		GROUP_CONCAT(prescricao) AS texto,
		last_update,
		updated_by,
		( SELECT nome_completo FROM MEDICOS WHERE token = medico_token ) AS usuario_nome,
		( SELECT especialidade FROM MEDICOS WHERE token = medico_token ) AS especialidade,
		'' AS periodo,
		'' AS semana,
		agenda_token AS token,
		GROUP_CONCAT(produto_id) AS remedio,
		'PRESCRICAO' AS tipo,
        '' AS doc_file
	FROM
		PRESCRICOES 
	WHERE
	paciente_token = :token
	GROUP BY agenda_token
	) ORDER BY timestamp DESC");
              $stmt->bindValue(':token', $_GET['token']);
              $stmt->execute();
                
            $dados = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach($dados as $prontuario) {
                
                $txt = implode('<br>', array_map(function($key){
                    return base64_decode($key);
                }, explode(',', $prontuario->texto)));
                
                $remedios = count(explode(',', $prontuario->remedio));
                
                if($prontuario->tipo == 'PRESCRICAO') {
                    $esp = $especialidades[$prontuario->especialidade];
                } else {
                    $esp = $prontuario->especialidade;
                }
                
               echo '<div class="box-row">
                        <div class="box-icon">
                            <img src="/assets/images/ico-paciente.svg" alt="paciente">
                        </div>
                        <div class="box-description">
                            <h6 class="titulo-h6">'.($prontuario->tipo == 'PRESCRICAO' ? 'RECEITUÁRIO':$prontuario->tipo).'</h6>
                            <strong>Data: '.date('d/m/Y', strtotime($prontuario->timestamp)).'</strong>
                            <small>'.($prontuario->tipo == 'PRESCRICAO' ? 'DR(a): '.$prontuario->usuario_nome:'Acompanhante: '.$prontuario->usuario_nome). ' (<b>'.$esp.'</b>)</small>
                            <span class="page-box-txt">'.($prontuario->tipo == 'PRESCRICAO' ? 'Prescrição de Medicamento' : 'Acompanhamento de Tratamento').'</span>
                        </div>
                        <div class="box-txt">'.($prontuario->tipo == 'PRESCRICAO' ? "$remedios medicamento(s)" : "<p><b>".($prontuario->semana > 0 && $prontuario->periodo > 0 ? "{$prontuario->periodo}ª Acompanhamento, {$prontuario->semana}ª Semana":"")."</b></p> $txt").'</div>
                        '.($prontuario->tipo == 'PRESCRICAO' ? '<a title="Baixar Receita" href="/agenda/prescricao/receita/'.$prontuario->token.'" download="RECEITA_'.$prontuario->token.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'').'
                        '.($prontuario->tipo == 'RECEITA' ? '<a title="Baixar Receita" href="/data/docs/'.$prontuario->doc_file.'" download="RECEITA_'.$prontuario->doc_file.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'').'
                        '.($prontuario->tipo == 'EXAME' ? '<a title="Baixar Exame" href="/data/docs/'.$prontuario->doc_file.'" download="EXAME_'.$prontuario->doc_file.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'').'
                    </div>';
            }
           ?>
		   