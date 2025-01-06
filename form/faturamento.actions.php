<?php
require_once '../config.inc.php';

$action = $_GET['action'];
$token = $_GET['token'];

switch($action) {
    case 'delete_payment': {
        try {
            $result = $asaas->deleteCobranca($token);

            if($result->deleted) {
                $pdo->query("INSERT INTO `EVENT_LOGS` (`user_token`, `user_name`, `event_name`, `event_value`) VALUES ('$user->token', '$user->nome_completo', 'DELETE_PAYMENT', '$token')");
                $resp = [
                    'status' => 'success',
                    'icon' => 'success',
                    'text' => 'Pagamento Cancelado com Sucesso!'
                ];
            } else {
                $resp = [
                    'status' => 'error',
                    'icon' => 'error',
                    'text' => 'Não foi Possível cancelar o Pagamento!'
                ];
            }
        } catch(Exception $ex) {
            $resp = [
                'status' => 'error',
                'icon' => 'error',
                'text' => 'Erro ao Cancelar o Pagamento!'
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($resp, JSON_PRETTY_PRINT);