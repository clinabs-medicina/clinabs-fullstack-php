<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['token']) && $page->require_login == true)
{
    header('Location: /login?redirect='.$_SERVER['REQUEST_URI']);
}