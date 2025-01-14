<?php
require_once '../config.inc.php';


$result = [];

if(isset($_GET['data']) && isset($_GET['filter_ag']) && isset($_GET['select_filter'])) {
    
} else {
    $query = "SELECT
    (
    SELECT
        nome_completo
    FROM
        MEDICOS
    WHERE
        token = medico_token
) AS medico_nome,
(
    SELECT
        (
        SELECT
            nome
        FROM
            ESPECIALIDADES
        WHERE
            id = especialidade
    ) AS especialidade
FROM
    MEDICOS
WHERE
    token = medico_token
) AS medico_especialidade,
(
    SELECT
STATUS
FROM
    MEDICOS
WHERE
    token = medico_token
) AS medico_status,
calendario
FROM
    AGENDA_MEDICA
WHERE
    (
    SELECT
STATUS
FROM
    MEDICOS
WHERE
    token = medico_token
) = 'ATIVO' AND calendario LIKE '%\"{$_GET['data']}\"%';";

    $stmt = $pdo->query($query);

    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);


    foreach($rows as $row) {
        $online = false;
        $presencial = false;

        $horarios = [];

        $_horarios = json_decode($row->calendario, true)[$_GET['data']];

        foreach($_horarios as $k => $v) {
            $v['online'] == "true";
            $v['presencial'] = $v['presencial'] == "true";

            if($v['online'] == "true") {
                $online = true;
            }

            if($v['presencial'] == "true") {
                $presencial = true;
            }

            $horarios[] = $v;
        }

        $calendario[$row->medico_nome] = [
            'btn_online' => $online,
            'btn_presencial' => $presencial,
            'horarios' => $horarios 
        ];

        break;
    }
}



header('Content-Type: application/json; charset=utf-8');
echo json_encode($calendario, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);