<?php
$page = new stdClass();
$page->title = 'Forum';
$page->content = isset($_GET['token']) ? 'blog/medico/conteudo.php':'blog/medico/main.php';
$page->bc = true;
$page->name = 'link_blog_medico';
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



require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';