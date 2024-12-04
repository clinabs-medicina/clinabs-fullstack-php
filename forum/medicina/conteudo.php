<?php
$stmt = $pdo->prepare("SELECT * FROM `BLOG_MEDICO` WHERE token = :token");
$stmt->bindValue(':token', $_GET['token']);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<section class="main" id="user-main">
    <div class="flex-container">
        <div class="row dflex-column">
            <h3 class="form-title titulo-h1"><?=($dados['subject'])?></h3>
          <div class="toolbar-rtl" data-user="<?=($_GET['token'] ?? '')?>">
                <input type="search" class="form-control form-search" id="blog-add-post" data-search="pre">
                <button onclick="addBlogPost()"><i class="fa fa-plus fa-2x"></i></button>
            </div>
            
          <div class="blog-posts">
            <?php

                $items = $posts[0]['posts'];

                $comments = filter_posts($items, 'post_type', 'COMMENT');
                $questions = filter_posts($items, 'post_type', 'QUESTION');
                
                 foreach(filter_posts($items, 'post_type', 'POST') as $item) {
                  echo '<div class="blog-post" id="'.$_GET['token'].'" data-post-id="'.$token.'">
                  <div class="blog-header">
                    <div class="blog-header-user">
                      <img src="doutor.jpg" height="32px">
                      <div class="blog-user-info">
                        <strong>DR(a). '.$item['nome_completo'].'</strong>
                        <small>'.$item['especialidade'].'</small>
                      </div>
                    </div>
                    <a href="#" title="Editar Post" onclick="editarPost(this)"><i class="fa fa-edit"></i></a>
                  </div>
                  <div class="blog-post-body">
                    '.base64_decode($item['content']).'
                  </div>
                  <div class="blog-btns">
                    <small> publicado em '.date('d/m/Y H:i', strtotime($item['date'])).'</small>
                    <small><b>'.count($comments).'</b> <a href="#" data-item="#comments_'.$id.'">Comentario(s)</a>   e  <b>'.count($questions).'</b> <a href="#" data-item="#questions_'.$id.'">Perguntas(s)</a></small>
                    <small><a href="">Comentar</a> | <a href="">Fazer uma Pergunta</a></small>
                  </div>
                  
                  <fieldset class="blog-comments" id="comments_'.$id.'">
                    <legend><b>Comentários</b></legend>';
                  
                 foreach($comments as $comment) {
                    echo '<div class="blog-comment">
                          <div class="blog-header-user">
                                  <img src="doutor.jpg" height="32px">
                                  <div class="blog-user-info">
                                    <strong>DR(a). '.$comment['nome_completo'].'</strong>
                                    <small>'.$comment['especialidade'].'</small>
                                    <small><b>'.base64_decode($comment['content']).'</b></small>
                                  </div>
                                </div>
                        </div>';
                  }
                   
                  echo '</fieldset>
                  
                  <fieldset class="blog-comments" id="questions_'.$id.'">
                  <legend><b>Perguntas</b></legend>';
                  
                 foreach($questions as $question) {
                    echo '<div class="blog-comment">
                          <div class="blog-header-user">
                                  <img src="doutor.jpg" height="32px">
                                  <div class="blog-user-info">
                                    <strong>DR(a). '.$question['nome_completo'].'</strong>
                                    <small>'.$question['especialidade'].'</small>
                                    <small>R. <b>'.base64_decode($question['content']).'</b></small>
                                  </div>
                                </div>
                        </div>';

                        
                  }

                  
                   
                  echo '</fieldset>
                </div>';
 
                }

                          
                           
                echo '</fieldset>
           </div>';
                  

  			?>
          </div>
        </div>
    </div>
</section>