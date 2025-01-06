<!DOCTYPE html>
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
  .street, span, p, b {
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
          <p><b style="font-size: 12px">Endereço:</b> Rua dos bobos, 0 Centro, Cidade-UF</p>
          <p><b style="font-size: 12px">Telefone:</b> (41) 3366-0011</p>
          <p><b style="font-size: 12px">Dr(a). VITOR JORGE WOYTUSKI BRASIL | CRM: 18792 - PR</b></p>
        </td>
        <td>
          <img src="logo-anna.svg" height="48px">
        </td>
        <td>
          <img style="margin-left: 15px" src="logo-pucmed.svg" height="48px">
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
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">PACIENTE:</b> LUIZ INÁCIO LULA DA SILVA</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">ENDEREÇO:</b> SITIO DE ATIBAIA</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">IDADE:</b> 73 anos</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">ANAMNESE:</b> OUTROS</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">SEXO:</b> MASCULINO</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">TELEFONE:</b> (11) 9 9878-0256</p>
          <p style="font-size: 12px;height: 48px"><b style="font-size: 12px">E-MAIL:</b> lula.ladrao@gmail.com</p>
          <td class="text-top" style="font-size: 12px;height: 48px"><b style="font-size: 12px">DATA DE EMISSÃO:</b> 07/12/2023</td>
      </tr>
    </table>

    <div class="document-title" style="font-size: 1rem;font-weight: bold;color: #fff;text-align: center;padding: 0.50rem;background-color: #1da56c;">PRESCRIÇÃO MÉDICA</div>
    <p>Em avaliação o paciente diz que não consegue parar de roubar o Brasil.</p>
  </div>

  <div class="document-footer"
       style="display: flex;border-top: 1px solid #000;border-bottom: 1px solid #000;flex-direction: row;flex-wrap: nowrap; align-content: center;justify-content: space-between;align-items: center;margin-top: 3rem;padding: 2rem 0; margin-bottom: 4rem;">
    <div class="box1"
         style="display: flex;flex-direction: column;flex-wrap: nowrap;align-content: flex-start;justify-content: center;">
      <small>Com estima,</small>
      <h4>Dr. VITOR JORGE WOYTUSKI BRASIL | CRM: 18792 - PR</h4>
      <p><small style="margin-top: -48px">Clinico Geral</small></p>
      <p><small>Centro de Acolhimento em Terapia Canabinoide da Expo Cannabis</small></p>
    </div>

    <div class="box2"
         style="display: flex;flex-direction: column;flex-wrap: nowrap;align-content: center;justify-content: center;align-items: center;padding: 1rem;">
    </div>
  </div>
</div>
</body>

</html>