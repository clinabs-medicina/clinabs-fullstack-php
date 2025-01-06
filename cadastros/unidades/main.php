<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }

if($user->tipo == 'FUNCIONARIO') {
   $unidades = $pdo->query('SELECT * FROM `UNIDADES`');
   $unidades = $unidades->fetchAll(PDO::FETCH_OBJ);
?>

        <h1 class="titulo-h1">Unidades</h1>
                        </section>
                        
                        <div class="toolbar toolbar-btns-right">
                            <a onclick="addUnidade()" data-user="<?=$_user->token?>"><i class="fa fa-plus"></i> Adicionar Unidade</a>
                        </div>
                       <div class="unidades">
                           <?php
                                foreach($unidades as $unidade) {
                                    echo '
                                    <div class="secunid-flex">
                                        <div>
                                            <div class="secunid-img">
                                                <img src="'.$unidade->image.'" alt="" >
                                            </div>
                                    
                                            <div class="secunid-content">
                                                <div class="secunid-name">
                                                    <h3>'.$unidade->nome.'</h3>
                                                    <hr />
                                                </div>
                                                <div class="secunid-box">
                                                    <img src="/assets/images/ico-map.svg" height="25px" alt="" >
                                                    <p>
                                                        '.$unidade->logradouro.', Nº '.$unidade->numero.' - '.$unidade->bairro.'<br />
                                                        '.$unidade->cidade.' - '.$unidade->uf.', '.$unidade->cep.'
                                                    </p>
                                                </div>
                                                <div class="secunid-box">
                                                    <img src="/assets/images/ico-fone-bw.svg" height="25px" alt="" >
                                                    <p class="secunid-fone">'.$unidade->contato.'</p>
                                                </div>
                                                <a href="https://www.google.com/maps/search/?api=1&query='.str_replace(' ', '%20', $unidade->logradouro.', '.$unidade->numero.'  ,'.$unidade->bairro.', '.$unidade->cidade.' - '.$unidade->uf.','.$unidade->cep).'" target="_blank">
                                                    ACESSAR MAPA
                                                </a>
                                            </div>
                                        </div>
                                    </div>';
                                }
                            ?>
                            
                       </div>
                   <?php
               }                   