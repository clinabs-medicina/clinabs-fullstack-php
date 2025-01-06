<?php
// Importar as classes 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function calcularIdade($date){
    $time = strtotime($date);
    if($time === false){
      return '';
    }
 
    $year_diff = '';
    $date = date('Y-m-d', $time);
    list($year,$month,$day) = explode('-',$date);
    $year_diff = date('Y') - $year;
    $month_diff = date('m') - $month;
    $day_diff = date('d') - $day;
 
    return $year_diff;
}


require_once('../config.inc.php');

if($_REQUEST['obs']) {
    $txt = $_REQUEST['desc'].' ('.$_REQUEST['obs'].')';
} else {
    $txt = $_REQUEST['desc'];
}


$stmt = $pdo->prepare("INSERT INTO `RASTREAMENTO` ( `token`, `user`, `desc`) VALUES (:token, :user, :desc);");
$stmt->bindValue(':token', $_REQUEST['id']);
$stmt->bindValue(':user', $_REQUEST['token']);
$stmt->bindValue(':desc', $txt);


try{
    $stmt->execute();
    
    $json = [
            'status' => 'success',
            'data' => date('d/m/Y H:i'),
            'text' => 'Evento Registrado com Sucesso!'
        ];
        
        if($txt == 'Documento Auditado') {
            $doc = "api/pdf/pedido.php?pedido_code={$_REQUEST['pid']}";
            
         
            
            $sql = "SELECT *,
            (SELECT nome_completo FROM MEDICOS WHERE token = medico_token) as medico_nome,
             (SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token) as funcionario_nome
                    	FROM `FARMACIA` WHERE token = '". $_REQUEST['id']."'";

          $stmtx = $pdo->query($sql);
          $stmtx->execute();
          $pedido = $stmtx->fetch(PDO::FETCH_OBJ);
          
          
           $sql = "SELECT
                    	nome_completo, 
                    	nacionalidade, 
                    	nome_preferencia, 
                    	identidade_genero, 
                    	cpf, 
                    	rg, 
                    	data_nascimento, 
                    	telefone, 
                    	celular, 
                    	email, 
                    	doc_rg_frente, 
                    	doc_rg_verso, 
                    	doc_cpf_frente, 
                    	doc_cpf_verso, 
                    	doc_comp_residencia, 
                    	doc_procuracao, 
                    	doc_anvisa, 
                    	doc_termos
                    FROM
                    	PACIENTES
                    WHERE token = :token";
                    
            $stmt2 = $pdo->prepare($sql);
            $stmt2->bindValue(':token', $_REQUEST['user']);
                    
            $stmt2->execute();
            $paciente = $stmt2->fetch(PDO::FETCH_OBJ);
            
            
            $stmt3 = $pdo->prepare('SELECT * FROM ENDERECOS WHERE user_token = :token AND isDefault = 1');
            $stmt3->bindValue(':token', $_REQUEST['user']);
                    
            $stmt3->execute();
            $endereco = $stmt3->fetch(PDO::FETCH_OBJ);
            
            $idade = calcularIdade($paciente->data_nascimento);
            $cpf = preg_replace('/[^0-9]/', '', $paciente->cpf);
            $rg = preg_replace('/[^0-9]/', '', $paciente->rg);
            $celular = preg_replace('/[^0-9]/', '', $paciente->celular);
            $cep = preg_replace('/[^0-9]/', '', $endereco->cep);
            

            $htmlContents = "--------------------------------------------------------------------".PHP_EOL;
            $htmlContents .= "Dados do Paciente".PHP_EOL;
            $htmlContents .= "--------------------------------------------------------------------".PHP_EOL;
            $htmlContents .= "Paciente: {$paciente->nome_completo}".PHP_EOL;
            $htmlContents .= "Idade: {$idade} anos".PHP_EOL;
            $htmlContents .= "CPF: {$cpf}".PHP_EOL;
            $htmlContents .= "RG: {$rg}".PHP_EOL;
            $htmlContents .= "Email: {$paciente->email}".PHP_EOL;
            $htmlContents .= "Contato: {$celular}".PHP_EOL;
            $htmlContents .= "".PHP_EOL;
            
            $htmlContents .= "Endereço: {$endereco->logradouro}, {$endereco->numero} {$endereco->bairro}/{$endereco->cidade} {$endereco->uf}".PHP_EOL;
            $htmlContents .= "CEP: {$cep}".PHP_EOL;
            $htmlContents .= "--------------------------------------------------------------------".PHP_EOL;
            $htmlContents .= "Informações Adicionais".PHP_EOL;
            $htmlContents .= "--------------------------------------------------------------------".PHP_EOL;
            $htmlContents .= "Médico: {$pedido->medico_nome}".PHP_EOL;
            $htmlContents .= "Atendente: {$pedido->funcionario_nome}".PHP_EOL;
            $htmlContents .= "--------------------------------------------------------------------".PHP_EOL;
            $htmlContents .= "Detalhes do Pedido".PHP_EOL;
            
            
            
            $c = new CarrinhoCalc($pdo);
            
            
            $frete = 170;
            
          
          foreach(json_decode($pedido->produtos) as $p) {
            $stmty = $pdo->prepare('SELECT * FROM PRODUTOS WHERE id = "'.$p->id.'"');
            $stmty->execute();
            
            $prod = $stmty->fetch(PDO::FETCH_OBJ);
            
            $produto_item = $c->getProdByPromo($p->id, $p->qtde);
            
            $valorTotal = ($produto_item['valor'] * $p->qtde);
            
            
            $valor_unitario =  number_format(preg_replace('/[^0-9]/', '', $produto_item['valor']) / 100, 2, ',', '.');
            $valorTotal = number_format(preg_replace('/[^0-9]/', '', $valorTotal) /100, 2, ',', '.');
            
            $htmlContents .= "{$p->qtde}x     R$ {$valor_unitario}     {$prod->nome}     R$ {$valorTotal}".PHP_EOL;
            
            if($frete == 170 && $produto_item['valor_frete'] == 0) {
                $frete = $produto_item['valor_frete'];
            }
          }
          
          $frete = $frete == 0 ? 'Grátis': number_format($frete, 2, ',', '.');
          
        $htmlContents .= "--------------------------------------------------------------------".PHP_EOL;
        $htmlContents .= "Frete: {$frete}".PHP_EOL;
        $htmlContents .= "--------------------------------------------------------------------".PHP_EOL;

            
            // Norberto
            $wa->sendLinkMessage(
                '5541996301085', 
                $htmlContents,
                "https://$hostname/", 
                'CLINABS', 
                'Pedidos', 
                'https://$hostname/assets/images/logo.png'
            );
            
            //Junior
            $wa->sendLinkMessage(
                '5541992319253', 
                $htmlContents,
                "https://$hostname/", 
                'CLINABS', 
                'Pedidos', 
                'https://'.$hostname.'/assets/images/logo.png'
            );
            
           // Instância da classe
            $mail = new PHPMailer(false);
        
            
            
            try
            {
            
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Username   = 'desenvolvedor@clinabs.com';
                $mail->Password   = 'gfjnAVJ5NcOP8tkp';
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp-relay.brevo.com';
                $mail->Port = 587;
                $mail->setFrom('naoresponder@clinabs.com', 'CLINABS');
                
                $mail->addAddress('norberto@clinabs.com', 'Norberto');
                $mail->addAddress('junior@clinabs.com', 'Junior');
                $mail->addAddress('desenvolvedor@clinabs.com', 'Desenvolvedor');
                
                $mail->isHTML(false);
                $mail->Subject = utf8_decode('Informaçes de Pedido');
                $mail->Body = utf8_decode($htmlContents);

                $mail->send();         
            }
            catch (Exception $e)
            {

            } 
        }
}catch(PDOException $ex) {

    $json = [
        'status' => 'warning',
        'text' => 'Erro ao Registrar Evento.'
        ];
}

$json = json_encode($json);

header('Content-Type: application/json');
echo $json;