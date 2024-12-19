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
                            <label for="dataAgendamento">Data/Hora da Consulta</label>
                            <input name="dataAgendamento" type="datetime-local" class="form-control" id="dataAgendamento" required>
                        </div>
                    </div>
            
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
            </form>
        `,
        didOpen: () => {
            $.get('/forms/fetch.tb.php?tb=pacientes&key=token,nome_completo').done(function (data) {
                for (let i in data.results) {
                    let paciente = data.results[i];

                    if ('id' in paciente) {
                        $('#pacienteSelect').append(`<option value="${paciente.token}">${paciente.text}</option>`);
                    }
                }
            });

            $.get('/forms/fetch.tb.php?tb=medicos&key=valor_consulta,valor_consulta_online,nome_completo').done(function (data) {
                for (let i in data.results) {
                    let medico = data.results[i];

                    let valor_p = medico.valor_consulta;
                    let valor_o = medico.valor_consulta_online;

                    if ('id' in medico) {
                        $('#medicoSelect').append(`<option value="${medico.token}" data-valor-presencial="${valor_p}" data-valor-online="${valor_o}">${medico.text}</option>`);
                    }
                }

                $('#modalidadeSelect').on('change', function () {
                    let modalidade = $(this).find('option:selected').val();

                    if (modalidade === 'ONLINE') {
                        $('#valorConsulta').val($('#medicoSelect option:selected').data('valor-online'));
                    } else {
                        $('#valorConsulta').val($('#medicoSelect option:selected').data('valor-presencial'));
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