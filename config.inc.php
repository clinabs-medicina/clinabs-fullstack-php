<?php
session_start();

ini_set('display_errors', 1);
error_reporting(1);

$YOUTUBE_LINK = 'https://www.youtube.com/@Clinabs';
$FACEBOOK_LINK = 'https://www.facebook.com/share/U8N9ob4r3pvJJ1FT/?mibextid=qi2Omg';
$INSTAGEM_LINK = 'https://www.instagram.com/clinabsmedicinaintegrativa/';

$is_nabscare = false;

$frete = 225;
$_frete = 225;

date_default_timezone_set('America/Sao_Paulo');

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	$date_time = date('Y-m-d H:i:s');
	$errorLogFile = $_SERVER['DOCUMENT_ROOT'] . '/data/logs/php_errors.log';

	if (!file_exists($errorLogFile)) {
		mkdir($_SERVER['DOCUMENT_ROOT'] . '/data/logs', 0777, true);
	}

	$errorLogEntry = [
		'timestamp' => date('Y-m-d H:i:s'),
		'error_code' => $errno,
		'error_message' => $errstr,
		'file' => $errfile,
		'line' => $errline,
	];

	file_put_contents($errorLogFile, "{$date_time} - " . json_encode($errorLogEntry) . PHP_EOL, FILE_APPEND);
});

set_exception_handler(function ($exception) {
	$date_time = date('Y-m-d H:i:s');
	$exceptionLogFile = $_SERVER['DOCUMENT_ROOT'] . '/data/logs/exceptions_errors.log';

	if (!file_exists($exceptionLogFile)) {
		mkdir($_SERVER['DOCUMENT_ROOT'] . '/data/logs', 0777, true);
	}

	$errorLogEntry = [
		'timestamp' => date('Y-m-d H:i:s'),
		'error_code' => $exception->getCode(),
		'error_message' => $exception->getMessage(),
		'file' => $exception->getFile(),
		'line' => $exception->getLine(),
	];

	file_put_contents($errorLogFile, "{$date_time} - " . json_encode($errorLogEntry) . PHP_EOL, FILE_APPEND);
});

register_shutdown_function(function () {
	$last_error = error_get_last();
	if ($last_error !== null) {
		$date_time = date('Y-m-d H:i:s');
		$exceptionLogFile = $_SERVER['DOCUMENT_ROOT'] . '/data/logs/fatal_errors.log';

		if (!file_exists($exceptionLogFile)) {
			mkdir($_SERVER['DOCUMENT_ROOT'] . '/data/logs', 0777, true);
		}

		$errorLogEntry = [
			'timestamp' => date('Y-m-d H:i:s'),
			'error_code' => $last_error['type'],
			'error_message' => $last_error['message'],
			'file' => $last_error['file'],
			'line' => $last_error['line'],
		];

		file_put_contents($exceptionLogFile, "{$date_time} - " . json_encode($errorLogEntry) . PHP_EOL, FILE_APPEND);
	}
});

$hostname = $_SERVER['HTTP_HOST'];
$sessionName = 'token';

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

$servername = '64.225.0.218';
$database = 'clinabs_homolog';
$username = 'clinabs_dev';
$password = '&?7z?Yw$0]62N!gbn=l_@bbA0O{TRg:s';

$tz = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('P');

// Banco de Dados
try {
	$pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo 'Connection Failed: ' . $e->getMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once ($_SERVER['DOCUMENT_ROOT'] . '/libs/List.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/libs/CarrinhoCalc.php');
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
require_once ($_SERVER['DOCUMENT_ROOT'] . '/libs/CalendarWeekly.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/libs/Meet.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/libs/WhatsApp.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/asaas.class.php');

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

require_once ($_SERVER['DOCUMENT_ROOT'] . '/application.php');
