<?php

declare(strict_types=1);
ini_set('display_errors', 0);
error_reporting(0);


@ini_set('upload_max_size', '64M');
@ini_set('post_max_size', '64M');
@ini_set('max_execution_time', '120');
session_set_cookie_params([
    'secure' => true,  // Só envia o cookie por HTTPS
    'httponly' => true,  // Impede acesso via JavaScript
    'samesite' => 'Strict'  // Previne envio em requisições de outros sites
]);
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$YOUTUBE_LINK = 'https://www.youtube.com/@Clinabs';
$FACEBOOK_LINK = 'https://www.facebook.com/share/U8N9ob4r3pvJJ1FT/?mibextid=qi2Omg';
$INSTAGEM_LINK = 'https://www.instagram.com/clinabsmedicinaintegrativa/';

$is_nabscare = false;

$frete = 225;
$_frete = 225;

date_default_timezone_set('America/Sao_Paulo');

ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/errors.log');

$hostname = $_SERVER['HTTP_HOST'];
$sessionName = 'sessid_clinabs';

$notificacoes_receitas = ['4133000780', '41995927699', '41992319253'];
$notificacoes_consultas = ['4133000780', '41995927699', '41992319253'];
$notificacoes_cadastros_errors = ['41995927699', '41992319253'];
$horario_funcionamento = ['inicio' => '08:00', 'fim' => '18:00'];

$host = $_SERVER['HTTP_HOST'];


define('hostname', $hostname);

$notificationPaymentCell = ['41998000425', '41992319253'];
$Numerofinanceiro = '';

use PHPMailer\PHPMailer\PHPMailer;

$sandbox = true;

$servername = 'localhost';
$database = 'clinabs_db';
$username = 'clinabs_dev';
$password = '&?7z?Yw$0]62N!gbn=l_@bbA0O{TRg:s';


$tz = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('P');

// Banco de Dados
try {
	$pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Connection Failed: " . $e->getMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/libs/List.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/libs/CarrinhoCalc.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/String.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/SQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Carrinho.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Funcionarios.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Medicos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Pacientes.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Produtos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Agenda.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Favoritos.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/Modules.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/libs/CalendarWeekly.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/libs/Meet.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/libs/WhatsApp.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/asaas.class.php');

$wa = new \HalloAPI\WhatsApp(
	instanceKey: 'GLCG-000629-i12u-aofB-X77N9XQ3GXZB',
	instanceToken: '5RLU7PHL-zPBP-4AVw-BgNb-2B67W4L9VX85',
	login: '9V60VP5I-SYAxyJ-AMbim7a2-ZHY47UPR3LCY',
);


$ASAAS_API_PROD = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDA0NjMxNjU6OiRhYWNoXzhhMWRmMDdlLWIyNDgtNDE1MS1hNmE3LTNkZjQxMzE5NjhjOA==';
$ASAAS_API_SANDBOX = '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAwODMzNDk6OiRhYWNoXzQzMmQyMDBhLTYxNzctNGQ2Ni05MmY5LWQ3MjhmYzZiNDRhZA==';

$asaas = new ASAAS(
	sandbox: $sandbox,
	pdo: $pdo,
	api_key: $ASAAS_API_SANDBOX,
	wallet_id: 'f4ab8c68-1175-4f26-a4c4-5e18a55a2a01'
);

// Email
$mailer = new PHPMailer();
$mailer->IsSMTP();
$mailer->SMTPDebug = false;
$mailer->SMTPSecure = 'tls';
$mailer->Port = 587;
$mailer->Host = 'smtp-relay.brevo.com';
$mailer->SMTPAuth = true;
$mailer->Username = 'desenvolvedor@clinabs.com';
$mailer->Password = 'gfjnAVJ5NcOP8tkp';
$mailer->FromName = 'CLINABS';
$mailer->From = 'naoresponder@clinabs.com';
$mailer->isHTML(true);


$funcionarios = new Funcionarios($pdo);
$medicos = new Medicos($pdo);
$pacientes = new Pacientes($pdo);
$produtos = new Produtos($pdo);
$carrinho = new Carrinho($pdo);
$favoritos = new Favoritos($pdo);
$agenda = new Agenda($pdo);

require_once($_SERVER['DOCUMENT_ROOT'] . '/application.php');