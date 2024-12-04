<html>

<head>
    <title>Redirecionando...</title>

    <style>
    .preloader-container {
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }
    </style>
</head>

<body>

    <div class="preloader-container">
        <div class="preloader-content"></div>
        <div class="preloader">
            <img src="/assets/images/loading.gif">
        </div>
    </div>

    <?php
  require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  $doc = $_GET;
  $token = $_GET['token'];
  $cid = $_GET['cid'];

  $ag = $agenda->getByToken($doc['token']);

  require_once 'CertiSign.php';
  $certisign = new CertiSign(
    token: 'd29fca2043fc41f0ab07d48a11b00d13'
  );

  $doc = $certisign->bind('signature', [
    "file" => "https://clinabs.com/api/pdf/receita.php?token={$token}&cid={$cid}&dl=true",
    "nome" => $ag->medico_nome,
    "email" => $ag->medico_email,
    "cpf" =>  preg_replace('/[^0-9]/', '', $ag->medico_cpf),
    "receitaId" => "Receita: #{$ag->token}",
    "medicoNome" => $ag->medico_nome,
    "pacienteNome" => "Paciente: {$ag->nome_completo}",
    "receitaId" => $token,
    "token" => $ag->token
  ]);

  if(isset($doc['url']) && isset($doc['chave'])) {
    header("Location: {$doc['url']}");
  } else {
    header("Location: https://portaldeassinaturas.com.br/Home/LoginMail");
  }
  ?>
</body>

</html>