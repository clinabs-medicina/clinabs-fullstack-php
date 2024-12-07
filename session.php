<?php
if(!isset($_COOKIE['sessid_clinabs']) && $page->require_login == true)
{
    header('Location: /login?redirect='.$_SERVER['REQUEST_URI']);
}