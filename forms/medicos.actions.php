<?php
global $agenda;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

switch($_GET['action']) {
    case 'delete-medico': {
        try{
            $stmt = $pdo->prepare('UPDATE MEDICOS SET status = :sts WHERE token = :token');
            $stmt->bindValue(':sts', 'INATIVO');
            $stmt->bindValue(':token', $_REQUEST['token']);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $json = json_encode([
                    'status' => 'success',
                    'text' => 'Médico Removido com Sucesso!',
                ], JSON_PRETTY_PRINT);
            } else {
                $json = json_encode([
                    'status' => 'error', 
                    'text' => 'Erro ao Atualizar',
                    'request' => $_REQUEST,
                    'response' => $ag->status
                ], JSON_PRETTY_PRINT);
            }
        } catch(Exception $ex) {
            $json = json_encode([
                'status' => 'error', 
                'text' => $ex->getMessage()
            ], JSON_PRETTY_PRINT);
        }
        break;
    }
}

header('Content-Type: application/json');
echo $json;