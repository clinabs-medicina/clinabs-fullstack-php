<?php
global $carrinho, $_user;

$result = [];
$result['user_id'] = $_COOKIE['sessid_clinabs_uid'];

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
            'id' => $item->product_id,
            'qtde' => $item->qtde,
            'valor' => $item->valor,
            'token' => $item->pid
        ];
  }
  
  $result['subtotal_produtos'] = $sum;
  $result['frete'] = ($_frete);
  $result['subtotal'] = ($sum + $_frete);
  $result['desconto'] = ['porcentagem' => ($promo_count > 1 ? 10 : $desc), 'valor' => (($sum + $_frete) / 100 * ($promo_count > 1 ? 10 : $desc)) ];
  $result['total'] = (($promo ? abs(($sum - ($sum / 100 * ($promo_count > 1 ? 10 : $desc)))) : ($sum)) + $_frete);
  $result['total_pix'] = $sum - ($sum / 100 * 5) + $_frete;
  $dados = json_decode(json_encode($result));
    ?>
    
    <section class="main">
    <section class="flex-container">
        <div class="flex-page">
        <section class="carrinho-header">
            <h1 class="titulo-h1">Carrinho de Compras</h1>
            <h2 class="cart-empty"><?= count($dados->items) > 0 ? '' : 'Seu Carrinho Está Vazio' ?></h2>
        </section>

        <?php
        if(isset($_COOKIE['sessid_clinabs_uid']) && $_user !== false){
            ?>
        <section class="carrinho-header user-profile">
            <div class="profile-details">
            <img src="<?=Modules::getUserImage($_COOKIE['sessid_clinabs_uid']) ?>">
            <div class="details">
            <h2><?=$_user->nome_completo?></h2>
            <h4><?=$_user->email?></h4>
            </div>
            </div>
        </section>
        <?php
        }
        ?>

            <form method="post" class="carrinho-flex" action="?payment">
                <section class="product-carrinho">
                <section class="carrinho-btns">
                  <?= count($dados->items) > 0 ? '<a data-action="remove" data-item="all" class="btn-link">REMOVER TODOS OS PRODUTOS</a>' : '' ?>
                </section>
                
                <?php
                foreach ($dados->items as $item) {
             
                    echo '<div class="product-carrinho-flex" id="' .
                        $item->pid .
                        '">
                            <div class="product-carrinho-group">
                                <div class="product-carrinho-image">
                                    <img src="/data/images/produtos/'.$item->image.'">
                                </div>
              
                                <div class="product-carrinho-description">
                                    <p>SKU: '.$item->sku.'</p>
                                    <h2>' .
                        $item->nome .
                        '</h2>
                                    <span>PRODUTO ADICIONADO NO CARRINHO</span>
                                </div>
                                <div class="product-carrinho-group-text">
                    
                                    <div class="product-info">
                                        <p>Quant.</p>
                                        
                                        <div class="product-qtde">
                                            <button type="button" data-action="-" data-for="id_' .
                        $item->pid .
                        '">⦑</button>
                                            <input id="id_' .
                        $item->pid .
                        '" value="' .
                        round($item->qtde) .
                        '">
                                            <button type="button" data-action="+" data-for="id_' .
                        $item->pid .
                        '">⦒</button>                   
                                        </div>
                                        <a data-action="remove" data-item="' .
                        $item->pid .
                        '" class="btn-link-2">REMOVER</a>
                                    </div>
                                </div>

                                <div class="product-carrinho-price">
                                    <p class="text1" data-pix="id_' .
                        $item->pid .
                        '">R$ ' .
                        number_format(round($item->valor), 2, ',', '.') .
                        '</p>
                                    <p class="text2">(À vista no PIX)</p>
                                </div>
                            </div>
                        </div>';

                        echo '<input type="hidden" name="produto[]" value="'.$item->pid.'">';
               
                }
                ?>
                </section>
              
        <section class="product-total"<?= count($dados->items) > 0 ? '' : ' style="display: none"' ?>>
        <div class="prduct-total-flex">
            <h3>RESUMO</h3>
            <div class="prduct-total-flex-text1">
                <p>Valor dos Produtos: <strong id="valor-total">R$ <?= number_format(round($dados->subtotal_produtos), 2, ',', '.') ?></strong></p>
                <hr>
                <p>Frete: <strong>R$ <?= number_format($dados->frete, 2, ',', '.') ?></strong></p>
                <p>Valor Total a prazo: <strong id="valor-total-prazo">R$ <?= number_format(round($dados->subtotal), 2, ',', '.') ?></strong></p>
                <p>(em até 10x de R$ <span id="total-prazo-sj"><?= number_format(round($dados->total) / 10, 2, ',', '.') ?></span> sem juros)</p>
            </div>
            <div class="prduct-total-flex-text2">
                <span>Valor à vista no <strong>Pix:</strong></span>
                <span><strong class="valor1" id="valor-total-pix">R$ <?= number_format(round($dados->total_pix), 2, ',', '.') ?></strong></span>
                <span>(Economize: <strong id="valor-total-pix-cb">R$ 0,00</strong>)</span>
            </div>
            <button class="btn-button1" type="submit">IR PARA O PAGAMENTO</button>
            <button type="button" data-link="/produtos" class="btn-button2">CONTINUAR COMPRANDO</button>
        </div>
    </section>
    <input type="hidden" name="valor_frete" value="<?=$dados->frete?>">
    <input type="hidden" name="valor_frete" value="<?=$dados->frete?>">
    <input type="hidden" name="valor_produtos" value="<?=round($dados->subtotal_produtos)?>">
    <input type="hidden" name="valor_total" value="<?=round($dados->subtotal)?>">
    <input type="hidden" name="valor_total_pix" value="<?=round($dados->total_pix)?>" id="valor_total_pix">
    <?php
    foreach($produtos as $produto) {
        $tk = $produto['token'];
        echo '<input type="hidden" class="product-query" id="'.$tk.'" name="produtos[]" value="'.http_build_query($produto).'">';
    }
    ?>
        
            </form>
        </div>
</section>
</section>
    
    <?php

    
} catch(Exception $ex) {
  echo $ex->getMessage();
}


?>
