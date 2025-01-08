<?php
function getBrowser()
{
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version = '';

  // First get the platform?
  if (preg_match('/linux/i', $u_agent)) {
    $platform = 'linux';
  } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
    $platform = 'mac';
  } elseif (preg_match('/windows|win32/i', $u_agent)) {
    $platform = 'windows';
  }

  // Next get the name of the useragent yes seperately and for good reason
  if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
    $bname = 'Internet Explorer';
    $ub = 'MSIE';
  } elseif (preg_match('/Firefox/i', $u_agent)) {
    $bname = 'Mozilla Firefox';
    $ub = 'Firefox';
  } elseif (preg_match('/OPR/i', $u_agent)) {
    $bname = 'Opera';
    $ub = 'Opera';
  } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
    $bname = 'Google Chrome';
    $ub = 'Chrome';
  } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
    $bname = 'Apple Safari';
    $ub = 'Safari';
  } elseif (preg_match('/Netscape/i', $u_agent)) {
    $bname = 'Netscape';
    $ub = 'Netscape';
  } elseif (preg_match('/Edge/i', $u_agent)) {
    $bname = 'Edge';
    $ub = 'Edge';
  } elseif (preg_match('/Trident/i', $u_agent)) {
    $bname = 'Internet Explorer';
    $ub = 'MSIE';
  }

  // finally get the correct version number
  $known = array('Version', $ub, 'other');
  $pattern = '#(?<browser>' . join('|', $known)
    . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
  if (!preg_match_all($pattern, $u_agent, $matches)) {
    // we have no matching number just continue
  }
  // see how many we have
  $i = count($matches['browser']);
  if ($i != 1) {
    // we will have two since we are not using 'other' argument yet
    // see if version is before or after the name
    if (strripos($u_agent, 'Version') < strripos($u_agent, $ub)) {
      $version = $matches['version'][0];
    } else {
      $version = $matches['version'][1];
    }
  } else {
    $version = $matches['version'][0];
  }

  // check if we have a number
  if ($version == null || $version == '') {
    $version = '?';
  }

  return array(
    'name' => $bname,
    'version' => $version,
    'platform' => $platform,
    'pattern' => $pattern
  );
}

// now try it
$ua = getBrowser();

if ($ua['name'] == 'Apple Safari' && explode('.', $ua['version'])[0] < 13) {
  ?>
  <!DOCTYPE html>
  <html lang="pt-BR">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Navegador Não Suportado</title>
      <style>
          body {
              font-family: Arial, sans-serif;
              text-align: center;
              margin: 0;
              padding: 0;
              background-color: #f4f4f4;
              color: #333;
          }
          .container {
              max-width: 600px;
              margin: 50px auto;
              padding: 20px;
              background: #fff;
              border: 1px solid #ddd;
              border-radius: 8px;
          }
          h1 {
              color: #e74c3c;
          }
          p {
              font-size: 18px;
              line-height: 1.6;
          }
          a {
              color: #3498db;
              text-decoration: none;
              font-weight: bold;
          }
          a:hover {
              text-decoration: underline;
          }
          .browser-logos img {
              width: 100px;
              margin: 10px;
          }
          .browser-logos {
              display: flex;
              justify-content: space-around;
              flex-wrap: wrap;
              flex-direction: row;,
              align-content: center;
              align-items: center;
          }

          .browser-logos a {
              display: flex;
              flex-direction: column;
              flex-wrap: nowrap;
              align-content: space-between;
              justify-content: center;
              align-items: center;
          }
      </style>
  </head>
  <body>
      <div class="container">
          <h1>Navegador Não Suportado</h1>
          <p>Parece que você está usando o <?= ($ua['name']) ?>, que não é suportado pelo nosso site. Para uma melhor experiência, por favor, Atualize ou mude para um dos seguintes navegadores:</p>
          <div class="browser-logos">
              <a href="https://www.google.com/chrome/" target="_blank">
                  <img src="https://www.google.com/chrome/static/images/chrome-logo.svg" alt="Google Chrome">
                  Google Chrome
              </a>
              <a href="https://www.mozilla.org/firefox/" target="_blank">
                  <img src="data:image/jpeg;base64,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" alt="Mozilla Firefox">
                  Mozilla Firefox
              </a>
          </div>
          <p>Obrigado pela sua compreensão!</p>
      </div>
  </body>
  </html>
  <?php

  exit();
}
