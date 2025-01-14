<?php
require_once '../config.inc.php';

$stmt = $pdo->prepare("SELECT *,(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp FROM MEDICOS WHERE id  = :id");
$stmt->bindValue(':id', $_GET['id']);


        
    try {
        $stmt->execute();
        $medico = $stmt->fetch(PDO::FETCH_OBJ);

        
        $xstmt = $pdo->prepare("SELECT *,(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp FROM MEDICOS WHERE grupo_especialidades = :grupo_especialidades AND id != :id AND status = 'ATIVO'");
        $xstmt->bindValue(':id', $_GET['id']);
        $xstmt->bindValue(':grupo_especialidades', $medico->grupo_especialidades);
        $xstmt->execute();
        $medicos = $xstmt->fetchAll(PDO::FETCH_OBJ);


        $medico->image = Modules::getUserImage($medico->token);


        $queixas = [];

        $xxx = $pdo->query('SELECT nome,id FROM ANAMNESE');

        foreach($xxx->fetchAll(PDO::FETCH_OBJ) as $row) {
            $queixas[$row->id] = $row->nome;
        }

        foreach($medicos as $item) {
            $item->image = Modules::getUserImage($item->token);
            $item->prefixo = strtoupper($item->identidade_genero) == 'MASCULINO' ? 'Dr.':'Dra.';
        }

        $_queixas = [];

            foreach(json_decode($medico->anamnese) as $queixa) {
                $_queixas[] = $queixas[$queixa];
            }

            $medico->queixas = $_queixas;

        $resp = [
            'status' => 'success',
            'medico' => $medico,
            'atrelados' => $medicos
        ];

    } catch(Exception $ex) {
        $resp = [
            'status' => 'error'
        ];
    }


    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($resp, JSON_PRETTY_PRINT);