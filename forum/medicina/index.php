<?php
$page = new stdClass();
$page->title = 'Forum';
$page->content = isset($_GET['token']) ? 'forum/medicina/conteudo.php':'forum/medicina/main.php';
$page->bc = true;
$page->name = 'link_forum_medicina';
$page->require_login = true;



function filter_posts($items, $key, $value) {
    $result = [];
  
    for($i = 0; $i < count($items); $i++) {
      $item = $items[$i];
  
      foreach($item as $k => $v) {
        if($k == $key && $v == $value) {
          $result[] = $item;
        }
      }
    }
  
    return $result;
  }
  
  
  $posts = array(
    array(
        'id' => 1,
        'timestamp' => '2024-05-29 19:01:17',
        'image' => 'https://static.wixstatic.com/media/8a9379c91e6042f887fd3637603c3f6b.jpg/v1/fit/w_100,h_67,al_c,q_80/file.webp',
        'subject' => 'Saúde Infantil',
        'text_desc' => 'Descreva a categoria do seu fórum. Chame atenção dos seus leitores e incentive-os a ler.',
        'nome_completo' => 'SEBASTIÃO DANILO VIEIRA',
        'medico_token' => '65fb460097bb5',
        'especialidade' => 'CIRURGIA GERAL',
        'posts' => array(
            array(
                'id' => 1,
                'post_id' => 1,
                'nome_completo' => 'SEBASTIÃO DANILO VIEIRA',
                'medico_token' => '65fb460097bb5',
                'especialidade' => 'CIRURGIA GERAL',
                'post_type' => 'ANSWER',
                'content' => 'PHA+b25kZSBjb21wcm8gZXN0YSA8c3Ryb25nPnJlbSZlYWN1dGU7ZGlvPC9zdHJvbmc+PzwvcD4='
            ),
            array(
                'id' => 1,
                'post_id' => 1,
                'nome_completo' => 'SEBASTIÃO DANILO VIEIRA',
                'medico_token' => '65fb460097bb5',
                'especialidade' => 'CIRURGIA GERAL',
                'post_type' => 'QUESTION',
                'content' => 'PHA+b25kZSBjb21wcm8gZXN0ZSBwcm9kdXRvPzwvcD4='
            ),
            array(
              'id' => 1,
              'post_id' => 1,
              'nome_completo' => 'SEBASTIÃO DANILO VIEIRA',
              'medico_token' => '65fb460097bb5',
              'especialidade' => 'CIRURGIA GERAL',
              'post_type' => 'COMMENT',
              'content' => 'PHA+b25kZSBjb21wcm8gZXN0YSA8c3Ryb25nPnJlbSZlYWN1dGU7ZGlvPC9zdHJvbmc+PzwvcD4='
            ),
            array(
              'id' => 1,
              'post_id' => 1,
              'nome_completo' => 'SEBASTIÃO DANILO VIEIRA',
              'medico_token' => '65fb460097bb5',
              'especialidade' => 'CIRURGIA GERAL',
              'post_type' => 'COMMENT',
              'content' => 'PHA+b25kZSBjb21wcm8gZXN0YSA8c3Ryb25nPnJlbSZlYWN1dGU7ZGlvPC9zdHJvbmc+PzwvcD4='
            ),
        ),
        'token' => '59e6e56t5t6',
    )
  );






//$stmt = $pdo->query('SELECT * FROM `BLOG_MEDICO`');
//$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);






require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';