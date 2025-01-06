<?php
$stmt = $pdo->prepare("SELECT * FROM `BLOG_MEDICO` WHERE token = :token");
$stmt->bindValue(':token', $_GET['token']);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);


 $stmty = $pdo->query("SELECT *,(SELECT nome FROM ESPECIALIDADES WHERE id= (SELECT especialidade FROM MEDICOS WHERE token = medico_token)) AS especialidade, (SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS nome_completo FROM `BLOG_POSTS` WHERE post_ref = '".$dados['id']."';");
 $items = $stmty->fetchAll(PDO::FETCH_ASSOC);
 ?>

<section class="main" id="user-main">
    <div class="flex-container">
      <div class="row dflex-column">
        <h3 class="form-title titulo-h1"><?=($dados['subject'])?></h3>
        <div class="toolbar-rtl" data-user="<?=($_GET['token'] ?? '')?>">
          <input type="search" class="form-control form-search" id="blog-add-post" data-search="pre">
          
          <?=($user->perms->blog_add_post ? '<button onclick="addBlogPost('.$dados['id'].')"><i class="fa fa-plus fa-2x"></i></button>':'')?>
        </div>
            
        <div class="blog-posts" data-id="<?=($dados['id'])?>">
            <?php
                foreach(filter_posts($items, 'post_type', 'POST') as $item) {
                  $comments = filter_posts($items, 'post_type', 'COMMENT');
                  $questions = filter_posts($items, 'post_type', 'QUESTION');
                  $anwers = filter_posts($items, 'post_type', 'ANSWER');
                  $anexos = json_decode($item['anexos'], true);
            
                  $id = uniqid();
            
                  echo '<div class="blog-post" data-doctor="'.$item['medico_token'].'" data-token="'.$_GET['token'].'" data-post-id="'.$dados['id'].'">
                          <div class="blog-header">
                            <div class="blog-header-user">
                              <img src="'.Modules::getUserImage($item['medico_token']).'" height="32px">
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
                            <small> publicado em '.date('d/m/Y H:i', strtotime($item['timestamp'])).'</small>
                            <small><b>'.count($comments).'</b> <a href="#" data-item="#comments_'.$id.'">Comentario(s)</a>   e  <b>'.count($questions).'</b> <a href="#" data-item="#questions_'.$id.'">Perguntas(s)</a>, <b>'.count($anexos).'</b> <a href="#" data-item="#anexos_'.$id.'">Anexo(s)</a></small>
                            <small><a class="btn-comment" data-post-action="comment" data-id="'.$item['id'].'">Comentar</a> | <a data-post-action="question" data-id="'.$item['id'].'">Fazer uma Pergunta</a></small>
                          </div>
                          
                          <fieldset class="blog-comments" id="comments_'.$id.'">
                            <legend><b>Comentários</b></legend>';
                          
                              foreach($comments as $comment) {
                                  echo '<div class="blog-comment">
                                        <div class="blog-header-user">
                                                <img src="'.Modules::getUserImage($comment['medico_token']).'" height="32px">
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
                                                <img src="'.Modules::getUserImage($question['medico_token']).'" height="32px">
                                                <div class="blog-user-info">
                                                  <strong>DR(a). '.$question['nome_completo'].'</strong>
                                                  <small>'.$question['especialidade'].'</small>
                                                  <small><b>'.base64_decode($question['content']).'</b></small>
                                                </div>
                                       		</div>
                                              
                                              <div class="btns-blog">
                                              	<a onclick="responderBlog('.$question['id'].')">Responder</a>
                                              </div>
                      
                                      </div>';
                                
                                echo '<fieldset>';
                  						echo '<legend>Respostas</legend>';
                  
                                        foreach($anwers as $answer) {
                                            echo '<div class="blog-comment">
                                                      <div class="blog-header-user">
                                                              <img src="'.Modules::getUserImage($answer['medico_token']).'" height="32px">
                                                              <div class="blog-user-info">
                                                                <strong>DR(a). '.$answer['nome_completo'].'</strong>
                                                                <small>'.$answer['especialidade'].'</small>
                                                                <small><b>'.base64_decode($answer['content']).'</b></small>
                                                              </div>
                                                       </div>
                                                    </div>';
                                        }
                                    echo '</fieldset>';
                                    }
                  				
                
                          echo '</fieldset>
                    
                          <fieldset class="blog-comments" id="anexos_'.$id.'">
                            <legend><b>Anexos</b></legend>';
                            
                                foreach($anexos as $anexo) {
                                    echo '<div class="blog-comment">
                                          <div class="blog-header-user">
                                            <img src="/assets/images/ico-doc-pdf.svg" height="32px">
                                              <div class="blog-user-info">
                                                <h6>Descrição do Anexo</h6>
                                                <a href="/data/attachments/'.$anexo['uri'].'">Ver Conteudo</a>
                                              </div>
                                            </div>
                                        </div>';  
                                  }
                      
                          
                          
                          echo '</fieldset>
                        </div>';
              }
            ?>
        </div>
      </div>
    </div>
</section>