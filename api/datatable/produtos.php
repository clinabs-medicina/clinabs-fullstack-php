<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$sql = "select column_name as data, column_comment as title
from information_schema.COLUMNS 
where table_name = 'PRODUTOS' 
and column_comment != '' ORDER BY ORDINAL_POSITION";

$data =  $pdo->query($sql);

$result = [];


$result['dom'] = 'Bfrtip';
$result['stateSave'] = false;
$result['responsive'] = true;
$result['processing'] = true;
$result['retrieve'] =  true;
$result['serverSide'] = false;
$result['buttons'] = array(
    'colvis',
    'pageLength',
    'print'
);

$columns = [];
$values = [];

$columns_keys = [];

$allowed = array('nome', 'codigo', 'objeto');


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


foreach($pdo->query('SELECT '.implode(', ', $columns_keys).',token FROM PRODUTOS')->fetchAll(PDO::FETCH_ASSOC) as $item) {
   $items = [];

   foreach($item as $k => $v) {
    
     if($k == 'objeto') {
        $items[$k] = '
        <button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="editar-produto"><i class="fa fa-pencil table-action-btn" aria-hidden="true" title="Editar Produto"></i></button>';
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
