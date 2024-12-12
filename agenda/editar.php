<?php
$useWb = true;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['userObj'])) {
    try {
       $user = (object) $_SESSION['userObj'];
   } catch (PDOException $e) {
 
   }
}
$ag = $agenda->get($_SESSION['token']);

$stmt2 = $pdo->prepare("SELECT *,(SELECT nome FROM ANAMNESE WHERE id = queixa_principal) AS queixa,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico,FLOOR(DATEDIFF(CURDATE(), data_nascimento) / 365.25) AS age FROM PACIENTES WHERE token = :token");
$stmt2->bindValue(':token', $ag->paciente_token);

$stmt2->execute();
$_user = $stmt2->fetch(PDO::FETCH_OBJ);
$presc = [];

$wb = json_decode($ag->meet);

function strip_html($html) {
    $data = str_replace('<p>', '', $html);

    $data = str_replace('</p>', '???', $html);

    $data = strip_tags($data);

    return str_replace('???', '<br>', $data);
}
?>

<section class="main" id="user-main">
    <div class="flex-container">
        <iframe data-src="<?= $wb->hostRoomUrl ?>" id="wb-view"
            allow="camera; microphone; fullscreen; speaker; display-capture; compute-pressure"
            style="height: 600px;width: 600px">
        </iframe>
        <div class="wb-play">
            <button class="btn-play-wb"><img src="/assets/images/ico-play.svg" height="32px" class="icon-cli"> Iniciar
                Teleconsulta</button>
        </div>

        <form id="formPrescricao" action="/formularios/prescricao.add.php" method="POST" class="form-prescricao"
            data-id="<?= ($ag->token) ?>" <?= ($ag->status == 'EFETIVADO' ? ' data-dl="true" ' : 's') ?>>
            <h3 class="form-title titulo-h1">Prontuário</h3>

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
                    <p><a href="#formPrescricao"><img src="/assets/images/ico-live.svg" height="28px"
                                onclick="start_meet('<?=$wb->hostRoomUrl?>', '<?=$ag->token?>')"></a>
                    </p>
                </div>
            </div>



            <section id="tabControl1" class="tabControl" data-lock="false">
                <div class="tab-toolbar">
                    <span class="active" data-index="1" data-tab="tabControl1">Prescrição</span>
                    <span data-index="2" data-tab="tabControl1">Acompanhamento</span>
                    <span data-index="3" data-tab="tabControl1">Anexos</span>
                </div>

                <div class="tab active" data-index="1" data-tab="tabControl1">
                    <section class="form-grid area-full">
                        <section class="form-group">
                            <label for="nacionalidade">Prescrição</label>
                            <table id="tablePrescricao"
                                data-append="<?= ($user->perms->add_medicamentos == 1 ? 'true' : 'false') ?>"
                                data-doc="<?= ($ag->file_signed != '' ? 'true' : 'false') ?>"
                                data-file="<?= (basename($ag->file_signed)) ?>" class="prescricao"
                                data-token="<?= ($_GET['token']) ?>" data-user="<?= ($ag->paciente_token) ?>"
                                data-medico="<?= ($ag->medico_token) ?>"
                                data-dl="<?= (strlen($ag->file_signed) > 0) ?>">
                                <thead>
                                    <th width="32px">ID</th>
                                    <th>Data/Hora</th>
                                    <th>Prescrição</th>
                                    <th width="200px">Medicamento</th>
                                    <th width="64px">Frascos</th>
                                    <th width="80px">Ações</th>
                                </thead>
                                <tbody>
                                    <?php

                                    $stmt = $pdo->prepare('SELECT 
                                            *,
                                            (SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome
                                            FROM PRESCRICOES 
                                            WHERE agenda_token = :agt');
                                    $stmt->bindValue(':agt', $_GET['token']);
                                    $stmt->execute();

                                    $i = 1;

                                    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $item) {

                                        if ($item->produto_ref == 'MEDICAMENTOS') {
                                            $stmt2 = $pdo->prepare('SELECT * FROM MEDICAMENTOS WHERE id = :id');
                                            $stmt2->bindValue(':id', $item->produto_id);
                                            $stmt2->execute();
                                            $medicamento = $stmt2->fetch(PDO::FETCH_OBJ);
                                            $item->produto_nome = "{$medicamento->nome} - {$medicamento->conteudo}{$medicamento->unidade_medida}";
                                        }

                                        file_put_contents('presc.html', base64_decode($item->prescricao));

                                        $prescricao = strip_html(base64_decode($item->prescricao));
                                        $presc[] = strip_tags($prescricao);
                                        $ts = date('d/m/Y H:m', strtotime($item->timestamp));
                                        echo "<tr data-ref=\"{$item->produto_ref}\" data-id=\"" . $item->id . "\" data-id=\"" . $item->medico_token . "\" data-product=\"" . $item->produto_id . "\" data-frascos=\"" . $item->frascos . "\" data-prescricao=\"" . base64_encode(utf8_decode(base64_decode($item->prescricao))) . "\">";
                                        echo "<td data-set=\"id\"><div class=\"td-flex\"><input name=\"presc-item\" class=\"presc-item\" type=\"checkbox\" value=\"{$item->id}\"> {$i}</div></td>";
                                        echo "<td data-set=\"timestamp\">{$ts}</td>";
                                        echo "<td data-set=\"prescricao\">{$prescricao}</td>";
                                        echo "<td data-set=\"produto_nome\">{$item->produto_nome}</td>";
                                        echo "<td data-set=\"frascos\">{$item->frascos}</td>";
                                        echo '<td class="td-act">
                                                    <div class="btns-act">
                                                        <div class="btns-table">';
                                        echo "<button type=\"button\" title=\"Cancelar Prescriço\" class=\"btn-action\" onclick=\"action_btn_presc(this)\" data-action=\"presc-delete\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                                        echo "<button style=\"display: none;\" type=\"button\" title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_presc(this)\" data-action=\"presc-edit\"><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                                        echo '</div>
                                                    </div>
                                                    </td>';

                                        echo "</tr>";

                                        $i++;
                                    }
                                    $ag->prescricao = $presc;
                                    ?>
                                </tbody>
                            </table>
                        </section>
                    </section>
                </div>
                <div class="tab" data-index="2" data-tab="tabControl1" id="prescricao-ag">
                    <?php
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
                        doc_file,
                        doc_tipo,
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
                            '' AS doc_file,
                            '' AS doc_tipo,
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

                        $txt = implode('<br>', array_map(function ($key) {
                            return base64_decode($key);
                        }, explode(',', $prontuario->texto)));

                        $remedios = count(explode(',', $prontuario->remedio));

                        echo '<div class="box-row" data-token="'.$prontuario->id.'">
                                            <div class="box-icon">
                                                <img src="/assets/images/ico-paciente.svg" alt="paciente">
                                            </div>
                                            <div class="box-description">
                                                <h6 class="titulo-h6">' . ($prontuario->tipo == 'PRESCRICAO' ? 'RECEITUÁRIO' : $prontuario->doc_tipo) . '</h6>
                                                <strong>Data: ' . date('d/m/Y', strtotime($prontuario->timestamp)) . '</strong>
                                                <small>' . ($prontuario->tipo == 'PRESCRICAO' ? 'DR(a): ' . $prontuario->usuario_nome : 'Acompanhante: ' . $prontuario->usuario_nome) . '</small>
                                                <span class="page-box-txt">' . ($prontuario->tipo == 'PRESCRICAO' ? 'Prescrição de Medicamento' : 'Acompanhamento de Tratamento') . '</span>
                                            </div>
                                            <div class="box-txt">' . ($prontuario->tipo == 'PRESCRICAO' ? "$remedios medicamento(s)" : $txt) . '</div>
                                            ' . ($prontuario->tipo == 'PRESCRICAO' ? ($prontuario->file_signed != '' ? '<a title="Baixar Receita" href="/agenda/prescricao/receita/' . $prontuario->token . '" download="RECEITA_' . $prontuario->token . '"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>' : '<a disabled title="Receita Não Assinada Digitalmente"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>') : (strtolower($prontuario->doc_tipo)  == 'receita' || strtolower($prontuario->doc_tipo) == 'exame' ? '<a title="Baixar Anexo" href="/data/docs/'.$prontuario->doc_file.'" download="'.$prontuario->doc_file.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'')) . '
                                        </div>';
                    }
                    ?>
                </div>
                <div class="tab" data-index="3" data-tab="tabControl1">
                    <h1 class="titulo-h1">Anexos Enviados pelo Paciente</h1>
                    <?php
                $stmt = $pdo->prepare("SELECT * FROM ANEXOS_PACIENTES WHERE paciente_token = :token");
                $stmt->bindValue(':token', $ag->paciente_token);
                $stmt->execute();
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);

                foreach ($dados as $anexo) {
                    echo '<div class="box-row" data-token="'.$anexo->id.'">
                                            <div class="box-icon">
                                                <img src="/assets/images/ico-paciente.svg" alt="paciente">
                                            </div>
                                            <div class="box-description">
                                                <h6 class="titulo-h6">' . $anexo->doc_type . '</h6>
                                                <strong>Data: ' . date('d/m/Y', strtotime($anexo->timestamp)) . '</strong>
            
                                            </div>
                                            <span class="box-txt">' . base64_decode($anexo->descricao) . '</span>
                                                <a title="Baixar Receita" href="' . $anexo->doc_path . '" download="' . basename($anexo->path) . '"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>
                                            </div>';
                }
                    ?>
                </div>
            </section>
    </div>
</section>
</form>
</div>
</section>