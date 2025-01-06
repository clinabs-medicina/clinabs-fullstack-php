<?php
function customErrorHandler($errno, $errstr, $errfile, $errline) {
  echo "<b>Error:</b> [$errno] $errstr<br>";
  echo "Error on line $errline in $errfile<br>";
}


require_once $_SERVER['DOCUMENT_ROOT']. '/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/libs/Modules.php';


  $stmt2 = $pdo->prepare("SELECT *,(SELECT uf_conselho FROM MEDICOS WHERE token = :medico_token) AS uf_conselho, (SELECT num_conselho FROM MEDICOS WHERE token = :medico_token) AS num_conselho,(SELECT nome_completo FROM MEDICOS WHERE token = :medico_token) AS medico_nome FROM `PACIENTES` WHERE token = :paciente_token");
  $stmt2->bindValue(':medico_token', $_REQUEST['medico_token']);
  $stmt2->bindValue(':paciente_token', $_REQUEST['paciente_token']);
  $stmt2->execute();

  $paciente = $stmt2->fetch(PDO::FETCH_OBJ);

  $stmt_local = $pdo->prepare('SELECT * FROM ENDERECOS WHERE token = :token');
  $stmt_local->bindValue(':token', $_REQUEST['local']);

  try {
    $stmt_local->execute();
    $endereco = $stmt_local->fetch(PDO::FETCH_OBJ);
  } catch(Exception $error) {
    $endereco = null;
  }

  $imgData = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/images/logo-bg-default.png'));
  $img = base64_encode(file_get_contents('https://api.invertexto.com/v1/qrcode?token=6974%7C7h8zdmWONV4jLvUyIMULVYOQrah4aT4u&text=https://clinabs.com/agenda/prescricao/receita/'.$_REQUEST['token']));
  $html = '<!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=5">
    <title>RECEITUÁRIO - CLINABS</title>
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

      table {
        margin-top: 50%;
        width: 100%;
        height: 60%;
      }

      th {
        color: #333;
        font-weight: normal;
        font-size: 20px;
      }
    </style>
  </head>

  <body>
  <div class="document-container" id="doc">
    <div class="document-header">
      <table width="100%">
  
        <tr>
          <td width="128px">
            <img src="data:image/png;base64,'.$imgData.'" height="80px" style="margin-right: 32px">
          </td>
          <td class="street">
            <p style="font-size: 12px"><b style="font-size: 12px">Dr(a).</b> '.$paciente->medico_nome.'</p>
            <p style="font-size: 12px"><b style="font-size: 12px">CRM:</b> '.$paciente->num_conselho.' <b style="font-size: 12px">'.$paciente->uf_conselho.'</b> </p>
            <p style="font-size: 12px"><b style="font-size: 12px">Endereço:</b> Rua Bruno Filgueira, 369 cj.1604 | Edifício 109</p>
            <p style="font-size: 12px"><b style="font-size: 12px">Localidade:</b> AGUA VERDE | CURITIBA - PR - CEP: CEP: 80240-220</p>
            <p style="font-size: 12px"><b style="font-size: 12px">Telefone:</b> (41) 3300-0790</p>
          </td>
          <td width="128px">
              <img src="data:image/png;base64,'.$img.'" height="80px" width="80px" style="margin-right: 32px">
          </td>
        </tr>
      </table>
    </div>
  <br>

  <table class="atestado-body">
    <tr>
        <th><h3>ATESTADO</h3></th>
    </tr>
    <tr>
        <th><br><br></th>
    </tr>
    <tr>
        <th>Declaro que para fins <b style="text-decoration: underline">'.$_GET['motivo'].'</b></th>
    </tr>   
    <tr>
        <th>que o Sr(a) <b style="text-decoration: underline">'.$paciente->nome_completo.'</b></th>
    </tr>   
    <tr>
        <th>foi por min atendido(a) no dia de hoje, necessitando</th>
    </tr>
    <tr>
        <th>de <b style="text-decoration: underline">'.$_GET['afastamento'].'  dia(s)</b> de afastamento de suas atividades laborativas.</th>
    </tr> 

    <tr>
      <th><br><br></th>
    </tr>

    <tr>
      <th>'.($endereco->cidade ?? 'Curitiba').', '.($_GET['data-afastamento'] ?? date('d/m/Y')).'</th>
    </tr>

    <tr>
      <th><br><br></th>
    </tr>
    <tr>
      <th><span style="font-size: 18px">DR. '.$paciente->medico_nome.'</span></th>
    </tr>
    <tr>
      <th><span style="font-size: 16px"><small>CRM:</b> '.$paciente->num_conselho.' '.$paciente->uf_conselho.'</small></th>
    </tr>

    <tr>
      <th><small style="font-size: 14px">CID: '.$_GET['cid'].'<b></b></small></th>
    </tr>
  </table>

  </div>
  </body>
  </html>';


  $fileName = $_SERVER['DOCUMENT_ROOT'].'/tmp/RECEITA_'.uniqid().'.pdf';

  $mpdf = new \Mpdf\Mpdf();
  $mpdf->setFooter("<small style=\"float: left\">Impresso em {DATE d/m/Y H:i} por ".$paciente->medico_nome." </small> - Página {nb} de {nbpg}");
  $mpdf->WriteHTML($html);
  $filename = "RECEITA-{$_GET['token']}.pdf";
  $mpdf->Output($filename, 'I');

  if (file_exists($filename)) {
      header('Content-type: application/force-download');
      header('Content-Disposition: attachment; filename=' . $filename);
      readfile($filename);
  }