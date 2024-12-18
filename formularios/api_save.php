<?php
require_once '../config.inc.php';
$config = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/.auth.json'), true);


if(isset($_POST['api_save'])) {
    $config['mail']['smtp_server'] = $_POST['smtp_server'];
    $config['mail']['smtp_port'] = $_POST['smtp_port'];
    $config['mail']['smtp_user'] = $_POST['smtp_user'];
    $config['mail']['smtp_pwd'] = $_POST['smtp_pwd'];
    $config['mail']['smtp_from_mail'] = $_POST['smtp_from_mail'];
    $config['mail']['smtp_from_name'] = $_POST['smtp_from_name'];
    $config['whatsapp']['key'] = $_POST['instanceKey'];
    $config['whatsapp']['token'] = $_POST['instanceToken'];
    $config['whatsapp']['login'] = $_POST['instanceLogin'];
    $config['whatsapp']['number'] = $_POST['instanceNumber'];
    $config['whatsapp']['webhook_url'] = $_POST['webhook_url'];
}