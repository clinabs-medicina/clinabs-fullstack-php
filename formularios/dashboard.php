<?php
/*
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
    $pedidos = $pdo->query('SELECT
                                COUNT(*) AS pedidos,
                                ( SELECT COUNT(*) FROM PACIENTES ) AS pacientes,
                                ( SELECT COUNT(*) FROM MEDICOS ) AS medicos,
                                ( SELECT COUNT(*) FROM FUNCIONARIOS ) AS funcionarios,
                                ( SELECT COUNT(*) FROM VENDAS WHERE `status`=\'AGUARDANDO PAGAMENTO\') AS faturas,
                                ( SELECT COUNT(*) FROM AGENDA_MED WHERE `status` = \'AGENDADO\') AS consultas 
                            FROM
                                FARMACIA');
                                                    
    $dashboard = $pedidos->fetch(PDO::FETCH_ASSOC);
    $INICIO = date('Y-m-d H:i:s', strtotime('-1 minute '.date('Y-m-d H:i:s')));
    $FIM = date('Y-m-d H:i:s', strtotime('+1 minute '.date('Y-m-d H:i:s')));

                                                    $stmt0 = $pdo->query("SELECT * FROM `SESSIONS` WHERE last_ping BETWEEN '$INICIO' AND '$FIM'");
                                                    $clients_online = [];

                                                    try {
                                                    
                                                      foreach($stmt0->fetchAll(PDO::FETCH_ASSOC) as $item) {
                                                        $stmt01 = $pdo->prepare('SELECT * FROM (
                                                                                        SELECT nome_completo,email,cpf,objeto FROM `PACIENTES` UNION
                                                                                        SELECT nome_completo,email,cpf,objeto FROM `MEDICOS` UNION
                                                                                        SELECT nome_completo,email,cpf,objeto FROM `FUNCIONARIOS`
                                                                                    ) AS T
                                                                                    WHERE T.email = :user OR T.cpf = :user LIMIT 1');

                                                        $stmt01->bindValue(':user', $item['user_token']);
                                                        $stmt01->execute();

                                                        
                                                        $clients_online[] = $item;

                                                        $date1 = new DateTime($item['startTime']);
                                                        $date2 = new DateTime(date('Y-m-d H:i:s'));
                                                        $interval = $date1->diff($date2);

                                                        $XX = $stmt01->fetch(PDO::FETCH_ASSOC);

                                                        $clients_online['results'][] = array(
                                                          $XX['objeto'],
                                                          $XX['nome_completo'],
                                                          $item['ip'],
                                                          date('d/m/Y H:i', strtotime($item['startTime'])),
                                                          ($interval->d == 0 && $interval->h == 0 && $interval->i == 0 ? ' menos de 1 minuto ':($interval->d > 0 ? $interval->d.' dia(s) ':'').($interval->h > 0 ? $interval->h.' hora(s) ':'').($interval->i > 0 ? $interval->i.' minuto(s) ':'')),
                                                          '<button class="btn-action" type="button" onclick="deleteSession(\''.$item['session_id'].'\')"><img title="Encerrar esta Sessão" src="https://www.clinabs.com/assets/images/ico-delete.svg" height="28px"></button>'
                                                        );
                                                        
                                                      }
                                                    } catch(PDOException $ex) {
                                                   
                                                    }

    $dashboard['clients_online'] = $stmt0->rowCount();
    $api = [];
    $api['ip'] = $_SERVER['REMOTE_ADDR'];
    $api['timestamp'] = date('H:i');
    $api['counters'] = $dashboard;
    $api['results'] = $clients_online['results'];
    $api['tz'] = date_default_timezone_get();
    $api['server'] = [
      'cpu' => intval(trim(shell_exec("python3 -c 'import psutil\nprint(round(psutil.cpu_percent(interval=1)))'"))),
      'memory' => intval(trim(shell_exec("python3 -c 'import psutil\nprint(round(psutil.virtual_memory().percent))'"))),
      'storage' => intval(trim(preg_replace("/[^A-Za-z0-9]/", "", shell_exec('df -h | grep \'/dev/vda1\' | awk \'{print $5}\' | head -n1'))))
    ];
    unset($clients_online['results']);
    
    header('Content-Type: application/json');
    echo json_encode($api, JSON_PRETTY_PRINT);
    */