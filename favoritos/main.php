<?php
$produtos_favoritos = $favoritos->getAll($user->cpf)
?>
<section class="main">
    <section class="flex-container">
        <div class="flex-page">
        <section class="carrinho-header">
            <h1 class="titulo-h1">Meus Favoritos</h1>
        </section>
            <form method="post" action="#" class="carrinho-flex">
                <section class="product-carrinho">
                <section class="carrinho-btns">
                  <?=(count($produtos_favoritos) > 0 ? '<a data-action="remove-favorites" data-item="all" class="btn-link">REMOVER TODOS OS PRODUTOS</a>':'') ?>
                </section>
                
                <?php
                    $total = 0;

                    foreach($produtos_favoritos as $item){;
                            echo '<div class="product-carrinho-flex" id="'.$item->pid.'">
                            <!--PRODUCT-FLEX-->
                            <div class="product-carrinho-group">
                                <!--GRUPO DE PRODUTOS-->
                                <div class="product-carrinho-image">
                                    <!--IMAGEM DO PRODUTO-->
                                    <img src="'.$item->imagens.'">
                                </div>
                                <!--FIM IMAGEM DO PRODUTO-->
                                <div class="product-carrinho-description">
                                    <!--BOX NOME PRODUTOS -->
                                    <p>Anna</p>
                                    <h2>'.$item->nome.'</h2>
                                    <span>PRODUTO ADICIONADO NO CARRINHO</span>
                                </div>
                                <!--FIM BOX NOME PRODUTOS -->
                                <div class="product-carrinho-price">
                                    <!--BOX NOME PRODUTOS -->
                                    <p class="text1" data-pix="id_'.$item->pid.'">R$ '.number_format($item->valor, 2, ',', '.').'</p>
                                    <p class="text2">(À vista no PIX)</p>
                                </div>
                                <!--FIM BOX NOME PRODUTOS -->
                                <div class="product-carrinho-price">
                                  <button class="btn-button1">COMPRAR AGORA</button>
                                  <a data-action="remove-favorite" data-item="'.$item->pid.'" class="btn-link">REMOVER</a>
                                </div>
                            </div>
                            <!--FIM GRUPO DE PRODUTOS-->
                        </div>
                        <!--FIM PRODUCT-FLEX-->';
                    }
                    ?>
                </section>
            </form>
        </div>
</section>
