<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

if(!isset($_SESSION['token']))
{
    header('Location: /login?redirect='.$_SERVER['REQUEST_URI']);
}