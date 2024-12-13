<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/libs/Modules.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/api/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if(isset($_SESSION['userObj'])) {
$user = (object) $_SESSION['userObj'];
}


ini_set('diusplay_errors', 1);
error_reporting(1);

$sql = "SELECT * FROM `FARMACIA` WHERE token = '".$_REQUEST['pedido_code']."'";

$stmt = $pdo->query($sql);
$pedido = $stmt->fetch(PDO::FETCH_OBJ);

$produtos = [];

foreach(json_decode($pedido->produtos) as $prod) {
    $stmt = $pdo->prepare('SELECT * FROM PRODUTOS WHERE id = :id');
    $stmt->bindValue(':id', $prod->id);
    $stmt->execute();

    $produtos[] = $stmt->fetch(PDO::FETCH_OBJ);
}

$pedido->produtos2 = $produtos;

$pedido->paciente = $pacientes->getPacienteByToken($pedido->paciente_token);
$pedido->medico = $medicos->getMedicoByToken($pedido->medico_token);
$pedido->funcionario = $funcionarios->getFuncionarioByToken($pedido->funcionario_token);

$endereco = $pdo->prepare('SELECT
	ENDERECOS.logradouro AS endereco, 
	ENDERECOS.cep, 
	ENDERECOS.numero, 
	ENDERECOS.complemento, 
	ENDERECOS.cidade, 
	ENDERECOS.bairro, 
	ENDERECOS.uf
FROM
	ENDERECOS
WHERE
	ENDERECOS.token = :token');
$endereco->bindValue(':token', $pedido->endereco_entrega);
$endereco->execute();
$endereco = $endereco->fetch(PDO::FETCH_OBJ);

$c = new CarrinhoCalc($pdo);
//$imageString = 'https://clinabs.com/api/pdf/pedido.php?pedido_code='.$pedido->token;


$html = '<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=5">
  <title>replit</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap");

    html {
      margin: 0;
      padding: 0;
      list-style: none;
      font-family: "Inter", sans-serif;
      box-sizing: border-box;
    }
  .street, span, p, b,div {
    font-family: "Inter", sans-serif;
    font-size: 1rem;
  }

  .txt-top {
    display: flex;
    justify-content: center;
    align-items: self-end;
  }

  p {

  }
    .titulo-h1::after {
      content: "";
      background-color: #03e3c1;display: block;width: 10rem;height: 0.4rem;margin: 0.4rem auto;
    }

    td {
        font-size: 16px !important;
    }

    tr,td {
        mergin: 0 !important;
        padding: 0 !important;
    }
  </style>
</head>

<body>
<div class="document-container" id="doc">
  <div class="document-header">
    <table width="100%">
    <tr>
        <th colspan="3"><h2>RELATÓRIO DE PEDIDO</h2></th>
    </tr>
      <tr>
        <td width="128px">
          <img src="/assets/images/logo.svg" height="80px" style="margin-right: 32px">
        </td>
        <td class="street">
          <p style="font-size: 12px"><b style="font-size: 12px">Dr(a).</b> '.$pedido->medico->nome_completo.'</p>
          <p style="font-size: 12px"><b style="font-size: 12px">CRM:</b> '.$pedido->medico->num_conselho.' <b style="font-size: 12px">'.$pedido->medico->uf_conselho.'</b> </p>
          <p style="font-size: 12px"><b style="font-size: 12px">Endereço:</b> Rua Bruno Filgueira, 369 cj.1604 | Edifício 109 - AGUA VERDE | CURITIBA - PR</p>
          <p style="font-size: 12px"><b style="font-size: 12px">Telefone:</b> (41) 3300-0790</p>
        </td>
        <td width="128px">
            <img src="" height="80px" style="margin-right: 32px">
        </td>
      </tr>
    </table>
   
  </div>
<br>
  <div class="document-body"
       style="display: grid;grid-template-columns: 1fr;grid-template-rows: 1fr;grid-row-gap: 1rem;">
    <div class="document-title"
         style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">
      DADOS DO PACIENTE</div>
    <table width="100%" style="margin-top: 16px;margin-bottom: 16px">
      <tr>
        <td width="65%">
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">PACIENTE:</b> '.$pedido->paciente->nome_completo.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">IDADE:</b> '.Modules::calcularIdade($pedido->paciente->data_nascimento).'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Queixa:</b> '.$pedido->paciente->anamnese.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">SEXO:</b> '.$pedido->paciente->identidade_genero.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">TELEFONE:</b> '.$pedido->paciente->celular.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">E-MAIL:</b> '.$pedido->paciente->email.'</p>
          </td>

          <td width="35%">
            <div style="float: right">
                <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">DATA DE EMISSÃO:</b> '.date('d/m/Y').'</p>
                <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">DATA DE VALIDADE:</b> '.date('d/m/Y', strtotime('+6 months')).'</p>
                <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">CEP:</b> '.$endereco->cep.'</p>
                <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">ENDEREÇO:</b> '.$endereco->endereco.'</p>
                <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">CIDADE:</b> '.$endereco->cidade.'/'.$endereco->uf.'</p>
                <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">BAIRRO:</b> '.$endereco->bairro.'</p>
            </div>
          </td>
      </tr>
      </table>

    <div class="document-title" style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">PRESCRIÇÃO DE MEDICAMENTOS</div>
     <table width="100%">
        <thead>
            <tr>
              <th style="text-align: left !important;" width="64px">Qtde</th>
              <th style="text-align: left !important;">Nome do Medicamento</th>
              <th width="180px" style="text-align: left !important;">Valor Unitário</th>
              <th width="180px" style="text-align: left !important;">Valor Total</th>
            </tr>
        </thead>
        <tbody>
    ';
    
    $i = 1;
    $x = 0;
    $color = "#c9c9c947";

    foreach($pedido->produtos2 as $produto) {
        $p = json_decode($pedido->produtos)[$x];
        
        $prod = $c->getProdByPromo($p->id, $p->qtde);

        $vt = number_format($prod['valor'] * $p->qtde, 2, ',', '.');

        $html .= "<tr".($color == '#c9c9c947' ? '':' style="background-color: #c9c9c947"').">";
        $html .= "<td style=\"text-align: center !important;vertical-align: middle !important;max-height: 64px !important;\">{$p->qtde}</td>";
        $html .= "<td style=\"text-align: left !important;vertical-align: middle !important;max-height: 64px !important;\">{$produto->nome}</th>";
        $html .= "<td style=\"text-align: center !important;vertical-align: middle !important;max-height: 64px !important;\">R$ {$prod['valor']}</td>";
        $html .= "<td style=\"text-align: center !important;vertical-align: middle !important;max-height: 64px !important;\">R$ {$vt}</td>";
        $html .= "</tr>";

        $i++;
        $x++;

        $color = ($color == '#c9c9c947' ? '':'#c9c9c947');
    }
    
   $html .= '
        </tbody>
    </table>
    
    
     <div style="font-size: 25px;color: #222;margin-top: 50px;width: 100%;text-align: center"><b>Valor Pago: <u style="color: red">R$ '.number_format($pedido->valor_total, 2, ',', '.').'</u></b></div>
  </div>
</div>
</body>
</html>';



$fileName = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.uniqid();
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L',
    'orientation' => 'L'
]);

mkdir('/home/clinabs/tmp_downloads');

$file = '/home/clinabs/tmp_downloads/'.md5(uniqid()).'.pdf';
$mpdf->setFooter("<small style=\"float: left\">Impresso em {DATE d/m/Y H:i} por ".$user->nome_completo." </small> - Página {nb} de {nbpg}");
$mpdf->WriteHTML($html);
$mpdf->Output($file);

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"PEDIDO_" . $pedido->token . "\".pdf"); 
readfile($file); 