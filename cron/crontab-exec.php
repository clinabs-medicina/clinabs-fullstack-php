<?php
require_once '../config.inc.php';
file_put_contents('last_sync_cron.txt', date('Y-m-d H:i:s'));

if (strtotime(date('H:i')) >= strtotime('07:00') && strtotime(date('H:i')) <= strtotime('22:00')) {
    $today = date('Y-m-d');

    $stmt = $pdo->prepare("SELECT * FROM `CRONTAB` WHERE `data` LIKE :dt AND `status` = 'PENDENTE'");
    $stmt->bindValue(':dt', "%{$today}%");
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    $agendamentos = [];

    if($stmt->rowCount() > 0) {
        foreach($rows as $row) {
            if(date('Y-m-d H:i', strtotime($row->data)) == date('Y-m-d H:i')) {
                if($row->type == 'AGENDA_MED') {
                    $wa->sendLinkMessage(
                        $row->celular,  
                        $row->message, 
                        $row->link ?? 'https://clinabs.com/agenda', 
                        'CLINABS', 
                        'Lembrete de Consulta', 
                        'https://clinabs.com/assets/images/logo.png'
                    );
    
                    $pdo->query("UPDATE `CRONTAB` SET `status` = 'FINALIZADO' WHERE `id` = '{$row->id}'");
                } else if($row->type == 'DELETE_PAYMENT'){
                    $payment = $asaas->getCobranca($row->payment_id);

                    if($payment->status == 'PENDING' || $payment->status == 'OVERDUE') {
                        $asaas->deleteCobranca($row->payment_id);
                        $asaas->desfazerCobrancaRemovida($row->payment_id);
                        $pdo->query("UPDATE `CRONTAB` SET `status` = 'FINALIZADO' WHERE `payment_id` = '{$row->payment_id}'");
                    }
                }
            } else {
                
            }
        }
    }

    header('content-Type: application/json');
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents('last_sync_exec.txt', date('Y-m-d H:i'));
} else {
    header('content-Type: application/json');

    echo json_encode([
        'status' => 'error', 
        'text' => 'Agendamento fora de Horário de Execução',
        'timers' => [
            'time' => strtotime(date('H:i')),
            'min' => strtotime('07:00'),
            'max' => strtotime('20:00')
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}