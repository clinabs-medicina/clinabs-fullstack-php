<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');

$paciente = $agenda->get($_REQUEST['token']);

$html = '<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
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
  </style>
</head>

<body>
<div class="document-container" id="doc">
  <div class="document-header">
    <table width="100%">
      <tr>
        <td>
          <img src="logo-clinabs-pdf.svg" height="80px" style="margin-right: 32px">
        </td>
        <td class="street">
          <p style="font-size: 12px"><b style="font-size: 12px">Dr(a).</b> '.$paciente->medico_nome.'</p>
          <p style="font-size: 12px"><b style="font-size: 12px">CRM:</b> '.$paciente->num_conselho.' <b style="font-size: 12px">'.$paciente->uf_conselho.'</b> </p>
          <p style="font-size: 12px"><b style="font-size: 12px">Endereço:</b> Av. Visc. de Guarapuava, Nº 2744 - Centro, Curitiba - PR - Brasil</p>
          <p style="font-size: 12px"><b style="font-size: 12px">Telefone:</b> (41) 3300-0341</p>
        </td>
      </tr>
    </table>
  </div>
<br>
  <div class="document-body"
       style="display: grid;grid-template-columns: 1fr;grid-template-rows: 1fr;grid-row-gap: 1rem;">
    <div class="document-title"
         style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">
      PARECER TÉCNICO</div>
    <table width="100%" style="margin-top: 16px;margin-bottom: 16px">
      <tr>
        <td width="70%">
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">PACIENTE:</b> '.$paciente->nome_completo.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">ENDEREÇO:</b> '.$paciente->endereco.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">IDADE:</b> '.Modules::calcularIdade($paciente->data_nascimento).'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">ANAMNESE:</b> '.$paciente->anamnese.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">SEXO:</b> '.$paciente->identidade_genero.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">TELEFONE:</b> '.$paciente->telefone.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">E-MAIL:</b> '.$paciente->email.'</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">DATA DE EMISSÃO:</b> '.date('d/m/Y').'</p>
          </td>
      </tr>
    </table>

    <div class="document-title" style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">PRESCRIÇÃO MÉDICA</div>
    ';
    
    $i = 1;

    foreach(json_decode($paciente->prescricao, true) as $prescricao) {
       if($prescricao != '') {
          $html .= '<div style="width: 100% !important; height: 32px !important"></div><p><small style="font-size: 9px !important;">'.$i.' - '.$prescricao.'</small></p>';
       $i++;
       }
    }
    
    
   $html .= '
  </div>

  <div class="document-footer"
       style="display: flex;border-top: 1px solid #000;border-bottom: 1px solid #000;flex-direction: row;flex-wrap: nowrap; align-content: center;justify-content: space-between;align-items: center;margin-top: 3rem;padding: 1rem 0; margin-bottom: 3rem;">
    <div class="box1"
         style="display: flex;flex-direction: column;flex-wrap: nowrap;align-content: flex-start;justify-content: center;">
      <small>Com estima,</small>
      <h4>Dr(a). '.$paciente->medico_nome.'<br>CRM: '.$paciente->num_conselho.''.($paciente->uf_conselho !== '' ? ' - '.$paciente->uf_conselho : '').'</h4>
      <p><small style="margin-top: -48px">'.$paciente->especialidade.'</small></p>
      <p><small></small></p>
    </div>
  </div>
 </div>


</div>
</body>
</html>';



echo $html;
