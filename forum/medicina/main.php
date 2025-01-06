<section class="main" id="user-main">
    <div class="flex-container">
        <div class="row dflex-column">
            <h3 class="form-title titulo-h1">Blog</h3>
            <div class="toolbar-rtl" data-user="<?=($_GET['token'] ?? '')?>">
                <input type="search" class="form-control form-search" id="prontuario-search" data-search=".page-box">
                <button onclick="addPrescFunc2()"><i class="fa fa-plus fa-2x"></i></button>
            </div>
            <pre>
                <?php
                   //print_r($posts);
                ?>
            </pre>
            <?php
                    /*
                foreach($posts as $item) {
                  $comments = count(filter_posts($item['posts'], 'post_type', 'COMMENT'));
                  $questions = count(filter_posts($item['posts'], 'post_type', 'QUESTION'));

                        echo '<div class="box-row page-box d-column">
                                <div class="row">
                                	<div class="box-icon">
                                    	<img src="'.$item['image'].'" alt="paciente" style="border-radius: 1rem !important">
                                	</div>
                                    <div class="box-description">
                                        <h6 class="titulo-h6"><a href="'.$item['token'].'" style="color: #222;text-decoration: none;">'.$item['subject'].'</a></h6>
                                        <strong>Data: '.date('d/m/Y', strtotime($item['timestamp'])).'</strong>
                                        <small>DR(a). '.$item['nome_completo']. ' (<b>'.$item['especialidade'].'</b>)</small>
                                        <small class="page-box-txt">'.$item['text_desc'].'</small>
                                    </div>
                                </div>
                                <div class="row row-sb">
                                	<small>publicado em '.date('d/m/Y H:i', strtotime($item['timestamp'])).'</small>
                                    <div class="blog-indicators">
                                        <small><i class="fa fa-comments"></i>  <b>'.$comments.'</b> Comentario(s), </small>
                                        <small><i class="fa fa-user-tag"></i> <b>'.$questions.'</b> Pergunta(s)</small>
                                    </div>
                                </div>
                            </div>';
                    }
                */
            ?>
        </div>
    </div>
</section>