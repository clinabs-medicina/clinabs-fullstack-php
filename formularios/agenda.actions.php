<?php
require_once '../config.inc.php';
ini_set('display_errors', 1);
error_reporting(1);

$action = $_REQUEST['action'];
$token = $_REQUEST['token'];

$json = [];

switch($action) {
    case 'agenda-cancel': {
        $sts = 'CANCELAMENTO PENDENTE';

        try {
            $stmt = $pdo->prepare('UPDATE AGENDA_MED SET `status` = :sts WHERE token = :ref');
            $stmt->bindValue(':sts', 'CANCELADO');
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

            $stmt2 = $pdo->prepare('UPDATE VENDAS SET `status` = :sts2 WHERE reference = :tk');
            $stmt2->bindValue(':sts2', 'CANCELAMENTO PENDENTE');
            $stmt2->bindValue(':tk', $token);

            $stmt2->execute();

            $json = [
                'status' => 'success',
                'text' => 'Agendamento Cancelado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'result' => $sts
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Cancelar Agendamento!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }

    case 'agenda-start': {
        $sts = 'EM CONSULTA';

        try {
            $stmt = $pdo->prepare('UPDATE AGENDA_MED SET `status` = :sts,startTime = :st WHERE token = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);
            $stmt->bindValue(':st', date('H:i:s'));

            $stmt->execute();

            $ag = $pdo->query("SELECT meet FROM AGENDA_MED WHERE token = '{$token}'");
            $row = $ag->fetch(PDO::FETCH_ASSOC);

            $meet = json_decode($row['meet']);


            $json = [
                'status' => 'success',
                'text' => 'Consulta Iniciada com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'newStatus' => 'PAGO',
                'result' => $sts,
                'token' => $token,
                'link' => $meet->hostRoomUrl
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Iniciar Consulta!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }
    
    case 'agenda-finish': {
        $sts = 'EFETIVADO';

        try {
            $stmt = $pdo->prepare('UPDATE AGENDA_MED SET `status` = :sts,endTime = :et,data_efetivacao = :data_efetivacao WHERE token = :ref');
            $stmt->bindValue(':sts', $sts);
            $stmt->bindValue(':ref', $token);
            $stmt->bindValue(':et', date('H:i:s'));
            $stmt->bindValue(':data_efetivacao', date('Y-m-d H:i:s'));


            $stmt->execute();

            $json = [
                'status' => 'success',
                'text' => 'Agendamento Finalizado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'newStatus' => 'PAGO',
                'result' => $sts
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Finalizar Agendamento!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }

    case 'agenda-delete-item': {
        try {
            $stmt = $pdo->prepare('DELETE FROM AGENDA_MED WHERE token = :ref');
            $stmt->bindValue(':ref', $token);

            $stmt->execute();

            $json = [
                'status' => 'success',
                'text' => 'Agendamento Deletado com Sucesso!',
                'icon' => 'success',
                'allowOutSideClick' => 'false',
                'newStatus' => 'PAGO',
                'result' => $sts
            ];
        }catch (Exception $ex) {
            $json = [
                'status' => 'error',
                'text' => 'Falha ao Deletar Agendamento!',
                'icon' => 'error',
                'allowOutSideClick' => 'false'
            ];
        }

        break;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json, JSON_PRETTY_PRINT);