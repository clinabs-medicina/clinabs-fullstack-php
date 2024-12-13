<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// if(isset($_SESSION['userObj'])) {
//     $user = (object) $_SESSION['userObj'];
// }

$page = new stdClass();
$page->title = 'Dashboard';
$page->content = 'dashboard/main.php';
$page->bc = true;
$page->name = 'link_dashboard';
$page->require_login = true;
$page->includePlugins = true;

require_once('../config.inc.php');


$stmt_agendamento = $pdo->query("SELECT id,( SELECT nome_completo FROM PACIENTES WHERE token = paciente_token ) AS paciente_nome, ( SELECT nome_completo FROM MEDICOS WHERE token = medico_token ) AS medico_nome, data_agendamento, paciente_token, modalidade, ( SELECT payment_id FROM VENDAS WHERE reference = token ) AS payment_id, STATUS FROM AGENDA_MED WHERE STATUS IN( 'AGENDADO', 'AGUARDANDO PAGAMENTO' ) AND data_agendamento >= NOW() ORDER BY id DESC LIMIT 10;");
$agendamentos = [] ;

foreach($stmt_agendamento->fetchAll(PDO::FETCH_OBJ) as $item) {
    $agendamentos[$item->data_agendamento] = $item;
}

ksort($agendamentos);






$stmt_pacientes = $pdo->query("SELECT nome_completo,token,createTime, (SELECT nome FROM ANAMNESE WHERE id = queixa_principal) AS queixa_principal, YEAR(CURDATE()) - YEAR(data_nascimento) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(data_nascimento, '%m%d')) AS age FROM PACIENTES WHERE creation_date >= NOW() - INTERVAL 3 DAY ORDER BY id DESC LIMIT 10;");
$pacientes = [];
foreach($stmt_pacientes->fetchAll(PDO::FETCH_OBJ) as $item) {
    $pacientes[$item->creation_date] = $item;
}
ksort($pacientes);


$stmt_medicos = $pdo->query("SELECT nome_completo,token,creation_date, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade, YEAR(CURDATE()) - YEAR(data_nascimento) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(data_nascimento, '%m%d')) AS age FROM MEDICOS WHERE creation_date >= NOW() - INTERVAL 3 DAY ORDER BY id DESC LIMIT 10;");
$medicos = [];
foreach($stmt_medicos->fetchAll(PDO::FETCH_OBJ) as $item) {
    $medicos[$item->creation_date] = $item;
}
ksort($medicos);


$stmt_acompanhamento = $pdo->query("SELECT *,(SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token) AS funcionario_nome,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome FROM `ACOMPANHAMENTO` WHERE proximo_acompanhamento >= NOW();");
$acompanhamentos = [];
foreach($stmt_acompanhamento->fetchAll(PDO::FETCH_OBJ) as $item) {
    $acompanhamentos[$item->timestamp] = $item;
}
ksort($medicos);


$stmt_counter = $pdo->query("SELECT * FROM ( (SELECT COUNT(*) AS medicos FROM MEDICOS) AS A, (SELECT COUNT(*) AS pacientes FROM PACIENTES) AS B, (SELECT COUNT(*) AS vendas FROM VENDAS WHERE status = 'AGUARDANDO PAGAMENTO') AS C, (SELECT COUNT(*) AS agendamentos FROM AGENDA_MED WHERE status = 'AGENDADO') AS D, (SELECT COUNT(*) AS pedidos FROM FARMACIA WHERE status != 'CANCELADO') AS E );");
$counter = $stmt_counter->fetch(PDO::FETCH_OBJ);

require_once $_SERVER['DOCUMENT_ROOT'].'/MasterPage.php';