<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }

 $stmt2 = $pdo->prepare("SELECT *,(SELECT nome FROM ANAMNESE WHERE id = queixa_principal) AS queixa,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico,FLOOR(DATEDIFF(CURDATE(), data_nascimento) / 365.25) AS age FROM PACIENTES WHERE token = :token");
 $stmt2->bindValue(':token', $_GET['token']);

 $stmt2->execute();
 $_user = $stmt2->fetch(PDO::FETCH_OBJ);
 
 $presc = []; 
?>
<section class="main" id="user-main">
    <div class="flex-container">
        <div class="row dflex-column">
            <h3 class="form-title titulo-h1">Acompanhamento</h3>

            <div class="paciente-box" style="width: 100% !important">
                <div class="paciente-box-user">
                    <div>
                        <img src="<?=(Modules::getUserImage($_user->token))?>" height="140px" class="imagem-user" />
                    </div>
                </div>
                <div class="paciente-box-user">
                    <span class="crm-bg"><a targe="whatsapp"
                            href="https://api.whatsapp.com/send/?phone=<?=($_user->celular)?>"><img
                                title="Enviar mensagem via WhatsApp" src="/assets/images/icon-whatsapp.svg"
                                height="22px"><?=Str::formatPhoneNumber($_user->celular)?></a></span>
                    <h3><a href="/perfil/<?=($_user->token)?>"
                            title="Ver Perfil do Paciente"><?=( $_user->nome_completo)?></a></a></h3>
                    <p><b>Idade:</b> <?=($_user->age)?></p>
                    <p><b>Queixa:</b> <?=($_user->queixa ?? 'Não informado')?></p>
                    <p><b>Médico:</b> <?=($_user->medico ?? ' Não informado')?></p>
                </div>
            </div>

            <div class="toolbar-rtl" data-user="<?=($_GET['token'])?>">
                <input type="search" class="form-control form-search" id="prontuario-search" data-search=".page-box">
                <button onclick="addPrescFunc2()"><i class="fa fa-plus fa-2x"></i></button>
                <button onclick="relatorioAcompanhamento('.prontuario-check-item:checked', '<?=($_GET['token'])?>')"><i
                        class="fa-solid fa-hospital-user"></i></button>
            </div>
            <?php
            $especialidades = [];
            
            $sst = $pdo->query('SELECT id,nome FROM ESPECIALIDADES');
            $_especialidades = $sst->fetchAll(PDO::FETCH_ASSOC);
            
            
            foreach($_especialidades as $esp) {
                $especialidades[$esp['id']] = strtoupper($esp['nome']);
            }
            
            
            
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
    doc_file,
    proximo_acompanhamento,
    titulo_acompanhamento
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
        '' AS doc_file,
        '' AS proximo_acompanhamento,
        '' AS titulo_acompanhamento
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
                            <input type="checkbox" class="prontuario-check-item" value="'.base64_encode(json_encode(['id' => $prontuario->id, 'tipo' => $prontuario->tipo])).'">
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

                        '.($user->perms->editar_acompanhamento_prontuario == 1 && $prontuario->tipo == 'ACOMPANHAMENTO' ? '<a onclick="editPrescFunc(this)" data-action="editar-acompanhamento" title="Editar este Item" data-title="'.$prontuario->titulo_acompanhamento.'" data-periodo="'.$prontuario->periodo.'" data-anexo="'.$prontuario->doc_file.'" data-content="'.$prontuario->texto.'" data-tipo="'.$prontuario->tipo.'" data-proximo="'.$prontuario->proximo_acompanhamento.'"><img src="/assets/images/ico-edit.svg" style="border-radius: 28px" height="28px"></a>':'').'
                        '.($prontuario->tipo == 'ACOMPANHAMENTO' ? ($user->perms->deletar_acompanhamento_prontuario == 1 ? '<a delete-acompanhamento="this.item" title="Deletar este Item" data-id="'.$prontuario->id.'" data-href="/data/docs/'.$prontuario->doc_file.'"><img src="/assets/images/ico-trash.svg" style="border-radius: 28px" height="28px"></a>':''): '').'
                        </div>';
            }
           ?>
        </div>
    </div>
</section>