<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/config.inc.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/libs/Modules.php';

$cid = $_GET['cid'] ?? '';

$link = 'https://clinabs.com/receitas/'.$_GET['token'];
$receita = $agenda->getByToken($_GET['token']);

  $stmt2 = $pdo->prepare("SELECT * FROM `ENDERECOS` WHERE user_token = '{$receita->paciente_token}' AND isDefault = 1;");
  $stmt2->execute();

  $endereco = $stmt2->fetch(PDO::FETCH_OBJ);

  $imgData = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/images/logo-bg-default.png'));
  $img = base64_encode(file_get_contents('https://api.invertexto.com/v1/qrcode?token=6974%7C7h8zdmWONV4jLvUyIMULVYOQrah4aT4u&text='.$link));
  $html = '<!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
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
          <th colspan="3"><h2>RECEITUÁRIO SIMPLES</h2></th>
      </tr>
        <tr>
          <td width="128px">
            <img src="data:image/png;base64,'.$imgData.'" height="80px" style="margin-right: 32px">
          </td>
          <td class="street">
            <p style="font-size: 12px"><b style="font-size: 12px">Dr(a).</b> '.$receita->medico_nome.'</p>
            <p style="font-size: 12px"><b style="font-size: 12px">CRM:</b> '.$receita->num_conselho.' <b style="font-size: 12px">'.$receita->uf_conselho.'</b> </p>
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
    <div class="document-body"
         style="display: grid;grid-template-columns: 1fr;grid-template-rows: 1fr;grid-row-gap: 1rem;">
      <div class="document-title"
           style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">
        DADOS DO PACIENTE</div>
      <table width="100%" style="margin-top: 16px;margin-bottom: 16px">
        <tr>
          <td width="65%">
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Paciente:</b> '.$receita->nome_completo.'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Idade:</b> '.Modules::calcularIdade($receita->data_nascimento).'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">CID:</b> '.$cid.'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Sexo:</b> '.$receita->identidade_genero.'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Contato:</b> '.$receita->celular.'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">E-mail:</b> '.$receita->email.'</p>
            </td>

            <td width="35%">
              <div style="float: right">
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Data de Emissão:</b> '.date('d/m/Y').'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Data de Validade:</b> '.date('d/m/Y', strtotime('+6 months')).'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">CEP::</b> '.$endereco->cep.'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Endereço:</b> '.$endereco->logradouro.','.$endereco->numero.'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Cidade:</b> '.$endereco->cidade.'/'.$endereco->uf.'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Bairro:</b> '.$endereco->bairro.'</p>
              </div>
            </td>
        </tr>
        </table>

      <div class="document-title" style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">PRESCRIÇÃO DE MEDICAMENTOS</div>
       <table width="100%">
          <tbody>';
      $i = 1;


      $selectors = '';

      if(isset($_GET['include'])) {
        $selectors = " AND id IN ({$_GET['include']})";
      }


      $stmt3 = $pdo->prepare("SELECT *,(SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome FROM `PRESCRICOES` WHERE agenda_token = '{$receita->token}' {$selectors};");

      $stmt3->execute();

      $prescricoes = $stmt3->fetchAll(PDO::FETCH_OBJ);

      $i = 1;
      
      foreach($prescricoes as $presc) {
        if($presc->produto_ref == 'MEDICAMENTOS') {
          $sts = $pdo->prepare('SELECT * FROM MEDICAMENTOS WHERE id = :id');
          $sts->bindValue(':id', $presc->produto_id);
          $sts->execute();
          $remedio = $sts->fetch(PDO::FETCH_OBJ);
          $produto_nome = "{$remedio->nome} - {$remedio->conteudo}{$remedio->unidade_medida}";
        } else {
          $produto_nome = strtoupper($presc->produto_nome);
        }

          $prescricao = base64_decode($presc->prescricao);
          

          $html .= "<tr>";
          $html .= "<td style=\"text-align: left !important;vertical-align: middle !important;max-height: 64px !important;width: 48px\">{$i}</div></td>";
          $html .= "<td style=\"text-align: left !important;vertical-align: middle !important;max-height: 64px !important;font-weight: bold\">{$produto_nome}</td>";
          $html .= "<td style=\"width:128;text-align: left !important;vertical-align: middle !important;max-height: 64px !important;font-weight: bold\">{$presc->frascos} frasco(s)</td>";
          $html .= "</tr>";

          $html .= "<tr><td colspan=\"4\" style=\"color: #222\"><br><br>{$prescricao}</td></tr>";
          $i++;
      }

     $html .= '
          </tbody>
      </table>
    </div>
  </div>
  </body>
  </html>';


  if($_GET['dump'] == 1) {
    header('Content-Type: application/json');
    echo json_encode([
      'body' => $_REQUEST,
      'rule' => $selectors,
      'headers' => getallheaders()
    ], JSON_PRETTY_PRINT);
  } else {
    $fileName = $_SERVER['DOCUMENT_ROOT'].'/tmp/RECEITA_'.$_GET['token'].'.pdf';
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->setFooter("<small style=\"float: left\">Impresso em {DATE d/m/Y H:i} por ".$user->nome_completo."</small> - Página {nb} de {nbpg}");
    $mpdf->WriteHTML($html);
    $mpdf->Output($fileName, 'F');
    
    $fsn = basename($fileName);
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$fsn.'"');
    header('Content-Length: ' . filesize($fileName));
    
    readfile($fileName);
    exit;
    }
