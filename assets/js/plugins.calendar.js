// scripts.js
const calendar = document.getElementById('calendar');

const renderCalendar = (date, events = {}) => {
    document.getElementById('calendar').dataset.year = _year;

    $('.calendar-prev-btn').off('click');
    $('.calendar-next-btn').off('click');

    monthSelect = date.getMonth();
    const selectedMonth = parseInt(monthSelect);
    let months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

    const element = document.querySelector('#calendar-info');

    if (element)
        element.textContent = `${months[monthSelect]}, ${dt.getFullYear()}`;

    // Set to the first day of the month
    date.setDate(1);

    const firstDayIndex = date.getDay();
    let selectedYear = date.getFullYear();


    // Get the last day of the month
    date.setMonth(selectedMonth + 1);
    date.setDate(0);
    const lastDay = date.getDate();

    // Clear the calendar
    calendar.innerHTML = '';


    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var week = today.getDay();

    // Add day headers
    const days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
    for (let i = 0; i < days.length; i++) {
        let day = days[i];

        const dayElement = document.createElement('div');
        dayElement.classList.add('day-header');

        dayElement.textContent = day;
        calendar.appendChild(dayElement);
    }

    // Get the last day of the previous month
    const prevMonth = new Date(date.getFullYear(), selectedMonth, 0);
    const lastDayPrevMonth = prevMonth.getDate();

    // Add blank days for the previous month with actual dates
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const blankDay = document.createElement('div');
        blankDay.textContent = lastDayPrevMonth - i;
        blankDay.classList.add('day');

        let m = selectedMonth < 10 ? '0' + (selectedMonth) : selectedMonth;
        let d = lastDayPrevMonth - i < 10 ? '0' + (lastDayPrevMonth - i) : lastDayPrevMonth - i;

        blankDay.dataset.date = `${date.getFullYear()}-${m}-${d}`;

        if (events.length > 0) {
            events.forEach(event => {
                if (event === `${date.getFullYear()}-${m}-${d}`) {
                    // blankDay.classList.add('event-day');
                } else {
                    blankDay.classList.add('day-lock');
                }
            });
        }

        calendar.appendChild(blankDay);
    }



    // Add days of the current month
    for (let i = 1; i <= lastDay; i++) {
        const dayElement = document.createElement('div');
        dayElement.classList.add('day', 'current');
        dayElement.textContent = i;

        let m = selectedMonth + 1 < 10 ? '0' + (selectedMonth + 1) : selectedMonth + 1;
        let d = i < 10 ? '0' + (i) : i;

        if (`${yyyy}-${mm}-${dd}` === `${date.getFullYear()}-${m}-${d}`) {
            dayElement.classList.add('day-today');
        }

        dayElement.dataset.date = `${date.getFullYear()}-${m}-${d}`;
        dayElement.addEventListener('click', function (e) {
            $('#dt_ag').val(`${date.getFullYear()}-${m}-${d}`);
            $('form').submit();
        });

        if (events.length > 0) {
            events.forEach(event => {
                if (event === `${date.getFullYear()}-${m}-${d}`) {
                    dayElement.classList.add('event-day');
                    dayElement.classList.remove('day-lock');
                } else {
                    //dayElement.classList.add('day-lock');
                }
            });
        }

        if (i < new Date().getDate()) {
            dayElement.classList.add('day-locked');
        }

        calendar.appendChild(dayElement);
    }

    // Add blank days for the next month
    const lastDayIndex = calendar.children.length % 7;


    if (lastDayIndex !== 0) {
        for (let i = 1; i <= 7 - lastDayIndex; i++) {
            const blankDay = document.createElement('div');
            blankDay.classList.add('day', 'day-disabled');
            blankDay.textContent = i;

            let m = selectedMonth + 2 < 10 ? parseInt('0' + (selectedMonth + 2)) : selectedMonth + 2;
            let d = i < 10 ? '0' + i : i;
            blankDay.dataset.date = `${date.getFullYear()}-${m}-${d}`;

            if (events.length > 0) {
                events.forEach(event => {

                    if (event === `${date.getFullYear()}-${m}-${d}`) {
                        //blankDay.classList.add('event-day');
                    } else {
                        blankDay.classList.add('day-lock');
                    }


                });
            }

            calendar.appendChild(blankDay);
        }
    }

    const elementClk = document.querySelector('.calendar-next-btn');
    if (elementClk)
        $(elementClk).off('click');
    elementClk.addEventListener('click', () => {
        date.setMonth(selectedMonth + 1);
        let nextMonth = date.getMonth();

        if (nextMonth > 11) {
            nextMonth = 0;
        }

        monthSelect = nextMonth;

        $('#filter_ag_select').attr('data-month', monthSelect);
        $('#filter_ag_select').attr('data-year', selectedYear);

        preloader('Verificando Disponibilidade...');

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

            if (monthSelect == 0) {
                selectedYear += 1;
            }


            renderCalendar(new Date(selectedYear, monthSelect), events);
            Swal.close();
        }).catch((error) => {
            Swal.fire({
                title: 'Atenção',
                text: 'Ocorreu um Erro ao Carregar os Horários.',
                icon: 'error'
            });
        });

        $('input[name="filter_ag"]').on('change', function (e) {
            $('#select2-filter_ag_select-container').text('Selecione uma Opção');

            $('#filter_ag_select').select2('open');
        });

        return false;
    });

    const elementCalend = document.querySelector('.calendar-prev-btn');
    if (elementCalend)
        $(elementCalend).off('click');
    elementCalend.addEventListener('click', () => {
        let prevMonth = selectedMonth - 1;
        if (prevMonth < 0) {
            prevMonth = 11;
            selectedYear--;
        }

        monthSelect = prevMonth;

        $('#filter_ag_select').attr('data-month', monthSelect);
        $('#filter_ag_select').attr('data-year', selectedYear);

        preloader('Verificando Disponibilidade...');


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



            renderCalendar(new Date(selectedYear, monthSelect), events);
            Swal.close();
        }).catch((error) => {
            Swal.fire({
                title: 'Atenção',
                text: 'Ocorreu um Erro ao Carregar os Horários.',
                icon: 'error'
            });
        });

        return false;
    });

    $('.day.day-lock.event-day.current').each(function () {
        $(this).removeClass('day-lock');
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

document.addEventListener('DOMContentLoaded', function () {
    //$('#filter_ag_anamnese,#filter_ag_medicos,#filter_ag_especialidades').off('change');
    let date = new Date();

    let selectedYear = new Date().getFullYear();

    document.getElementById('calendar').dataset.year = selectedYear;

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

            renderCalendar(new Date(selectedYear, monthSelect), events);
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

        renderCalendar(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()), {});


        if ($('.calendar-container').length > 0) {
            preloader('Verificando Disponibilidade...');
            let uri = '/form/agenda.medico.php?key=medicos';

            fetch(uri).then((resp) => resp.json()).then((resp) => {
                let events = [];
                for (let k in resp) {
                    events.push(resp[k]);
                }

                renderCalendar(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()), events);

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

                            renderCalendar(new Date(new Date().getFullYear(), new Date().getMonth(), 1), events);
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

                        renderCalendar(new Date(new Date().getFullYear(), new Date().getMonth(), 1), events);
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

                renderCalendar(new Date(new Date().getFullYear(), new Date().getMonth(), 1), events);
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

    $('.calendar-prev-btn').off('click');
    $('.calendar-next-btn').off('click');
});