<?php
global $pdo;
require_once '../config.inc.php';
ini_set('display_errors', 1);
error_reporting(1);
$valor_total = (int)$_REQUEST['valor_total'] * 100;
$stmt = $pdo->prepare('SELECT * FROM CUPONS WHERE token = :token');
$stmt->bindValue(':token', $_REQUEST['cupom']);
$stmt->execute();
$desc = $stmt->fetch(PDO::FETCH_OBJ);

if($desc->duracao!= 'PERMANENTE'){
    $json = [
        'status' => 'error',
        'text' => 'Cupom já Utilizado',
        'icon' => 'warning',
    ];
} else {
    if($desc->tipo == 'PORCENTAGEM'){
        $vt = ($valor_total/100);
        $desconto = ($vt *= (1-$desc->valor/100));

        $json = [
            'status' => 'success',
            'cupom' => $desc->token,
            'tipo' => $desc->tipo,
            'valor' => 'R$ '.number_format($desc->valor, 2, '.', ','),
            'valor_pedido' => $valor_total,
            'valor_total' => $desconto /100,
            'valor_total_formatado' => number_format($desconto, 2, ',', '.'),
            'valor_pedido_formatado' => number_format($valor_total /100, 2, ',', '.'),
            'desconto' => $desc->valor,
            'desconto_formatado' => number_format($desconto, 2, ',', '.'),
        ];
    }else{
        $desconto = ($valor_total - $desc->valor);

        $json = [
            'status' => 'success',
            'cupom' => $desc->token,
            'tipo' => $desc->tipo,
            'valor' => 'R$ '.number_format($desc->valor /100, 2, '.', ','),
            'valor_pedido' => $valor_total,
            'valor_total' => $desconto /100,
            'valor_total_formatado' => number_format($desconto /100, 2, ',', '.'),
            'valor_pedido_formatado' => number_format($valor_total/100, 2, ',', '.'),
            'desconto' => $desc->valor,
            'desconto_formatado' => number_format($desc->valor / 100, 2, ',', '.'),
        ];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json, JSON_PRETTY_PRINT);