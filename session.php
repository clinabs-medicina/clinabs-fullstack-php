<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['token'])) {
    if ($page->require_login) {
        header('Location: /login');
    }
/*    
    try{    
        $dat = substr($_SERVER['REQUEST_URI'], 0, 9); //$_SERVER['REQUEST_URI'];
        error_log("Valor session.php request: $dat\r\n" . PHP_EOL, 3, 'C:\xampp\htdocs\errors.log');
    } catch (PDOException $e) {
    }
*/
    if ((substr($_SERVER['REQUEST_URI'], 0, 13) !== '/agendamento/') &&
            (substr($_SERVER['REQUEST_URI'], 0, 9) !== '/medicos/') &&
            (substr($_SERVER['REQUEST_URI'], 0, 10) !== '/unidades/') &&
            (substr($_SERVER['REQUEST_URI'], 0, 10) !== '/cadastro/') &&
            //(substr($_SERVER['REQUEST_URI'], 0, 9) !== '/academy/') &&
            (substr($_SERVER['REQUEST_URI'], 0, 8) !== '/agenda/') 
            //&& (substr($_SERVER['REQUEST_URI'], 0, 13) !== '/blog/medico/')
        ) {
        header('Location: /login?redirect=' . $_SERVER['REQUEST_URI']);
    }
}
