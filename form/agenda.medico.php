<?php
require_once '../config.inc.php';

$data = $_REQUEST;

if (isset($data['key'])) {
    if ($data['key'] == 'medicos' && isset($data['value'])) {
        $stmt = $pdo->prepare("SELECT nome_completo, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,token FROM MEDICOS WHERE id = :id AND `status` = 'ATIVO' LIMIT 1");
        $stmt->bindValue(':id', $data['value']);

        try {
            $stmt->execute();
            $medico = $stmt->fetch(PDO::FETCH_OBJ);

            $stmtx = $pdo->prepare('SELECT medico_token,calendario,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) as status,(SELECT status FROM MEDICOS WHERE token = medico_token) as status FROM `AGENDA_MEDICA` WHERE medico_token = :medico_token AND (SELECT status FROM MEDICOS WHERE token = medico_token) = "ATIVO"');
            $stmtx->bindValue(':medico_token', $medico->token);

            $stmtx->execute();

            if ($stmtx->rowCount() > 0) {
                $ag = [];

                foreach (json_decode($stmtx->fetch(PDO::FETCH_OBJ)->calendario) as $k => $v) {
                    $allowed = false;

                    foreach ($v as $h => $a) {
                        $allowed = strtotime("{$k} {$h}") >= strtotime(date('Y-m-d H:i'));
                    }

                    if (strtotime($k) >= strtotime(date('Y-m-d')) && $allowed) {
                        foreach ($v as $x => $y) {
                            $stmty = $pdo->query('SELECT data_agendamento,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token LIMIT 1) AS paciente_nome FROM `AGENDA_MED` WHERE DATE_FORMAT(data_agendamento, "%Y-%m-%d %H:%i") = "' . date('Y-m-d H:i', strtotime($k . ' ' . $x)) . '" AND medico_token = "' . $medico->token . '" GROUP BY data_agendamento');
                            $paciente = $stmty->fetch(PDO::FETCH_OBJ);

                            if ($stmty->rowCount() == 0) {
                                if (date('Y-m-d', strtotime($stmty->data_agendamento)) == date('Y-m-d') && strtotime($stmty->data_agendamento) >= time()) {
                                    $yyyy = date('Y', strtotime($k));
                                    $mm = date('m', strtotime($k));
                                    $dd = date('d', strtotime($k));

                                    $ag[] = $k;
                                } else {
                                    $yyyy = date('Y', strtotime($k));
                                    $mm = date('m', strtotime($k));
                                    $dd = date('d', strtotime($k));

                                    $ag[] = $k;
                                }
                            }
                        }
                    }
                }

                $resp = [
                    'status' => 'success',
                    'medico_nome' => $medico->nome_completo,
                    'especialidade' => $medico->especialidade,
                    'today' => date('Y-m-d'),
                    'day' => date('d'),
                    'month' => date('m'),
                    'year' => date('Y'),
                    'data' => array_unique($ag)
                ];
            } else {
                $resp = [
                    'status' => 'success',
                    'today' => date('Y-m-d'),
                    'day' => date('d'),
                    'month' => date('m'),
                    'year' => date('Y'),
                    'data' => []
                ];
            }
        } catch (PDOException $error) {
            $resp = [
                'status' => 'error',
                'text' => $error->getMessage()
            ];
        }
    } else if ($data['key'] == 'especialidades' && isset($data['value'])) {
        $ag = [];

        $stmty = $pdo->query("SELECT calendario,MEDICOS.especialidade FROM AGENDA_MEDICA,MEDICOS WHERE MEDICOS.token = AGENDA_MEDICA.medico_token AND MEDICOS.especialidade = '{$data['value']}' AND MEDICOS.status = 'ATIVO'");
        $agenda = $stmty->fetchAll(PDO::FETCH_OBJ);

        foreach ($agenda as $date) {
            try {
                $dataX = json_decode($date->calendario);

                foreach ($dataX as $k => $v) {
                    $allowed = false;

                    foreach ($v as $h => $a) {
                        $allowed = strtotime("{$k} {$h}") > strtotime(date('Y-m-d H:i'));
                    }

                    if (strtotime($k) > strtotime(date('Y-m-d')) && $allowed) {
                        $ag[] = date('Y-m-d', strtotime($k));
                    }
                }
            } catch (Exception $ex) {
                $ag[] = $ex->getMessage();
            }
        }

        $resp = [
            'status' => 'success',
            'today' => date('Y-m-d'),
            'day' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
            'data' => $ag
        ];
    } else if ($data['key'] == 'anamnese' && isset($data['value'])) {
        $ag = [];

        $stmty = $pdo->query('SELECT * FROM ( SELECT calendario, medico_token,(SELECT `status` FROM MEDICOS WHERE token = medico_token) AS sts, (SELECT anamnese FROM MEDICOS WHERE token = medico_token) AS anamnese FROM AGENDA_MEDICA ) AS T WHERE anamnese != "[]" AND anamnese LIKE \'%"' . $data['value'] . '"%\';');
        $agenda = $stmty->fetchAll(PDO::FETCH_OBJ);

        foreach ($agenda as $date) {
            if ($date->sts == 'ATIVO') {
                try {
                    $dataX = json_decode($date->calendario);

                    foreach ($dataX as $k => $v) {
                        $allowed = false;

                        foreach ($v as $h => $a) {
                            $allowed = strtotime("{$k} {$h}") > strtotime(date('Y-m-d H:i'));
                        }

                        if (strtotime($k) >= strtotime(date('Y-m-d')) && $allowed) {
                            $ag[] = date('Y-m-d', strtotime($k));
                        }

                        $a = strtotime($k);
                        $b = strtotime(date('Y-m-d'));
                    }
                } catch (Exception $ex) {
                    $ag[] = [];
                }
            }
        }

        $resp = [
            'status' => 'success',
            'today' => date('Y-m-d'),
            'day' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
            'data' => $ag
        ];
    } else {
        $ag = [];

        $stmty = $pdo->query('SELECT calendario,medico_token,(SELECT `status` FROM MEDICOS WHERE token = medico_token) as `status` FROM AGENDA_MEDICA');
        $agenda = $stmty->fetchAll(PDO::FETCH_OBJ);

        foreach ($agenda as $date) {
            if ($date->status == 'ATIVO') {
                try {
                    $data = json_decode($date->calendario);

                    foreach ($data as $k => $v) {
                        $allowed = false;

                        foreach ($v as $h => $a) {
                            $allowed = strtotime("{$k} {$h}") >= strtotime(date('Y-m-d H:i') && $allowed);
                        }

                        $xstmt = $pdo->prepare('SELECT * FROM AGENDA_MED WHERE data_agendamento = :dt AND medico_token = :mt');
                        $xstmt->bindValue(':dt', "{$k} {$h}");
                        $xstmt->bindValue(':mt', $date->medico_token);
                        $xstmt->execute();

                        if (strtotime("{$k} {$h}") >= strtotime(date('Y-m-d H:i')) && $xstmt->rowCount() == 0) {
                            $ag[] = date('Y-m-d', strtotime($k));
                        }
                    }
                } catch (Exception) {
                }
            }
        }

        $resp = [
            'status' => 'success',
            'today' => date('Y-m-d'),
            'day' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
            'data' => $ag
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(array_values(array_unique($resp['data'])), JSON_PRETTY_PRINT);
