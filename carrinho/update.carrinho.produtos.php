<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';


$stmty = $pdo->prepare('SELECT *,(SELECT id FROM PRODUTOS WHERE id= product_id) AS prod_id,(SELECT nome FROM PRODUTOS WHERE id= product_id) AS nome,(SELECT nome FROM MARCAS WHERE id=(SELECT id FROM PRODUTOS WHERE id= product_id)) AS marca,(SELECT image FROM PRODUTOS WHERE id= product_id) AS image, (SELECT codigo FROM PRODUTOS WHERE id= product_id) AS sku,(SELECT valor FROM PRODUTOS WHERE id= product_id) AS valor,(SELECT valor_frete_venda FROM PRODUTOS WHERE id=product_id) AS frete_valor FROM `CARRINHO` WHERE user_ref = :user_token;');
$stmty->bindValue(':user_token', $_COOKIE['sessid_clinabs_uid']);

try {
  $stmty->execute();
  $items = $stmty->fetchAll(PDO::FETCH_OBJ);
  
  $result['items'] = $items;
  
  $sum = 0;
  $promo = false;
  $promo_count = 0;
  $desc = 0;
  
  $produtos = [];
  
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
      $item->valor = intval($item->valor) * $item->qtde;
    }

    $sum += $item->subtotal;
    
    $produtos[] = [
        'token' => $item->pid,
        'product' => http_build_query([
            'id' => $item->product_id,
            'qtde' => $item->qtde,
            'valor' => $item->valor
            ])
    ];
  }
} catch(Exception $ex) {

}

header('Content-Type: application/json');
echo json_encode($produtos, JSON_PRETTY_PRINT);