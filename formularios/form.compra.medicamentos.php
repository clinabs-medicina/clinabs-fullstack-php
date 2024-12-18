<?php
require_once '../config.inc.php';
require_once '../libs/sendMail.php';

function upload_docs() {
    $docs = [
        'doc_rg_frente', 
        'doc_rg_verso' , 
        'doc_cpf_frente',
        'doc_cpf_verso', 
        'doc_comp_residencia', 
        'doc_procuracao',
        'doc_anvisa', 
        'doc_termos'
        ];
        
        
   foreach($docs as $doc) {
        if(isset($_POST[$doc]) && file_exists('../tmp/'.$_POST[$doc])) {
                if(isset($_POST[$doc]) && file_exists($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$_POST[$doc])) {
                    shell_exec("cp {$_SERVER['DOCUMENT_ROOT']}/tmp/{$_POST[$doc]} {$_SERVER['DOCUMENT_ROOT']}/data/images/docs/{$_POST[$doc]}");
                }
           }
        }
  }


try{
    $token = uniqid();
    $stmt = $pdo->prepare("INSERT INTO `FARMACIA` (`doc_receita`,`produtos`, `valor_total`, `funcionario_token`, `paciente_token`,`medico_token`, `token`, `status`, `payment_method`, `cupom`, `endereco_entrega`) VALUES (:doc_receita, :produtos, :valor_total, :funcionario_token, :paciente_token, :medico_token, :token, :sts, :payment_method, :cupom, :endereco_entrega);");
    $produtos = [];

    foreach($_POST['produtos'] as $prod) {
        parse_str($prod, $p);

        $produtos[] = $p;
    }
    
    $stmt->bindValue(':doc_receita', base64_decode($_POST['doc_receita']));
    $stmt->bindValue(':produtos', json_encode($produtos));
    $stmt->bindValue(':valor_total', $_POST['valor_total']);
    $stmt->bindValue(':funcionario_token', $_POST['fid']);
    $stmt->bindValue(':paciente_token', $_POST['paciente_token']);
    $stmt->bindValue(':medico_token', $_POST['medico_token']);
    $stmt->bindValue(':token', $token);
    $stmt->bindValue(':sts', 'AGUARDANDO PAGAMENTO');
    $stmt->bindValue(':payment_method', $_POST['payment_mode']);
    $stmt->bindValue(':cupom', $_POST['cupom']);
    $stmt->bindValue(':endereco_entrega', $_POST['endereco']);
    
    try {
        $stmt->execute();
    } catch(PDOException $ex) {

    }


    $json = [
        'status' => 'success',
        'token' => $token,
        'method' => $_POST['payment_mode'],
        'amount' => $_POST['valor_total'],
        'paciente_token' => $_POST['paciente_token'],
        'text' => 'Compra de Medicamentos Realizada Com Sucesso!'
    ];


if (isset($_POST["enderecos"])) {
    $user_token = $_POST['paciente_token'];
    
    foreach($_POST["enderecos"] as $item) {
        $endereco  = json_decode(str_replace("'", '"', $item));
        $token = isset($endereco->token) ? $endereco->token : uniqid();
        
    $stmt = $pdo->prepare("INSERT INTO `ENDERECOS` ( 
     `nome`, 
     `cep`,
     `logradouro`, 
	 `numero`, 
	 `complemento`, 
	 `cidade`, 
	 `bairro`, 
	 `uf`, 
	 `user_token`, 
	 `isDefault`,
	 `token` 
	)
    VALUES
	(
	:nome, 
    :cep,
    :logradouro, 
	:numero, 
	:complemento, 
	:cidade, 
	:bairro, 
	:uf, 
	:user_token, 
	:isDefault,
	:token
	) ON DUPLICATE KEY UPDATE
	 `nome` = nome, 
     `cep` = cep,
     `logradouro` = logradouro, 
	 `numero` = numero, 
	 `complemento` = complemento, 
	 `cidade` = cidade, 
	 `bairro` = bairro, 
	 `uf` = uf, 
	 `isDefault` = VALUES(isDefault);
	");
	
	$stmt->bindValue(':nome', $endereco->endereco_nome); 
    $stmt->bindValue(':cep', $endereco->cep);
    $stmt->bindValue(':logradouro', $endereco->endereco);
    $stmt->bindValue(':numero', $endereco->numero);
    $stmt->bindValue(':complemento', $endereco->complemento);
    $stmt->bindValue(':cidade', $endereco->cidade);
    $stmt->bindValue(':bairro', $endereco->bairro);
    $stmt->bindValue(':uf', $endereco->uf);
    $stmt->bindValue(':user_token', $user_token);
    $stmt->bindValue(':isDefault', $endereco->isDefault);
    $stmt->bindValue(':token', $token);
    
    
        try {
          $stmt->execute();
    } catch(PDOException $ex) {

    }
  }
    
}

    
    try{
        $stmt2 = $pdo->prepare('UPDATE PACIENTES
            SET rg = :rg, 
            nacionalidade = :nacionalidade, 
            medico_token = :medico_token, 
            doc_rg_frente = :doc_rg_frente, 
            doc_rg_verso = :doc_rg_verso, 
            doc_cpf_frente = :doc_cpf_frente, 
            doc_cpf_verso = :doc_cpf_verso, 
            doc_comp_residencia = :doc_comp_residencia, 
            doc_procuracao = :doc_procuracao, 
            doc_anvisa = :doc_anvisa, 
            doc_termos = :doc_termos
            WHERE token = :token;');


    	$stmt2->bindValue(':token',  $_POST['paciente_token']); 
    	$stmt2->bindValue(':rg',  $_POST['rg']); 
    	$stmt2->bindValue(':nacionalidade',  $_POST['nacionalidade']); 
    	$stmt2->bindValue(':medico_token',  $_POST['medico_token']); 
    	$stmt2->bindValue(':doc_rg_frente',  $_POST['doc_rg_frente']); 
    	$stmt2->bindValue(':doc_rg_verso',  $_POST['doc_rg_verso']); 
    	$stmt2->bindValue(':doc_cpf_frente',  $_POST['doc_cpf_frente']); 
    	$stmt2->bindValue(':doc_cpf_verso',  $_POST['doc_cpf_verso']); 
    	$stmt2->bindValue(':doc_comp_residencia',  $_POST['doc_comp_residencia']); 
    	$stmt2->bindValue(':doc_procuracao',  $_POST['doc_procuracao']); 
    	$stmt2->bindValue(':doc_anvisa',  $_POST['doc_anvisa']); 
    	$stmt2->bindValue(':doc_termos',  $_POST['doc_termos']); 
        
        $stmt2->execute();
    } catch(PDOException $ex) {
        file_put_contents('error.apaciente.txt', print_r($ex, true));
    }

    $stmtx = $pdo->prepare('SELECT * FROM PACIENTES WHERE token = :token');
    $stmtx->bindValue(':token', $_POST['paciente_token']);
    $stmtx->execute();
    
    $paciente = $stmtx->fetch(PDO::FETCH_OBJ);
  
  $valor = number_format($_POST['valor_total'], 2, ',','.');

    if($_POST['payment_mode'] == 'pix') {
        /*
        $wa->sendLinkMessage(
            $paciente->celular, 
            'Para finalizar A Compra de Seus Medicamentos, efetue o pagamento de R$ '.$valor.' via PIX Acessando sua Conta na nossa Plataforma selecione Agenda e clique no icone po PIX',
            '', 
            $COMPANY_NAME, 
            'Financeiro', 
            $COMPANY_LOGO
        );
        */
    }else {
        /*
            $wa->sendLinkMessage(
                $paciente->celular, 
                'Para finalizar  A Compra de Seus Medicamentos, efetue o pagamento de R$ '.$valor.' via Cartão de Crdito no link que vamos enviar em breve.',
                '', 
                $COMPANY_NAME, 
                'Financeiro', 
                $COMPANY_LOGO
            );
        */
            $wa->sendLinkMessage(
               '554198000537', 
               'Olá, o Paciente'.$paciente->nome_completo.', 
                Está solicitando um link de Pagamento via Carão de Crédito, 
                referente  A Compra de Medicamentos, 
                Valor: R$'.$valor,
                '', 
                'Solicitao de Pagamento via Cartão de Crédito', 
                'Financeiro',
                $COMPANY_LOGO
            );
            
        }

        //sendMail($mailer, $to = array('email' => $paciente->email, 'name' => $paciente->nome_completo), $subject = 'Compra de Medicamentos', $body = 'Para finalizar  A Compra de Seus Medicamentos, efetue o pagamento de R$ '.$_POST['valor_total'].' via Cartão de Crédito no link que vamos enviar em breve.')

       $pdo->query('DELETE FROM CARRINHO WHERE user_ref="'.$_POST['paciente_token'].'"');
}catch(Exception $ex) {
    $json = [
        'status' => 'error',
        'text' => 'Falha ao Realizar Venda',
        'icon' => 'error'
    ];
}

header('Content-Type: application/json');
echo json_encode($json, JSON_PRETTY_PRINT);