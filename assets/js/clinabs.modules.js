function invoke_payment_reminder(elem) {
    let tk = $(elem).data('token');

    Swal.fire({
        title: "Atenção",
        text: "Deseja Enviar Lembrete de Pagamento para o Paciente e Notificação de Agendamento para o Médico?",
        icon: 'question',
        allowOutSideClick: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: 'SIM',
        denyButtonText: 'NÃO'
    }).then(function (evt) {
        if (evt.isConfirmed) {
            preloader('Enviando Notificação.');
            $.get('/form/wa-notify-without-payment.php', { token: tk }).done(function (data) {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Notificação Enviada com Sucesso' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro ao Enviar Notificação.' });
                }
            })
        }
    })
}


function caixa_recebimento_exec(payment_id, payment_value, client_name) {
    payment_value = mask_money(payment_value);

    return Swal.fire({
        title: 'Receber Pagamento',
        width: '600px',
        allowOutSideClick: false,
        html: `
            <div class="container-row  row-gap-1">
            <div class="row">
                <div class="col-12">
                <h5>PACIENTE: ${client_name}</h5>
                </div>
            </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group c-flex">
                            <label for="payment_method">Forma de Recebimento</label>
                            <select name="payment_method" id="payment_method" name="form-control">
                                <option disabled selected>Selecione uma Opção</option>
                                <option value="CREDIT_CARD">Cartão de Crédito</option>
                                <option value="CREDIT_CARD_CASH" data-id="pay-cc-m">Cartão de Crédito + Dinheiro</option>
                                <option value="DEBIT_CARD">Cartão de débito</option>
                                <option value="DEBIT_CARD_CASH" data-id="pay-cd-m">Cartão de débito + Dinheiro</option>
                                <option value="DEBIT_CARD_CREDIT" data-id="pay-cccd-m">Cartão de débito + Cartão de Crédito</option>
                                <option value="RECEIVED_IN_CASH">Dinheiro/Presencial</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group c-flex">
                            <label for="payment_id">Autorização do Pagamento</label>
                            <input name="payment_id" class="form-control" id="payment_id">
                        </div>
                    </div>
                </div>


                <div class="row payment-item hide" id="pay-cc-m">
                        <div class="col-6">
                            <div class="form-group c-flex">
                                <label for="pay-cc-m-v1">Valor no Cartão de Crédito</label>
                                <input name="cartao_credito" class="form-control money-format" id="pay-cc-m-v1">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group c-flex">
                                <label for="pay-cc-m-v2">Valor em Dinheiro</label>
                                <input name="dinheiro" class="form-control money-format" id="pay-cc-m-v2">
                            </div>
                        </div>
                    </div>

                    <div class="row payment-item hide" id="pay-cd-m">
                        <div class="col-6">
                            <div class="form-group c-flex">
                                <label for="pay-cd-m-v1">Valor no Cartão de Débito</label>
                                <input name="cartao_debito" class="form-control money-format" id="pay-cd-m-v1">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group c-flex">
                                <label for="pay-cd-m-v2">Valor em Dinheiro</label>
                                <input name="dinheiro" class="form-control money-format" id="pay-cd-m-v2">
                            </div>
                        </div>
                    </div>

                    <div class="row payment-item hide" id="pay-cccd-m">
                        <div class="col-6">
                            <div class="form-group c-flex">
                                <label for="pay-cccd-m-v1">Valor no Cartão de Crédito</label>
                                <input name="cartao_credito" class="form-control money-format" id="pay-cccd-m-v1">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group c-flex">
                                <label for="pay-cccd-m-v2">Valor no Cartão de Débito</label>
                                <input name="cartao_debito" class="form-control money-format" id="pay-cccd-m-v2">
                            </div>
                        </div>
                    </div>

                <div class="row">
                    <div class="col-4">
                        <div class="form-group c-flex">
                            <label for="valor_cobrado">Valor Cobrado</label>
                            <input disabled name="valor_cobrado" class="form-control text-center money-format" value="${payment_value}" id="valor_cobrado">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group c-flex">
                            <label for="valor_recebido">Valor Recebido</label>
                            <input name="valor_recebido" class="form-control text-center money-format" value="0,00" id="valor_recebido">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group c-flex">
                            <label for="troco">Troco</label>
                            <input name="troco" class="form-control text-center money-format" disabled value="R$ 0,00" id="troco">
                        </div>
                    </div>
                </div>
            </div>
            `,
        didOpen: function () {
            $('input.money-format').on('keyup', function () {
                this.value = mask_money(this.value ?? 0);
            });


            $('#payment_method').on('change', function () {
                $(".payment-item").addClass("hide");
                if (this.value != 'CREDIT_CARD' && this.value != 'DEBIT_CARD' && this.value != 'RECEIVED_IN_CASH') {
                    $(`#${this.options[this.selectedIndex].dataset.id
                        }`).removeClass("hide");
                    $('.container-row').find('input').each(function () {
                        $(this).removeAttr('required');
                    });

                    $(`#${this.options[this.selectedIndex].dataset.id
                        } input`).each(function () {
                            $(this).attr('required', 'required');
                        });

                    $(`#${this.options[this.selectedIndex].dataset.id
                        } input`).on('keyup', function () {
                            let vr = 0;

                            $('.form-control.money-format[required]').each(function () {
                                vr += parseFloat(`${this.value
                                    }`.replace(/\D/g, "")) ?? 0;
                            });

                            let troco = (vr - parseFloat($('#valor_cobrado').val().replace(/\D/g, "")));

                            $('#valor_recebido').val(mask_money(vr ?? 0));
                            $('#troco').val(mask_money(troco ?? 0));
                        });
                } else { }
            });

            $('#valor_recebido').on('keyup', function () {
                $('#troco').val(mask_money(($('#valor_cobrado').val().replace(/\D/g, "") - $(this).val().replace(/\D/g, ""))));
            });
        },
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: 'CONFIRMAR',
        denyButtonText: 'CANCELAR',
        allowoutSideClick: false,
        preConfirm: function (evt) {
            let inputs = $('.container-row').find('input[required]');
            let payments = {};

            payments['method'] = $('#payment_method').val();
            $(inputs).each(function () {
                payments[this.name] = parseFloat(this.value.replace(/\D/g, ""));
            });

            payments['troco'] = ($('#valor_cobrado').val().replace(/\D/g, "") - $('#troco').val().replace(/\D/g, ""));

            const payload = {
                'payment_id': payment_id,
                'payment_value': parseFloat($('#valor_cobrado').val().replace(/\D/g, "")),
                'identification': $('#payment_id').val(),
                'method': $('#payment_method').val(),
                'paid': $('#valor_recebido').val().replace(/\D/g, ""),
                'payments': payments
            };

            preloader('Processando Pagamento...');
            $.post('/forms/confirmar.pagamento.externo.php', payload).done(function (result) {
                Swal.fire(result).then(function () {
                    if (result.staus === 'success') {
                        window.location.reload();
                    }
                });
            }).fail(function (exception) { });

            return false;
        }
    }).then(function (modal) {
        if (modal.isConfirmed) {
            let inputs = $('.container-row').find('input[required]');
            let payments = {};

            payments['method'] = $('#payment_method').val();
            $(inputs).each(function () {
                payments[this.name] = parseFloat(this.value.replace(/\D/g, ""));
            });

            payments['troco'] = $('#troco').val().replace(/\D/g, "");

            preloader('Processando pagamento...');

            /*
                            $.post('/forms/confirmar.pagamento.externo.php', {
                                'payment_id': payment_id,
                                'payment_value': parseFloat($('#valor_cobrado').val().replace(/\D/g, "")),
                                'identification': $('#payment_id').val(),
                                'method': $('#payment_method').val(),
                                'paid': $('#valor_recebido').val().replace(/\D/g, ""),
                                'payments': payments
                            })
                            .done(function(result) {
                                Swal.fire(result).then(function(evt) {
                                    if(result.staus === 'success') {
                                        window.location.reload();
                                    }
                                });
                            }).fail(function(exception){
                          
                            });
            */
        }
    });
}


function mask_money(money) {
    let v = parseFloat(`${money}`.replace(/\D/g, ""));

    return (v / 100).toLocaleString('pt-br', {
        style: 'currency',
        currency: 'BRL'
    });
}


function get_payment_info(elem) {
    let id = $(elem).data('id');

    preloader('Obtendo Informações do Pagamento...');


    $.get('/form/payment.info.php', { payment_id: id }).done(function (resp) {
        if (response.success == 'success') {
            Swal.fire({
                title: 'Informações do Pagamento', html: `
                    <p><b>Forma de Pagamento:</b> ${resp.data.method
                    }</p>
                    <p><b>Forma de Pagamento:</b> ${resp.data.method
                    }</p>
                `})
        }
    });
}


$('document').ready(function () {
    $('#google-sync-calendar').on('click', function () {
        let token = $(this).data('token');

        Swal.fire({
            title: 'Atenção',
            width: 600,
            html: `
                <h4><strong>Autorização para Sincronização com a Agenda do Google</strong></h4>
                <p>
                    Para facilitar a gestão de seus compromissos e melhorar sua experiência, solicitamos sua autorização para sincronizar os dados da sua agenda com o Google Calendar. 
                    Ao permitir esta sincronização, seus eventos, compromissos e outras informações relevantes poderão ser transferidos e armazenados em sua conta Google.
                </p>
                <p><strong>Por favor, leia as seguintes informações antes de continuar:</strong></p>
                <ul>
                    <li>Os dados sincronizados podem incluir títulos de eventos, descrições, horários e participantes.</li>
                    <li>As informações serão sincronizadas automaticamente e poderão ser acessadas por meio de qualquer dispositivo vinculado à sua conta Google.</li>
                    <li>por motivos de segurança a sua agenda é sincronizada somente se você aceitar nesta caixa.</li>
                </ul>
            `,
            icon: 'warning',
            showConfirmButton: true,
            showDenyButton: true,
            confirmButtonText: 'Aceitar',
            denyButtonText: 'Recusar',
            allowOutSideClick: false
        }).then(function (evt) {
            if (evt.isConfirmed) {
                preloader('Sincronizando...');

                fetch('/api/gcalendar', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (!response.ok) {
                        Swal.fire({ title: 'Atenção', icon: 'danger', text: response.status });
                    }

                    return response.json();
                }).then(data => {
                    Swal.fire(data);
                }).catch(error => {
                    Swal.fire({ title: 'Atenção', icon: 'danger', text: error });
                });

            }
        })
    });

    setInterval(async function () {
        new Promise((resolve, reject) => {
            $.get('/forms/session.sync.php', {
                token: $('meta[name="user-id"]').attr('content'),
                user: $('meta[name="user"]').attr('content') + 'S'
            }).done(() => { }).always(function () { });
        });
    }, 10000);


    setInterval(async function () {
        new Promise((resolve, reject) => {
            $.get('/forms/session.sync.php?fetch=all').done(function (data) {
                $(data).each(function () {
                    $(`#USER_${this.id
                        }`).attr('data-online', this.session_online);
                    $(`#session_${this.id
                        }`).text(this.duration);

                    resolve(data);
                });
            });
        })
    }, 20000);


    setInterval(async function () {
        new Promise((resolve, reject) => {
            $('#tableAgendamento > tbody > tr').each(function () {
                let meetName = $(this).data('room');

                let roomName = meetName.replace('/', '');
                let roomId = $(this).attr('id');

                $.get('/forms/whereby.sync.php', {
                    'roomName': roomName,
                    'roomId': roomId
                }).done(function (data) {
                    $(`#${roomId}`).find('.paciente-nome').attr('data-online', data.paciente_online);
                    $(`#${roomId}`).find('.medico-nome').attr('data-online', data.medico_online);
                }).fail(function (data) { }).always(function () {
                    resolve('ok');
                });
            });
        });
    }, 30000);
});


function validarAlteracaoAgendamento(data, agendamento, intervalo, agendamento_anterior, proximo_agendamento) {
    const data_agendada = new Date(data).getTime();
    const data_agendamento = new Date(agendamento).getTime();
    const ag_anterior = new Date(agendamento_anterior).getTime();
    const ag_proximo = new Date(proximo_agendamento).getTime();

    const duracao_consulta = (intervalo * 60);

    const diff = (data_agendamento - data_agendada) / 1000;
    const diff_anterior = (data_agendamento - ag_anterior) / 1000;
    const diff_proximo = (data_agendamento - ag_proximo) / 1000;

    if (diff < duracao_consulta) {
        Swal.fire({ title: 'Atenção!', icon: 'warning', text: `Você Está Agendando esta Consulta para um intervalo menor que ${intervalo} minutos!` });
    } else if (diff_anterior < duracao_consulta) {
        Swal.fire({ title: 'Atenção!', icon: 'warning', text: `Você Está Agendando esta Consulta para um intervalo menor que ${intervalo} minutos, ao agendamento anterior!` });
    } else if (diff_proximo < duracao_consulta) {
        Swal.fire({ title: 'Atenção!', icon: 'warning', text: `Você Está Agendando esta Consulta para um intervalo menor que ${intervalo} minutos, ao próximo agendamento!` });
    }
}
