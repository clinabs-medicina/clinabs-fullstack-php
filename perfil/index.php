<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';


if(!isset($_SESSION[$sessionName])) {
   //header('Location: /login');
}


$page = new stdClass();
$page->title = 'Meu Perfil';
$page->content = 'perfil/main.php';
$page->bc = true;
$page->name = 'link_perfil';
$page->require_login = true;

$useDT = true;
$useSelector = true;
$useEditor = true;
$useCalendar = true;

$conf = new stdClass();

function parse_bool($value) {
   if(gettype($value) == 'string') {
      $value = strtolower($value);
      if($value == 'true') {
         return true;
      }
      else {
         return false;
      }
   } else if (gettype($value) == 'boolean') {
      return $value;
   } else if (gettype($value) == 'integer') {
      if($value == 1) {
         return true;
      }
      else {
         return false;
      }
   } else {
      return false;
   }
}

//header('Content-Type: application/json');
//echo json_encode($_user, JSON_PRETTY_PRINT);


require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';