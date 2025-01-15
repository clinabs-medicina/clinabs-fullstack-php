$('docuement').ready(function () {
    $('#btn_newAgendamento').on('click', function () {
        newAgendamento();
    });
});


function newAgendamento() {
    Swal.fire({
        title: 'Novo Agendamento',
        allowOutsideClick: false,
        width: window.innerWidth > 1024 ? 700 : '90%',
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: 'CONFIRMAR',
        denyButtonText: 'CANCELAR',
        html: `
            <form id="newAgendamento" method="POST" action="/form/agendamento.manual.php">
                <input type="hidden" value="${$('meta[name="user-id"]').attr('content')}" name="userId">
                <input type="hidden" value="${$('meta[name="user-name"]').attr('content')}" name="userName">
                <div class="row check-row">
                    <div class="col-4">
                        <div class="form-check-group">
                            <label for="optEncaixe">Encaixe</label>
                            <input checked name="optAgendamento" type="radio" class="form-control" id="optEncaixe" value="ENCAIXE"/>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check-group">
                            <label for="optRetorno">Retorno</label>
                            <input name="optAgendamento" type="radio" class="form-control" id="optRetorno" value="RECONSULTA"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="pacienteSelect">Paciente</label>
                            <select name="pacienteSelect" class="form-select" id="pacienteSelect" required>
                                <option value="" disabled selected>Selecione uma Opção</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="medicoSelect">Médico</label>
                            <select name="medicoSelect" class="form-select" id="medicoSelect" required>
                                <option value="" disabled selected>Selecione uma Opção</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="modalidadeSelect">Modalidade</label>
                            <select name="modalidadeSelect" class="form-select" id="modalidadeSelect" disabled required>
                                <option value="" disabled selected>Selecione uma Opção</option>
                                <option value="ONLINE">ONLINE</option>
                                <option value="PRESENCIAL">PRESENCIAL</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="dataAgendamento">Data/Hora da Consulta</label>
                            <input name="dataAgendamento" type="datetime-local" class="form-control" id="dataAgendamento" required>
                            <div class="row" id="dataAgendamento2">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input name="_dataAgendamento" type="date" class="form-control" id="_dataAgendamento" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <select name="data_Agendamento" class="form-select data-gendamento" id="data_Agendamento">
                                            <option value="" disabled selected>Selecione uma Opção</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="valorConsulta">Valor da Consulta</label>
                            <input name="valorConsulta" type="text" class="form-control" id="valorConsulta" value="" required/>
                        </div>
                    </div>
            
                    <div class="col-6">
                        <div class="form-group">
                            <label for="formaPgto">Forma de Pagamento</label>
                            <select name="formaPgto" class="form-select" id="formaPgto" required>
                                <option value="" disabled selected>Selecione uma Opção</option>
                                <option value="PIX">PIX</option>
                                <option value="CREDIT_CARD">CARTÃO DE CRÉDITO/DÉBITO ( ONLINE )</option>
                                <option value="CASH">DINHEIRO ( PRESENCIAL )</option>
                                <option value="CREDIT_CARD_EXTERNAL">CARTÃO DE CRÉDITO/DÉBITO ( PRESENCIAL )</option>
                                <option value="ABONAR">ABONAR</option>
                            </select>
                        </div>
                    </div>
                </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="descricao">Observação</label>
                        <textarea name="descricao" id="descricao" value="" required placeholder="Digite uma Observação, Ex. Dr. João Autorizou o Agendamento de Emergência para o Paciente..."></textarea>
                    </div>
                </div>
            </div>
            </form>
        `,
        didOpen: () => {
            $.get('/forms/fetch.tb.php?tb=medicos&key=valor_consulta,valor_consulta_online,nome_completo').done(function (data) {
                for (let i in data.results) {
                    let medico = data.results[i];

                    let valor_p = medico.valor_consulta;
                    let valor_o = medico.valor_consulta_online;

                    if ('id' in medico) {
                        $('#medicoSelect').append(`<option value="${medico.token}" data-valor-presencial="${valor_p}" data-valor-online="${valor_o}">${medico.text}</option>`);
                    }
                }

                $('#_dataAgendamento').on('change', function () {

                    let medico_token = $('#medicoSelect').val();
                    let select_data = $(this).val();
                    let modalidade = $('#modalidadeSelect').val();

                    $.get(`/form/agenda.medico.horarios.php`, {
                        'medico_token': medico_token,
                        'data': select_data,
                        'modalidade': modalidade,
                        'only_time': true
                    }).done(function (horarios) {
                        console.log(horarios);

                        if (horarios.status === 'success') {
                            $('#data_Agendamento').html('');
                            $('#data_Agendamento').append(`<option value="" disabled selected>Selecione uma Opção</option>`);

                            for (let i = 0; i < horarios.items.length; i++) {
                                console.log(horarios.items[i]);

                                if (horarios.items[i]) {
                                    $('#data_Agendamento').append(`<option value="${horarios.items[i]}">${horarios.items[i]}</option>`);
                                }
                            }
                        } else {
                            $('#data_Agendamento').html('');
                            $('#data_Agendamento').append(`<option value="" disabled selected>Selecione uma Opção</option>`);
                        }
                    });
                });


                $('input[name="optAgendamento"]').on('change', function () {
                    const tipo_agendamento = $('input[name="optAgendamento"]:checked').val();

                    if (tipo_agendamento === 'ENCAIXE') {

                        $('#dataAgendamento').show();
                        $('#dataAgendamento2').hide();
                    } else {
                        $('#dataAgendamento2').show();
                        $('#dataAgendamento').hide();
                    }
                })

                $('#data_Agendamento').on('change', function () {
                    const dt = `${$('#_dataAgendamento').val()} ${$(this).val()}`;
                    console.log(dt);
                    $('#dataAgendamento').attr('value', dt);
                    $('#dataAgendamento').trigger('change');
                });

                $('#modalidadeSelect').on('change', function () {
                    let modalidade = $(this).find('option:selected').val();

                    const valor_consulta = $('#medicoSelect').find('option:selected').data('valor-presencial');
                    const valor_online = $('#medicoSelect').find('option:selected').data('valor-online');

                    const tipo_agendamento = $('input[name="optAgendamento"]:checked').val();

                    if (modalidade === 'ONLINE') {
                        $('#valorConsulta').val(valor_online);
                    } else {
                        $('#valorConsulta').val(valor_consulta);
                    }

                    if (tipo_agendamento !== 'ENCAIXE') {
                        $('#_dataAgendamento').trigger('change');
                    }

                });

                $('#medicoSelect').on('change', function () {
                    let modalidade = $('#modalidadeSelect').val();

                    if (modalidade == null) {
                        $('#modalidadeSelect').val('ONLINE').trigger('change');
                    }

                    $('#modalidadeSelect').removeAttr('disabled');
                });
            });


            $.get('/forms/fetch.tb.php?tb=pacientes&key=token,nome_completo').done(function (data) {
                for (let i in data.results) {
                    let paciente = data.results[i];

                    if ('id' in paciente) {
                        $('#pacienteSelect').append(`<option value="${paciente.token}">${paciente.text}</option>`);
                    }
                }
            });

            $('#formaPgto').on('change', function () {
                if (this.value == 'ABONAR') {
                    $('#valorConsulta').addClass('strike');
                } else {
                    let modalidade = $('#modalidadeSelect').find('option:selected').val();

                    if (modalidade === 'ONLINE') {
                        $('#valorConsulta').val($('#medicoSelect option:selected').data('valor-online'));
                    } else {
                        $('#valorConsulta').val($('#medicoSelect option:selected').data('valor-presencial'));
                    }

                    $('#valorConsulta').removeClass('strike');
                }
            });

            $('#newAgendamento').on('submit', function (e) {
                e.preventDefault();

                $.post('/form/agendamento.manual.php', $(this).serializeArray()).done(function (data) {
                    Swal.fire(data);
                })
            });

            $('.swal2-deny').on('click', function () {
                Swal.close();
            });

            $('select[name]').select2();

            const tipo_agendamento = $('input[name="optAgendamento"]:checked').val();

            if (tipo_agendamento === 'ENCAIXE') {

                $('#dataAgendamento').show();
                $('#dataAgendamento2').hide();
            } else {
                $('#dataAgendamento2').show();
                $('#dataAgendamento').hide();
            }
        }
    }).then(function (evt) {
        if (evt.isConfirmed) {
            const data = $('#newAgendamento').serializeArray();

            preloader('Enviando dados de agendamento...');

            $.post('/form/agendamento.manual.php', data).done(function (resp) {
                Swal.fire(resp).then(function () {
                    window.location.reload();
                });
            }).fail(function () {
                Swal.fire({
                    title: "Atenção",
                    text: "Erro ao Enviar a Solicita\xe7\xe3o, tente novamente",
                    icon: "error"
                });
            });
        }
    });
}


function auth_payment(elem, useId = false) {
    let token = $(elem).data('token');

    preloader('Carregando Ordem de Pagamento...');

    $.get('/form/auth.payment.php', { 'token': token, 'useId': useId }).done(function (resp) {
        console.log(resp);

        if (resp.status === 'success') {
            Swal.fire({
                allowOutsideClick: true,
                showConfirmButton: true,
                showDenyButton: true,
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'CONFIRMAR',
                denyButtonText: 'REJEITAR',
                cancelButtonText: 'FECHAR',
                width: 'auto',
                html: `
                <div style="text-align:left">
                    <b>Solicitante:</b> ${resp.data.solicitante}<br>
                    <b>Data da Solicitação:</b> ${resp.data.created}<br>
                    <b>Data do Agendamento:</b> ${resp.data.agendamento}<br>
                    <b>Tipo de Agendamento:</b> ${resp.data.agendamento_tipo}</br>
                    <b>Nome do Paciente:</b> ${resp.data.paciente}<br>
                    <b>Nome do Medico:</b> ${resp.data.medico}<br>
                    <b>Valor:</b> R$ ${resp.data.amount}<br>
                    <b>Forma de Pagamento:</b> ${resp.data.payment_method}<br>
                    <b>Observação:</b> ${resp.data.descricao ? resp.data.descricao : resp.data.nome}<br>
                    <br>
                </div>
            `
            }).then(function (resp) {
                if (resp.isConfirmed) {
                    preloader('Realizando Pagamento...');

                    $.post('/form/auth.payment.php', {
                        'token': token,
                        'useId': useId,
                        'status': 'CONFIRMADO',
                        'confirm': resp.isConfirmed,
                        'user_id': $('meta[name="user-id"]').attr('content'),
                        'user_name': $('meta[name="user-name"]').attr('content'),
                        'user_type': $('meta[name="user"]').attr('content')
                    }).done(function (resp) {
                        Swal.fire(resp).then(function () {
                            window.location.reload();
                        });
                    }).fail(function () {
                        Swal.fire({
                            title: "Aten\xe7\xe3o",
                            text: "Ocorreu um erro ao realizar o pagamento, tente novamente",
                            icon: "error"
                        });
                    });
                } else if (resp.isDenied) {
                    preloader('Cancelando Pagamento...');

                    $.post('/form/auth.payment.php',
                        {
                            'token': token,
                            'useId': useId,
                            'status': 'REJEITADO',
                            'confirm': resp.isConfirmed,
                            'user_id': $('meta[name="user-id"]').attr('content'),
                            'user_name': $('meta[name="user-name"]').attr('content'),
                            'user_type': $('meta[name="user"]').attr('content')
                        }).done(function (resp) {
                            Swal.fire(resp).then(function () {
                                window.location.reload();
                            });
                        }).fail(function () {
                            Swal.fire({
                                title: "Aten\xe7\xe3o",
                                text: "Ocorreu um erro ao cancelar o pagamento, tente novamente",
                                icon: "error"
                            });
                        });
                }
            });
        } else {
            Swal.fire({
                title: "Atenção",
                text: 'Serviço Indisponivel',
                icon: "warning"
            });
        }
    });
}