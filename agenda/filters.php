<?php
require_once('../config.inc.php');
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


if(isset($_SESSION['token']) && $user->tipo == 'FUNCIONARIO') {
  $query = "SELECT
  AGENDA_MED.data_agendamento, 
  AGENDA_MED.paciente_token, 
  (SELECT ANAMNESE.nome FROM ANAMNESE WHERE AGENDA_MED.anamnese = ANAMNESE.id) AS anamnese, 
  AGENDA_MED.medico_token, 
  AGENDA_MED.data_alteracoes,
  AGENDA_MED.duracao_agendamento, 
  AGENDA_MED.data_efetivacao,
  AGENDA_MED.data_cancelamento,
  AGENDA_MED.startTime,
  AGENDA_MED.endTime,
  AGENDA_MED.`status`, 
  AGENDA_MED.token, 
  AGENDA_MED.`timestamp`, 
  AGENDA_MED.prescricao,
  
  AGENDA_MED.meet, 
  AGENDA_MED.file_signed, 
  AGENDA_MED.valor, 
  AGENDA_MED.modalidade,
  (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_nome, 
  (SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS whatsapp, 
  (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_nome, 
  (SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_status
FROM
  AGENDA_MED
  WHERE paciente_token = '".$_SESSION['token']."';";
}
else if($user->tipo == 'PACIENTE') {
$query = "SELECT
AGENDA_MED.data_agendamento, 
AGENDA_MED.paciente_token, 
(SELECT ANAMNESE.nome FROM ANAMNESE WHERE AGENDA_MED.anamnese = ANAMNESE.id) AS anamnese, 
AGENDA_MED.medico_token, 
AGENDA_MED.duracao_agendamento, 
AGENDA_MED.data_efetivacao, 
AGENDA_MED.data_cancelamento,
AGENDA_MED.startTime,
AGENDA_MED.endTime,
AGENDA_MED.`status`, 
AGENDA_MED.token, 
AGENDA_MED.`timestamp`, 
AGENDA_MED.prescricao, 
AGENDA_MED.meet,
AGENDA_MED.file_signed,
AGENDA_MED.valor, 
AGENDA_MED.modalidade,
(SELECT payment_id FROM VENDAS WHERE code = token) as payment_id,
(SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_nome,
(SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS whatsapp, 
(SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_nome, 
(SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_status
FROM
AGENDA_MED
WHERE paciente_token = '".$user->token."';";
}

else if($user->tipo == 'MEDICO') {
$query = "SELECT
AGENDA_MED.data_agendamento, 
AGENDA_MED.paciente_token, 
(SELECT ANAMNESE.nome FROM ANAMNESE WHERE AGENDA_MED.anamnese = ANAMNESE.id) AS anamnese, 
AGENDA_MED.medico_token, 
AGENDA_MED.data_alteracoes,
AGENDA_MED.duracao_agendamento, 
AGENDA_MED.data_efetivacao, 
AGENDA_MED.data_cancelamento,
AGENDA_MED.startTime,
AGENDA_MED.endTime,
AGENDA_MED.`status`, 
AGENDA_MED.token, 
AGENDA_MED.`timestamp`, 
AGENDA_MED.prescricao, 
AGENDA_MED.meet, 
AGENDA_MED.file_signed,
AGENDA_MED.valor, 
AGENDA_MED.modalidade,
(SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_nome,
(SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS whatsapp, 
(SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_nome, 
(SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_status
FROM
AGENDA_MED
WHERE medico_token = '".$user->token."';";
}
else {
  $query = "SELECT
  AGENDA_MED.data_agendamento, 
  AGENDA_MED.paciente_token, 
  (SELECT ANAMNESE.nome FROM ANAMNESE WHERE AGENDA_MED.anamnese = ANAMNESE.id) AS anamnese, 
  AGENDA_MED.medico_token, 
  AGENDA_MED.data_alteracoes,
  AGENDA_MED.duracao_agendamento, 
  AGENDA_MED.data_efetivacao, 
  AGENDA_MED.data_cancelamento,
  AGENDA_MED.`status`,
  AGENDA_MED.token,
  AGENDA_MED.`timestamp`,
  AGENDA_MED.`startTime`,
  AGENDA_MED.`endTime`,
  AGENDA_MED.prescricao,
  AGENDA_MED.meet,
  AGENDA_MED.file_signed,
  AGENDA_MED.valor, 
  AGENDA_MED.modalidade,
  AGENDA_MED.duracao_agendamento, 
  (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token LIMIT 1) AS paciente_nome,
  (SELECT PACIENTES.id FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token LIMIT 1) AS paciente_id,
  (SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token LIMIT 1) AS whatsapp, 
  (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token LIMIT 1) AS medico_nome,
  (SELECT MEDICOS.id FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token LIMIT 1) AS medico_id, 
  (SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token LIMIT 1) AS payment_status
FROM
  AGENDA_MED";
} 

$rows = $pdo->query($query)->fetchAll(PDO::FETCH_OBJ);

$medicos = [];
$pacientes = [];
$status = [];

foreach($rows as $row) {
  $medicos[$row->medico_id] = $row->medico_nome;
  $status[] = $row->status;
  $pacientes[$row->paciente_id] = $row->paciente_nome;
}


            foreach($rows as $column) {
                
                $meet = json_decode($column->meet);
                $meet_url = $user->tipo != 'MEDICO' ? $meet->roomUrl : $meet->hostRoomUrl;
                $startTime = $column->startTime;
                $dt = ($column->data_cancelamento != '') ?  $column->data_cancelamento : $column->data_agendamento;
                $data_agendamento = ($column->data_cancelamento != '') ?  date('d/m/Y H:i', strtotime($column->data_cancelamento)) : date('d/m/Y H:i', strtotime($column->data_agendamento));
                $dur = $column->duracao_agendamento;
                $hora_agendamento = date('H:i', strtotime($dt));
                $data_max = date('H:i', strtotime("+{$dur} minute", strtotime($dt)));

                $paciente_nome = strlen($column->paciente_nome) > 20 ? trim(substr($column->paciente_nome, 0, 20)).'...' : $column->paciente_nome;
                $medico_nome = strlen($column->medico_nome) > 20 ? trim(substr($column->medico_nome, 0, 20)).'...' : $column->medico_nome;

                $allow_tc = false;

                $date = date('Y-m-d', strtotime($dt));
                $date_formated = date('d/m/Y', strtotime($date));


              echo "<tr data-date=\"{$date}\" data-paciente=\"{$column->paciente_token}\" data=ts=\"{$ts}\" id=\"{$column->token}\" data-stime=\"{$startTime}\">";
                echo "<td data-label=\"Data: \" width=\"260px\" class=\"ag-day\"><div class=\"calendar-day\">
                <img src=\"/assets/images/ico-calendar.svg\" height=\"32px\">
                <div class=\"datetime-column\">
                <span>{$date_formated} {$hora_agendamento}</span>
                <span class=\"calendar-time\">Das {$hora_agendamento} as {$data_max}</span>
                </div>
                <span class=\"calendar-duration hide-mb\">
                <img src=\"/assets/images/ico-agenda-clock.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\">{$dur} Min</span>
                </div>
                </td>";

                $timer = date('Y-m-d').' '.$column->startTime;
                
                echo "<td data-label=\"Paciente: \"><img src=\"/assets/images/ico-pacienteblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"><span class=\"paciente-nome\">{$paciente_nome}</span></td>";
                echo "<td data-label=\"Queixa: \"><img src=\"/assets/images/ico-anamnese.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"> {$column->anamnese}</td>";
                echo "<td data-label=\"Médico: \"><img src=\"/assets/images/ico-medicoblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\">{$medico_nome}</td>";
                echo $column->status == 'EM CONSULTA' ? "<td data-label=\"Status: \" class=\"td-status\">{$column->status}<p><small data-time=\"{$timer}\">00:00:00</small></p></td>":"<td class=\"td-status\" data-label=\"Status: \" >{$column->status}</td>";
                echo "<td data-label=\"Modalidade: \">{$column->modalidade}</td>";
                echo "<td class=\"td-act\">
                <div class=\"btns-act\">
                <div class=\"btns-table\">";
                  
                if($user->tipo == 'MEDICO' || $user->tipo == 'FUNCIONARIO') {
                  if($column->status == 'EM CONSULTA' || $column->status == 'EFETIVADO') {
                    echo "<button title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-edit\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                  } else {
                    echo "<button style=\"transform: grayscale(100)\" title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-edit\" disabled><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                  }

                  switch($column->status) {
                    case 'PAGO': {
                      echo "<button title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = false;
                      break;
                    }

                    case 'AGUARDANDO PAGAMENTO': {
                        echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                        echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                        
                        if(isset($column->payment_id) && $user->tipo == 'PACIENTE') {
                          echo "<button title=\"Realizar o Pagamento\" class=\"btn-action\" onclick=\"invoke_payment_link('{$column->payment_id}')\" data-token=\"{$column->token}\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-money.svg\" height=\"28px\"></button>";
                        }
                        $allow_tc = false;

                        if($user->tipo == 'FUNCIONARIO') {
                          echo "<button title=\"Enviar Lembrete de Pagamento e da Consulta\" class=\"btn-action\" onclick=\"invoke_payment_reminder('{$column->token}')\" data-token=\"{$column->token}\" data-status=\"{$column->status}\"><img src=\"/assets/images/icon-whatsapp.svg\" height=\"28px\"></button>";
                        }
                        break;
                    }

                    case 'AGENDADO': {
                      echo $user->tipo == 'MEDICO' ? "<button title=\"Iniciar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-start\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>":"<button title=\"Iniciar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-start\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = true;
                      
                      if($column->modalidade == 'ONLINE') 
                      {
                        echo"<button data-meet=\"{$column->token}\" data-act=\"send-meet-link\" onclick=\"action_btn_form_agendamento(this)\" class=\"btn-action\"><img src=\"/assets/images/wa.svg\" height=\"28px\"></button>";
                      }
                      break;
                    }

                    case 'EM CONSULTA': {
                      echo $user->tipo == 'MEDICO' ? "<button title=\"Finalizar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-finish\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>":"<button title=\"Finalizar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-finish\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = true;
                      break;
                    }

                    case 'CANCELADO': {
                        echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                        echo "<button disabled title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                        $allow_tc = false;
                        break;
                    }

                    case 'EFETIVADO': {
                      echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button disabled title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = false;
                      break;
                    }

                    case 'CANCELAMENTO PENDENTE': {
                      echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button disabled title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = false;
                      break;
                    }
                  }
                }
                  

                  if($user->perms->deletar_item == 1) {
                    echo "<button title=\"Deletar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-delete-item\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-trash.svg\" height=\"28px\"></button>";
                  }

                try {
                  $alt = json_decode($column->data_alteracoes);
                } catch(Exception $ex) {
                  $alt = [];
                }

                if($user->perms->alterar_agendamento)
                 {
                    if(!isset($alt->status) && $column->status == 'AGENDADO') {
                      echo  "<button title=\"Alterar Agendamento\" class=\"btn-action\" onclick=\"alterar_agendamento(this)\" data-token=\"{$column->token}\" data-medico=\"{$column->medico_nome}\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-alter.svg\" height=\"28px\"></button>";
                    } else if(isset($alt->status) &&  $alt->status == 'PENDENTE' && $column->status == 'AGENDADO') {
                      echo  "<button title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"confirm_agendamento(this)\" data-token=\"{$column->token}\" data-medico=\"{$column->medico_nome}\" data-status=\"{$column->status}\"><img class=\"pulse\" src=\"/assets/images/ico-confirm.svg\" height=\"28px\"></button>";
                    }
              }

                  if($column->file_signed != '' && ($user->tipo == 'PACIENTE' || $user->tipo == 'FUNCIONARIO')) {
                    echo "<a class=\"btn-action\" href=\"https://clinabs.com/data/receitas/assinadas/{$column->file_signed}\" download=\"RECEITA_{$column->file_signed}\" title=\"Baixar Receita\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-download.svg\" height=\"28px\"></a>";
                  }

                  echo ($column->modalidade == 'ONLINE' && strtotime($data_agendamento) < strtotime(date('Y-m-d H:i:s')) - 3600)  ? "<button  title=\"Acessar Telemedicina\" class=\"btn-action show-mb\" onclick=\"action_btn_form_agendamento(this)\" data-user=\"FUNCIONARIO\" data-token=\"{$column->token}\" data-room=\"{$meet_url}\" data-act=\"agenda-meet\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-live-mb.svg\" height=\"28px\" class=\"show-mb\"><span data-end=\"{$data_agendamento}\" class=\"timer-rem\"></span></button></td>":"";

                echo "</div>
                </div>
                </td>";
                echo ($column->modalidade == 'ONLINE' && strtotime($data_agendamento) < strtotime(date('Y-m-d H:i:s')) - 3600) ? "<td><button title=\"Acessar Telemedicina\" class=\"btn-action hide-mb\" onclick=\"action_btn_form_agendamento(this)\" data-user=\"FUNCIONARIO\" data-token=\"{$column->token}\" data-room=\"{$meet_url}\" data-act=\"agenda-meet\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-live.svg\" height=\"28px\" class=\"hide-mb\"><img src=\"/assets/images/ico-live-mb.svg\" height=\"28px\" class=\"show-mb\"><span data-end=\"{$data_agendamento}\" class=\"timer-rem\"></span></button></td>":"<td><button disabled title=\"Acessar Telemedicina\" class=\"btn-action\"><img src=\"/assets/images/ico-live.svg\" height=\"28px\"><span class=\"timer-rem\"></span></button></td>";
              echo "</tr>";
            }
            