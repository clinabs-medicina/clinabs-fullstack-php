<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$user = $_SESSION['user'];
$_user = $_SESSION['_user'];

if ($user->tipo == 'FUNCIONARIO') {
  $query = 'SELECT
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
  AGENDA_MED.medico_online,
  AGENDA_MED.paciente_online,
  AGENDA_MED.modalidade,
  (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_nome, 
  (SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS whatsapp, 
  (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_nome, 
  (SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_status,
  (SELECT VENDAS.payment_id FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_id,
  (SELECT PACIENTES.id FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_id,
  (SELECT MEDICOS.id FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_id,
  anamnese as anamnese_id
FROM
  AGENDA_MED';
} else if ($user->tipo == 'PACIENTE') {
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
AGENDA_MED.medico_online,
AGENDA_MED.paciente_online,
AGENDA_MED.modalidade,
(SELECT payment_id FROM VENDAS WHERE code = token) as payment_id,
(SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_nome,
(SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS whatsapp, 
(SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_nome, 
(SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_status,
(SELECT VENDAS.payment_id FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_id,
  (SELECT PACIENTES.id FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_id,
  (SELECT MEDICOS.id FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_id,
  anamnese as anamnese_id
FROM
AGENDA_MED
WHERE paciente_token = '" . $user->token . "';";
} else if ($user->tipo == 'MEDICO') {
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
AGENDA_MED.medico_online,
AGENDA_MED.paciente_online,
AGENDA_MED.modalidade,
(SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_nome,
(SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS whatsapp, 
(SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_nome, 
(SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_status,
(SELECT VENDAS.payment_id FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_id,
  (SELECT PACIENTES.id FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_id,
  (SELECT MEDICOS.id FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_id,
  anamnese as anamnese_id
FROM
AGENDA_MED
WHERE medico_token = '" . $user->token . "';";
} else {
  $query = 'SELECT
  AGENDA_MED.data_agendamento, 
  AGENDA_MED.paciente_token, 
  (SELECT ANAMNESE.nome FROM ANAMNESE WHERE AGENDA_MED.anamnese = ANAMNESE.id) AS anamnese, 
  AGENDA_MED.medico_token, 
  AGENDA_MED.data_alteracoes,
  AGENDA_MED.data_efetivacao, 
  AGENDA_MED.data_cancelamento,
  AGENDA_MED.`status`,
  AGENDA_MED.token,
  AGENDA_MED.`timestamp`,
  AGENDA_MED.`startTime`,
  AGENDA_MED.`endTime`,
  AGENDA_MED.descricao,
  AGENDA_MED.meet,
  AGENDA_MED.file_signed,
  AGENDA_MED.valor,
  AGENDA_MED.medico_online,
  AGENDA_MED.paciente_online,
  AGENDA_MED.modalidade,
  AGENDA_MED.duracao_agendamento, 
  (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token LIMIT 1) AS paciente_nome,
  (SELECT PACIENTES.id FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token LIMIT 1) AS paciente_id,
  (SELECT PACIENTES.celular FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token LIMIT 1) AS whatsapp, 
  (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token LIMIT 1) AS medico_nome,
  (SELECT MEDICOS.id FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token LIMIT 1) AS medico_id, 
  (SELECT VENDAS.status FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token LIMIT 1) AS payment_status,
  (SELECT PACIENTES.id FROM PACIENTES WHERE PACIENTES.token = AGENDA_MED.paciente_token) AS paciente_id,
  (SELECT VENDAS.payment_id FROM VENDAS WHERE VENDAS.reference = AGENDA_MED.token) AS payment_id,
  (SELECT MEDICOS.id FROM MEDICOS WHERE MEDICOS.token = AGENDA_MED.medico_token) AS medico_id,
  anamnese as anamnese_id
FROM
  AGENDA_MED';
}

$rows = $pdo->query($query)->fetchAll(PDO::FETCH_OBJ);

$medicos = [];
$pacientes = [];
$status = [];

foreach ($rows as $row) {
  $medicos[$row->medico_id] = $row->medico_nome;
  $status[] = $row->status;
  $pacientes[$row->paciente_id] = $row->paciente_nome;
}

?>
<section class="main">
    <section>
        <h1 class="titulo-h1"><?= ($user->tipo == 'PACIENTE' ? 'Meus Agendamentos' : 'Agenda do Médico') ?></h1>
        <br>
        <div class="toolbar-btns">
          <?php
          if ($user->tipo == 'FUNCIONARIO') {
            echo '<button id="btn_newAgendamento" class="btn-button1">NOVO AGENDAMENTO</button>';
          }
          ?>
        </div>
    </section>
    <div class="flex-container produtos-flex">
        <form class="filter-container" id="agendamento-filters">
            <div class="row">
                <div class="col-md-4">
                    <fieldset class="filter-field">
                        <legend>Filtrar Data de Agendamento</legend>
                        <i class="fa-solid fa-filter-circle-xmark filter-clear"></i>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_start">Data de Inicio</label>
                                    <input name="data_agendamento" type="date" id="date_start"
                                        class="form-control filter-element" data-value="data_agendamento"
                                        onchange="filter_ag_req(this)" data-trigger="get_date">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_end">Data de Fim</label>
                                    <input name="data_agendamento" type="date" id="date_end"
                                        class="form-control filter-element" data-value="data_agendamento"
                                        onchange="filter_ag_req(this)">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="filter-field">
                        <legend>Filtrar por Médico</legend>
                        <i class="fa-solid fa-filter-circle-xmark filter-clear"></i>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <legend for="filtro_medico">Selecione um Médico</legend>
                                    <div class="select-container">
                                        <div class="custom-select" data-value="medico_nome">
                                            <div class="select-selected">Selecione uma Opção</div>
                                            <div class="select-arrow"></div>
                                            <div class="select-items">
                                                <input type="text" name="filtro_medico" class="search-input" placeholder="Pesquisar...">
                                                <ul class="options-list">
                                                    <?php
                                                    foreach (array_unique($medicos) as $id => $medico) {
                                                      echo "<li data-value=\"{$id}\">{$medico}</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                  </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="filter-field">
                        <legend>Filtrar por Paciente</legend>
                        <i class="fa-solid fa-filter-circle-xmark filter-clear"></i>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <legend for="filtro_paciente">Selecione um Paciente</legend>
                                    <div class="select-container">
                                        <div class="custom-select" id="filtro_paciente" data-value="paciente_nome">
                                            <div class="select-selected">Selecione uma Opção</div>
                                            <div class="select-arrow"></div>
                                            <div class="select-items">
                                                <input type="text" name="filtro_paciente" class="search-input" placeholder="Pesquisar..."
                                                    data-value="paciente_nome">
                                                <ul class="options-list">
                                                    <?php
                                                    foreach (array_unique($pacientes) as $id => $paciente) {
                                                      echo "<li data-value=\"{$id}\">{$paciente}</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="filter-field">
                        <legend>Filtrar por Status</legend>
                        <i class="fa-solid fa-filter-circle-xmark filter-clear"></i>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <legend for="filtro_status">Selecione um Status</legend>
                                    <div class="select-container">
                                        <div class="custom-select" id="filtro_status" data-value="status">
                                            <div class="select-selected">Selecione uma Opção</div>
                                            <div class="select-arrow"></div>
                                            <div class="select-items">
                                                <input type="text" name="filtro_status" class="search-input" placeholder="Pesquisar...">
                                                <ul class="options-list">
                                                    <?php
                                                    foreach (array_unique($status) as $sts) {
                                                      echo "<li data-value=\"{$sts}\">{$sts}</li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-2">
                    <fieldset class="filter-field">
                        <legend>Filtrar por Modalidade</legend>
                        <i class="fa-solid fa-filter-circle-xmark filter-clear"></i>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <legend for="filtro_modalidade">Selecione uma Modalidade</legend>
                                    <div class="select-container">
                                        <div class="custom-select" id="filtro_modalidade" data-value="modalidade">
                                            <div class="select-selected">Selecione uma Opção</div>
                                            <div class="select-arrow"></div>
                                            <div class="select-items">
                                                <input type="text" name="filtro_modalidade" class="search-input" placeholder="Pesquisar...">
                                                <ul class="options-list">
                                                    <li data-value="ONLINE">ONLINE</li>
                                                    <li data-value="PRESENCIAL">PRESENCIAL</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

            </div>
        </form>
        <table id="tableAgendamento" class="display dataTable">
            <thead>
                <tr>
                    <th data-name="data_agendamento">Data <span class="hide-mb">Agendamento</span></th>
                    <th data-name="paciente_nome">Paciente</th>
                    <th class="hide-mb" data-name="queixa_principal">Queixa</th>
                    <th class="hide-mb" data-name="medico_nome">Médico</th>
                    <th data-name="status">Status</th>
                    <th class="hide-mb" data-name="modalidade">Tipo</th>
                    <th width="90px">Ações</th>
                    <th data-name="telemedicina"><span class="hide-mb">Telemedicina</span></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;

                foreach ($rows as $column) {
                  $meet = json_decode($column->meet);
                  $meet_url = $user->tipo != 'MEDICO' ? $meet->roomUrl : $meet->hostRoomUrl;
                  $roomName = str_replace('/', '', $meet->roomName);

                  $startTime = $column->startTime;
                  $dt = ($column->data_cancelamento != '') ? $column->data_cancelamento : $column->data_agendamento;
                  $data_agendamento = ($column->data_cancelamento != '') ? date('d/m/Y H:i', strtotime($column->data_cancelamento)) : date('d/m/Y H:i', strtotime($column->data_agendamento));
                  $dur = $column->duracao_agendamento;
                  $hora_agendamento = date('H:i', strtotime($dt));
                  $data_max = date('H:i', strtotime("+{$dur} minute", strtotime($dt)));

                  $paciente_nome = strlen($column->paciente_nome) > 20 ? trim(substr($column->paciente_nome, 0, 20)) . '...' : $column->paciente_nome;
                  $medico_nome = strlen($column->medico_nome) > 20 ? trim(substr($column->medico_nome, 0, 20)) . '...' : $column->medico_nome;

                  $allow_tc = false;

                  $date = date('Y-m-d', strtotime($dt));
                  $date_formated = date('d/m/Y', strtotime($date));

                  echo "<tr data-room=\"{$roomName}\" data-index=\"{$i}\" class=\"ag_row ag_row{$i}\" data-date=\"{$date}\" data-paciente=\"{$column->paciente_token}\" data=ts=\"{$ts}\" id=\"{$column->token}\" data-stime=\"{$startTime}\">";
                  echo "<td data-value=\"{$date}\" data-label=\"Data: \" width=\"260px\" class=\"ag-day\"><div class=\"calendar-day\">
                  <img src=\"/assets/images/ico-calendar.svg\" height=\"32px\">
                  <div class=\"datetime-column\">
                  <span>{$date_formated} {$hora_agendamento}</span>
                  <span class=\"calendar-time\">Das {$hora_agendamento} as {$data_max}</span>
                  </div>
                  <span class=\"calendar-duration hide-mb\">
                  <img src=\"/assets/images/ico-agenda-clock.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\">{$dur} Min</span>
                  </div>
                  </td>";

                  $timer = date('Y-m-d') . ' ' . $column->startTime;

                  echo "<td data-value=\"{$column->paciente_id}\" data-label=\"Paciente: \"><img src=\"/assets/images/ico-pacienteblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"><span class=\"paciente-nome\" data-type=\"visitor\" data-online=\"{$column->paciente_online}\">{$paciente_nome}</span></td>";
                  echo "<td data-value=\"{$column->anamnese_id}\" data-label=\"Queixa: \"><img src=\"/assets/images/ico-anamnese.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"> {$column->anamnese}</td>";
                  echo "<td data-value=\"{$column->medico_id}\" data-label=\"Médico: \"><img src=\"/assets/images/ico-medicoblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"><span class=\"medico-nome\" data-type=\"host\" data-online=\"{$column->medico_online}\">{$medico_nome}</span></td>";
                  echo $column->status == 'EM CONSULTA' ? "<td data-value=\"{$column->status}\" data-label=\"Status: \" class=\"td-status\">{$column->status}<p><small data-time=\"{$timer}\">00:00:00</small></p></td>" : "<td data-value=\"{$column->status}\" class=\"td-status\" data-label=\"Status: \" >{$column->status}</td>";
                  echo "<td data-value=\"{$column->modalidade}\" data-label=\"Modalidade: \">{$column->modalidade}</td>";
                  echo '<td class="td-act">
                  <div class="btns-act">
                  <div class="btns-table">';
                  if (strlen(preg_replace('/[^0-9a-zA-Z]+/', '', $column->descricao)) > 0) {
                    echo "<button title=\"Informações\" class=\"btn-action\" onclick=\"Swal.fire({text: '{$column->descricao}', icon: 'info'})\" data-token=\"{$column->token}\" data-act=\"agenda-info\"><i class=\"fas fa-info-circle\" style=\"font-size: 1.25rem;\"></i></button>";
                  }
                  if ($user->tipo == 'MEDICO' || $user->tipo == 'FUNCIONARIO') {
                    if ($column->status == 'EM CONSULTA' || $column->status == 'EFETIVADO') {
                      echo "<button title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-edit\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                    } else {
                      echo "<button style=\"transform: grayscale(100)\" title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-edit\" disabled><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                    }

                    switch ($column->status) {
                      case 'PAGO':
                        {
                          echo "<button title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                          echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                          $allow_tc = false;
                          break;
                        }

                      case 'AGUARDANDO PAGAMENTO':
                        {
                          echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                          echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";

                          if (isset($column->payment_id) && $user->tipo == 'PACIENTE') {
                            echo "<button title=\"Realizar o Pagamento\" class=\"btn-action\" onclick=\"invoke_payment_link('{$column->payment_id}')\" data-token=\"{$column->token}\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-money.svg\" height=\"28px\"></button>";
                          }
                          $allow_tc = false;

                          echo "<button class=\"btn-action\"><img onclick=\"wa_notify('{$column->payment_id}', '{$column->paciente_nome}', 0)\" title=\"Enviar Lembrete de Cobrança via WhatsApp\" src=\"/assets/images/icon-whatsapp.svg\" height=\"22px\"></button>";

                          break;
                        }

                      case 'AGENDADO':
                        {
                          echo $user->tipo == 'MEDICO' ? "<button title=\"Iniciar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-start\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-play.svg\" height=\"28px\"></button>" : "<button title=\"Iniciar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-start\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-play.svg\" height=\"28px\"></button>";
                          echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                          $allow_tc = true;

                          if ($column->modalidade == 'ONLINE') {
                            echo "<button data-meet=\"{$column->token}\" data-act=\"send-meet-link\" onclick=\"action_btn_form_agendamento(this)\" class=\"btn-action\"><img src=\"/assets/images/wa.svg\" height=\"28px\"></button>";
                          }
                          break;
                        }

                      case 'EM CONSULTA':
                        {
                          echo $user->tipo == 'MEDICO' ? "<button title=\"Finalizar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-finish\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-stop.svg\" height=\"28px\"></button>" : "<button title=\"Finalizar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-finish\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-stop.svg\" height=\"28px\"></button>";
                          echo "<button title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                          $allow_tc = true;
                          break;
                        }

                      case 'CANCELADO':
                        {
                          echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                          echo "<button disabled title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                          $allow_tc = false;
                          break;
                        }

                      case 'EFETIVADO':
                        {
                          echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                          echo "<button disabled title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                          $allow_tc = false;
                          break;
                        }

                      case 'CANCELAMENTO PENDENTE':
                        {
                          echo "<button disabled title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                          echo "<button disabled title=\"Cancelar Consulta\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                          $allow_tc = false;
                          break;
                        }
                    }
                  }

                  if ($user->perms->deletar_item == 1) {
                    echo "<button title=\"Deletar Agendamento\" class=\"btn-action\" onclick=\"action_btn_form_agendamento(this)\" data-token=\"{$column->token}\" data-act=\"agenda-delete-item\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-trash.svg\" height=\"28px\"></button>";
                  }

                  try {
                    $alt = json_decode($column->data_alteracoes);
                  } catch (Exception $ex) {
                    $alt = [];
                  }

                  if ($user->perms->alterar_agendamento) {
                    if (!isset($alt->status) && $column->status == 'AGENDADO') {
                      echo "<button title=\"Alterar Agendamento\" class=\"btn-action\" onclick=\"alterar_agendamento(this)\" data-token=\"{$column->token}\" data-medico=\"{$column->medico_nome}\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-alter.svg\" height=\"28px\"></button>";
                    } else if (isset($alt->status) && $alt->status == 'PENDENTE' && $column->status == 'AGENDADO') {
                      echo "<button title=\"Confirmar Agendamento\" class=\"btn-action\" onclick=\"confirm_agendamento(this)\" data-token=\"{$column->token}\" data-medico=\"{$column->medico_nome}\" data-status=\"{$column->status}\"><img class=\"pulse\" src=\"/assets/images/ico-confirm.svg\" height=\"28px\"></button>";
                    }
                  }

                  if ($column->file_signed != '' && ($user->tipo == 'PACIENTE' || $user->tipo == 'FUNCIONARIO')) {
                    echo "<a class=\"btn-action\" href=\"https://clinabs.com/data/receitas/assinadas/{$column->file_signed}\" download=\"RECEITA_{$column->file_signed}\" title=\"Baixar Receita\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-download.svg\" height=\"28px\"></a>";
                  }

                  echo ($column->modalidade == 'ONLINE' && $column->status == 'EM CONSULTA' && strtotime($data_agendamento) < strtotime(date('Y-m-d H:i:s')) - 3600) ? "<button  title=\"Acessar Telemedicina\" class=\"btn-action show-mb\" onclick=\"action_btn_form_agendamento(this)\" data-user=\"FUNCIONARIO\" data-token=\"{$column->token}\" data-room=\"{$meet_url}\" data-act=\"agenda-meet\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-live-mb.svg\" height=\"28px\" class=\"show-mb\"><span data-end=\"{$data_agendamento}\" class=\"timer-rem\"></span></button></td>" : '';

                  echo '</div>
                  </div>
                  </td>';
                  echo ($column->modalidade == 'ONLINE' && $column->status == 'EM CONSULTA' && strtotime($data_agendamento) < strtotime(date('Y-m-d H:i:s')) - 3600) ? "<td data-value=\"{$meet_url}\"><button title=\"Acessar Telemedicina\" class=\"btn-action hide-mb\" onclick=\"action_btn_form_agendamento(this)\" data-user=\"FUNCIONARIO\" data-token=\"{$column->token}\" data-room=\"{$meet_url}\" data-act=\"agenda-meet\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-live.svg\" height=\"28px\" class=\"hide-mb\"><img src=\"/assets/images/ico-live-mb.svg\" height=\"28px\" class=\"show-mb\"><span data-end=\"{$data_agendamento}\" class=\"timer-rem\"></span></button></td>" : "<td data-value=\"{$meet_url}\"><button disabled title=\"Acessar Telemedicina\" class=\"btn-action\"><img src=\"/assets/images/ico-live.svg\" height=\"28px\"><span class=\"timer-rem\"></span></button></td>";
                  echo '</tr>';

                  $i++;
                }

                ?>
            </tbody>
        </table>
        <div class="pagination" id="paginationControls"></div>
    </div>
</section>