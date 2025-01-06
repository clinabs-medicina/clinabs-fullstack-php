$('document').ready(function () {
    setTimneout(() => {

        $("#select-marcas").select2({
            language: "pt",
            tags: true,
            multiple: true,
        });

        $('select[data-search="true"]').select2();

        $('head').append(`<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">`);
        $('head').append(`<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">`);
        $('body').append(`<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>`);

        AOS.init();
    }, 5000);
});

