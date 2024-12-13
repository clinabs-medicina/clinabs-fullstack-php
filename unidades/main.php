<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }
?>
<section class="main">
    <section>
        <h1 class="titulo-h1">Nossas Unidades</h1>
    </section>
    <?php
    $unidades = $pdo->query('SELECT * FROM `UNIDADES`');
    $unidades = $unidades->fetchAll(PDO::FETCH_OBJ);
    if ($user->tipo == 'FUNCIONARIO') { ?>

        <div class="toolbar toolbar-btns-right">
            <a onclick="addUnidade()" data-user="<?= $_user->token ?>"><i class="fa fa-plus"></i> Adicionar Unidade</a>
        </div>
    <?php }
    ?>
    <div class="unidades">
        <?php foreach ($unidades as $unidade) {
            echo '
                                    <div class="secunid-flex">
                                        <div>
                                            <div class="secunid-img">
                                                <img src="' .
                $unidade->image .
                '" />
                                            </div>
                                    
                                            <div class="secunid-content">
                                                <div class="secunid-name">
                                                    <h3>' .
                $unidade->nome .
                '</h3>
                                                    <hr />
                                                </div>
                                                <div class="secunid-box">
                                                    <img src="/assets/images/ico-map.svg" height="25px" />
                                                    <p>
                                                        ' .
                $unidade->logradouro .
                ', Nº ' .
                $unidade->numero .
                ' - ' .
                $unidade->bairro .
                '<br />
                                                        ' .
                $unidade->cidade .
                ' - ' .
                $unidade->uf .
                ', ' .
                $unidade->cep .
                '
                                                    </p>
                                                </div>
                                                <div class="secunid-box">
                                                    <img src="/assets/images/ico-fone-bw.svg" height="25px" />
                                                    <p class="secunid-fone">' .
                $unidade->contato .
                '</p>
                                                </div>
                                                <a href="https://maps.app.goo.gl/hNyNwg1NBjFa8DU88" target="_blank">
                                                    ACESSAR MAPA
                                                </a>
                                                
                                                ' .
                ($user->perms->editar_unidades == 1 ? '<a href="#editar" onclick="addUnidade(this)" data-item="' . base64_encode(json_encode($unidade)) . '">Editar</a>' : '') .
                '
                                            </div>
                                        </div>
                                    </div>';
        } ?>

    </div>