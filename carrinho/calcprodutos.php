<?php
global $carrinho, $user;
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once '../config.inc.php';

ini_set('display_errors', 1);
error_reporting(1);
  
$result = [];
$result['user_id'] = $_SESSION['token'];

$stmt = $pdo->prepare('SELECT *,(SELECT marca FROM PRODUTOS WHERE id= product_id) AS marca,(SELECT image FROM PRODUTOS WHERE id= product_id) AS image, (SELECT codigo FROM PRODUTOS WHERE id= product_id) AS sku,(SELECT valor FROM PRODUTOS WHERE id= product_id) AS valor,(SELECT valor_frete_venda FROM PRODUTOS WHERE id=product_id) AS frete_valor FROM `CARRINHO` WHERE user_ref = :user_token;');
$stmt->bindValue(':user_token', $_SESSION['token']);

try {
  $stmt->execute();
  $items = $stmt->fetchAll(PDO::FETCH_OBJ);
  
  $result['items'] = $items;
  
  $sum = 0;
  $promo = false;
  $promo_count = 0;
  $desc = 0;
  
  foreach($items as $item) {
    $stmtx = $pdo->query("SELECT * FROM `PRODUTOS_PROMOCOES` WHERE produto_id LIKE '%{$item->sku}%' AND frascos = {$item->qtde}  AND frascos > 1 ORDER BY frascos ASC");
    $item->subtotal = ($item->valor * $item->qtde);
    
    if($stmtx->rowCount() > 0) {
      $promotion = $stmtx->fetch(PDO::FETCH_OBJ);
      
      $item->valor = intval($promotion->valor);
      $item->subtotal = ($item->valor);
      $item->desconto = floor($item->subtotal - ($item->subtotal - ($item->subtotal / 100 * $promotion->desconto)));
      
      if($promo == false) {
        $promo = true;
        $desc = $promotion->desconto;
      }
      
      $promo_count++;
    } else {
      $item->valor = intval($item->valor);
    }

    $sum += $item->subtotal;
  }
  
  $result['subtotal_produtos'] = $sum;
  $result['frete'] = ($_frete);
  $result['subtotal'] = ($sum + $_frete);
  $result['desconto'] = ['porcentagem' => ($promo_count > 1 ? 10 : $desc), 'valor' => (($sum + $_frete) / 100 * ($promo_count > 1 ? 10 : $desc)) ];
  $result['total'] = (($promo ? abs(($sum - ($sum / 100 * ($promo_count > 1 ? 5 : 0)))) : ($sum)) + $_frete);
  $result['total_pix'] = $result['total_pix'] = $sum - ($sum / 100 * ($promo_count > 1 ? 5 : $desc)) + $_frete;
} catch(Exception $ex) {
  
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT);