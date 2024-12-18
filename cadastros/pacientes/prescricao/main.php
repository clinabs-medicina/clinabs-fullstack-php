<?php
 $stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE token = :token");
 $stmt2->bindValue(':token', $_GET['token']);

 $stmt2->execute();
 $_user = $stmt2->fetch(PDO::FETCH_OBJ);
 $presc = [];
?>

<section class="main" id="user-main">
  <div class="flex-container">
    <form id="formPrescricao" action="/formularios/prescricao.add.sr.php" method="POST" class="form-prescricao">
            <h3 class="form-title titulo-h1">Prescrição</h3>
            <section id="tabControl1" class="tabControl" data-lock="false">
                <div class="tab-toolbar">
                    <span class="active" data-index="1" data-tab="tabControl1">Prescrição</span>
                    <span data-index="2" data-tab="tabControl1">Acompanhamento</span>
                    <span data-index="3" data-tab="tabControl1">Mensagens</span>
                </div>
                
                <section class="carrinho-header user-profile">
                    <div class="profile-details">
                        <img src="<?=(Modules::getUserImage($_user->token))?>">
                        <div class="details">
                            <h2><?=($_user->nome_completo)?></h2>
                            <h4><?=($_user->email)?></h4>
                            <h6></h6>
                            <small><?=Modules::formatPhone($_user->celular)?></small>
                        </div>
                    </div>
                </section>
        
                <!-- passo1 -->
                <div class="tab active" data-index="1" data-tab="tabControl1">
                    <section class="form-grid area-full">
                        <section class="form-group">
                            <label for="nacionalidade">Prescrição</label>
                                <table id="tablePrescricaoSR" class="prescricao" data-token="<?=($_GET['token'])?>" data-user="<?=($_GET['token'])?>" data-medico="<?=($user->token)?>">
                                    <thead>
                                        <th width="32px"></th>
                                        <th>Data/Hora</th>
                                        <th>Prescrição</th>
                                        <th width="200px">Medicamento</th>
                                        <th width="64px">Frascos</th>
                                        <th width="80px">Ações</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        
                                            $stmt = $pdo->prepare('SELECT 
                                            id, 
                                        	paciente_token, 
                                        	medico_token, 
                                        	`timestamp`, 
                                        	prescricao, 
                                        	produto_id, 
                                        	frascos,
                                            (SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome,
                                            "true" as isEditable,
                                            "true" as tipo
                                            FROM PRESCRICOES_SR
                                            WHERE paciente_token = :agt
                                            UNION (
                                                SELECT 
                                             id, 
                                        	paciente_token, 
                                        	medico_token, 
                                        	`timestamp`, 
                                        	prescricao, 
                                        	produto_id, 
                                        	frascos,
                                            (SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome,
                                            "false" as isEditable,
                                            "false" as tipo
                                            FROM PRESCRICOES
                                            WHERE paciente_token = :agt
                                            )');
                                            $stmt->bindValue(':agt', $_GET['token']);
                                            $stmt->execute();
    
                                            $i = 1;
    
                                            foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $item) {
                                                $prescricao = base64_decode($item->prescricao);
                                                $presc[] = strip_tags($prescricao);
                                                $ts = date('d/m/Y H:m', strtotime($item->timestamp));
                                                
                                                $pd = date('Y-m-d', strtotime($item->timestamp));
                                                $cd = date('Y-m-d');
                                                
                                                $checked = ($cd == $pd) ? ' checked':' disabled';
                                                
                                               if(date('d/m/Y', strtotime($item->timestamp)) == date('d/m/Y')){
                                                    echo "<tr data-id=\"".$item->id."\" data-id=\"".$item->medico_token."\" data-product=\"".$item->produto_id."\" data-frascos=\"".$item->frascos."\" data-prescricao=\"".$item->prescricao."\">";
                                                    echo $item->tipo == 'presc_sr' ? "<td data-set=\"id\"><input class=\"checkbox-item presc-id\" type=\"checkbox\" name=\"prescricao_id[]\" value=\"{$item->id}\"></td>":"<td data-set=\"id\"><input deisabled class=\"checkbox-item presc-id\" type=\"checkbox\" name=\"prescricao_id[]\" value=\"{$item->id}\"></td>";
                                                    echo "<td data-set=\"timestamp\">{$ts}</td>";
                                                    echo "<td data-set=\"prescricao\">{$prescricao}</td>";
                                                    echo "<td data-set=\"produto_nome\">{$item->produto_nome}</td>";
                                                    echo "<td data-set=\"frascos\">{$item->frascos}</td>";
                                                    echo '<td class="td-act">
                                                    <div class="btns-act">
                                                        <div class="btns-table">';
                                                        echo "<button ".($item->isEditable == 'true' ? '':'disabled ')."type=\"button\" title=\"Cancelar Prescrição\" class=\"btn-action\" onclick=\"action_btn_presc(this)\" data-action=\"presc-delete\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                                                        echo "<button ".($item->isEditable == 'true' ? '':'disabled ')."type=\"button\" title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_presc(this)\" data-action=\"presc-edit\"><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                                                echo '</div>
                                                    </div>
                                                    </td>';
                                
                                                echo "</tr>";
                                               }
    
                                                $i++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </section>
                        </section>
                </div>
                <div class="tab" data-index="2" data-tab="tabControl1" id="prescricao-ag">
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
                    	( SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token UNION(
                    	    SELECT nome_completo FROM MEDICOS WHERE token = funcionario_token) 
                    	) AS usuario_nome,
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
                                    if($prontuario->tipo == 'PRESCRICAO') {
                    $esp = $especialidades[$prontuario->especialidade];
                } else {
                    $esp = $prontuario->especialidade;
                }
                
                                    $txt = implode('<br>', array_map(function($key){
                                        return base64_decode($key);
                                    }, explode(',', $prontuario->texto)));
                                    
                                    $remedios = count(explode(',', $prontuario->remedio));
                                    
                                   echo '<div class="box-row">
                                            <div class="box-icon">
                                                <img src="/assets/images/ico-paciente.svg" alt="paciente">
                                            </div>
                                            <div class="box-description">
                                                <h6 class="titulo-h6">'.($prontuario->tipo == 'PRESCRICAO' ? 'RECEITUÁRIO':$prontuario->tipo).'</h6>
                                                <strong>Data: '.date('d/m/Y', strtotime($prontuario->timestamp)).'</strong>
                                                <small>'.($prontuario->tipo == 'PRESCRICAO' ? 'DR(a): '.$prontuario->usuario_nome:'Acompanhante: '.$prontuario->usuario_nome).' (<b>'.$esp.'</b>)</small>
                                                <span class="page-box-txt">'.($prontuario->tipo == 'PRESCRICAO' ? 'Prescrição de Medicamento' : 'Acompanhamento de Tratamento').'</span>
                                            </div>
                                            <div class="box-txt">'.($prontuario->tipo == 'PRESCRICAO' ? "$remedios medicamento(s)" : $txt).'</div>
                                            '.($prontuario->tipo == 'PRESCRICAO' ? '<a title="Baixar Receita" href="/agenda/prescricao/receita/'.$prontuario->token.'" download="RECEITA_'.$prontuario->token.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'').'
                                            '.($prontuario->tipo == 'RECEITA' ? '<a title="Baixar Receita" href="/data/docs/'.$prontuario->doc_file.'" download="RECEITA_'.$prontuario->doc_file.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'').'
                                            '.($prontuario->tipo == 'EXAME' ? '<a title="Baixar Exame" href="/data/docs/'.$prontuario->doc_file.'" download="EXAME_'.$prontuario->doc_file.'"><img src="/assets/images/download.svg" style="border-radius: 28px" height="28px"></a>':'').'
                                        </div>';
                                }
                               ?>
                            </div>
                            <div class="tab" data-index="3" data-tab="tabControl1">
                                <?php
                                  $msgs = [];
                                  /*
                                  $msgs[0] = [
                                        'nome_completo' => 'DR. SEBASTIÃO DANILO VIEIRA ( CIRURGIA GERAL )',
                                        'data' => '25/05/2023 10:00',
                                        'text' => 'Valdir, está tudo certo com seu tratamento?',
                                        'user_token' => '65fb460097bb5'
                                      ];
                                      
                                      $msgs[1] = [
                                        'nome_completo' => 'VALDIR SILVA ( PACIENTE )',
                                        'data' => '25/05/2023 10:20',
                                        'text' => 'Sim Doutor, estou seguindo corretamente o tratamento!',
                                        'user_token' => '65ccc672db293'
                                      ];
                                      */
                                  
                                  foreach($msgs as $msg) {
                                      echo '<div class="box-row">
                                            <div class="box-icon">
                                                <img src="'.(Modules::getUserImage($msg['user_token'])).'" alt="paciente">
                                            </div>
                                            <div class="box-description">
                                                <h6 class="titulo-h6">'.$msg['nome_completo'].'</h6>
                                                <strong>Data: '.$msg['data'].'</strong>
                                                <p class="box-txt">'.$msg['text'].'</p>
                                            </div>
                                           
                                        </div>';
                                  }
                                ?>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </form>
    </div>
</section>