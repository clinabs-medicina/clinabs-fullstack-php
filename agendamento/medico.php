<?php
function getUnidades(PDO $pdo) {
    $agendamentos_unidades = [];

    $today = $_GET['data'];

    $unidades = [];

    $_unidades = $pdo->query("SELECT nome, token FROM UNIDADES")->fetchAll(PDO::FETCH_ASSOC);

    foreach($_unidades as $unidade) {
        $unidades[$unidade['token']] = $unidade['nome'];
    }

    $stmt = $pdo->prepare("SELECT data_agendamento,unidade_atendimento,modalidade FROM `AGENDA_MED` WHERE `data_agendamento` LIKE :dt AND `unidade_atendimento` LIKE '%UNIDADES%'");
    $stmt->bindValue(':dt', "{$today}%");
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $array = [];

        $dados = json_decode($row['unidade_atendimento'], true);

        $array['nome'] = $unidades[$dados['token']];

        if(isset($row['unidade_atendimento'])) {
            $item = $row;

            $un = $pdo->query('SELECT * FROM UNIDADES WHERE token = "'.$dados['token'].'"')->fetch(PDO::FETCH_ASSOC);
            $un['modalidade'] = $row['modalidade'];

            $agendamentos_unidades[$dados['token']][$item['data_agendamento']] = $un;
        }
    }

    return $agendamentos_unidades;
}

function get_enderecos($pdo) {
    $stmt = $pdo->query("SELECT nome, logradouro, numero, cidade, bairro, cep, uf, token FROM `UNIDADES` UNION SELECT nome, logradouro, numero, cidade, bairro, cep, uf, token FROM `ENDERECOS`");

    $enderecos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $enderecos[$row['token']] = $row;
    }           

    return $enderecos;
}

function getU($pdo, $token) {
    try {
        $stmt = $pdo->prepare("SELECT nome,token,'UNIDADES' AS tipo FROM `UNIDADES` WHERE `token` = :token UNION SELECT nome,token,'ENDERECOS' AS tipo FROM `ENDERECOS` WHERE `token` = :token");
        $stmt->bindValue(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

$_agendamentos_unidades = getUnidades($pdo);

$_enderecos = get_enderecos($pdo);

?>
<section class="main">
    <section>
        <h1 class="titulo-h1">Agendamento</h1>
        <h5><b>Data do Agendamento:</b> <?=date('d/m/Y', strtotime($_GET['data']))?></h5>
    </section>

    <section class="list-medical-flex">
        <?php
             ini_set('display_errors', 1);
             error_reporting(1);
             $items = [];

             $c = [];
             
             if(isset($_GET['data'])) {
                 $DATE = $_GET['data'];
                try {
                 error_log("Valor da variável \$DATE: $DATE\r\n" . PHP_EOL);
                } catch (PDOException $e) {
                }
            
                 if(!isset($_GET['filter_ag'])) {
                     $sql = "SELECT DISTINCT
                        	TB1.medico_token,
                        	( SELECT COUNT(*) FROM ENDERECOS WHERE user_token = TB1.medico_token AND tipo_endereco = 'ATENDIMENTO' ) AS presencialDisponivel,
                        	TB1.calendario,
                        	TB2.nome_completo AS medico_nome,
                        	TB2.tipo_conselho,
                        	TB2.uf_conselho,
                        	(SELECT nome FROM ESPECIALIDADES WHERE id = TB2.especialidade) AS especialidade,
                        	TB2.num_conselho,
                        	TB2.identidade_genero,
                        	TB2.`status`,
                        	TB2.valor_consulta AS valor_presencial,
                        	TB2.duracao_atendimento,
                        	TB2.valor_consulta_online AS valor_online,
                            TB2.tempo_limite_online,
                            TB2.tempo_limite_presencial
                        FROM
                        	AGENDA_MEDICA AS TB1,
                        	MEDICOS AS TB2 
                        WHERE
                        	calendario LIKE '%\"{$DATE}\"%' 
                        	AND TB1.medico_token = TB2.token 
                        	AND TB2.`status` = 'ATIVO'";
                 }
                  else {
                     if(isset($_GET['filter_ag']) && $_GET['filter_ag'] == 'medicos') {
                         $sql = "SELECT DISTINCT
                        	TB1.medico_token,
                        	( SELECT COUNT(*) FROM ENDERECOS WHERE user_token = TB1.medico_token AND tipo_endereco = 'ATENDIMENTO' ) AS presencialDisponivel,
                        	TB1.calendario,
                        	TB2.nome_completo AS medico_nome,
                        	TB2.tipo_conselho,
                        	TB2.uf_conselho,
                        	(SELECT nome FROM ESPECIALIDADES WHERE id = TB2.especialidade) AS especialidade,
                        	TB2.num_conselho,
                        	TB2.identidade_genero,
                        	TB2.`status`,
                        	TB2.valor_consulta AS valor_presencial,
                        	TB2.duracao_atendimento,
                        	TB2.valor_consulta_online AS valor_online,
                            TB2.tempo_limite_online,
                            TB2.tempo_limite_presencial
                        FROM
                        	AGENDA_MEDICA AS TB1,
                        	MEDICOS AS TB2 
                        WHERE
                        	calendario LIKE '%\"{$DATE}\"%'
                        	AND TB2.id = '{$_GET['select_filter']}'
                        	AND TB1.medico_token = TB2.token 
                        	AND TB2.`status` = 'ATIVO'";
                     }
                     else if(isset($_GET['filter_ag']) && $_GET['filter_ag'] == 'especialidades') {
                         $sql = "SELECT DISTINCT
                        	TB1.medico_token,
                        	( SELECT COUNT(*) FROM ENDERECOS WHERE user_token = TB1.medico_token AND tipo_endereco = 'ATENDIMENTO' ) AS presencialDisponivel,
                        	TB1.calendario,
                        	TB2.nome_completo AS medico_nome,
                        	TB2.tipo_conselho,
                        	TB2.uf_conselho,
                        	(SELECT nome FROM ESPECIALIDADES WHERE id = TB2.especialidade) AS especialidade,
                        	TB2.num_conselho,
                        	TB2.identidade_genero,
                        	TB2.`status`,
                        	TB2.valor_consulta AS valor_presencial,
                        	TB2.duracao_atendimento,
                        	TB2.valor_consulta_online AS valor_online,
                            TB2.tempo_limite_online,
                            TB2.tempo_limite_presencial
                        FROM
                        	AGENDA_MEDICA AS TB1,
                        	MEDICOS AS TB2 
                        WHERE
                        	calendario LIKE '%\"{$DATE}\"%'
                        	AND TB1.medico_token = TB2.token 
                        	AND TB2.`status` = 'ATIVO'
                        	AND TB2.especialidade = '".$_GET['select_filter']."'";
                     }
                     
                     else if(isset($_GET['filter_ag']) && $_GET['filter_ag'] == 'anamnese') {
                         $sql = "SELECT DISTINCT
                        	TB1.medico_token,
                        	( SELECT COUNT(*) FROM ENDERECOS WHERE user_token = TB1.medico_token AND tipo_endereco = 'ATENDIMENTO' ) AS presencialDisponivel,
                        	TB1.calendario,
                        	TB2.nome_completo AS medico_nome,
                        	TB2.tipo_conselho,
                        	TB2.uf_conselho,
                        	(SELECT nome FROM ESPECIALIDADES WHERE id = TB2.especialidade) AS especialidade,
                        	TB2.num_conselho,
                        	TB2.identidade_genero,
                        	TB2.`status`,
                        	TB2.valor_consulta AS valor_presencial,
                        	TB2.duracao_atendimento,
                        	TB2.valor_consulta_online AS valor_online,
                            TB2.tempo_limite_online,
                            TB2.tempo_limite_presencial
                        FROM
                        	AGENDA_MEDICA AS TB1,
                        	MEDICOS AS TB2 
                        WHERE
                        	calendario LIKE '%\"{$DATE}\"%'
                        	AND TB1.medico_token = TB2.token 
                        	AND TB2.`status` = 'ATIVO'
                        	AND TB2.anamnese LIKE '%\"".$_GET['select_filter']."\"%'";
                     }
                 }

                 
                

                        	$stmt = $pdo->prepare($sql);
                        	$stmt->execute();
                        	
                        	$items = $stmt->fetchAll(PDO::FETCH_OBJ);


                        	foreach($items as $item) {
                        	    $id = uniqid();

                                $online_item = false;
                                $presencial_item = false;


                        	    $agendamentos = json_decode($item->calendario, true)[$DATE];

                                try {
                                    $unidade_token = json_decode($item->calendario, true)['token'];
                                    $unidade_tipo = json_decode($item->calendario, true)['table'];
                                } catch (Exception $e) {
                                    $unidade_token = null;
                                    $unidade_tipo = null;
                                }

                                $prefixo = strtoupper($item->identidade_genero) == 'FEMININO' ? 'Dra.':'Dr.';


                                $stmtx1 = $pdo->prepare("SELECT
                                                                            data_agendamento,
                                                                          ( SELECT nome_completo FROM MEDICOS WHERE token = medico_token ) AS medico_nome,
                                                                          ( SELECT valor_consulta FROM MEDICOS WHERE token = medico_token ) AS valor_presencial,
                                                                          ( SELECT valor_consulta_online FROM MEDICOS WHERE token = medico_token ) AS valor_online,
                                                                          ( SELECT nome_completo FROM PACIENTES WHERE token = paciente_token ) AS paciente_nome,
                                                                          ( SELECT nome_completo FROM PACIENTES WHERE token = paciente_token ) AS paciente_nome,
                                                                          ( SELECT tempo_limite_online FROM MEDICOS WHERE token = medico_token ) AS tempo_limite_online,
                                                                          ( SELECT tempo_limite_presencial FROM MEDICOS WHERE token = medico_token ) AS tempo_limite_presencial
                                                                        FROM
                                                                             AGENDA_MED
                                                                        WHERE
                                                                            data_agendamento LIKE :date
                                                                        AND
                                                                            medico_token = :medico_token");
                                                                                
                                                    $stmtx1->bindValue(':date', date('Y-m-d', strtotime($_GET['data'])).'%');
                                                    $stmtx1->bindValue(':medico_token', $item->medico_token);
                                                    
                                                    $stmtx1->execute();

                                                    $hrs = [];
                                                    $ags = [];
                                                    
                                                    foreach( $stmtx1->fetchAll(PDO::FETCH_ASSOC) as $dt) {
                                                        $hrs[] = $dt['data_agendamento'];
                                                       
                                                        

                                                        if(!in_array(date('Y-m-d H:i:s', strtotime($_GET['data'].' '.$ag['time'])), $hrs) && strtotime($_GET['data'].' '.$ag['time']) > strtotime(date('Y-m-d H:i')))
                                                            {
                                                                $ags[] = $ag;
                                                            }
                                                    }

                                                    foreach($agendamentos as $ag) {
                                                          if(!in_array(date('Y-m-d H:i:s', strtotime($_GET['data'].' '.$ag['time'])), $hrs) && strtotime($_GET['data'].' '.$ag['time']) > strtotime(date('Y-m-d H:i')))
                                                            {
                                                                $ags[] = $ag;

                                                                if($ag['online'] == "true") {
                                                                    $online_item = true;
                                                                }
                                                                
                                                                if($ag['presencial'] == "true") {
                                                                    $presencial_item = true;
                                                                }
                                                            }
                                                             
                                                    }

                                                    
                                              
                        	   if(count($ags) >= 1) {
                                $c[] = $item;

                                $hrs = [];

                                $include = false;


                                foreach($agendamentos as $ag) {
                                    $online = $ag['online'];
                                    $presencial = $ag['presencial'];

                                    $unidade_token = $ag['token'];
                                    
         
                                      if(!in_array(date('Y-m-d H:i', strtotime($_GET['data'].' '.$ag['time'])), $hrs) && strtotime($_GET['data'].' '.$ag['time']) > strtotime(date('Y-m-d H:i')))
                                        {
                                            $xx = $pdo->query("SELECT data_agendamento FROM AGENDA_MED WHERE data_agendamento = '{$_GET["data"]} {$ag["time"]}' AND medico_token = '{$item->medico_token}'");
                                            
                                            $data_agendamento = "{$_GET['data']} {$ag['time']}:00";
                                    

                                            if($xx->rowCount() == 0){
                                                $online  = $online ? 'SIM':'NÃO';
                                                $presencial = $presencial ? 'SIM':'NÃO';
                                                $title = '';
                                                $time_left = (strtotime($ag['date'].' '.$ag['time']) - time());
                                                $time_limit = ($item->tempo_limite_online * 60);
                                                
                                                if($time_left >= $time_limit && $ag['online'] && $ag['presencial']) {
                                                    $include = true;
                                                } else {
                                                    if($ag['presencial']  && !isset($_agendamentos_unidades[$ag['endereco']][$data_agendamento])) {
                                                        if($time_left >= $time_limit) {
                                                            $include = true;
                                                        }
                                                    } else {
                                                        if($time_left >= $time_limit) {
                                                            $include = true;
                                                        }
                                                    }
                                                }
                                            }
                                      }
                                }

                                

                                if($include) {
                        	    echo '<form class="box-mediclist" method="GET" action="/agenda/consulta" id="form_'.$id.'">
                                            <div class="listmedic-box-esq">
                                                <div class="listmedic-box-esq-user">
                                                    <div><img src="'.Modules::getUserImage($item->medico_token).'" height="140px" class="imagem-user" alt=""></div>
                                                    <!-- <span><img src="/assets/images/ico-heart.svg" alt="">1000 likes</span> -->
                                            </div>
                                            <div class="listmedic-box-dir-user">
                                                <span class="crm-bg">'.($item->tipo_conselho.' '.$item->num_conselho.' '.$item->uf_conselho).'</span>
                                                <h4>'.$prefixo.' '.$item->medico_nome.'</h4>
                                                <hr>
                                                <p class="listmedic-box-dir-subtitle">'.$item->especialidade.'</p>
                                                <div class="listmedic-box-dir-ico location-clinic">
                                                    <img src="/assets/images/ico-map.svg" alt="">
                                                    <div class="street-info street-item-info"></div>
                                                </div>
                                                <div class="listmedic-box-dir-ico listmedic-values" style="min-width: 150px">
                                                    
                                                </div>
                                                <div class="listmedic-boxlink-box">
                                                <input'.($presencial_item == true && $online_item == false ? ' checked':'').' required class="btn-modalidade" type="radio" name="atendimento" style="display: none" id="atendimento_presencial_'.$id.'" value="presencial" data-value="'.intval($item->valor_presencial).'" data-duration="'.$item->duracao_atendimento.'">
                                                <input'.($presencial_item == false && $online_item == true ? ' checked':'').' required class="btn-modalidade" type="radio" name="atendimento" style="display: none" id="atendimento_online_'.$id.'" value="online" data-value="'.intval($item->valor_online ?? $item->valor_presencial).'" data-duration="'.$item->duracao_atendimento.'">';
                                                    echo $presencial_item && (strtotime(date('H:i'))) >= strtotime($horario_funcionamento['inicio']) && (strtotime(date('H:i'))) <= strtotime($horario_funcionamento['fim']) ? '<label'.($presencial_item == true && $online_item == false ? ' checked':'').' for="atendimento_presencial_'.$id.'" class="listmedic-boxlink" data-for="atendimento" data-form="form_'.$id.'" data-value="presencial">PRESENCIAL</label>':'';
                                                    echo $online_item ? '<label'.($presencial_item == false && $online_item == true ? ' checked':'').' for="atendimento_online_'.$id.'" class="listmedic-boxlink" data-for="atendimento" data-form="form_'.$id.'" data-value="online">ONLINE</label>':'';
                                                echo '</div>
                                            </div>
                        
                                        </div>

                                        <div class="listmedic-box-dir">
                                            <div class="listmedic-box-dir-title">
                                                <h3>Horários Disponíveis</h3>
                                                <hr>
                                            </div>
  
                                            <div class="listmedic-box-dir-boxtime item-disabled">';

                                            
                                            $xy = 0;

                                                foreach($agendamentos as $ag) {
                        
                                                      if(!in_array(date('Y-m-d H:i', strtotime($_GET['data'].' '.$ag['time'])), $hrs) && strtotime($_GET['data'].' '.$ag['time']) > strtotime(date('Y-m-d H:i')))
                                                        {
                                                            $xx = $pdo->query("SELECT data_agendamento FROM AGENDA_MED WHERE data_agendamento = '{$_GET["data"]} {$ag["time"]}' AND medico_token = '{$item->medico_token}'");
                                                            
                                                            $data_agendamento = "{$_GET['data']} {$ag['time']}:00";

                                                            if($_agendamentos_unidades[$ag["endereco"]][$data_agendamento]["modalidade"] == 'PRESENCIAL') {
                                                                $ag['presencial'] = false;
                                                            }
        
                                                            if($_agendamentos_unidades[$ag['endereco']][$data_agendamento]['modalidade'] == 'ONLINE') {
                                                                $ag['online'] = false;
                                                            }

                                                          
                                                                $online  = $online ? 'SIM':'NÃO';
                                                                $presencial = $presencial ? 'SIM':'NÃO';
                                                                $title = '';
                                                                $time_left = (strtotime($ag['date'].' '.$ag['time']) - time());
                                                                $time_limit = ($item->tempo_limite_online * 60);

                                                         
                                                                if($time_left >= $time_limit && $ag['online'] && $ag['presencial'] && (strtotime(date('H:i'))) >= strtotime($horario_funcionamento['inicio']) && (strtotime(date('H:i'))) < strtotime($horario_funcionamento['fim'])) {
                                                                    $xy++;
                                                                    echo '<div data-date="'.date('d/m/Y', strtotime($_GET['data'])).'" title="'.$title.'" class="ag-item-time-btn" data-street-name="'.$ag['endereco_nome'].'" data-street="'.$ag['desc'].'" data-online="'.($ag['online']).'" data-presencial="'.($ag['presencial']).'">
                                                                        <img src="/assets/images/ico-agenda-clock.svg" alt="" height="25px">'.$ag['time'].'
                                                                    </div>';
                                                                } else {
                                                                    if($ag['presencial'] && !isset($_agendamentos_unidades[$ag['endereco']][$data_agendamento])) {
                                                                        if($time_left >= $time_limit) {
                                                                            $xy++;
                                                                            echo '<div data-date="'.date('d/m/Y', strtotime($_GET['data'])).'" title="'.$title.'" class="ag-item-time-btn" data-street-name="'.$ag['endereco_nome'].'" data-street="'.$ag['desc'].'" data-online="'.($ag['online']).'" data-presencial="'.($ag['presencial']).'">
                                                                                <img src="/assets/images/ico-agenda-clock.svg" alt="" height="25px">'.$ag['time'].'
                                                                            </div>';
                                                                        }
                                                                    } else {
                                                                        if($time_left >= $time_limit) {
                                                                            $xy++;
                                                                            echo '<div data-date="'.date('d/m/Y', strtotime($_GET['data'])).'" title="'.$title.'" class="ag-item-time-btn" data-street-name="'.$ag['endereco_nome'].'" data-street="'.$ag['desc'].'" data-online="'.($ag['online']).'" data-presencial="'.($ag['presencial']).'">
                                                                                <img src="/assets/images/ico-agenda-clock.svg" alt="" height="25px">'.$ag['time'].'
                                                                            </div>';
                                                                        }
                                                                    }
                                                                }
                                                            
                                                      }
                                                }
                  
                                        
                                            echo '</div>
                        
                                        </div>
                                        <input type="hidden" name="redirect" value="/agenda/consulta">
                                        <input type="hidden" name="medico_token" value="'.trim($item->medico_token).'">
                                        <input type="hidden" name="endereco" value="">
                                        <input type="hidden" name="tipo_endereco" value="">
                                        
                                        <input type="hidden" name="valor_consulta_presencial" value="'.trim($item->valor_presencial).'">
                                        <input type="hidden" name="valor_consulta_online" value="'.trim($item->valor_online).'">
                                        <input type="hidden" name="duracao_consulta" value="'.trim($item->duracao_atendimento).'"> 
                                        <input type="hidden" name="data_agendamento" value="'.$_GET['data'].'">
                                        <input type="hidden" id="no-show" value="'.($xy > 0 ? 'visible':'hidden').'">
                                    </form>';
                                            }
                            }
                        }
             }

          if(count($c) == 0) {
            echo '
            <div class="msg-sys">
            <div class="msg-sys-flex">
              <figure>
              <svg width="100" height="100" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M40.84 81.69C63.35 81.69 81.69 63.35 81.69 40.84C81.69 18.36 63.35 -0.0100021 40.84 -0.0100021C18.36 -0.0100021 -0.0100098 18.36 -0.0100098 40.84C-0.0100098 63.35 18.36 81.69 40.84 81.69Z" fill="#049B8A"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M27.35 35.4C30.27 35.4 32.68 33.02 32.68 30.1C32.68 27.15 30.27 24.77 27.35 24.77C24.43 24.77 22.02 27.15 22.02 30.1C22.02 33.02 24.43 35.4 27.35 35.4Z" fill="white"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M57.08 35.4C60 35.4 62.41 33.02 62.41 30.1C62.41 27.15 60 24.77 57.08 24.77C54.16 24.77 51.75 27.15 51.75 30.1C51.75 33.02 54.16 35.4 57.08 35.4Z" fill="white"/>
              <path d="M23.07 60.31C22.87 60.82 22.28 61.05 21.77 60.85C21.26 60.65 21.03 60.06 21.23 59.55C23.89 53.23 28.34 49.15 33.39 47.13C35.97 46.08 38.69 45.6 41.41 45.66C44.13 45.69 46.85 46.26 49.38 47.3C54.45 49.43 58.85 53.56 61.23 59.57C61.43 60.08 61.17 60.65 60.66 60.85C60.15 61.05 59.58 60.79 59.38 60.28C57.2 54.84 53.2 51.07 48.61 49.14C46.31 48.18 43.85 47.67 41.38 47.64C38.94 47.61 36.45 48.04 34.12 48.97C29.56 50.79 25.5 54.53 23.07 60.31Z" fill="white"/>
              </svg>  
              </figure>
              <p><strong>OPS!</strong> Não há horários disponíveis para este serviço no momento.<br>Por favor, tente novamente em breve.</p>
            </div>
        </div>';
        }
        
       ?>
    </section>