<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';


$sql = "select column_name as data, column_comment as title
from information_schema.COLUMNS 
where table_name = 'FUNCIONARIOS' 
and column_comment != '' ORDER BY ORDINAL_POSITION";

$data =  $pdo->query($sql);

$result = [];


$result['dom'] = 'Bfrtip';
$result['processing'] = true;
$result['retrieve'] =  true;
$result['stateSave'] = false;
$result['responsive'] = true;
$result['initComplete'] = "function(){ Swal.close(); }";
$result['buttons'] = array(
    'colvis',
    'pageLength',
    'print'
);

$columns = [];
$values = [];

$columns_keys = [];

$allowed = array('nome_completo', 'telefone', 'setor', 'objeto');


foreach($data->fetchAll(PDO::FETCH_ASSOC) as $col => $val) {
    $val['visible'] = in_array($val['data'], $allowed);

    if($col == 'objeto') {
        $val['width'] = '80px';
    }

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


foreach($pdo->query('SELECT '.implode(', ', $columns_keys).',token FROM FUNCIONARIOS')->fetchAll(PDO::FETCH_ASSOC) as $item) {
   $items = [];

   foreach($item as $k => $v) {
    
     if($k == 'objeto') {
        $items[$k] = '<i class="fa fa-pencil table-action-btn" aria-hidden="true" data-token="'.$item['token'].'" onclick="doAction(this, \'editar-perfil\')" title="Editar Perfil">';
     }else {
        $items[$k] = $v;
     }
   }
   
    $values[] = $items;
}


$result['columns'] = $columns;
$result['data'] = $values;


header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
