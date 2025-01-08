<?php
foreach($_SESSION as $key => $value) {
    unset($_SESSION[$key]);
}

header('Location: /login');