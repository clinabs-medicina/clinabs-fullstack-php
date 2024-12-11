<?php
define('ROOT', $_SERVER['DOCUMENT_ROOT']); 
function inline_files(string $type, array $files) {
    echo "<{$type}>";
    
    foreach($files as $f) {
        echo file_get_contents($_SERVER['DOCUMENT_ROOT']."/{$f}");
    }

    echo "</{$type}>";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.2, maximum-scale=5">
    <meta content="text/html; charset=UTF-8;" http-equiv="Content-Type" />
    <meta name="user" content="<?= $user->tipo ?? 'none' ?>" />
    <meta name="user-id" content="<?= $user->token ?? 'none' ?>" />
    <meta name="user-name" content="<?= $user->nome_completo ?? 'none' ?>" />
    <meta name="X-IP-Address" content="<?= $_SERVER['REMOTE_ADDR'] ?>" />
    <meta name="X-App-Version" content="rev_1.0.4" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="x-frame-options" content="sameorigin" />
    <title><?= $page->title == 'Home' ? 'Início' : $page->title ?> - Clinabs - O tratamento natural para seus problemas
        de saúde.</title>
    <?php if (isset($_REQUEST['pedido_code'])) {
    echo '<meta name="track_id" content="' . $_REQUEST["pedido_code"] . '">';
  } ?>

<?php
    
    $fws = [
        "/assets/font-awesome/css/all.min.css",
        //"/assets/font-awesome/css/fws.min.css",
       // "/assets/font-awesome/css/fws2.min.css",
        //"/assets/font-awesome/css/fws3.min.css",
        //"/assets/font-awesome/css/fws4.min.css",
        //"/assets/font-awesome/css/fws5.min.css",
        //"/assets/css/calendar.css",
        //"/assets/css/select2.css"
    ];

    inline_files('style', $fws);
    ?>
    <meta name="description"
        content="Agende sua consulta online hoje mesmo e comece a aproveitar os benefícios do CBD. Nossos médicos especialistas em canabinoides estão aqui para ajudá-lo a melhorar sua vida com o uso de CBD.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link rel="manifest" href="/manifest.json" />
    <link rel="icon favicon" href="/assets/images/favicon.ico">
    <style>
        @font-face {
            font-family: 'Inter';
            src: url('/assets/fonts/Inter-Regular.ttf') format('truetype');
        }

        @font-face {
            font-family: 'Proxima Nova';
            src: url('/assets/fonts/ProximaNova-Reg-webfont.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }


        * {
            user-select: none;
        }


        :root {
            --main-bg-color1: #05ad94;
            --main-bg-color2: #03e3c1;
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #212529;
        }

    </style>
    <?php

    if(isset($useDT)) {
        echo '<link href="/assets/css/datatable.css" rel="stylesheet" type="text/css" crossorigin="sameorign" />';
        echo '<link href="/assets/css/responsive.dataTables.css" rel="stylesheet" type="text/css" crossorigin="sameorign" />';
        echo '<link href="/assets/css/buttons.dataTables.css" rel="stylesheet" type="text/css" crossorigin="sameorign" />';
    }
    
    $bootstrap = [
        "/assets/bootstrap/css/bootstrap.css",
        "/assets/bootstrap/css/bootstrap2.css",
        "/assets/bootstrap/css/bootstrap3.css",
        "/assets/bootstrap/css/bootstrap4.css",
        "/assets/bootstrap/css/bootstrap5.css",
        "/assets/css/sweetalert.css",
        "/assets/css/croppie.css"
    ];

    inline_files('style', $bootstrap);
    ?>


    <link href="/assets/css/select2.css" rel="stylesheet" type="text/css" crossorigin="sameorign" />

    <?php
    $templates = [
        "/assets/css/style.css",
        "/assets/css/style2.css",
        "/assets/css/style3.css",
        "/assets/css/style4.css"
    ];

    inline_files('style', $templates);
    ?>
    <?php

    if(isset($page->useDT) && $page->useDT == true) {
    echo '<link rel="preload" rel="stylesheet" href="/assets/css/datatable.css" crossorigin="anonymsameorignous">
    <link rel="preload" rel="stylesheet" href="/assets/css/responsive.dataTables.css" crossorigin="sameorign">
    <link rel="preload" rel="stylesheet" href="/assets/css/buttons.dataTables.css" crossorigin="sameorign">';
    }

    $templates = [
        "/assets/css/theme3.css",
        "/assets/css/template1.css",
        "/assets/css/template2.css",
        "/assets/css/template3.css",
        "/assets/css//clinabs.plugins.css",
        "/assets/css/quill.snow.css"
    ];

        inline_files('style', $templates);
    ?>
    

    <style>
    .doctor-card {
        background-color: #24b39e;
        color: white;
        padding: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        max-height: 160px;

    }

    .doctor-card img {
        border-radius: 50%;
        width: 225px;
        height: 225px;
        object-fit: cover;
        margin-right: 20px;
    }

    .doctor-info {
        flex-grow: 1;
    }

    .doctor-info h5 {
        margin: 0;
        font-size: 1.25rem;
    }

    .doctor-info p {
        margin: 0;
    }

    .doctor-link {
        color: #007bff;
        text-decoration: none;
    }

    .doctor-link:hover {
        text-decoration: underline;
    }
    </style>

    <style>
    .custom-icon {
        width: 80px;
        height: 80px;
        margin-bottom: -45px;
    }

    .custom-box {
        background-color: #24b39e;
        padding: 30px;
        border-radius: 15px;
        font-family: "Poppins";
        text-align: center;
        color: white;
        margin-bottom: 20px;
    }

    .custom-box h5 {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .custom-container {
        letter-spacing: 0.2px;
        margin: 0 auto;
        padding: 10px 5%;
    }

    .square-card {
        aspect-ratio: 1/1;
        max-width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    </style>

</head>

<div class="preloader-container">
    <div class="preloader-content"></div>
    <div class="preloader">
        <img height="64px" width="64px" src="/assets/images/loading.gif" alt="Loading">
    </div>
</div>

<body>
    <!-- TOPO HEADER SITE -->
    <?php require_once ROOT . '/header.php'; ?>

    <!-- FIM TOPO HEADER SITE -->

    <!-- SLIDER -->
    <?= (count(explode('/', $_SERVER['REQUEST_URI'])) < 3 && !$is_nabscare) ? '' : '<!--' ?>

    <section class="slider" id="slider1">
        <div class="slider-content">
            <div class="slider-box active">
                <div id="row-btn-banner" class="row">
                    <div class="col-4 col-lg-4 col-md-6">
                    </div>
                    <div class="col-8 col-lg-8 col-md-6" style="margin-top: -112px;">
                        <div class="row" style="display: flex;">
                            <div class="col-3 col-md-3">
                                <div class="slider-text">
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="slider-text">
                                    <a href="/agendamento"
                                        class="btn btn-light btn-lg m-2 px-5 fw-semibold fs-3">AGENDA</a>
                                </div>
                            </div>
                            <div class="col-4 col-md-4">
                                <div class="slider-text">
                                    <a href="https://wa.me/554133000790?text=Seja+bem+vindo%21"
                                        class="btn btn-light btn-lg m-2 px-5 fw-semibold fs-3">DÚVIDAS</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="row-btn" class="row m-0 p-2">
                    <div class="col-6 col-md-6">
                        <div class="slider-text">
                            <a href="/agendamento" class="btn btn-light btn-md m-2 px-4 fw-semibold fs-3">AGENDA</a>
                        </div>
                    </div>
                    <div class="col-6 col-md-6">
                        <div class="slider-text">
                            <a href="https://wa.me/554133000790?text=Seja+bem+vindo%21"
                                class="btn btn-light btn-md m-2 px-4 fw-semibold fs-3">DÚVIDAS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?= (count(explode('/', $_SERVER['REQUEST_URI'])) < 3) ? '' : '-->' ?>
    <!-- fim SLIDER -->

    <!-- CONTEUDO PRINCIPAL -->
    <?php
  require_once $page->content;
  ?>
    <!-- FIM CONTEUDO PRINCIPAL -->

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>

    <!-- FIM FOOTER -->

    <!-- POLITICA DE PRIVACIDADE -->
    <div class="rounded-1 cmplz-cookiebanner hide">
        <div class="container">
            <div class="icogroup">
                <img height="50px" src="/assets/images/ico-cookies.svg" alt="Cookie">
                <h4>Esse site usa Cookies</h4>
            </div>
            <p>Armazenamos dados temporariamente para melhorar a sua experincia de navegação. Ao utilizar nossos
                servios, você concorda com tal monitoramento.</p>
            <br>
            <span class="politica-link" style="text-align: center;"><a
                    href="/assets/doc/politica_de_privacidade.pdf">Política de privacidade</a></span>
            <div class="cmplz-cookiebanne-linksgroup">
                <button class="btn-danger">Não Aceitar</button>
                <button class="btn-accept" id="btn-accept-cookies">Aceitar</button>
            </div>
        </div>
    </div>

    <!-- FIM POLITICA DE PRIVACIDADE -->
    
    
    <script type="text/javascript" src="/assets/js/ClinabsJS.js"></script>
    <script type="text/javascript" src="/assets/js/clinabs.js"></script>

    <script type="text/javascript" src="/assets/js/select2.js"></script>
    <script type="text/javascript" src="/assets/modules/sweetalert2/sweetalert2.js"></script>
    <script type="text/javascript" src="/assets/js/croppie.js"></script>
    
    <?php
    if(isset($useDT)) {
        echo '<script type="text/javascript" src="/assets/js/datatable.js"></script>
                <script type="text/javascript" src="/assets/js/buttons.colVis.js"></script>
                <script type="text/javascript" src="/assets/js/dataTables.buttons.js"></script>
                <script type="text/javascript" src="/assets/js/dataTables.rowReorder.js"></script>
                <script type="text/javascript" src="/assets/js/responsive.dataTables.js"></script>
                <script type="text/javascript" src="/assets/js/dts.js"></script>';
    }
    ?>

    <!--<script src="https://cdn.tiny.cloud/1/o69uuqv853g4pxc40ctycrnc5e3imuz426yspmq9l28bvv0v/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>-->
    
    <?php
    if(isset($useEditor)) {
        echo '<script src="/assets/js/plugins/tinymce/tinymce.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>';
    }
    ?>

    <script src="/assets/js/imask.min.js"></script>
    <script src="/assets/js/clinabs.plugins.js"></script>
    <script type="text/javascript" src="/assets/js/agenda.js"></script>
    <script type="text/javascript" src="/assets/js/endereco.js"></script>
    <script type="text/javascript" src="/assets/js/services.js"></script>
    <script type="text/javascript" src="/assets/js/services.upload.js"></script>
    
     
    <script type="text/javascript" src="/assets/js/scripts2.js"></script>
    <script type="text/javascript" src="/assets/js/scripts.js"></script>
    
    <?php
        if($page->name == 'link_agendar_consulta') {
            echo '<script type="text/javascript" src="/assets/js/calendar.js"></script>';
            echo '<script type="text/javascript" src="/assets/js/plugins.calendar.js"></script>';
        }
    ?>

    <script type="text/javascript" src="/assets/js/script-doctor.js"></script>
    <script type="text/javascript" src="/assets/js/test.js"></script>
    <script type="text/javascript" src="/assets/js/certificado.js"></script>
    <script type="text/javascript" src="/assets/js/dashboard.js"></script>
    <script type="text/javascript" src="/assets/js/clinabs.modules.js"></script>
    <script type="text/javascript" src="/assets/js/agenda.module.js"></script>
    <script type="text/javascript" src="/assets/js/agendamento.js"></script>
    <?php
    /*
      if($warningMsg !== false && $warningMsg !== null) {
        echo '<script type="text/javascript" id="swal2-script">
                Swal.fire({
                  icon: "warning",
                  title: "Atenção",
                  text: "'.$warningMsg.'"
                }).then(() => {
                  $("#swal2-script").remove();
                });
              </script>';
      }
    */
  ?>
<!--
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-16646948240"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'AW-16646948240');
    </script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YJCEGR2LXF"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-YJCEGR2LXF');
    </script>

    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-N7VLSJ5J');
    </script>
-->
   <?php
   if(isset($useWb)) {
        echo '<script src="https://cdn.srv.whereby.com/embed/v2-embed.js" type="module"></script>';
   }
   ?> 
</body>

</html>