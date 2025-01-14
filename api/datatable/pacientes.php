<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$sql = "select column_name as data, column_comment as title
from information_schema.COLUMNS 
where table_name = 'PACIENTES' 
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

if(!isset($_REQUEST['columns'])) {
    foreach($pdo->query('SELECT '.implode(', ', $columns_keys).',token FROM PACIENTES')->fetchAll(PDO::FETCH_ASSOC) as $item) {
    $items = [];

    foreach($item as $k => $v) {
        
        if($k == 'objeto') {
            $items[$k] = '<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="cart-products" ><i class="fa fa-cart-plus table-action-btn" aria-hidden="true" title="Comprar Produtos"></i></button>
            <button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agendar-consulta"><i class="fa fa-user-md table-action-btn" aria-hidden="true" title="Agendar Consulta"></i></button>
            <button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="editar-perfil"><i class="fa fa-pencil table-action-btn" aria-hidden="true" title="Editar Perfil"></i></button>';
        }else {
            $items[$k] = $v;
        }
    }
        $values[] = $items;
    }
    $result['data'] = $values;
    $result['columns'] = $columns;

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
}else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($columns);
}




