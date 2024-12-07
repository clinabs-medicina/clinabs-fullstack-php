<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

use Mpdf\Mpdf;

$mpdf = new \Mpdf\Mpdf();
$obj = base64_decode($_GET['items']);
$obj = json_decode($obj);
$data = [];

$items = [];

foreach($obj as $item) {
  $items[] = json_decode(base64_decode($item));
}

$prontuario = [

];

function rel_add($id, $tipo, $items) {
  $res = false;

  foreach($items as $item) {
    if($item->id == $id && $item->tipo == $tipo) {
      $res = true;
    } 
  }

  return $res;
}

$paciente_token = $_REQUEST['paciente_token'];
$stmt = $pdo->prepare('SELECT *,TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) AS age FROM PACIENTES WHERE token = :token');
$stmt->bindValue(':token', $paciente_token);
$stmt->execute();

$endereco = $pdo->query("SELECT * FROM `ENDERECOS` WHERE user_token = '{$paciente_token}' AND tipo_endereco = 'CASA'")->fetch(PDO::FETCH_OBJ);

$paciente = $stmt->fetch(PDO::FETCH_OBJ);

$especialidades = [];

$espx  = $pdo->query("SELECT * FROM ESPECIALIDADES")->fetchAll(PDO::FETCH_OBJ);


foreach($espx as $esp) {
  $especialidades[$esp->id] = strtoupper($esp->nome);
}

$stmt = $pdo->prepare("SELECT
id,
paciente_token,
`timestamp`,
texto AS texto,
last_update,
updated_by,
( SELECT nome_completo FROM FUNCIONARIOS WHERE token = funcionario_token ) AS usuario_nome,
( SELECT nome_completo FROM MEDICOS WHERE token = funcionario_token ) AS medico_nome,
( SELECT objeto FROM FUNCIONARIOS WHERE token = funcionario_token ) AS especialidade,
periodo,
semana,
'' AS token,
'' AS remedio,
doc_tipo AS tipo,
  doc_file,
  proximo_acompanhamento,
  titulo_acompanhamento
FROM
ACOMPANHAMENTO 
WHERE
paciente_token = :token 
UNION
(
SELECT
  id,
  paciente_token,
  `timestamp`,
  GROUP_CONCAT(prescricao) AS texto,
  last_update,
  updated_by,
  ( SELECT nome_completo FROM MEDICOS WHERE token = medico_token ) AS usuario_nome,
  ( SELECT nome_completo FROM MEDICOS WHERE token = funcionario_token ) AS medico_nome,
  ( SELECT especialidade FROM MEDICOS WHERE token = medico_token ) AS especialidade,
  '' AS periodo,
  '' AS semana,
  agenda_token AS token,
  GROUP_CONCAT(produto_id) AS remedio,
  'PRESCRICAO' AS tipo,
      '' AS doc_file,
      '' AS proximo_acompanhamento,
      '' AS titulo_acompanhamento
FROM
  PRESCRICOES 
WHERE
paciente_token = :token
GROUP BY agenda_token
) ORDER BY timestamp DESC");

  $stmt->bindValue(':token', $_GET['paciente_token']);
  $stmt->execute();
              
  $dados = $stmt->fetchAll(PDO::FETCH_OBJ);

  $imgData = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/images/logo-bg-default.png'));
  $img = base64_encode(file_get_contents('https://api.invertexto.com/v1/qrcode?token=6974%7C7h8zdmWONV4jLvUyIMULVYOQrah4aT4u&text=https://clinabs.com/agenda/prescricao/receita/'.$_REQUEST['token']));
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

      .box-row {
        width: 90%;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: flex-start;
        align-items: center;
        border-top:: 1px dotted #03e3c1 !important;
        padding: 1rem 2rem;
        border-radius: 10px;
        gap: 2rem;
    }

    .box-icon {
      display: flex;
      flex-direction: row;
      flex-wrap: nowrap;
      align-content: center;
      justify-content: center;
      align-items: center;
      gap: 1rem;
  }

  .box-description {
      display: flex;
      flex-direction: column;
      flex-wrap: nowrap;
      align-content: center;
      justify-content: center;
      align-items: flex-start;
      padding: 1rem;
  }

  .titulo-h6 {
    font-size: 1rem;
    text-align: left;
    width: 100%;
    padding: 1rem 0;
}
b, strong {
    font-weight: bolder;
}

small, .small {
    font-size: 0.875em;
}

.box-txt {
    text-align: center;
    flex-grow: 1;
}

.row.dflex-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    margin-bottom: 1rem;
}

.row {
    display: flex;
    flex-wrap: wrap;
}

.no-break {
        page-break-inside: avoid;
    }
    </style>
  </head>

  <body>
  <div class="document-container" id="doc">
    <div class="document-header">
      <table width="100%">
      <tr>
          <th colspan="3"><h4>HISTÓRICO DE ACOMPANHAMENTO DO PACIENTE</h4></th>
      </tr>
        <tr>
          <td width="128px">
            <img src="data:image/png;base64,'.$imgData.'" height="80px" style="margin-right: 32px">
          </td>
          <td class="street">
            <p style="font-size: 12px"><b style="font-size: 12px">Endereço:</b> Rua Bruno Filgueira, 369 cj.1604 | Edifício 109</p>
            <p style="font-size: 12px"><b style="font-size: 12px">Localidade:</b> AGUA VERDE | CURITIBA - PR - CEP: CEP: 80240-220</p>
            <p style="font-size: 12px"><b style="font-size: 12px">Telefone:</b> (41) 3300-0790</p>
          </td>
          <td width="128px">
              <img src="data:image/png;base64,'.$img.'" height="50px" width="50px" style="margin-right: 32px">
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
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Paciente:</b> '.$paciente->nome_completo.'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Idade:</b> '.$paciente->age.' ano(a)</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Sexo:</b> '.$paciente->identidade_genero.'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Contato:</b> '.sprintf('(%s) *****-%s', substr($paciente->celular, 2, 2), substr($paciente->celular, 3, 4)).'</p>
            <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">E-mail:</b> '.$paciente->email.'</p>
            </td>

            <td width="35%">
              <div style="float: right">
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Data de Emissão:</b> '.date('d/m/Y').'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">CEP:</b> '.($endereco->cep ?? 'Não Informado').'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Endereço:</b> '.($endereco->logradouro ?? 'Não Informado').' '.($endereco->numero ? ', '.$endereco->numero : '').'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Cidade:</b> '.($endereco->cidade ?? 'Não Informado').' '.($endereco->uf ? '/'.$endereco->uf : '').'</p>
                  <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">Bairro:</b> '.($endereco->bairro ?? 'Não Informado').'</p>
              </div>
            </td>
        </tr>
        </table>';
        $html .= "<div class=\"row dflex-column\">";

        foreach($dados as $prontuario) {
           if(rel_add($prontuario->id, $prontuario->tipo, $items)) {
              $txt = implode('<br>', array_map(function($key){
                return base64_decode($key);
            }, explode(',', $prontuario->texto)));
            
            $remedios = count(explode(',', $prontuario->remedio));
            
            if($prontuario->tipo == 'PRESCRICAO') {
                $esp = strlen($prontuario->medico_nome) > 0 ? $prontuario->especialidade : $especialidades[$prontuario->especialidade];
                $usuario_nome = '<b>Médico Prescritor:</b> Dr(a): '.$prontuario->usuario_nome;
                $medicamentos = '';

                $med = $pdo->query("SELECT * FROM `PRESCRICOES` WHERE agenda_token = '{$prontuario->token}'")->fetchAll(PDO::FETCH_ASSOC);

                foreach($med as $m) {
                  $prod = $pdo->query("SELECT * FROM `{$m['produto_ref']}` WHERE id = '{$m['produto_id']}'")->fetch(PDO::FETCH_ASSOC);
                  $medicamentos .= '<p><b>'.$m['frascos'].'x '.$prod['nome'].'</p>'.base64_decode($m['prescricao']).'</b><br>';
                }
            } else {
                $esp = $prontuario->especialidade > 0 ? $especialidades[$prontuario->especialidade] : $prontuario->especialidade;
                $usuario_nome = strlen($prontuario->medico_nome) == 0 ? '<b>Acompanhante:</b> '.$prontuario->usuario_nome : '<b>Acompanhante:</b> Dr(a) '.$prontuario->medico_nome;
            }

            if(isset($esp)) {
                $esp = '  (<b>'.$esp.'</b>))';
            }

            $html .= '----------------------------------------------------------------------------------------------------------------------------------------------
            <div class="box-row no-break">
                      <div class="box-description">
                          <h6 class="titulo-h6">'.($prontuario->tipo == 'PRESCRICAO' ? 'RECEITUÁRIO':$prontuario->tipo).'</h6><br>
                          <strong><b>Data</b>: '.date('d/m/Y', strtotime($prontuario->timestamp)).'</strong><br>
                          <small><b>'.$usuario_nome. '</b></small><br>
                          <span class="page-box-txt">'.($prontuario->tipo == 'PRESCRICAO' ? 'Prescrição de Medicamento' : 'Acompanhamento de Tratamento').'</span><br>
                      </div>
                      <div class="box-txt">'.($prontuario->tipo == 'PRESCRICAO' ? "$medicamentos" : "<p><b>".($prontuario->semana > 0 && $prontuario->periodo > 0 ? "{$prontuario->periodo}ª Acompanhamento, {$prontuario->semana}ª Semana":"")."</b></p> $txt").'</div>
                      </div><br>';
            }
        }
    $html .= "</div>";

$mpdf->setFooter("<small style=\"float: left\">Impresso em {DATE d/m/Y H:i} por ".$user->nome_completo."</small> - Página {nb} de {nbpg}");
$mpdf->WriteHTML($html);

$mpdf->Output();
