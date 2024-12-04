<?php
require_once '../config.inc.php';

if(isset($_POST['agenda_token']) && isset($_FILES['doc_receita'])) {
    $fname = '../data/receitas/assinadas/'.uniqid().'.pdf';

    if(move_uploaded_file($_FILES['doc_receita']['tmp_name'], $fname)) {
        $stmt = $pdo->prepare('UPDATE AGENDA_MED SET file_signed = :doc_receita WHERE token = :token');
        $stmt->bindValue(':doc_receita', basename($fname));
        $stmt->bindValue(':token', $_POST['agenda_token']);

        try{
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $resp = [
                    'status' => 'success',
                    'text' => 'Receita Anexada com Sucesso!'
                ];

                foreach($notificacoes_receitas as $number) {
                    try {
                        $stmt = $pdo->prepare('SELECT *,(SELECT identidade_genero FROM MEDICOS WHERE token = medico_token) AS sexo,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome,(SELECT nome_completo FROM PACIENTES WHERE token = paciente_token) AS paciente_nome FROM AGENDA_MED WHERE token = :token');
                        $stmt->bindValue(':token', $_POST['agenda_token']);
                        $stmt->execute();
                        $ag = $stmt->fetch(PDO::FETCH_OBJ);
                        $prefixo = strtoupper($ag->sexo) == 'MASCULINO' ? 'Dr.' : 'Dra.';
                        $dt = date('d/m/Y');
                        $text = "*Nova Receita Emitida pela Plataforma*\n\nMédico: *{$prefixo} {$ag->medico_nome}*\n\nPaciente: *{$ag->paciente_nome}*\n\nData da Emissão: {$dt}";
                        $docUrl = 'https://'.$_SERVER['HTTP_HOST'].'/data/receitas/assinadas/'.$ag->file_signed;
                        $wa->sendDocMessage('5541995927699', $text, $docUrl, 'Nova Receita Emitida pela Plataforma');
                   } catch(Exception $error) {
                     
                   }
                }

            } else {
                $resp = [
                    'status' => 'warning',
                    'text' => 'Ocorreu um Erro ao Enviar a Receita!'
                ];
            }
        } catch(PDOException $error) {
            $resp = [
                'status' => 'error',
                'text' => $erro->getMessage()
            ];
        }
    } else {
        $resp = [
            'status' => 'warning',
            'text' => 'Nenhum Documento foi Enviado!'
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($resp, JSON_PRETTY_PRINT);