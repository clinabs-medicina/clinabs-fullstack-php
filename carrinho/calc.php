<?php
global $carrinho, $user;

require_once '../config.inc.php';

ini_set('display_errors', 1);
error_reporting(1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// if(isset($_SESSION['token'])) 
// $user = (object) $_SESSION['userObj'];

$result = [];

if(isset($_REQUEST['qtde']) && isset($_REQUEST['pid'])) {
  $qtde = $_REQUEST['qtde'];
  $pid = $_REQUEST['pid'];
  
  $carrinho->update($pid, $qtde);
  
  $stmty = $pdo->prepare('SELECT *,(SELECT nome FROM PRODUTOS WHERE id= product_id) AS nome,(SELECT nome FROM MARCAS WHERE id=(SELECT id FROM PRODUTOS WHERE id= product_id)) AS marca,(SELECT image FROM PRODUTOS WHERE id= product_id) AS image, (SELECT codigo FROM PRODUTOS WHERE id= product_id) AS sku,(SELECT valor FROM PRODUTOS WHERE id= product_id) AS valor,(SELECT valor_frete_venda FROM PRODUTOS WHERE id=product_id) AS frete_valor FROM `CARRINHO` WHERE user_ref = :user_token;');
  $stmty->bindValue(':user_token', $_SESSION['token']);

  try {
    $stmty->execute();
    $items = $stmty->fetchAll(PDO::FETCH_OBJ);

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
      
      if($item->pid == $pid) {
        $result['current_item'] = $item;
      }
    }

    $result['subtotal_produtos'] = $sum;
    $result['frete'] = ($_frete);
    $result['subtotal'] = ($sum + $_frete);
    $result['desconto'] = ['porcentagem' => ($promo_count > 1 ? 10 : $desc), 'valor' => (($sum + $_frete) / 100 * ($promo_count > 1 ? 10 : $desc)) ];
    $result['total'] = (($promo ? abs(($sum - ($sum / 100 * ($promo_count > 1 ? 10 : $desc)))) : ($sum)) + $_frete);
    $result['total_pix'] = $result['total_pix'] = $sum - ($sum / 100 * 5) + $_frete;
  }
  
  catch(Exception $error) {
  
  }

   header('Content-Type: application/json');
   echo json_encode($result, JSON_PRETTY_PRINT);
}