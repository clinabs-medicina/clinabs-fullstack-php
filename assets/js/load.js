$('document').ready(function () {
    setTimeout(() => {
        $('#row-btn-banner').before(`
            <img width="100%" height="auto" class="img-desktop" src="/assets/images/banner-home.webp" alt="slider1" preload>
            <img width="100%" height="auto" class="img-mobile" src="/assets/images/banner-home-mobile.webp" alt="slider1" preload>
            `);

        $('body').append(`<script src="/assets/js/plugins/tinymce/tinymce.min.js"></script>`);
        $('body').append(`<script type="text/javascript" src="/assets/modules/sweetalert2/sweetalert2.js"></script>`);
        $('body').append(`<script type="text/javascript" src="/assets/js/datatable.js"></script>`);
        $('body').append(`<script type="text/javascript" src="/assets/js/croppie.js"></script>`);
        $('body').append(`<script type="text/javascript" src="/assets/js/buttons.colVis.js"></script>`);
        $('body').append(`<script type="text/javascript" src="/assets/js/dataTables.buttons.js"></script>`);
        $('body').append(`<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>`);

        $('head').append(`<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.css" crossorigin="sameorign">`);
        $('head').append(`<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap2.css" crossorigin="sameorign">`);
        $('head').append(`<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap3.css" crossorigin="sameorign">`);
        $('head').append(`<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap4.css" crossorigin="sameorign">`);
        $('head').append(`<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap5.css" crossorigin="sameorign">`);

        $('head').append(`<link rel="stylesheet" href="/assets/css/select2.css" crossorigin="sameorign">`);
        $('head').append(`<link rel="stylesheet" href="/assets/css/calendar.css" crossorigin="sameorign">`);
        $('head').append(`<link href="/assets/css/select2.css" rel="stylesheet" type="text/css" crossorigin="sameorign">`);

        $('.preloader-container').fadeOut();

        $('#banner-load').remove();
    }, 1600);
});