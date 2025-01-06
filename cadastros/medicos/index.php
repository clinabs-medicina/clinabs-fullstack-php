<?php

          if (session_status() === PHP_SESSION_NONE) {
            session_start();
          }
          if(isset($_SESSION['userObj'])) {
            try {
                $user = (object) $_SESSION['userObj'];
            } catch (PDOException $e) {
        
            }
          }

$page = new stdClass();
$page->title = 'Médicos';
$page->content = 'cadastros/medicos/main.php';
$page->bc = true;
$page->name = 'link_medicos';
$page->require_login = true;
$page->includePlugins = true;

$useDT = true;
$useSelector = true;

require_once $_SERVER['DOCUMENT_ROOT'].'/session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';