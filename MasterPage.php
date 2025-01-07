<?php
define('ROOT', $_SERVER['DOCUMENT_ROOT']); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/*
if(isset($_SESSION['userObjEditPerfil']) && ($_SESSION['userObjEditPerfil'] !== null)) {
    $_user = (object) $_SESSION['userObjEditPerfil'];
    error_log("user sel ok\r\n" . PHP_EOL);
    $tp = $_user->tipo ?? "";
    error_log("valor MasterPage user->tipo $tp\r\n" . PHP_EOL);
} else
if(isset($_SESSION['userObj'])) {
    $user = (object) $_SESSION['userObj'];
    $_user = $user;
    $tp = $_user->tipo ?? "";
    error_log("valor MasterPage user->tipo $tp\r\n" . PHP_EOL);

}
*/
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
        content="width=device-width, initial-scale=0.9, minimum-scale=0.9, maximum-scale=0.9">
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
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
</style>
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
    <link href="/assets/css/select2.css" rel="stylesheet" type="text/css" crossorigin="sameorign" />
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
        .form-grid.row-4 {
            grid-template-columns: 1fr 1fr 1fr 1fr;
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
    *[data-badge]::after {
        display: flex;
        width: 20px;
        height: 20px;
        background-color: #00b196;
        position: absolute;
        margin-top: -33px;
        content: attr(data-badge);
        border-radius: 100%;
        color: #fff;
        vertical-align: middle;
        margin-left: 22px;
        padding: 0.13rem;
        font-size: 0.80rem;
        text-align: center;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }
    .flex-container .menu-header ul li a {
        font-size: 12px;
    }

    .menu-user-links {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 1rem 0rem;
        overflow-y: auto;
        font-size: 14px;
    }

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

    .main, body {
        width: 100%;
        overflow-x: hidden;
        min-width: 100%;
    }
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


    
    @media (max-width: 1000px) {
        img.border.border-4.border-primary.rounded-circle {
            width: 100px !important;
            height: 100px !important;
        }

        span.doctor-title {
            font-size: 12px;
            font-family: 'Poppins';
            font-weight: 700;
        }

        .doctor-card {
            background-color: #24b39e;
            color: white;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            max-height: 100px;
        }

        .row.text-center.doctor-rows {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: center;
            align-items: stretch;
        }

        .row.text-center.doctor-rows .col-3.col-lg-3.col-md-3.col-sm-12 {
            width: 100%;
        }

        .help-flex button {
            font-size: 16px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: -1.3px;
            fill: #FFFFFF;
            color: #FFFFFF;
            background-color: transparent;
            background-image: linear-gradient(89deg, #03e3c1 0%, #05ad94 100%);
            border-style: solid;
            border-width: 0px 0px 0px 0px;
            border-color: #03e3c1;
            border-radius: 100px 100px 100px 100px;
            padding: 25px 50px 25px 50px;
            transition: .4s;
            cursor: pointer;
            margin: 2rem 0;
        }

        .medico-item a {
            margin: 0px !important;
            font-size: 12px;
        }

        .header .flex-container img:first-child {
            height: 50px;
        }

        .whatsapp-widget {
            display: block;
            position: fixed;
            height: auto;
            right: 1rem;
            bottom: 1.5rem;
            width: 300px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 10px;
        }

        .footer-column * {
            font-size: 12px;
        }

        .agendamento-filters h3 {
            font-size: 14px;
        }

        .agendamento-filters .filtros label {
            font-size: 11px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            align-content: space-between;
            justify-items: center;
            align-items: baseline;
            gap: 4px;
            padding: 3px;
            font-size: 11px;
        }


        div#calendar .day {
            font-size: 10px;
            padding: 2px 2px;
            width: 28px;
        }

        .calendar-container {
            background: #fff;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.3);
            padding: 0px;
            border-radius: 15px;
            width: 100%;
            max-width: 520px;
            text-align: center;
            height: auto;
        }

        h2#calendar-info {
            font-size: 14px;
        }

        .calendar-header button {
            background-color: white;
            border: none;
            color: #333333c4;
            padding: 2px 13px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 1.50rem;
        }

        .calendar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            flex-direction: row;
            flex-wrap: nowrap;
            align-content: center;
            gap: 15px;
        }

        h5, .h5 {
            font-size: 1rem;
        }

        .listmedic-box-dir-boxtime {
            width: 100%;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            align-items: stretch;
            align-content: center;
        }

        .listmedic-box-dir-ico.location-clinic {
            display: none;
        }

        .listmedic-box-dir-user {
            display: flex;
            width: fit-content;
            flex-direction: column;
            padding: 1rem;
            gap: 6px;
        }

        .listmedic-box-dir-user>* {
            display: flex;
            font-size: 12px;
            flex-direction: row;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: center;
            align-items: flex-start;
            font-size: 10px;
        }

        .header .flex-container {
            width: 94%;
            gap: 0.35rem;
        }

        .link-consult-mobile a {
            font-size: 11px !important;
        }

        .toolbar-btns {
            display: flex;
            padding: 1rem;
        }

        .toolbar-btns button {
            padding: 10px 8px;
        }

        .toolbar-btns button {
            padding: 4px 5px;
            width: 128px;
            font-size: 12px;
        }

        fieldset.filter-field legend {
            font-size: 11px;
            background: #eee;
            width: 256px;
            margin-top: 10px;
            margin-right: 32px;
            border: 1px solid #eee;
            border-radius: 10px;
            text-align: left;
        }

        td > * {
            font-size: 11px !important;
        }

        ul.user-menu {
            width: 65%;
        }

        .menu-user-links {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 1rem 0rem;
            overflow-y: auto;
        }

        .flex-container .menu-ico ul {
            display: flex;
            align-items: center;
            gap: 0.30rem;
        }

        .menu-user-title {
            padding: 0rem 0rem !important;
        }

        ol, ul {
            padding-left: 1rem;
        }

        .mobile-menu {
            background-color: #05ad94;
            height: 90px;
            padding: 21px 0px;
            width: auto;
        }

        section.grid-container {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            align-content: flex-start;
            justify-content: center;
            align-items: center;
        }

        section.grid-container .grid-item {
            width: 100%;
        }

        .page-boxlement {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.3rem;
            flex-direction: column;
        }

        .page .page-flex {
            width: 90%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        h1, .h1 {
            font-size: calc(1rem + 1.5vw);
        }

        .week-name span:nth-child(1) {
            font-size: 0.7rem;
            text-transform: uppercase !important;
            text-align: center;
        }

        .week-name span:nth-child(2) {
            font-size: 0.7rem;
            text-transform: uppercase !important;
            text-align: center;
        }

        .main .flex-container {
            width: 100%;
            display: flex;
            justify-content: center;
            text-align: center;
            margin: 0 auto !important;
            padding: 0rem !important;
        }

        form {
            width: 100% !important;
            padding: 0 !important;
            flex-direction: column;
        }

        .calendar-slide.active {
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            min-height: 400px;
            justify-content: flex-start;
            align-content: center;
        }

        .tabControl .tab.active {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
            padding: 0;
            align-content: flex-start;
            flex-wrap: nowrap;
        }

        .week-name span:nth-child(2) {
            font-size: 10px;
            text-transform: uppercase !important;
            text-align: center;
        }

        .calendar-slide.active {
            width: 100%;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            min-height: 400px;
            justify-content: center;
            align-content: center;
            gap: 5px;
        }

       .week-item {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: center;
            align-items: center;
            padding: 0.1rem;
            gap: 0.35rem;
            width: 15%;
        }

        label.week-time.listmedic-box-dir-time {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: center;
            align-items: center;
            font-size: 9px;
            font-weight: bold;
        }


        .tabControl .tab.active {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 0;
            align-content: flex-start;
            flex-wrap: nowrap;
            justify-content: center;
        }

        .main, body {
            width: 100%;
            overflow-x: hidden;
            min-width: 100%;
        }

        label.week-schedule.listmedic-box-dir-time i {
            z-index: 9999;
        }


        button.next-week {
            margin-right: 12px;
        }

        button.prev-week {
            margin-left: 0px;
            visibility: hidden;
        }
    }

    nav.menu-ico .m-0 {
        gap: 8px;
    }


    @media (max-width: 1000px) {
        table.dataTable td *, table.dataTable th * {
            font-size: 9px;
        }

        table.dataTable td, table.dataTable th {
            box-sizing: content-box;
            vertical-align: middle;
            flex-direction: row;
            align-content: center;
            align-items: center;
            margin-top: 18px;
            font-size: 11px;
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
    }

    .listmedic-box-dir-time i.fa {
        font-size: 20px !important;
    }

    .filter-container .row {
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        display: flex;
        flex-wrap: wrap;
        margin-top: 0;
        margin-right: 0;
        margin-left: 0;
        flex-direction: row;
        align-content: center;
        justify-content: center;
        align-items: flex-start;
    }

    .filter-container .row .form-group label {
        font-size: 12px;
        font-weight: 600;
    }

    .filter-container .row .form-group {
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: stretch;
        text-align: center;
        width: 100%;
    }

    .filter-container .row .form-group label {
        width: 100%;
        display: flex;
        text-align: center;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    fieldset.filter-field {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        padding: 0rem 8px 8px;
        position: relative;
    }

    fieldset.filter-field legend {
        font-size: 12px;
        background: #eee;
        width: 100%;
        margin-top: 0;
        text-align: left;
        border: 1px solid #eee;
        border-radius: 10px;
    }

    i.fa-solid.fa-filter-circle-xmark.filter-clear {
        position: absolute;
        float: right;
        right: 0px;
        padding: 8px;
    }

    .btns-table button > img {
        height: 22px;
    }

    .btns-table {
        display: flex;
        gap: 5px;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    button.btn-action img {
        height: 22px;
    }

    table.dataTable td, table.dataTable th {
        box-sizing: content-box;
        vertical-align: middle;
        flex-direction: column-reverse;
        flex-wrap: wrap;
        align-content: center;
        align-items: center;
        margin-top: 18px;
        font-size: 11px;
    }


    label.week-time.week-schedule.listmedic-box-dir-time.active.disabled-wt {
        background-color: #f9052a;
        border-color: #f9052a;
    }

    .row.row-config-agenda {
        display: none;
        flex-direction: column;
    }

    .week-item label {
        width: 100% !important;
        height: 100% !important;
    }

    .row-weeks .week-item input {
        display: none;
    }


    i.fas.fa-info-circle {
        font-size: 2.50rem;
        color: #0d6efd;
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

    <script type="text/javascript" src="/assets/js/scripts.js"></script>
    <script type="text/javascript" src="/assets/js/scripts2.js"></script>

    <script type="text/javascript" src="/assets/js/agendamento.js"></script>
    
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

   <?php
   if(isset($useWb)) {
        echo '<script src="https://cdn.srv.whereby.com/embed/v2-embed.js" type="module"></script>';
   }
   
   if(isset($useCalendar)) {
        echo '<script src="/assets/js/script2.js"></script>';
   }
   ?> 
   

   <script>
        $('document').ready(function(){
            //$('select').select2();
            $('select.form-control.select-tags').select2({
                tags: true
            });

            setTimeout(() => {
                $(".whatsapp-header i").trigger('click');
            }, 3500);

            if(window.innerWidth < 600) {
                $('label.week-time.listmedic-box-dir-time').on('contextmenu', function(){
                    $(this).trigger('dblclick');
                });
            }
        });
   </script>
</body>

</html>