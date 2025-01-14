<?php
global $pdo;
require_once $_SERVER['DOCUMENT_ROOT'].'/agenda/Calendar.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$calendar = new Calendar();

$sql = "select column_name as data, column_comment as title
from information_schema.COLUMNS 
where table_name = 'AGENDA_MED' 
and column_comment <> '' ORDER BY ORDINAL_POSITION";

$data =  $pdo->query($sql);

$result = [];

$result['dom'] = 'Bfrtip';
$result['processing'] = true;
$result['retrieve'] =  true;
$result['stateSave'] = false;
$result['responsive'] = true;
//$result['order'] = [[1, 'desc']];
$result['buttons'] = array(
    'colvis',
    'pageLength',
    'print'
);

$columns = [];
$values = [];

$columns_keys = [];

$allowed = array('data_agendamento', 'paciente_token', 'anamnese', 'medico_token', 'token');


foreach($data->fetchAll(PDO::FETCH_ASSOC) as $col => $val) {
   $val['visible'] = in_array($val['data'], $allowed);

   switch($col) {
      case 'data_agendamento': {
         $val['width'] = '200px';
         break;
      }
   }
   $columns[$col] = $val;
   $columns_keys[] = $val['data'];
}


$columns_keys[] = 'objeto';

foreach($pdo->query("SELECT
          `id`,
          (SELECT `nome_completo` FROM `PACIENTES` WHERE `PACIENTES`.`token` = paciente_token) AS nome,
          `duracao_agendamento`,
          `data_agendamento`,
          ( SELECT `nome_completo` FROM `MEDICOS` WHERE `MEDICOS`.`token` = medico_token ) AS medico_token,
          ( SELECT `nome` FROM `ANAMNESE` WHERE `ANAMNESE`.`id` = anamnese ) AS anamnese,
          `data_efetivacao`,
          `descricao`,
          `status`,
          `meet`,
          `token`,
          `paciente_token` 
          FROM
          `AGENDA_MED` AS `A` 
          ".($user->tipo == 'MEDICO' ? " WHERE medico_token = '".$user->token."' ":'')."
          ORDER BY
       `id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {
   $items = [];

   foreach($item as $k => $v) {
     if($k == 'token') {
        $items[$k] = '<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="editar-agenda"><i class="fa fa-pencil table-action-btn" aria-hidden="true" title="Editar Perfil"></i></button>';
      }else if ($k == 'data_agendamento'){
         $items[$k] = '<div class="calendar-day">
         <img src="/assets/images/ico-calendar.svg" height="32px">
         <div class="datetime-column">
            <span>'.date('d/m/Y', strtotime($v)).'</span>
            <span class="calendar-time">Das '.date('H:i', strtotime($v)).' as '.date('H:i', strtotime('+'.$item['duracao_agendamento'].' minutes', strtotime($v))).'</span>
         </div>
         <span><img src="/assets/images/ico-agenda-clock.svg" width="24px" style="display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;">'.$item['duracao_agendamento'].' Min</span>
         </div>';
      } if ($k == 'nome') {
         $items[$k] = '<img src="/assets/images/user.svg" height="32px"> '.$v.'- ';
      }
      
      else {
        $items[$k] = $v;
     }
   }
   
    $values[] = $items;
}

if(isset($_REQUEST['columns'])) {
   $result['columns'] = $columns;
}else {
   $result['columns'] = $columns;
   $result['data'] = $values;
}


header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT);
