<section class="main" id="user-main">
    <div class="flex-container">
        <div class="row dflex-column">
            <h3 class="form-title titulo-h1">Blog dos Médicos</h3>
            <div class="toolbar-rtl" data-user="<?=($_GET['token'] ?? '')?>">
                <input type="search" class="form-control form-search" id="prontuario-search" data-search=".page-box">
                <button onclick="addBlogPost()"><i class="fa fa-plus fa-2x"></i></button>
            </div>
          <div class="blog-themes">
            <?php
                $stmtx = $pdo->query('SELECT (SELECT nome FROM ESPECIALIDADES WHERE id= (SELECT especialidade FROM MEDICOS WHERE token = medico_token)) AS especialidade, (SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS nome_completo, `id`, `timestamp`, `image`, `subject`, `text_desc`, `token`, `medico_token` FROM `BLOG_MEDICO`');
                $posts = $stmtx->fetchAll(PDO::FETCH_ASSOC);

                    
                foreach($posts as $item) {
                  $comments = 0;
                  $questions = 0;

                  $stmty = $pdo->query("SELECT * FROM `BLOG_POSTS` WHERE post_ref = '".$item['id']."';");
                  $items = $stmty->fetchAll(PDO::FETCH_ASSOC);

                  $comments = filter_posts($items, 'post_type', 'COMMENT') ?? [];
                  $questions = filter_posts($items, 'post_type', 'QUESTION') ?? [];

                  $id = uniqid();

                        echo '
                        <div class="blog-post-theme" onclick="$(\'#'.$id.'\').submit()">
                                <div class="row">
                                	<div class="blog-box-img">
                                    	<img src="/data/attachments/'.$item['image'].'" alt="paciente">
                                	</div>
                                    <div class="box-description">
                                        <h6 class="titulo-h6">'.$item['subject'].'</h6>
                                        <small>DR(a). '.$item['nome_completo']. ' (<b>'.$item['especialidade'].'</b>)</small>
                                        <small class="page-box-txt">'.$item['text_desc'].'</small>
                                    </div>
                                </div>
                                <div class="row row-sb">
                                	<small>publicado em '.date('d/m/Y H:i', strtotime($item['timestamp'])).'</small>
                                    <div class="blog-indicators">
                                        <small><i class="fa fa-comments"></i>  <b>'.count($comments).'</b> Comentario(s), </small>
                                        <small><i class="fa fa-user-tag"></i> <b>'.count($questions).'</b> Pergunta(s)</small>
                                    </div>
                                </div>

                                <form method="GET" id="'.$id.'" action="'.$item['token'].'" style="display: none">

                                </form>
                            </div>';
                    }
            ?>
          </div>
        </div>
    </div>
</section>