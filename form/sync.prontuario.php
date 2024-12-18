<?php
require_once '../config.inc.php';
$ag = $agenda->get($_GET['token']);
$wb = json_decode($ag->meet);

$stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE token = :token");
$stmt2->bindValue(':token', $ag->paciente_token);

$stmt2->execute();
$_user = $stmt2->fetch(PDO::FETCH_OBJ);

$stmt = $pdo->prepare("SELECT
                    	id,
                    	paciente_token,
                    	`timestamp`,
                    	texto AS texto,
                    	last_update,
                    	updated_by,
                    	( SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token UNION(
                    	    SELECT nome_completo FROM MEDICOS WHERE token = funcionario_token) 
                    	) AS usuario_nome,
                    	periodo,
                    	semana,
                    	'' AS token,
                        '' AS file_signed,
                    	'' AS remedio,
                    	'ACOMPANHAMENTO' AS tipo 
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
                    		'' AS periodo,
                    		'' AS semana,
                    		agenda_token AS token,
                            (SELECT file_signed FROM AGENDA_MED WHERE token = agenda_token) AS file_signed,
                    		GROUP_CONCAT(produto_id) AS remedio,
                    		'PRESCRICAO' AS tipo 
                    	FROM
                    		PRESCRICOES 
                    	WHERE
                    	paciente_token = :token
                    	GROUP BY agenda_token
                    	) ORDER BY timestamp DESC");
$stmt->bindValue(':token', $ag->paciente_token);
$stmt->execute();

$dados = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach ($dados as $prontuario) {
    $txt = implode(
        '<br>',
        array_map(function ($key) {
            return base64_decode($key);
        }, explode(',', $prontuario->texto))
    );

    $remedios = count(explode(',', $prontuario->remedio));

    echo '<div class="box-row">
                                            <div class="box-icon">
                                                <img src="/assets/images/ico-paciente.svg" alt="paciente">
                                            </div>
                                            <div class="box-description">
                                                <h6 class="titulo-h6">' .
        ($prontuario->tipo == 'PRESCRICAO' ? 'RECEITUÁRIO' : $prontuario->tipo) .
        '</h6>
                                                <strong>Data: ' .
        date('d/m/Y', strtotime($prontuario->timestamp)) .
        '</strong>
                                                <small>' .
        ($prontuario->tipo == 'PRESCRICAO' ? 'DR(a): ' . $prontuario->usuario_nome : 'Acompanhante: ' . $prontuario->usuario_nome) .
        '</small>
                                                <span class="page-box-txt">' .
        ($prontuario->tipo == 'PRESCRICAO' ? 'Prescrição de Medicamento' : 'Acompanhamento de Tratamento') .
        '</span>
                                            </div>
                                            <div class="box-txt">' .
        ($prontuario->tipo == 'PRESCRICAO' ? "$remedios medicamento(s)" : $txt) .
        '</div>
                                            ' .
        ($prontuario->tipo == 'PRESCRICAO'
            ? ($prontuario->file_signed != ''
                ? '<a title="Baixar Receita" href="/agenda/prescricao/receita/' . $prontuario->token . '" download="RECEITA_' . $prontuario->token . '"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>'
                : '<a disabled title="Receita Não Assinada Digitalmente"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>')
            : '') .
        '
                                        </div>';
}
