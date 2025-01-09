<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

global $agenda;
$request = json_decode(file_get_contents('php://input'), true);

$date = trim($request["data_agendamento"]);

$new_paciente = false;
$paciente = [];
$resp = [];

$continue = false;

require_once $_SERVER["DOCUMENT_ROOT"] . "/config.inc.php";
function dueDate($data_gendamento, $data_atual) {
    $agendamento = strtotime($data_gendamento);
  
    $diferenca = ($agendamento - strtotime($data_atual));
  
    $minutos = round($diferenca / 60);
    $horas = round($minutos / 60);
    $dias = round($horas / 24);


    $vencimento = date('Y-m-d', $agendamento);
    $week = date('D', strtotime($vencimento));
  

    if( $horas  <= 1) {
        return date('Y-m-d H:i', strtotime('+20 minutes'));
    } else if( $horas > 1 && $horas <= 6) {
      return date('Y-m-d H:i', strtotime('+20 minutes'));
    } else if( $dias == 1 && strtotime(date('Y-m-d H:i')) <= date('Y-m-d', strtotime($vencimento. ' -1 day')).' 18:00') {
      return date('Y-m-d').' 18:00';
    } else if( $dias == 1 && strtotime(date('Y-m-d H:i')) > date('Y-m-d', strtotime($vencimento. ' -1 day')).' 18:00') {
      return date('Y-m-d H:i', strtotime('+20 minutes'));
    } else if($dias >= 2) {
      $vencimento = date('Y-m-d', $agendamento);
      $week = date('D', strtotime($vencimento));
      return date('Y-m-d', strtotime($vencimento. ' -1 day')).' 18:00';
    }
  }


$medico = $pdo->query("SELECT duracao_atendimento,tempo_limite_online,tempo_limite_presencial FROM MEDICOS WHERE token = '{$request["medico_token"]}'");
$medico = $medico->fetch(PDO::FETCH_OBJ);
  
$tempo_limite = strtoupper($request['modalidade']) == "ONLINE" ? ($medico->tempo_limite_online * 60) : ($medico->tempo_limite_presencial * 60);

if(isset($request['paciente_token'])) {
    $paciente_token = $request['paciente_token'];
    $stmt_paciente = $pdo->query("SELECT * FROM PACIENTES WHERE token = '{$paciente_token}'");

    $new_paciente = ($stmt_paciente->rowCount() == 0);
} else {
    $new_paciente = false;
    $paciente = '';
}


if((strtotime($date) - time()) > $tempo_limite) {
    $stmt_ag = $pdo->query(
        "SELECT * FROM `AGENDA_MED` WHERE data_agendamento = '$date' AND medico_token = '{$request["medico_token"]}';"
    );

    $token = md5($request['cpf']).uniqid();

    $pwd = md5(sha1(uniqid()));
    $payment_error = "";
    $paciente = new stdClass();

    if (
        $stmt_ag->rowCount() == 0 &&
        strtotime($date) > strtotime(date("Y-m-d H:i"))
    ) {
        $meet = new WhereByMeet();
        $room = $meet->createRoom(uniqid());

        $ag = new stdClass();
        $ag->token = $token;
        $ag->paciente_token =
            strlen($request["paciente_token"]) > 0
                ? $request["paciente_token"]
                : uniqid();
        $ag->medico_token = $request["medico_token"];
        $ag->anamnese = $request["anamnese"];
        $ag->modalidade = strtoupper($request["atendimento"]);
        $ag->data_agendamento = $request["data_agendamento"];
        $ag->duracao_agendamento = $medico->duracao_atendimento;
        $ag->descricao = strtoupper($request["descricao"]);
        $ag->valor = $request["valor_pagar"];
        $ag->meet = json_encode($room);
        $ag->payment_method = $request["payment_mode"];
        $ag->cupom = $request["cupom"];

        if($new_paciente) {
            $paciente = new stdClass();
            $paciente->cpf = $request['cpf'];
            $paciente->nome_completo = $request['nome_completo'];
            $paciente->rg = $request['rg'];
            $paciente->nacionalidade = $request['nacionalidade'];
            $paciente->nome_preferencia = $request['nome_preferencia'];
            $paciente->identidade_genero = $request['identidade_genero'];
            $paciente->data_nascimento = $request['data_nascimento'];
            $paciente->email = $request['email'];
            $paciente->anamnese = $request['anamnese'];
            $paciente->celular = $request['celular'];
            $paciente->responsavel_cpf = $request['responsavel_cpf'];
            $paciente->responsavel_nome_completo = $request['responsavel_nome_completo'];
            $paciente->responsavel_rg = $request['responsavel_rg'];
            $paciente->responsavel_celular = $request['responsavel_celular'];

            $stmt_x = $pdo->query("SELECT * FROM PACIENTES WHERE email = '{$request['email']}'");

            if($stmt_x->rowCount() > 0) {
                $paciente_db = $stmt_x->fetch(PDO::FETCH_OBJ);
                
                $validation_mail = [
                    'icon' => 'warning',
                    'text' => 'Desculpe mas o e-mail \''.$request['email'].'\' já está sendo utilizado por \''.$paciente_db->nome_completo.'\'!.',
                    'request' => $request
                ];
            } else {
                $validation_mail = null;
            }
        } else {
            $paciente = $stmt_paciente->fetch(PDO::FETCH_OBJ);
        }

        if($validation_mail == null) {
            $birthdate = Modules::parseDate($request["data_nascimento"]);
            $birthDate = new DateTime($birthdate);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate);

            if($age->y < 18) {
                try {
                    $pac = $asaas->create_or_get_client(
                        token: uniqid(),
                        nome: $request['responsavel_nome_completo'],
                        cpf: $request['responsavel_cpf'],
                        email: $request['email'],
                        celular: $request['responsavel_celular']
                    );

                    $continue = true;
                } catch(Exception $ex) {
                    $continue = false;
                    $resp = [
                        'icon' => 'warning',
                        'text' => $ex->getMessage(),
                        'request' => $request
                    ];
                }
            } else {
                try {
                    $pac = $asaas->create_or_get_client(
                        token: $ag->paciente_token,
                        nome: $paciente->nome_completo,
                        cpf: $paciente->cpf,
                        email: $paciente->email,
                        celular: $paciente->celular
                    );

                   $continue = true;
                } catch(Exception $ex) {
                    $continue = false;
                    $resp = [
                        'icon' => 'warning',
                        'text' => $ex->getMessage(),
                        'request' => $request
                    ];
                }
            }

           
        }else {
            $resp = $validation_mail;
        }
        
    } else {
        $resp = [
            'icon' => 'warning',
            'text' => 'Desculpe mas este horário não está mais disponível.',
            'request' => $request
        ];
    }
} 
else {
    $resp = [
        'icon' => 'warning',
        'text' => 'Desculpe mas este horário não está mais disponível.',
        'request' => $request
    ];
}


if($continue) {
    $token = md5($request['cpf']).uniqid();


    $pwd = md5(sha1(uniqid()));
    $payment_error = "";
    $paciente = new stdClass();

    if (
        $stmt_ag->rowCount() == 0 &&
        strtotime($date) > strtotime(date("Y-m-d H:i"))
    ) {
        $meet = new WhereByMeet();
        $room = $meet->createRoom(uniqid());

        $ag = new stdClass();
        $ag->token = $token;
        $ag->paciente_token =
            strlen($request["paciente_token"]) > 0
                ? $request["paciente_token"]
                : uniqid();
        $ag->medico_token = $request["medico_token"];
        $ag->anamnese = $request["anamnese"];
        $ag->modalidade = strtoupper($request["atendimento"]);
        $ag->data_agendamento = $request["data_agendamento"];
        $ag->duracao_agendamento = $medico->duracao_atendimento;
        $ag->descricao = strtoupper($request["descricao"]);
        $ag->valor = $request["valor_pagar"];
        $ag->meet = json_encode($room);
        $ag->payment_method = $request["payment_mode"];
        $ag->cupom = $request["cupom"];

        $sql = "INSERT INTO `AGENDA_MED` (`cupom`,`valor`,`paciente_token`, `medico_token`, `modalidade`, `anamnese`, `data_agendamento`, `duracao_agendamento`, `descricao`, `meet`, `token`, `payment_method`) 
        VALUES (:cupom, :valor, :paciente_token, :medico_token, :modalidade, :anamnese, :data_agendamento, :duracao_agendamento, :descricao, :meet, :token, :payment_method);";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":cupom", $ag->cupom);
        $stmt->bindValue(":valor", $ag->valor);
        $stmt->bindValue(":paciente_token", $ag->paciente_token);
        $stmt->bindValue(":medico_token", $ag->medico_token);
        $stmt->bindValue(":modalidade", strtoupper($ag->modalidade));
        $stmt->bindValue(":anamnese", $ag->anamnese);
        $stmt->bindValue(":data_agendamento", $ag->data_agendamento);
        $stmt->bindValue(":duracao_agendamento", $ag->duracao_agendamento);
        $stmt->bindValue(":descricao", $ag->descricao);
        $stmt->bindValue(":meet", $ag->meet);
        $stmt->bindValue(":token", $ag->token);
        $stmt->bindValue(":payment_method", $ag->payment_method);

        try {
            if ($request["add_new"] != $request["cpf"]) {
                $paciente->nome_completo = strtoupper($request["nome_completo"]);
                $paciente->nacionalidade = $request["nacionalidade"];
                $paciente->nome_preferencia = $request["nome_preferencia"];
                $paciente->identidade_genero = $request["identidade_genero"];
                $paciente->cpf = preg_replace("/[^0-9]+/", "", $request["cpf"]);
                $paciente->rg = preg_replace("/[^0-9]+/", "", $request["rg"]);
                $paciente->data_nascimento = Modules::parseDate($request["data_nascimento"]);
                $paciente->telefone = "";
                $paciente->celular = preg_replace(
                    "/[^0-9]+/",
                    "",
                    $request["celular"]
                );
                $paciente->email = $request["email"];
                $paciente->senha = $pwd;
                $paciente->token = $ag->paciente_token;
                $paciente->medico_token = $request["medico_token"];
                $paciente->queixa_principal = $request["anamnese"];
                $paciente->doc_rg_frente = "";
                $paciente->doc_rg_verso = "";
                $paciente->doc_cpf_frente = "";
                $paciente->doc_cpf_verso = "";
                $paciente->doc_comp_residencia = "";
                $paciente->doc_procuracao = "";
                $paciente->doc_anvisa = "";
                $paciente->doc_termos = "";
                $paciente->perms = 12;
                $paciente->responsavel_nome = $request['responsavel_nome_completo'];
                $paciente->responsavel_cpf = $request['responsavel_cpf'];
                $paciente->responsavel_rg = $request['responsavel_rg'];
                $paciente->responsavel_contato = $request['responsavel_celular'];

                if ($pacientes->Add($paciente)) {
                    $celular = preg_replace("/[^0-9]+/", "", $request["celular"]);
                    $ddd = substr($celular, 0, 2);
                    $cell = substr($celular, 2, strlen($celular));

                    $added = true;

                    $wa->sendLinkMessage(
                        phoneNumber: $paciente->celular,
                        text: "clique no link para confirmar sua conta!",
                        linkUrl: "https://".$_SERVER['HTTP_HOST']."/login?newPassword&token=".$ag->paciente_token,
                        linkTitle: "CLINABS",
                        linkDescription: "Confirmar Conta",
                        linkImage: "https://".$_SERVER['HTTP_HOST']."/assets/images/logo.png"
                    );
                }

                $sessid = md5($ag->paciente_token);
                $time = time() + (3600 * 24) * 365;
                //setcookie('sessid_clinabs', $sessid, $time, '/', hostname, true);
            }
            else {
                $paciente->nome_completo = strtoupper($request["nome_completo"]);
                $paciente->nacionalidade = $request["nacionalidade"];
                $paciente->nome_preferencia = $request["nome_preferencia"];
                $paciente->identidade_genero = $request["identidade_genero"];
                $paciente->cpf = $request["cpf"];
                $paciente->rg = $request["rg"];
                $paciente->data_nascimento = date(
                    "Y-m-d",
                    strtotime($request["data_nascimento"])
                );
                $paciente->celular = preg_replace(
                    "/[^0-9]+/",
                    "",
                    $request["celular"]
                );
                $paciente->email = $request["email"];
                $paciente->token = $ag->paciente_token;
                $paciente->medico_token = $request["medico_token"];
                $paciente->queixa_principal = $request["anamnese"];

                $pacientes->basicUpdate($paciente);
            }

            $paciente = $pdo->query(
                "SELECT * FROM PACIENTES WHERE token = '{$ag->paciente_token}';"
            );

            $paciente = $paciente->fetch(PDO::FETCH_OBJ);

            try {
                $birthdate = Modules::parseDate($request["data_nascimento"]);

                $birthDate = new DateTime($birthdate);
                $currentDate = new DateTime();
                $age = $currentDate->diff($birthDate);

                if($age->y < 18) {
                    $pac = $asaas->create_or_get_client(
                        token: uniqid(),
                        nome: $request['responsavel_nome_completo'],
                        cpf: $request['responsavel_cpf'],
                        email: $request['email'],
                        celular: $request['responsavel_celular']
                    );
                } else {
                    $pac = $asaas->create_or_get_client(
                        token: $ag->paciente_token,
                        nome: $paciente->nome_completo,
                        cpf: $paciente->cpf,
                        email: $paciente->email,
                        celular: $paciente->celular
                    );
                }

                if (
                    !isset($pac->errors) &&
                    isset($pac->id)
                )
                {
                   
                    try {
                        if($request['payment_mode'] != 'UNDEFINED') {
                            $link = $asaas->cobrar(
                                id: $pac->id, 
                                tipo:  $ag->payment_method == 'PAYMENT_LINK' ? 'UNDEFINED':$ag->payment_method, 
                                valor: $ag->valor, 
                                reference: $token, 
                                descricao: "VENDA DE CONSULTA MÉDICA",
                                paymentDue: date('Y-m-d', strtotime($ag->data_agendamento))
                            );
                        } else {
                            $link = [];
                        }

                        
                        if(isset($link->invoiceUrl)) {
                            $pdo->query(
                                "UPDATE PACIENTES SET payment_id = '{$pac->id}' WHERE token = '{$request["paciente_token"]}';"
                            );
        
        
                           
        
                            $stmt1 = $pdo->prepare("INSERT INTO `VENDAS` (
                                                                            `nome`,
                                                                            `code`,
                                                                                `amount`, 
                                                                                `customer`,
                                                                                `status`, 
                                                                                `payment_method`,
                                                                                    `reference`,
                                                                                    `module`,
                                                                                    `cupom`,
                                                                                    `payment_id`,
                                                                                    `dueTime`
                                                                                    )
                                                                            VALUES
                                                                                (
                                                                                :nome,
                                                                                :token,
                                                                                :valor,
                                                                                :paciente_token,
                                                                                :sts,
                                                                                :payment_method,
                                                                                :reference,
                                                                                :module,
                                                                                :cupom,
                                                                                :payment_id,
                                                                                :dueTime
                                                                                )");
        
                            $stmt1->bindValue(":nome", "VENDA DE CONSULTA MÉDICA");
                            $stmt1->bindValue(":token", $token);
                            $stmt1->bindValue(":valor", $ag->valor);
                            $stmt1->bindValue(":paciente_token", $ag->paciente_token);
                            $stmt1->bindValue(":sts", "AGUARDANDO PAGAMENTO");
                            $stmt1->bindValue(":payment_method", "NÃO DEFINIDO");
                            $stmt1->bindValue(":reference", $token);
                            $stmt1->bindValue(":module", "AGENDA_MED");
                            $stmt1->bindValue(":cupom", $ag->cupom);
                            $stmt1->bindValue(":payment_id", $link->id);
                            $stmt1->bindValue(":dueTime", dueDate($ag->data_agendamento, date('Y-m-d H:i')));
        
                            try {
                                $stmt1->execute();
        
                                $json = [
                                    "status" => "success",
                                    "icon" => "success",
                                    "text" => "Consulta Agendada com Sucesso!",
                                    "link" => $link->invoiceUrl,
                                    'linkUrl' => $added && !isset($_SESSION['token']) ? 'https://'.$_SERVER['HTTP_HOST'].'/login?action=newPassword&token='.$ag->paciente_token : '',
                                    "paymentLink" => true,
                                    "createPwd" => $added
                                ];


                                if($ag->modalidade == "ONLINE") {
                                    $msg1 = "Olá {$paciente->nome_completo}\n\nsegue o link da Teleconsulta  com {$prefixo}. {$medico_nome}\n*Data*: {$data_agendamento}\n*Especialidade*: {$especialidade}\n*Link*: {$room->roomUrl}";
                                    $wa->sendTextMessage($paciente->celular, $msg1);
                                }


                                foreach($notificacoes_consultas as $phoneNumber) {
                                    try {
                                        $med = $pdo->query("SELECT nome_completo,identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade FROM MEDICOS WHERE token = '{$request["medico_token"]}'");
                                        $pac = $pdo->query("SELECT nome_completo, celular FROM PACIENTES WHERE token = '{$request["paciente_token"]}'");
                                        $med = $med->fetch(PDO::FETCH_OBJ);
                                        $pac = $pac->fetch(PDO::FETCH_OBJ);
                                        $especialidade = $med->especialidade;
                                        $paciente_nome = $pac->nome_completo;
                                        $medico_nome = $med->nome_completo;
                                        $data_agendamento = date("d/m/Y H:i", strtotime($ag->data_agendamento));
                                        $prefixo = strtolower($med->identidade_genero) == "feminino" ? "Dra." : "Dr.";
                                        $fatura_valor = number_format($ag->valor, 2, ",", ".");

                                        $text = "Nova Consulta Agendada na Plataforma.\n\n\n*Paciente*: {$paciente->nome_completo}\n*Data da Consulta*: {$data_agendamento}\n*Médico*: {$prefixo} {$medico_nome}\n*Especialidade*: {$especialidade}\n*Valor*: R$ {$fatura_valor}\n\n*Link*: {$link->invoiceUrl}";
                                        $wa->sendTextMessage($phoneNumber, $text);

                                    } catch(Exception $e) {

                                    }
                                }

        
                                $stmtc = $pdo->query(
                                    "SELECT nome_completo,celular identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade FROM MEDICOS WHERE token = '{$request["medico_token"]}'"
                                );
                                $medico = $stmtc->fetch(PDO::FETCH_OBJ);
        
                                $fatura_valor = number_format($ag->valor, 2, ",", ".");
                                $data_agendamento = date(
                                    "d/m/Y H:i",
                                    strtotime($ag->data_agendamento)
                                );
                                $medico_nome = $medico->nome_completo;
                                $prefixo =
                                    strtolower($medico->identidade_genero) == "feminino"
                                        ? "Dra."
                                        : "Dr.";
                                $especialidade = $medico->especialidade;

                            if ($ag->payment_method == "PAYMENT_LINK") {
                                $stmtc = $pdo->query(
                                    "SELECT nome_completo,nome_clinica,celular, identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,token FROM MEDICOS WHERE token = '{$request["medico_token"]}'"
                                );
            
                                $medico = $stmtc->fetch(PDO::FETCH_OBJ);
            
                                $stmt2 = $pdo->prepare("SELECT * FROM ENDERECOS WHERE user_token = :medico_token");
                                $stmt2->bindValue(':medico_token', $medico->token);
                                $stmt2->execute();
            
                                $endereco = $stmtc->fetch(PDO::FETCH_OBJ);
            
                                $fatura_valor = number_format($ag->valor, 2, ",", ".");
                                $data_agendamento = date(
                                    "d/m/Y H:i",
                                    strtotime($ag->data_agendamento)
                                );
                                $medico_nome = $medico->nome_completo;
                                $prefixo =
                                    strtolower($medico->identidade_genero) == "feminino"
                                        ? "Dra."
                                        : "Dr.";
                                $especialidade = $medico->especialidade;
                                $clinica = strtoupper($medico->nome_clinica);
            
                                $msg = "Olá {$paciente->nome_completo},". PHP_EOL;
                                $msg .= "Agradecemos o seu contato e agendamento da consulta.". PHP_EOL;
                                $msg .= "". PHP_EOL;
                                $msg .= "". PHP_EOL;
                                $msg .= "Valor: R$ {$fatura_valor}". PHP_EOL;
                                $msg .=
                                    "Data do Agendamento: {$data_agendamento}". PHP_EOL;
                                $msg .= "Médico: {$prefixo} {$medico_nome}". PHP_EOL;
                                $msg .= "Especialidade: {$especialidade}". PHP_EOL;
                                
                                if($stmt2->rowCount() > 0) {
                                    $msg .= "". PHP_EOL;
                                    $msg .= "Clinica: {$medico->clinica_nome}". PHP_EOL;
                                    $msg .= "Endereço: {$endereco->logradouro}, {$endereco->numero}". PHP_EOL;
                                    $msg .= "Cidade: {$endereco->cidade}". PHP_EOL;
                                    $msg .= "Bairro: {$endereco->bairro}". PHP_EOL;
                                }

                                $msg .= "" . PHP_EOL;
                                $msg .=
                                    "Sua consulta foi agendada via convênio médico e o processo de liberação do plano será realizada no momento da consulta." .
                                    PHP_EOL;
                
                                $msg .= "" . PHP_EOL;
                                $msg .= "Qualquer dúvida, permanecemos à disposição!" .PHP_EOL;
                                $msg .= "" . PHP_EOL;
                                $msg .= "Clinabs Telemedicina (41) 3300-0790" .PHP_EOL;
                                $dta = date(
                                    "Y-m-d H:i",
                                    strtotime($data_agendamento . "-1 hour")
                                );
                                $stmts = $pdo->query(
                                    "INSERT INTO `CRONTAB` (`nome`, `data`, `message`, `celular`, `type`, `status`, `output`) VALUES ('{$paciente->nome_completo}', '{$dta}', '{$msg}', '{$paciente->celular}', 'AGENDA_MED', 'PENDENTE', '')"
                                );
            
                                $wa->sendLinkMessage(
                                    phoneNumber: $paciente->celular,
                                    text: $msg,
                                    linkUrl: "https://".$_SERVER['HTTP_HOST']."/agenda",
                                    linkTitle: "CLINABS",
                                    linkDescription: "Financeiro",
                                    linkImage: "https://".$_SERVER['HTTP_HOST']."/assets/images/logo.png"
                                );


                                $data = date('d/m/Y', strtotime($ag->data_agendamento));
                                $hora = date('H:i', strtotime($ag->data_agendamento));
                                $medico_nome = $medico->nome_completo;
            

                                $msg = "Olá, {$prefixo} {$medico->nome_completo},".PHP_EOL;
                                $msg .= "foi realizado um agendamento {$ag->modalidade} para dia *{$data}* para às {$hora}.".PHP_EOL;
                                $msg .= "O pagamento da consulta tem o prazo de até 48 horas para ser efetuado pelo paciente.".PHP_EOL;
                                $msg .= "No momento o pagamento encontra-se como pendente. ".PHP_EOL;
                                $msg .= "".PHP_EOL;
                                $msg .= "Obs: Após o prazo de 48 horas, caso não seja efetuado o pagamento, a agenda estará livre para novo agendamento.";
            
                                $wa->sendLinkMessage(
                                    phoneNumber: $medico->celular,
                                    text: $msg,
                                    linkUrl: "https://".$_SERVER['HTTP_HOST']."/agenda",
                                    linkTitle: "CLINABS",
                                    linkDescription: "Financeiro",
                                    linkImage: "https://".$_SERVER['HTTP_HOST']."/assets/images/logo.png"
                                );
                                
                            } 
                            else {
                                    $msg = "Olá {$paciente->nome_completo}," . PHP_EOL;
                                    $msg .=
                                        "Sua Consulta foi Agendada com Sucesso!" . PHP_EOL;
                                    $msg .= "" . PHP_EOL;
                                    $msg .=
                                        "para a efetivação é necessário realizar o pagamento desta Fatura para Confirmar o seu Agendamento." .
                                        PHP_EOL;
                                    $msg .= "" . PHP_EOL;
                                    $msg .= "Fatura: #{$token}" . PHP_EOL;
                                    $msg .= "Valor: R$ {$fatura_valor}" . PHP_EOL;
                                    $msg .=
                                        "Data do Agendamento: {$data_agendamento}" .
                                        PHP_EOL;
                                    $msg .= "Médico: {$prefixo} {$medico_nome}" . PHP_EOL;
                                    $msg .= "Especialidade: {$especialidade}" . PHP_EOL;
                                    $msg .= "" . PHP_EOL;
                                    $msg .= "" . PHP_EOL;
                                    $msg .=
                                        "clique neste link para efetivar o pagamento!" .
                                        PHP_EOL;
                                    $msg .= "" . PHP_EOL;
                                    $msg .= "" . PHP_EOL;
            
                                    $msg .= "Ajuda:" . PHP_EOL;
                                    $msg .= "" . PHP_EOL;
                                    $msg .=
                                        "-> Se o link não está abrindo, você deve adicionar nosso contato em seu celular." .
                                        PHP_EOL;
                                    $msg .=
                                        "-> Você pode pagar com PIX, Cartão de Crédito ou Débito" .
                                        PHP_EOL;
                                    $msg .=
                                        "-> A Validade deste pagamento é até hoje as 23:59:59" .
                                        PHP_EOL;
            
                                    $wa->sendLinkMessage(
                                        phoneNumber: $paciente->celular,
                                        text: $msg,
                                        linkUrl: $link->invoiceUrl,
                                        linkTitle: "CLINABS",
                                        linkDescription: "Fatura",
                                        linkImage: "https://".$_SERVER['HTTP_HOST']."/assets/images/logo.png"
                                    );


                                    $stmtc = $pdo->query(
                                        "SELECT nome_completo,celular, identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,token FROM MEDICOS WHERE token = '{$request["medico_token"]}'"
                                    );
                
                                    $medico = $stmtc->fetch(PDO::FETCH_OBJ);
                        
                                    $data = date('d/m/Y', strtotime($ag->data_agendamento));
                                    $hora = date('H:i', strtotime($ag->data_agendamento));
                                    $medico_nome = $medico->nome_completo;

                                    $prefixo =
                                        strtolower($medico->identidade_genero) == "feminino"
                                            ? "Dra."
                                            : "Dr.";


                                    $msg = "Olá, {$prefixo} {$medico->nome_completo},".PHP_EOL;
                                    $msg .= "foi realizado um agendamento {$ag->modalidade} para dia *{$data}* para às {$hora}.".PHP_EOL;
                                    $msg .= "O pagamento da consulta tem o prazo de até 48 horas para ser efetuado pelo paciente.".PHP_EOL;
                                    $msg .= "No momento o pagamento encontra-se como pendente. ".PHP_EOL;
                                    $msg .= "".PHP_EOL;
                                    $msg .= "Obs: Após o prazo de 48 horas, caso não seja efetuado o pagamento, a agenda estará livre para novo agendamento.";
                
                                    $res = $wa->sendLinkMessage(
                                        phoneNumber: $medico->celular,
                                        text: $msg,
                                        linkUrl: "https://".$_SERVER['HTTP_HOST']."/agenda",
                                        linkTitle: "CLINABS",
                                        linkDescription: "Financeiro",
                                        linkImage: "https://".$_SERVER['HTTP_HOST']."/assets/images/logo.png"
                                    );

                            }
        
                            } 
                            catch (Exception $ex) {
                                $json = [
                                    "status" => "error",
                                    "text" => "Erro ao Agendar Consulta",
                                ];
                            }
                        } 
                        else {
                            if($request['payment_mode'] != 'UNDEFINED') {
                                $stmt1 = $pdo->prepare("INSERT INTO `VENDAS` (
                                    `nome`,
                                    `code`,
                                        `amount`, 
                                        `customer`,
                                        `status`, 
                                        `payment_method`,
                                            `reference`,
                                            `module`,
                                            `cupom`,
                                            `payment_id`,
                                            `dueTime`
                                            )
                                    VALUES
                                        (
                                        :nome,
                                        :token,
                                        :valor,
                                        :paciente_token,
                                        :sts,
                                        :payment_method,
                                        :reference,
                                        :module,
                                        :cupom,
                                        :payment_id,
                                        :dueTime
                                        )");

                                $stmt1->bindValue(":nome", "VENDA DE CONSULTA MÉDICA");
                                $stmt1->bindValue(":token", $token);
                                $stmt1->bindValue(":valor", $ag->valor);
                                $stmt1->bindValue(":paciente_token", $ag->paciente_token);
                                $stmt1->bindValue(":sts", "AGUARDANDO PAGAMENTO");
                                $stmt1->bindValue(":payment_method", "NÃO DEFINIDO");
                                $stmt1->bindValue(":reference", $token);
                                $stmt1->bindValue(":module", "AGENDA_MED");
                                $stmt1->bindValue(":cupom", $ag->cupom);
                                $stmt1->bindValue(":payment_id", $link->id);
                                $stmt1->bindValue(":dueTime", dueDate($ag->data_agendamento, date('Y-m-d H:i')));

                                try {
                                    $stmt1->execute();

                                    $json = [
                                        "status" => "success",
                                        "icon" => "success",
                                        "text" => "Consulta Agendada com Sucesso!",
                                        "link" => $link->invoiceUrl,
                                        'linkUrl' => $added && !isset($_SESSION['token']) ? 'https://'.$_SERVER['HTTP_HOST'].'/login?action=newPassword&token='.$ag->paciente_token : '',
                                        "paymentLink" => true,
                                        "createPwd" => $added
                                    ];
                                } catch (Exception $ex) {
                                    $json = [
                                        "status" => "error",
                                        "text" => "Erro ao Agendar Consulta.",
                                        "exception" => $ex
                                    ];
                                }
                            } else {
                                $stmt1 = $pdo->prepare("INSERT INTO `VENDAS` (
                                    `nome`,
                                    `code`,
                                        `amount`, 
                                        `customer`,
                                        `status`, 
                                        `payment_method`,
                                            `reference`,
                                            `module`,
                                            `cupom`,
                                            `payment_id`,
                                            `dueTime`
                                            )
                                    VALUES
                                        (
                                        :nome,
                                        :token,
                                        :valor,
                                        :paciente_token,
                                        :sts,
                                        :payment_method,
                                        :reference,
                                        :module,
                                        :cupom,
                                        :payment_id,
                                        :dueTime
                                        )");

                                $stmt1->bindValue(":nome", "VENDA DE CONSULTA MÉDICA");
                                $stmt1->bindValue(":token", $token);
                                $stmt1->bindValue(":valor", $ag->valor);
                                $stmt1->bindValue(":paciente_token", $ag->paciente_token);
                                $stmt1->bindValue(":sts", "AGUARDANDO PAGAMENTO");
                                $stmt1->bindValue(":payment_method", "NÃO DEFINIDO");
                                $stmt1->bindValue(":reference", $token);
                                $stmt1->bindValue(":module", "AGENDA_MED");
                                $stmt1->bindValue(":cupom", $ag->cupom);
                                $stmt1->bindValue(":payment_id", 'local_'.uniqid());
                                $stmt1->bindValue(":dueTime", dueDate($ag->data_agendamento, date('Y-m-d H:i')));

                                try {
                                    $stmt1->execute();

                                    $json = [
                                        "status" => "success",
                                        "icon" => "success",
                                        "text" => "Consulta Agendada com Sucesso!",
                                        "link" => '',
                                        'linkUrl' => '',
                                        "paymentLink" => true,
                                        "createPwd" => $added
                                    ];
                                } catch (Exception $ex) {
                                    $json = [
                                        "status" => "error",
                                        "text" => "Erro ao Agendar Consulta.",
                                        "exception" => $ex
                                    ];
                                }
                            }
                          }
                        } catch(Exception $ex) {
                            $json = [
                                "status" => "error",
                                "text" => "Erro ao Agendar Consulta, Verifique os dados digitados",
                                "data" => $link->errors
                            ];
                        }
                } else {
                    $json = [
                        "status" => "error",
                        "icon" => "error",
                        "text" => "Erro ao Agendar Consulta",
                        "data" => $pac->errors
                    ];
                }
        } 
        catch (Exception $error) {
            $json = [
                "status" => "warning",
                "icon" => "error",
                "text" => "Erro ao Agendar a Consulta",
                "data" => []
            ];
        }

            $stmt->execute();
        } catch (Exception $error) {
            $json = [
                "status" => "warning",
                "icon" => "error",
                "text" => "Ocorreu um Erro ao Agendar a Consulta",
                "data" => []
            ];
        }
    } else {
        $json = [
            "status" => "warning",
            "redirect" => "/agendamento",
            "icon" => "warning",
            "text" => "Descupe-nos, mas este horário não está mais disponível no momento.",
            "data" => []
        ];
    }
} else {
    $json = [
        "status" => "warning",
        "icon" => "error",
        "text" => "Ocorreu um Erro ao Agendar a Consulta",
        "data" => $resp
    ];
}

header('Content-Type: application/json');

if(isset($json)) {
    echo json_encode($json, JSON_PRETTY_PRINT);
} else {
    echo json_encode($resp, JSON_PRETTY_PRINT);
}