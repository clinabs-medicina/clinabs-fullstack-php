// scripts.js
let calendarDate = new Date();
let events_days = [];

const renderCalendar = (date) => {
    const parentNode = $('.calendar-container');

    if ($('#calendar').length) {
        $('#calendar').remove();
    }

    const calendar = document.createElement('div');
    calendar.id = 'calendar';
    calendar.classList.add('calendar');

    $(parentNode).append(calendar);

    calendar.dataset.year = calendarDate.getFullYear();

    $.get('/agendamento/calendar_api.php', {
        month: date.getMonth() + 1,
        year: date.getFullYear()
    }).done(function (cal) {
        console.log({
            month: date.getMonth() + 1,
            year: date.getFullYear()
        });

        $('#calendar-info').text(`${cal.month}, ${cal.year}`);

        $(calendar).append(`<div class="day-header">Dom</div>`);
        $(calendar).append(`<div class="day-header">Seg</div>`);
        $(calendar).append(`<div class="day-header">Ter</div>`);
        $(calendar).append(`<div class="day-header">Qua</div>`);
        $(calendar).append(`<div class="day-header">Qui</div>`);
        $(calendar).append(`<div class="day-header">Sex</div>`);
        $(calendar).append(`<div class="day-header">Sab</div>`);


        for (let i = 0; i < cal.prev.length; i++) {
            const item = cal.prev[i];

            $(calendar).append(`<div class="day day-disabled day-lock" data-date="${item.date}">${item.day}</div>`);
        }

        for (let i = 0; i < cal.current.length; i++) {
            const item = cal.current[i];

            const evt = events_days.filter(function (ev) {
                return ev === item.date;
            });

            $(calendar).append(`<div onclick="openCalendarLink(this)" class="day selectable${evt.length > 0 ? ' event-day' : ''}" data-date="${item.date}">${item.day}</div>`);
        }

        for (let i = 0; i < cal.next.length; i++) {
            const item = cal.next[i];

            $(calendar).append(`<div class="day day-disabled day-lock" data-date="${item.date}">${item.day}</div>`);
        }
    });
};


function queryStringToJSON(queryString) {
    if (queryString.indexOf('?') > -1) {
        queryString = queryString.split('?')[1];
    }
    var pairs = queryString.split('&');
    var result = {};
    pairs.forEach(function (pair) {
        pair = pair.split('=');
        result[pair[0]] = decodeURIComponent(pair[1] || '');
    });
    return result;
}


function setEvents(events) {
    events_days = events;
}

function openCalendarLink(elem) {
    let date = $(elem).data('date');

    $('#dt_ag').val(date);

    console.log(elem);
    $('#form_agendamento2').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    calendarDate = new Date();
    //$('#filter_ag_anamnese,#filter_ag_medicos,#filter_ag_especialidades').off('change');
    let selectedYear = new Date().getFullYear();
    let monthSelect = new Date().getMonth() + 1;

    //document.getElementById('calendar').dataset.year = selectedYear;

    let query = queryStringToJSON(window.location.search);

    $('input[name="filter_ag"]').on('change', function () {
        if ($(this).is(':checked')) {
            $('#select2-filter_ag_select-container').text(this.dataset.title);
            $('select[name="select_filter"]').removeAttr('disabled');
            $('select[name="select_filter"]').trigger('click').select2('open');
        }

        $('#filter_ag_anamnese').on('change', function () {
            if ($(this).is(':checked')) {
                $('#select2-filter_ag_select-container').text(this.dataset.title);
                $('#filter_ag_select').select2('open');
            }
        });


        $('#filter_ag_especialidades').on('change', function () {
            if ($(this).is(':checked')) {
                $('#select2-filter_ag_select-container').text(this.dataset.title);
                $('#filter_ag_select').select2('open');
            }
        });



        let key = $('input[name="filter_ag"]:checked').val() ?? '';
        let val = $('#filter_ag_select').val();


        let uri = `/form/agenda.medico.php?key=${key}&value=${val}`;
        if (val == null) {
            uri = `/form/agenda.medico.php?key=${key}`;
        }

        fetch(uri).then((resp) => resp.json()).then((resp) => {
            let events = [];
            for (let k in resp) {
                events.push(resp[k]);
            }


            renderCalendar(new Date(selectedYear, monthSelect));
            setEvents(events);
            Swal.close();
        });


        setTimeout(function () {
            $('#filter_ag_medicos').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#select2-filter_ag_select-container').text(this.dataset.title);
                    $('#filter_ag_select').select2('open');
                }
            });
        }, 2000);
    });

    //preloader('Criando Calendário...');
    if ($('#calendar').length > 0) {
        $('#filter_ag_select').attr('data-month', new Date().getMonth());
        $('#filter_ag_select').attr('data-year', new Date().getFullYear());

        renderCalendar(calendarDate, {});


        if ($('.calendar-container').length > 0) {
            preloader('Verificando Disponibilidade...');
            let uri = '/form/agenda.medico.php?key=medicos';

            fetch(uri).then((resp) => resp.json()).then((resp) => {
                let events = [];
                for (let k in resp) {
                    events.push(resp[k]);
                }


                renderCalendar(calendarDate);
                setEvents(events);

                Swal.close();
            }).catch((error) => {
                Swal.fire({
                    title: 'Atenção',
                    text: 'Ocorreu um Erro ao Carregar os Horários.',
                    icon: 'error'
                });
            });

            if ($('#filter_ag_select[data-medico]').length > 0) {
                $('#filter_ag_select[data-medico]').each(function () {
                    preloader('Verificando Disponibilidade...');
                    monthSelect = new Date().getMonth();

                    fetch(`/form/agenda.medico.php?key=medicos&value=${$(this).data('medico')}`)
                        .then((resp) => resp.json())
                        .then((resp) => {
                            let events = [];

                            for (let k in resp) {
                                events.push(resp[k]);
                            }

                            setEvents(events);
                            renderCalendar(calendarDate);
                            Swal.close();
                            $('#filter_ag_medicos').trigger('click').trigger('change');
                        }).catch((error) => {
                            Swal.fire({
                                title: 'Atenção',
                                text: 'Ocorreu um Erro ao Carregar os Horários.',
                                icon: 'error'
                            });
                        });


                    Swal.close();
                });
            } else {
                $('#filter_ag_select').off('change');
                // Initial render
                monthSelect = new Date().getMonth();
                $('#filter_ag_select').bind('change', function () {
                    preloader('Verificando Disponibilidade...');

                    let key = $('input[name="filter_ag"]:checked').val() ?? '';
                    let val = this.value;

                    if (this.value > 0 && $('input[name="filter_ag"]:checked').val() === 'medicos') {

                        fetch(`/form/medico.perfil.php?id=${this.value}`)
                            .then((resp) => resp.json())
                            .then((resp) => {
                                $('.resumo_profissional').show()
                                $('.t_nome_doutor').text(resp.medico.nome_completo);
                                $('.t_crm').text(`${resp.medico.tipo_conselho}: ${resp.medico.num_conselho}/${resp.medico.uf_conselho}`);
                                $('.especialidade_medico').text(resp.medico.esp);
                                if (resp.medico.age_min > 0 && resp.medico.age_max > 0) {
                                    $('.faixa_etaria').html(`<b>Faixa Etária: </b> ${resp.medico.age_min} Ano(s) a ${resp.medico.age_max} Ano(s)`);
                                    $('.faixa_etaria').show();
                                } else {
                                    $('.faixa_etaria').hide();
                                }
                                $('.texto_resumo_profissional').text(resp.medico.descricao);
                                $('.foto_doutor').attr('src', resp.medico.image);

                                $('.sem_medico_selecionado').fadeIn(1500);

                                let valor_online = resp.medico.valor_consulta_online.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
                                let valor_presencial = resp.medico.valor_consulta.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })


                                $('.valores_consultas').html(`<p>${valor_online} (online), R$ ${valor_presencial} (presencial) - <b>${resp.medico.duracao_atendimento}min</b></p>`);


                                if ($('.doctor-tags').find('span').length === 0) {
                                    $(resp.medico.queixas).each(function () {
                                        $('.doctor-tags').append(`<span class="doctor-tag">${this}</span>`);
                                    });
                                }

                                $('#medicos-list-group').html('');

                                $('.curriculo-area').html(resp.medico.descricao_html);
                                $('.mini-curriculo button').off('click');

                                $('.mini-curriculo button').on('click', function () {
                                    if ($('.mini-curriculo-area').css('display') !== 'flex') {
                                        $('.mini-curriculo-area').css('display', 'flex');
                                        $(this).text('ver menos ▲');
                                    } else {
                                        $('.mini-curriculo-area').css('display', 'none');
                                        $(this).text('ver mais ▼');
                                    }
                                });


                                for (let i = 0; i < resp.atrelados.length; i++) {
                                    let medico = resp.atrelados[i];


                                    $('#medicos-list-group').append(`
                                <div class="col-6 text-center medico-item p-0 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
                                    <div class="doctor-card rounded-pill ms-3 mt-5">
                                        <div class="mt-5">
                                            <img class="border border-4 border-primary rounded-circle" src="${medico.image}" alt="Profile Picture" style="z-index: 1; margin-top: -45px; margin-left: -20px;" />
                                        </div>
                                        <div class="doctor-info">
                                            <div class="py-3 pe-5 text-start">
                                                <h5 style="font-family: 'Poppins';">${medico.prefixo} ${medico.nome_completo}</h5>
                                                <p>${medico.esp}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="btn btn-light ms-5" href="/agendamento/?filter_ag=medicos&select_filter=${medico.id}&filter_key=nome_completo&filter_value=${medico.nome_completo}">AGENDAR CONSULTA</a>
                                </div> 
                            `);
                                }
                            });
                    }

                    fetch(`/form/agenda.medico.php?key=${key}&value=${val}`).then((resp) => resp.json()).then((resp) => {
                        let events = [];

                        for (let k in resp) {
                            events.push(resp[k]);
                        }


                        renderCalendar(calendarDate);
                        setEvents(events);
                        Swal.close();
                        $('input[name="filter_ag"]:checked').trigger('click');
                    }).catch((error) => {
                        Swal.fire({
                            title: 'Atenção',
                            text: 'Ocorreu um Erro ao Carregar os Horários.',
                            icon: 'error'
                        });
                    });
                });
            }
        }

        if ('filter_ag' in query && 'select_filter' in query) {
            $(`input[name="filter_ag"][value="${query['filter_ag']}"]`).trigger('click');

            var newOption = new Option(query.filter_value, query.select_filter, false, false);

            $('select[name="select_filter"]').append(newOption).trigger('change');

            let key = $('input[name="filter_ag"]:checked').val() ?? '';
            let val = query.select_filter;
            $('#filter_ag_select').trigger('change');

            fetch(`/form/agenda.medico.php?key=${key}&value=${val}`).then((resp) => resp.json()).then((resp) => {
                let events = [];

                for (let k in resp) {
                    events.push(resp[k]);
                }


                renderCalendar(new Date(new Date().getFullYear(), new Date().getMonth(), 1));
                setEvents(events);
                Swal.close();
                $('input[name="filter_ag"]:checked').trigger('click');
            }).catch((error) => {
                Swal.fire({
                    title: 'Atenção',
                    text: 'Ocorreu um Erro ao Carregar os Horários.',
                    icon: 'error'
                });
            });
        } else {

            $('input[name="filter_ag"]').on('click', function () {
                $('.calendar .day').css('filter', 'grayscale(1)');
                $('.calendar .day').css('pointer-events', 'none');

                $('#filter_ag_select').on('change', function () {
                    $('.calendar .day').css('filter', 'grayscale(0)');
                    $('.calendar .day').css('pointer-events', 'all');

                    setTimeout(function () {
                        $('#filter_ag_select').trigger('click');

                        $('input[name="filter_ag"]:checked').trigger('click');

                        //$('#filter_ag_select').select2('positionDropdown', true);
                    }, 2500);
                });
            });

        }
    }

    $('.calendar-next-btn').on('click', function () {
        calendarDate.setMonth(calendarDate.getMonth() + 1);

        renderCalendar(calendarDate);
        setEvents(events_days);
    });

    $('.calendar-prev-btn').on('click', function () {
        calendarDate.setMonth(calendarDate.getMonth() - 1);

        renderCalendar(calendarDate);
        setEvents(events_days);
    });

});