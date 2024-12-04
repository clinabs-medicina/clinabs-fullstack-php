<?php
require_once('../config.inc.php');
header('');

$dados = json_decode(file_get_contents('php://input'));

$calendario = base64_decode($dados->calendario);
$calendario  = mb_convert_encoding($calendario, 'UTF-8', 'ISO-8859-1');

if(isset($calendario)){
    $stmt = $pdo->prepare("INSERT INTO `AGENDA_MEDICA` (medico_token, calendario) VALUES ( :medico_token, :calendario) ON DUPLICATE KEY UPDATE calendario = :calendario;");
    $stmt->bindValue(':medico_token', $dados->token);
    $stmt->bindValue(':calendario', $calendario);
        
    try {
        $stmt->execute();
    } catch(PDOException $ex) {
        
    }
}