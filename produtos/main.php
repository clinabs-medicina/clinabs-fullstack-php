<section>
    <h1 class="titulo-h1">Produtos</h1>

</section>

<section class="product-home">
    <div class="toolbar-rtl" data-user="<?=($_GET['token'] ?? '')?>">
        <input type="search" class="form-control" placeholder="Pesquisar" data-search=".product-home-flex">
    </div>
    <?php
        $dollar = json_decode(file_get_contents('../cotacao.json'), true);
        $dollar = floor($dollar['USD_BRL']['price']);
        
  		$marcas = [];
        $stmtz = $pdo->query("SELECT * FROM `MARCAS`");
             
        foreach($stmtz->fetchAll() as $marca) {
        	$marcas[$marca['id']] = $marca['nome'];
        }    

        if($user->perms->listar_produtos) {
            $stmt = $pdo->query("SELECT * FROM PRODUTOS WHERE excluido = 0 ORDER BY status ASC, nome ASC");

            foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $product) {
        
                $id = $product->id;

                if($user->tipo !== 'USUARIO') {
                    echo '<div class="product-home-flex'.($product->status == 'ATIVO' ? ' active' : ' inactive').'">
                    <div class="product-home-image">
                        <img class="product-home-image" src="/data/images/produtos/'.$product->image.'" alt="">
                    </div>
                    <div class="product-home-content">
                        <span class="product-name" title="'.strtoupper($product->nome).'">'.(strlen($product->nome) > 22 ? trim(substr(strtoupper($product->nome), 0 ,40)).'...' : $product->nome).'</span>
            
                        <p>'.(strlen($product->descricao) > 110 ? trim(substr($product->descricao, 0 ,110)).'...' : $product->descricao).'</p>
                    </div>
                    '.($user->perms->produtos_ver_preco ? '<div class="product-home-content-money" data-currency="'.$product->moeda.'"><p>'.($product->moeda == 'BRL' ? 'R$ '.number_format(intVal($product->valor_venda), 2, ',','.'):'$'.number_format(intVal($product->valor_venda) / 100, 2, ',','.')).'</p></div>':'').'
                    <div class="product-home-content-div">
                        '.($user->perms->add_produto_carrinho ? '<button class="medico-icone-button product-add-cart" data-id="'.uniqid().'" data-add="'.(isset($_COOKIE['sessid_clinabs_uid'])).'" data-product="'.$id.'" data-ref="'.(isset($_COOKIE['sessid_clinabs_uid']) ? $_COOKIE['sessid_clinabs_uid'] : $user->token).'">ADICIONAR NO CARRINHO</button>':'').'
                        '.($user->perms->produtos_editar ? '<button class="medico-icone-button" data-link="/produtos/editar/'.$product->token.'">EDITAR</button>':'').'
                        '.(file_exists("../data/catalogs/products/{$product->catalog_file}") && strlen($product->catalog_file) > 5 && $user->perms->produtos_catalog ? '<a type="button" href="/data/catalogs/products/'.$product->catalog_file.'" class="medico-icone-button" data-link="/produtos/editar/'.$product->token.'">SAIBA MAIS</a>':'').'
                    </div>
                    
                    <span>SKU: '.$product->codigo.'</span>
                </div>';
                } else {
                  $marcas = $user->marcas;
                  
                  if(in_array($product->marca, $marcas)) {
                  		echo '<div class="product-home-flex">
                                  <div class="product-home-image">
                                      <img class="product-home-image" src="/data/images/produtos/'.$product->image.'" alt="">
                                  </div>
                                  <div class="product-home-content">
                                      <span class="product-name" title="'.strtoupper($product->nome).'">'.(strlen($product->nome) > 22 ? trim(substr(strtoupper($product->nome), 0 ,40)).'...' : $product->nome).'</span>

                                      <p>'.(strlen($product->descricao) > 110 ? trim(substr($product->descricao, 0 ,110)).'...' : $product->descricao).'</p>
                                  </div>
                                  '.($user->perms->produtos_ver_preco ? '<div class="product-home-content-money" data-currency="'.$product->moeda.'"><p>'.($product->moeda == 'BRL' ? 'R$ ':'$').''.($product->moeda == 'BRL' ? number_format($product->valor_venda, 2, ',','.') : number_format(preg_replace("/[^0-9]/", "", $product->valor_venda) * $dollar / 100, 2, ',','.')).'</p></div>':'').'
                                  <div class="product-home-content-div">
                                      '.($user->perms->add_produto_carrinho ? '<button class="medico-icone-button product-add-cart" data-id="'.uniqid().'" data-add="'.(isset($_COOKIE['sessid_clinabs_uid'])).'" data-product="'.$id.'" data-ref="'.(isset($_COOKIE['sessid_clinabs_uid']) ? $_COOKIE['sessid_clinabs_uid'] : $user->token).'">ADICIONAR NO CARRINHO</button>':'').'
                                      '.($user->perms->produtos_editar ? '<button class="medico-icone-button" data-link="/produtos/editar/'.$product->token.'">EDITAR</button>':'').'
                                      '.(
                                        file_exists("../data/catalogs/products/{$product->catalog_file}") && strlen($product->catalog_file) > 5 && $user->perms->produtos_catalog ? '<a type="button" href="/data/catalogs/products/'.$product->catalog_file.'" class="medico-icone-button" data-link="/produtos/editar/'.$product->token.'">SAIBA MAIS</a>':'').'
                                  </div>

                                  <span>SKU: '.$product->codigo.'</span>
                              </div>';
                  }
                }
            }
        }else{

            echo '
            <div class="msg-sys">
            <div class="msg-sys-flex">
              <figure>
              <svg width="100" height="100" viewBox="0 0 82 82" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M40.84 81.69C63.35 81.69 81.69 63.35 81.69 40.84C81.69 18.36 63.35 -0.0100021 40.84 -0.0100021C18.36 -0.0100021 -0.0100098 18.36 -0.0100098 40.84C-0.0100098 63.35 18.36 81.69 40.84 81.69Z" fill="red"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M27.35 35.4C30.27 35.4 32.68 33.02 32.68 30.1C32.68 27.15 30.27 24.77 27.35 24.77C24.43 24.77 22.02 27.15 22.02 30.1C22.02 33.02 24.43 35.4 27.35 35.4Z" fill="white"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M57.08 35.4C60 35.4 62.41 33.02 62.41 30.1C62.41 27.15 60 24.77 57.08 24.77C54.16 24.77 51.75 27.15 51.75 30.1C51.75 33.02 54.16 35.4 57.08 35.4Z" fill="white"/>
              <path d="M23.07 60.31C22.87 60.82 22.28 61.05 21.77 60.85C21.26 60.65 21.03 60.06 21.23 59.55C23.89 53.23 28.34 49.15 33.39 47.13C35.97 46.08 38.69 45.6 41.41 45.66C44.13 45.69 46.85 46.26 49.38 47.3C54.45 49.43 58.85 53.56 61.23 59.57C61.43 60.08 61.17 60.65 60.66 60.85C60.15 61.05 59.58 60.79 59.38 60.28C57.2 54.84 53.2 51.07 48.61 49.14C46.31 48.18 43.85 47.67 41.38 47.64C38.94 47.61 36.45 48.04 34.12 48.97C29.56 50.79 25.5 54.53 23.07 60.31Z" fill="white"/>
              </svg>  
              </figure>
              <p><strong>OPS!</strong> Acesso Negado.</p>
            </div>
        </div>';
        }
        ?>
</section>