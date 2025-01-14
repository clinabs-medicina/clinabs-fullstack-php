$(document).ready(function () {
    //$('.img-mobile').attr('src', '/data/banners/banner-home-mobile.png');
    //$('.img-desktop').attr('src', '/data/banners/banner-home.png');


    $('.listmedic-box-dir-time').off('dblclick');
    $('.listmedic-box-dir-time').on('dblclick', function () {
        const time = $(this).data('time');
        let elem = $(this);

        if (!$(this).hasClass('active')) {
            $(this).addClass('active');
            $.get(`/forms/fetch.unidades.php?time=${time}`, {
                medico_id: $('.calendar-slide').data('id'),
                medico_token: $('.calendar-slide').data('token')
            }, function (data) {
                console.log({
                    time: time,
                    medico_id: $('.calendar-slide').data('id'),
                    medico_token: $('.calendar-slide').data('token'),
                    unidades: data.length
                });


                if (data.length === 0) {
                    $(elem).find('i.fa.fa-home').addClass('icon-disabled');

                    $(elem).find('i.fa.fa-home').css('pointer-events', 'none');
                }
            });

            $(elem).find('i.fa.fa-globe').on('click', function () {
                if (!$(this).hasClass('icon-disabled')) {
                    $(this).removeClass('icon-disabled');
                } else {
                    $(this).addClass('icon-disabled');
                }
            });

            $(elem).find('i.fa.fa-home').on('click', function () {
                if (!$(this).hasClass('icon-disabled')) {
                    $(this).removeClass('icon-disabled');
                } else {
                    $(this).addClass('icon-disabled');
                }
            });
        } else {
            $(this).removeClass('active');
            $(elem).find('i.fa.fa-home').removeClass('icon-disabled');
            $(elem).find('i.fa.fa-globe').removeClass('icon-disabled');
        }

    });

    $('.listmedic-box-dir-time i.fa.fa-gear').on('click', function () {
        let elem = $(`#${this.dataset.id}`);
        let street_id = null;
        const time = $(this).closest('.listmedic-box-dir-time').data('time');

        Swal.fire({
            title: 'Editar Horário',
            showConfirmButton: true,
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonText: 'SALVAR',
            denyButtonText: 'CANCELAR',
            html: `
                <div class="row">
                    <div class="col-12 col-md-6 row-center mod-online">
                        <div class="form-group-checkbox">
                            <input name="modalidade[]" id="modalidade_online" type="checkbox" checked="" value="ONLINE">
                            <label for="modalidade_online">ONLINE</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 row-center mod-presencial">
                        <div class="form-group-checkbox">
                            <input name="modalidade[]" id="modalidade_presencial" type="checkbox" checked="" value="PRESENCIAL">
                            <label for="modalidade_presencial">PRESENCIAL</label>
                        </div>
                    </div>
                </div>
            
                <div class="row" id="row-unidade">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="unidade_atendimento">Unidade de Atendimento</label>
                            <select name="unidade_atendimento" id="unidade_atendimento">
                                <option disabled="" selected="">Selecione uma Opção</option>
                            </select>
                        </div>
                    </div>
                </div>
            `,
            preConfirm: () => {
                $(elem).data('online', $('#modalidade_online').is(':checked'));
                $(elem).data('presencial', $('#modalidade_presencial').is(':checked'));
                $(elem).removeAttr('data-atendimento');
                $(elem).attr('data-atendimento', street_id);

                if ($('#modalidade_online').is(':checked')) {
                    $(elem).find('i.fa-globe').removeClass('icon-disabled');
                } else {
                    $(elem).find('i.fa-globe').addClass('icon-disabled');
                }


                if ($('#modalidade_presencial').is(':checked')) {
                    $(elem).find('i.fa-home').removeClass('icon-disabled');
                } else {
                    $(elem).find('i.fa-home').addClass('icon-disabled');
                }

                if ($('input#clone-items').is(':checked')) {
                    let i = $(elem).index();
                    $(elem).closest('.week-item').find('label').each(function () {
                        if ($(this).index() >= i) {
                            $(this).addClass("active");
                            $(this).data('online', $('#modalidade_online').is(':checked'));
                            $(this).data('presencial', $('#modalidade_presencial').is(':checked'));
                            $(this).removeAttr('data-atendimento');
                            $(this).attr('data-atendimento', street_id);

                            if ($('#modalidade_online').is(':checked')) {
                                $(this).find('i.fa-globe').removeClass('icon-disabled');
                            } else {
                                $(this).find('i.fa-globe').addClass('icon-disabled');
                            }

                            if ($('#modalidade_presencial').is(':checked')) {
                                $(this).find('i.fa-home').removeClass('icon-disabled');
                            } else {
                                $(this).find('i.fa-home').addClass('icon-disabled');
                            }
                        }
                    });
                }
            },
            didOpen: () => {
                $('#modalidade_presencial').prop('checked', !$(elem).find('.fa-home').hasClass('icon-disabled'));
                $('#modalidade_online').prop('checked', !$(elem).find('.fa-globe').hasClass('icon-disabled'));

                $('#modalidade_presencial').on('click', function () {
                    if ($(this).is(':checked')) {
                        $('#row-unidade').fadeIn(1000);
                    } else {
                        $('#row-unidade').fadeOut(1000);
                    }
                });

                $.get(`/forms/fetch.unidades.php?time=${time}`, {
                    medico_id: $('.calendar-slide').data('id'),
                    medico_token: $('.calendar-slide').data('token')
                }, function (data) {
                    for (let i = 0; i < data.length; i++) {
                        let unidade = data[i];
                        let option = document.createElement('option');
                        option.value = unidade.token;
                        option.dataset.tipo = unidade.tipo;
                        option.textContent = `${unidade.nome}`;
                        document.getElementById('unidade_atendimento').appendChild(option);
                    }

                    if (data.length === 0) {
                        $('#row-unidade').hide();

                        $('.mod-presencial').hide();
                    }

                    $('#unidade_atendimento').val($(elem).data('atendimento'));

                    $('select#unidade_atendimento').on('change', function () {
                        street_id = this.value;
                    });

                    $('select#unidade_atendimento').trigger('change');
                });
            }
        });
    });
});