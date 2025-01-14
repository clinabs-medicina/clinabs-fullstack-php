<?php
require_once '../config.inc.php';

try {
    if($_REQUEST['mode'] == 'undo') {
        $payment = $asaas->desfazer_recebimento_dinheiro($_GET['token']);
        $json = json_encode([
            'status' => 'success',
            'title' => 'Atenção',
            'icon' => 'success',
            'text' => 'Pagamento foi alterado com Sucesso!',
        ], JSON_PRETTY_PRINT);
    } else {
        $payment = $asaas->receber_dinheiro($_GET['token']);
        $json = json_encode([
            'status' => 'success',
            'title' => 'Atenção',
            'icon' => 'success',
            'text' => 'Pagamento Realizado com Sucesso!',
        ], JSON_PRETTY_PRINT);
    }
    
}catch(Exception $ex) {
    $json = json_encode([
        'status' => 'warning',
        'icon' => 'warning',
        'text' => 'Não foi Processar o Pagamento!.',
    ], JSON_PRETTY_PRINT);
}

header('Content-Type: application/json; charset=utf-8');
echo $json;