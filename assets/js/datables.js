$('document').ready(function(){
    $('table').each(function(){

        $(this).find('thead tr').find('th.hide-mb').each(function(){
            $(this).find(`tbody tr td:nth-child(${$(this).index()})`).find('th.hide-mb');
        });
    });
});