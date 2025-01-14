<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';


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

$allowed = array('data_agendamento', 'nome', 'anamnese', 'medico_token', 'status', 'token');


foreach($data->fetchAll(PDO::FETCH_ASSOC) as $col => $val) {
        $val['visible'] = in_array($val['data'], $allowed);
        
        switch($col) {
         case 'token': {
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
   `nome`,
   `data_nascimento`,
   `nacionalidade`,
   `telefone`,
   `email`,
   `cep`,
   `endereco`,
   `endereco_num`,
   `duracao_agendamento`,
   `data_agendamento`,
   ( SELECT `nome_completo` FROM `MEDICOS` WHERE `MEDICOS`.`token` = medico_token ) AS medico_token,
   ( SELECT `nome` FROM `ANAMNESE` WHERE `ANAMNESE`.`id` = anamnese ) AS anamnese,
   `data_efetivacao`,
   `descricao`,
   `status`,
   `token` 
   FROM
   `AGENDA_MED` AS `A`  
   ORDER BY
`id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {
   $items = [];

   $uid = uniqid();

   foreach($item as $k => $v) {

     if($k == 'token') {
        $items[$k] = '<!--<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agenda-accept"  data-status="'.$uid.'"><img src="/assets/images/ico-checked.svg" height="20px"></button>-->
        <button class="btn-action tooltip" title="Editar" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agenda-edit"><img src="/assets/images/ico-edit.svg" height="20px"></button>
        <!--<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agenda-cancel" data-status="'.$uid.'"><img src="/assets/images/ico-delete.svg" height="20px"></button>-->
        <button class="btn-action tooltip" title="Imprimir/Baixar em PDF" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agenda-doc"><img src="/assets/images/ico-printpdf.svg" height="20px"></button>';
      }else if ($k == 'data_agendamento'){
         $items[$k] = '<div class="calendar-day">
         <img src="/assets/images/ico-calendar.svg" height="32px">
         <div class="datetime-column">
            <span>'.date('d/m/Y', strtotime($v)).'</span>
            <span class="calendar-time">Das '.date('H:i', strtotime($v)).' as '.date('H:i', strtotime('+'.$item['duracao_agendamento'].' minutes', strtotime($v))).'</span>
         </div>
         <span><img src="/assets/images/ico-agenda-clock.svg" width="20px" style="display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;">'.$item['duracao_agendamento'].' Min</span>
         </div>';
      }
      else {
  
         switch($k) {
            case 'status': {
               $items[$k] = '<span id="'.$uid.'" class="tb-status">'.$v.'</span>';
               break;
            }

            case 'medico_token': {
               $items[$k] = '<img src="/assets/images/ico-medicoblack.svg" width="20px" style="display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"> '.$v;
               break;
            }

            case 'nome': {
               $items[$k] = '<img src="/assets/images/ico-pacienteblack.svg" width="20px" style="display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"> '.$v;
               break;
            }
            case 'anamnese': {
               $items[$k] = '<img src="/assets/images/ico-anamnese.svg" width="20px" style="display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"> '.$v;
               break;
            }

            default: {
               $items[$k] = $v;
               break;
            }
         }
     }
   }
   
    $values[] = $items;
}

if(isset($_REQUEST['columns'])) {

   $cls = [];

   foreach($columns as $k) {
      $cls[$k] = $k;
   }


   $result['columns'] = array();
}else {
   //$result['columns'] =  array_unique($columns);
   $result['data'] = $values;
}


header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
