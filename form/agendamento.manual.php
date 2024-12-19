<?php
require_once('../config.inc.php');

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



  $data = $_POST;
$token = uniqid();
$agenda_token = uniqid();

$meet = new WhereByMeet();
$room = $meet->createRoom(uniqid());

$medico = $pdo->query("SELECT (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,nome_completo, identidade_genero,duracao_atendimento,tempo_limite_online,tempo_limite_presencial FROM MEDICOS WHERE token = '{$_REQUEST["medicoSelect"]}'");
$medico = $medico->fetch(PDO::FETCH_OBJ);

$paciente = $pdo->query("SELECT (SELECT nome FROM ANAMNESE WHERE id = queixa_principal) AS queixa_principal,nome_completo,cpf,email,celular FROM PACIENTES WHERE token = '{$_REQUEST["pacienteSelect"]}'");
$paciente = $paciente->fetch(PDO::FETCH_OBJ);


$ag = new stdClass();
$ag->token = $token;
$ag->paciente_token = $_REQUEST["pacienteSelect"];
$ag->medico_token = $_REQUEST["medicoSelect"];
$ag->anamnese = $paciente->queixa_principal;
$ag->modalidade = $_REQUEST["modalidadeSelect"];
$ag->data_agendamento = date('Y-m-d H:i:s', strtotime($_REQUEST["dataAgendamento"]));
$ag->duracao_agendamento = $medico->duracao_atendimento;
$ag->valor = $_REQUEST["valorConsulta"];
$ag->meet = json_encode($room);
$ag->payment_method = $_REQUEST["formaPgto"];

if($_REQUEST['formaPgto'] == 'ABONAR') {
    $ag->status = 'AGENDADO';
} else {
    $ag->status = 'AGUARDANDO PAGAMENTO';
}


$sql = "INSERT INTO `AGENDA_MED` (`valor`,`paciente_token`, `medico_token`, `modalidade`, `anamnese`, `data_agendamento`, `duracao_agendamento`, `meet`, `token`, `payment_method`, `status`) 
VALUES (:valor, :paciente_token, :medico_token, :modalidade, :anamnese, :data_agendamento, :duracao_agendamento, :meet, :token, :payment_method, :sts);";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(":valor", $ag->valor);
$stmt->bindValue(":paciente_token", $ag->paciente_token);
$stmt->bindValue(":medico_token", $ag->medico_token);
$stmt->bindValue(":modalidade", strtoupper($ag->modalidade));
$stmt->bindValue(":anamnese", $ag->anamnese);
$stmt->bindValue(":data_agendamento", $ag->data_agendamento);
$stmt->bindValue(":duracao_agendamento", $ag->duracao_agendamento);
$stmt->bindValue(":meet", $ag->meet);
$stmt->bindValue(":token", $ag->token);
$stmt->bindValue(":payment_method", $ag->payment_method);
$stmt->bindValue(":sts", $ag->status);

try {
    $stmt->execute();

    if($_REQUEST['formaPgto'] == 'ABONAR') {
        $stmt1 = $pdo->prepare("INSERT INTO `VENDAS` (
            `nome`,
            `code`,
                `amount`, 
                `customer`,
                `status`, 
                `payment_method`,
                    `reference`,
                    `module`,
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
                :payment_id,
                :dueTime
                )");
    
        $stmt1->bindValue(":nome", "VENDA DE CONSULTA MÉDICA");
        $stmt1->bindValue(":token", $token);
        $stmt1->bindValue(":valor", $ag->valor);
        $stmt1->bindValue(":paciente_token", $ag->paciente_token);
        $stmt1->bindValue(":sts", "AGENDADO");
        $stmt1->bindValue(":payment_method", $ag->payment_method);
        $stmt1->bindValue(":reference", $agenda_token);
        $stmt1->bindValue(":module", "AGENDA_MED");
        $stmt1->bindValue(":payment_id", "");
        $stmt1->bindValue(":dueTime", dueDate($ag->data_agendamento, date('Y-m-d H:i')));
    

        try
            {
                $stmt1->execute();

                // Notificação do Paciente
                $data_agendamento = date("d/m/Y H:i", strtotime($ag->data_agendamento));
                $medico_nome = $medico->nome_completo;
                $especialidade = $medico->especialidade;
                $prefixo = strtolower($medico->identidade_genero) == "feminino"
                                        ? "Dra."
                                        : "Dr.";
                $especialidade = $medico->especialidade;

                $msg = "Olá {$paciente->nome_completo}," . PHP_EOL;
                $msg .=
                    "Sua Consulta foi Agendada com Sucesso!" . PHP_EOL;
                $msg .= "" . PHP_EOL;

                $msg .=
                    "Data do Agendamento: {$data_agendamento}" .
                    PHP_EOL;
                $msg .= "Médico: {$prefixo} {$medico_nome}" . PHP_EOL;
                $msg .= "Especialidade: {$especialidade}" . PHP_EOL;
                $msg .= "" . PHP_EOL;

                          
                if($ag->modalidade == 'ONLINE') {
                    $msg .= PHP_EOL."Link da Consulta: {$room->roomUrl}";
                }


                $wa->sendLinkMessage(
                    phoneNumber: $paciente->celular,
                    text: $msg,
                    linkUrl: "https://$hostname/",
                    linkTitle: "CLINABS",
                    linkDescription: "Fatura",
                    linkImage: "https://$hostname/assets/images/logo.png"
                );


                // Notificação do Médico
                $stmtc = $pdo->query(
                    "SELECT nome_completo,celular, identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,token FROM MEDICOS WHERE token = '{$_REQUEST["medicoSelect"]}'"
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

                          
                if($ag->modalidade == 'ONLINE') {
                    $msg .= PHP_EOL."Link da Consulta: {$room->hostRoomUrl}";
                }


                $res = $wa->sendLinkMessage(
                    phoneNumber: $medico->celular,
                    text: $msg,
                    linkUrl: "https://$hostname/",
                    linkTitle: "CLINABS",
                    linkDescription: "Financeiro",
                    linkImage: "https://$hostname/assets/images/logo.png"
                );

                $json = [
                    "status" => "success",
                    "icon" => "success",
                    "text" => "Consulta Agendada com Sucesso!",
                ];
        } catch(Expception $e) {
            $json = [
                "status" => "error",
                "icon" => "error",
                "text" => "Erro ao agendar consulta!",
            ];
        }
    } 
    else {
        if(in_array($_REQUEST['formaPgto'], array('PIX', 'CREDIT_CARD'))) {
            $pac = $asaas->create_or_get_client(
                token: $ag->paciente_token,
                nome: $paciente->nome_completo,
                cpf: $paciente->cpf,
                email: $paciente->email,
                celular: $paciente->celular
            );


            $link = $asaas->cobrar(
                id: $pac->id, 
                tipo:  $ag->payment_method, 
                valor: $ag->valor, 
                reference: $agenda_token, 
                descricao: "VENDA DE CONSULTA MÉDICA",
                paymentDue: date('Y-m-d', strtotime($ag->data_agendamento))
            );

            
            if(isset($link->invoiceUrl)) {
                if($_REQUEST['formaPgto'] == 'ABONAR') {
                    $asaas->receber_dinheiro($link->id);
                }

                $pdo->query(
                    "UPDATE PACIENTES SET payment_id = '{$pac->id}' WHERE token = '{$_REQUEST["pacienteSelect"]}';"
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
                        :payment_id,
                        :dueTime
                        )");
            
                $stmt1->bindValue(":nome", "VENDA DE CONSULTA MÉDICA");
                $stmt1->bindValue(":token", $token);
                $stmt1->bindValue(":valor", $ag->valor);
                $stmt1->bindValue(":paciente_token", $ag->paciente_token);
                $stmt1->bindValue(":sts", "AGUARDANDO PAGAMENTO");
                $stmt1->bindValue(":payment_method", $ag->payment_method);
                $stmt1->bindValue(":reference", $agenda_token);
                $stmt1->bindValue(":module", "AGENDA_MED");
                $stmt1->bindValue(":payment_id", $link->id);
                $stmt1->bindValue(":dueTime", dueDate($ag->data_agendamento, date('Y-m-d H:i')));
            
            
                $stmt1->execute();


                // Notificação do Paciente
                $fatura_valor = number_format($ag->valor, 2, ",", ".");
                $data_agendamento = date("d/m/Y H:i", strtotime($ag->data_agendamento));
                $medico_nome = $medico->nome_completo;
                $especialidade = $medico->especialidade;
                $prefixo = strtolower($medico->identidade_genero) == "feminino"
                                        ? "Dra."
                                        : "Dr.";
                $especialidade = $medico->especialidade;

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

                if($ag->modalidade == 'ONLINE') {
                    $msg .= PHP_EOL."Link da Consulta: {$meet->roomUrl}";
                }

                $wa->sendLinkMessage(
                    phoneNumber: $paciente->celular,
                    text: $msg,
                    linkUrl: $link->invoiceUrl,
                    linkTitle: "CLINABS",
                    linkDescription: "Fatura",
                    linkImage: "https://$hostname/assets/images/logo.png"
                );


                // Notificação do Médico
                $stmtc = $pdo->query(
                    "SELECT nome_completo,celular, identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,token FROM MEDICOS WHERE token = '{$_REQUEST["medicoSelect"]}'"
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

                if($ag->modalidade == 'ONLINE') {
                    $msg .= PHP_EOL."Link da Consulta: {$meet->hostRoomUrl}";
                }

                $res = $wa->sendLinkMessage(
                    phoneNumber: $medico->celular,
                    text: $msg,
                    linkUrl: "https://$hostname/agenda",
                    linkTitle: "CLINABS",
                    linkDescription: "Financeiro",
                    linkImage: "https://$hostname/assets/images/logo.png"
                );

    
                $json = [
                    "status" => "success",
                    "icon" => "success",
                    "text" => "Consulta Agendada com Sucesso!",
                ];
            } 
            else {
                $json = [
                    "status" => "warning",
                    "icon" => "error",
                    "text" => "Erro ao Agendar a Consulta",
                    "data" => ['errro1']
                ];
            }
        } 
        
        else {
            $pac = $asaas->create_or_get_client(
                token: $ag->paciente_token,
                nome: $paciente->nome_completo,
                cpf: $paciente->cpf,
                email: $paciente->email,
                celular: $paciente->celular
            );
    
            $link = $asaas->cobrar(
                id: $pac->id, 
                tipo:  'UNDEFINED', 
                valor: $ag->valor, 
                reference: $agenda_token, 
                descricao: "VENDA DE CONSULTA MÉDICA",
                paymentDue: date('Y-m-d', strtotime($ag->data_agendamento))
            );
    
            
            if(isset($link->invoiceUrl)) {
                $pdo->query(
                    "UPDATE PACIENTES SET payment_id = '{$pac->id}' WHERE token = '{$_REQUEST["pacienteSelect"]}';"
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
                        :payment_id,
                        :dueTime
                        )");
            
                $stmt1->bindValue(":nome", "VENDA DE CONSULTA MÉDICA");
                $stmt1->bindValue(":token", $token);
                $stmt1->bindValue(":valor", $ag->valor);
                $stmt1->bindValue(":paciente_token", $ag->paciente_token);
                $stmt1->bindValue(":sts", "AGUARDANDO PAGAMENTO");
                $stmt1->bindValue(":payment_method", 'UNDEFINED');
                $stmt1->bindValue(":reference", $agenda_token);
                $stmt1->bindValue(":module", "AGENDA_MED");
                $stmt1->bindValue(":payment_id", $link->id);
                $stmt1->bindValue(":dueTime", dueDate($ag->data_agendamento, date('Y-m-d H:i')));
            
            
                $stmt1->execute();


                // Notificação do Paciente
                $fatura_valor = number_format($ag->valor, 2, ",", ".");
                $data_agendamento = date("d/m/Y H:i", strtotime($ag->data_agendamento));
                $medico_nome = $medico->nome_completo;
                $especialidade = $medico->especialidade;
                $prefixo = strtolower($medico->identidade_genero) == "feminino"
                                        ? "Dra."
                                        : "Dr.";
                $especialidade = $medico->especialidade;

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
                    "você escolheu pagar no dia da Consulta!, então lembre-se de chegar um pouco antes para efetuar o pagamento e efetiovar sua consulta." .
                    PHP_EOL;
                $msg .= "" . PHP_EOL;
                $msg .= "" . PHP_EOL;

                if($ag->modalidade == 'ONLINE') {
                    $msg .= PHP_EOL."Link da Consulta: {$meet->roomUrl}";
                }

                $wa->sendLinkMessage(
                    phoneNumber: $paciente->celular,
                    text: $msg,
                    linkUrl: "https://$hostname/",
                    linkTitle: "CLINABS",
                    linkDescription: "Fatura",
                    linkImage: "https://$hostname/assets/images/logo.png"
                );


                // Notificação do Médico
                $stmtc = $pdo->query(
                    "SELECT nome_completo,celular, identidade_genero, (SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS especialidade,token FROM MEDICOS WHERE token = '{$_REQUEST["medicoSelect"]}'"
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

                if($ag->modalidade == 'ONLINE') {
                    $msg .= PHP_EOL."Link da Consulta: {$meet->hostRoomUrl}";
                }

                $res = $wa->sendLinkMessage(
                    phoneNumber: $medico->celular,
                    text: $msg,
                    linkUrl: "https://$hostname/",
                    linkTitle: "CLINABS",
                    linkDescription: "Financeiro",
                    linkImage: "https://$hostname/assets/images/logo.png"
                );

    
                $json = [
                    "status" => "success",
                    "icon" => "success",
                    "text" => "Consulta Agendada com Sucesso!",
                ];
            } 
            else {
                $json = [
                    "status" => "warning",
                    "icon" => "error",
                    "text" => "Erro ao Agendar a Consulta",
                    "data" => ['errro2']
                ];
            }
        }
    }
} catch (Exception $error) {

    $json = [
        "status" => "warning",
        "icon" => "error",
        "text" => "Erro ao Agendar a Consulta",
        "data" => $error
    ];
}

header('Content-Type: application/json');

echo json_encode($json, JSON_PRETTY_PRINT);