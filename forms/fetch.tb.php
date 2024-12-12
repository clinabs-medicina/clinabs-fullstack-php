<?php

require_once '../config.inc.php';
error_reporting(1);
ini_set('display_errors', 1);
set_time_limit(300); // Define o limite para 300 segundos (5 minutos)
header("pragma: no-cache");

if(isset($_REQUEST['tb']) && isset($_COOKIE['sessid_clinabs'])) {
    if(isset($_REQUEST['q'])) {
        $tab = $_REQUEST['tb'];
	try {
    	error_log("Valor da variável fetch.tb \$_REQUEST['tb']: $tab\r\n" . PHP_EOL, 3, 'C:\xampp\htdocs\errors.log');
	} catch (PDOException $e) {
	}

        if(strtolower($_REQUEST['tb']) == 'medicos') {
            $dados = $pdo->query("SELECT id,identidade_genero,".implode(', ', explode(',', $_REQUEST['key']))." AS text FROM ".strtoupper($_REQUEST['tb']).' WHERE LOWER('.$_REQUEST['key'].') LIKE "%'.strtolower($_REQUEST['q']).'%" AND status = "ATIVO" ORDER BY id ASC;');
        } else {
            $dados = $pdo->query("SELECT id,".implode(', ', explode(',', $_REQUEST['key']))." AS text FROM ".strtoupper($_REQUEST['tb']).' WHERE '.$_REQUEST['key'].' LIKE "%'.$_REQUEST['q'].'%" ORDER BY id ASC');
        }
    }else {
        if(strtolower($_REQUEST['tb']) == 'medicos') {
            $dados = $pdo->query("SELECT id,token,identidade_genero,".implode(', ', explode(',', $_REQUEST['key']))." AS text FROM ".strtoupper($_REQUEST['tb']).' WHERE status = "ATIVO" ORDER BY id ASC;');
        } else {
            $dados = $pdo->query("SELECT id,".implode(', ', explode(',', $_REQUEST['key']))." AS text FROM ".strtoupper($_REQUEST['tb']));
        }
    }

    $array = $dados->fetchAll(PDO::FETCH_ASSOC);

   
    $results = [];

    foreach($array as $item) {
        if($_REQUEST['selected']) {
            $item['selected'] = true;
        } 

       if($_GET['tb'] == 'medicos') {
        $stmt = $pdo->query("SELECT calendario FROM AGENDA_MEDICA WHERE medico_token = '{$item['token']}'");
        $calendario = $stmt->fetch(PDO::FETCH_OBJ);

        $result_medicos = [];

        try {
            $calendar = json_decode($calendario->calendario, true);

            $datas = [];

            foreach($calendar as $data) {
                foreach($data as $d) {
                    if(strtotime("{$d['date']} {$d['time']}") > strtotime(date('Y-m-d H:i'))) {
                        $datas[] = "{$d['date']} {$d['time']}";
                    }
                }
            }

        
        } catch (Exception $ex) {

        }

        if(count($datas) > 0) {
            $item = json_decode(json_encode($item), true);
            $prefixo = strtoupper($item['identidade_genero']) == 'FEMININO' ? 'Dra.':'Dr.';
            $item['text'] = "{$prefixo} {$item['text']}";
            unset($item['identidade_genero']);
            $results['results'][] = $item;
        }
       } 
       else if($_GET['tb'] == 'especialidades') {
            $stmtx = $pdo->query("SELECT token,id,especialidade,identidade_genero FROM MEDICOS WHERE status = 'ATIVO'");
            $rows = $stmtx->fetchAll(PDO::FETCH_OBJ);

            $especialidades = [];

            foreach($rows as $row) {
                $stmty = $pdo->query("SELECT calendario,medico_token,(SELECT especialidade FROM MEDICOS WHERE token = medico_token) AS esp FROM AGENDA_MEDICA WHERE medico_token = '{$row->token}' GROUP BY medico_token");
                $calendario = $stmty->fetch(PDO::FETCH_OBJ);
        
                $result_medicos = [];
        
                try {
                    $calendar = json_decode($calendario->calendario, true);
        
                    $datas = [];
        
                    foreach($calendar as $data) {
                        foreach($data as $d) {
                            if(strtotime("{$d['date']} {$d['time']}") > strtotime(date('Y-m-d H:i'))) {
                                $datas[] = "{$d['date']} {$d['time']}";
                            }
                        }
                    }
        
                
                } catch (Exception $ex) {
        
                }
        
                if(count($datas) > 0) {
                    $stmtz = $pdo->query("SELECT nome FROM ESPECIALIDADES WHERE id = '{$calendario->esp}'");
                    $esp = $stmtz->fetch(PDO::FETCH_OBJ);


                    $results['results'][] = ['id' => $calendario->esp, 'text' => $esp->nome];

                    $x = [];

                    foreach($results['results'] as $i) {
                        $x[$i['id']] = $i;
                    }

                    $results['results'] = [];

                    foreach($x as $y) {
                        $results['results'][] = $y; 
                    }
                }
            }
       }

       else if($_GET['tb'] == 'anamnese') {
        $stmtx = $pdo->query("SELECT token,id,anamnese FROM MEDICOS WHERE status = 'ATIVO'");
        $rows = $stmtx->fetchAll(PDO::FETCH_OBJ);

        $especialidades = [];

        foreach($rows as $row) {
            $stmty = $pdo->query("SELECT calendario,medico_token,(SELECT anamnese FROM MEDICOS WHERE token = medico_token) AS esp FROM AGENDA_MEDICA WHERE medico_token = '{$row->token}' GROUP BY medico_token");
            $calendario = $stmty->fetch(PDO::FETCH_OBJ);
    
            $result_medicos = [];
    
            try {
                $calendar = json_decode($calendario->calendario, true);
    
                $datas = [];
    
                foreach($calendar as $data) {
                    foreach($data as $d) {
                        if(strtotime("{$d['date']} {$d['time']}") > strtotime(date('Y-m-d H:i'))) {
                            $datas[] = "{$d['date']} {$d['time']}";
                        }
                    }
                }
    
            
            } catch (Exception $ex) {
    
            }
    
            if(count($datas) > 0) {
                $anamneses = json_decode($calendario->esp);

                foreach($anamneses as $anamnese) {
                    $stmtz = $pdo->query("SELECT nome FROM ANAMNESE WHERE id = '{$anamnese}'");
                    $esp = $stmtz->fetch(PDO::FETCH_OBJ);


                    $results['results'][] = ['id' => $anamnese, 'text' => $esp->nome];
                }

                $x = [];

                foreach($results['results'] as $i) {
                    $x[$i['id']] = $i;
                }

                $results['results'] = [];

                foreach($x as $y) {
                    $results['results'][] = $y; 
                }
            }
        }
   }

       else {
            $results['results'][] = $item;
       }
    
    }
    
   header('Content-Type: application/json');
   echo json_encode($results, JSON_PRETTY_PRINT);
}