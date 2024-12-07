<?php
require_once '../config.inc.php';

$linkUrl = $_GET['linkUrl'] ?? 'https://'.$hostname.'/';
$phoneNumber = $_GET['number'];
$text = $_GET['msg'];


if(substr($linkUrl, strlen($linkUrl) - 1, 1) != '/') {
    $linkUrl = "{$linkUrl}/";
}


$result = $wa->sendLinkMessage($phoneNumber, $text, $linkUrl, 'FINANCEIRO', 'Informações de Seu Pagamento', 'https://'.$hostname.'m/data/images/profiles/65c6047a35cb0.jpg');

header('Content-Type: application/json');
echo json_encode(
    [
        'icon' => 'success',
        'text' => 'Mensagem Enviada!',
        'width' => 'auto',
        'heightAuto' => true,
        'timer' => 3000,
        'timerProgressBar' => true,
        'showCancelButton' => false,
        'showConfirmButton' => false,
        'allowOutsideClick' => false
    ]
    );
    
