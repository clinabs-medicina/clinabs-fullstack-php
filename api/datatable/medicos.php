<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';


$sql = "select column_name as data, column_comment as title
from information_schema.COLUMNS 
where table_name = 'MEDICOS' 
and column_comment != '' ORDER BY ORDINAL_POSITION";

$data =  $pdo->query($sql);

$result = [];


$result['dom'] = 'Bfrtip';
$result['processing'] = true;
$result['retrieve'] =  true;
$result['stateSave'] = false;
$result['responsive'] = true;
$result['buttons'] = array(
    'colvis',
    'pageLength',
    'print'
);

$columns = [];
$values = [];

$columns_keys = [];

$allowed = array('nome_completo', 'telefone', 'objeto');


foreach($data->fetchAll(PDO::FETCH_ASSOC) as $col => $val) {
        $val['visible'] = in_array($val['data'], $allowed);
    
        $columns[$col] = $val;

        $columns_keys[] = $val['data'];
}
/*
$columns[] = [
    'data' => 'objeto',
    'title' => 'Ação',
    'visible' => true
];
*/

$columns_keys[] = 'objeto';


foreach($pdo->query('SELECT '.implode(', ', $columns_keys).',token FROM MEDICOS')->fetchAll(PDO::FETCH_ASSOC) as $item) {
   $items = [];

   foreach($item as $k => $v) {
    
     if($k == 'objeto') {
        $items[$k] = '<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="editar-perfil"><i class="fa fa-pencil table-action-btn" aria-hidden="true" title="Editar Perfil"></i></button>';
     }else {
        $items[$k] = $v;
     }
   }
   
    $values[] = $items;
}


$result['columns'] = $columns;
$result['data'] = $values;


header('Content-Type: application/json');
echo json_encode($result);
