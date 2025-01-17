$('document').ready(function () {
    if (localStorage.getItem('') !== null && $('.swal-textarea')) {
        $('.swal-textarea').val(window.atob(localStorage.setItem('.swal-textarea')));
    }

    $('.swal-textarea').on('keyup', function () {
        localStorage.setItem('.swal-textarea', window.btoa(this.value));
    });
    /*
        setInterval(() => {
            $('.week-time.week-schedule.listmedic-box-dir-time').each(function () {
                const min_time = parseInt($(this).data('atendimento-min').replace(':', ''));
                const max_time = parseInt($(this).data('atendimento-max').replace(':', ''));
    
                const time = parseInt($(this).data('time').replace(':', ''));
    
                if (time >= min_time && time <= max_time) {
                    $(this).find('i.fa.fa-home').hide();
                } else {
                    $(this).find('i.fa.fa-home').show();
                }
            });
        }, 500);
    
        */
});

function toast(
    title,
    icon = "success",
    beforeEvent = function () { },
    afterEvent = function () { },
    timer = 3000
) {
    return Swal.fire({
        toast: true,

        icon: icon,

        title: title,

        animation: true,

        position: "top-right",

        showConfirmButton: false,

        timer: timer,

        timerProgressBar: true,

        didOpen: beforeEvent,

        didClose: afterEvent,
    });
}

function preloader(txt = "Processando...") {
    return Swal.fire({
        title: "",
        html: `<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><strong>${txt}</strong></div>`,
        width: "auto",
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: function () {
            $(".swal2-container").addClass("no-entries");
        },
    });
}

function parseXmlToJson(xml) {
    const json = {};

    for (const res of xml.matchAll(
        /(?:<(\w*)(?:\s[^>]*)*>)((?:(?!<\1).)*)(?:<\/\1>)|<(\w*)(?:\s*)*\/>/gm
    )) {
        const key = res[1] || res[3];

        const value = res[2] && parseXmlToJson(res[2]);

        json[key] = (value && Object.keys(value).length ? value : res[2]) || null;
    }

    return json;
}

function deleteItem(item) {
    $(`${item}`).fadeOut(1500, function () {
        $(`${item}`).remove();
    });
}

function changeSVGColor(elem, color) {
    var svg = elem.contentDocument;

    var elements = svg.getElementsByClassName("primaryColor");

    for (var i = 0; i < elements.length; i++) elements[i].style.fill = color;
}

function bytesToMegaBytes(bytes) {
    return bytes / (1024 * 1024);
}

function deleteUser(elem) {
    let id = $(elem).data("token");

    Swal.fire({
        title: "Atenção",

        text: "Deseja Excluir este Usurio?",

        icon: "question",

        allowOutsideClick: false,

        showConfirmButton: true,

        showDenyButton: true,

        confirmButtonText: "SIM",

        denyButtonText: "NO",
    }).then(function (result) {
        if (result.isConfirmed) {
            $.post(`/forms/deleteFuncionario.php?token=${id}`, function (data) {
                if (data.icon === "success") {
                    toast(data.text, data.icon, function () {
                        $(`#${id}`).fadeOut(700);
                    });
                }
            });
        }
    });
}

function editUser(elem) {
    let id = $(elem).data("token");

    $(`#${id}`)
        .find("td[editable]")

        .each(function () {
            $(this).attr("data-value", $(this).text());

            $(this).text("");

            $(this).append(
                `<input name="" value="${$(this).data("value")}" style="width: 100%">`
            );
        });
}

async function task(func) {
    return await new Promise((resolve) => {
        resolve(func());
    });
}

function googleTranslateElementInit() { }

function add_presc() {
    if (
        $("textarea").filter(function () {
            return this.value == "";
        }).length == 0
    ) {
        $("#prescricao_medica").append(`<fieldset class="presc-editor">
           <textarea id="prescricao" name="prescricao[]" height="64px" placeholder="Nova Prescrição"></textarea>
           <div class="presc-btns">
               <button type="button" onclick="add_presc()" class="btn-action" title="Editar"><img src="/assets/images/ico-add.svg" height="30px;margin: 0 5px"></button>
               <button type="button" onclick="$(this).parent().parent().remove()" class="btn-action" title="Deletar"><img src="/assets/images/ico-delete.svg" height="30px;margin: 0 5px"></button>
           </div>
       </fieldset>`);
    } else {
        Swal.fire({
            title: "Atenção",
            text: "Preencha os Campos de Prescrição que est vazios!",
            icon: "warning",
        });
    }
}

function removeOptions(selectElement) {
    var i,
        L = selectElement.options.length - 1;
    for (i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

function chargeAction(elem) {
    let charge_id = $(elem).data("charge");
    let order_id = $(elem).data("order");
    let tr_item = $(`#${order_id}`);
    let action = $(elem).data("action");

    switch (action) {
        case "cancel": {
            Swal.fire({
                title: "Atenção",
                text: "Deseja Cancelar e Estornar Esta Transao?",
                icon: "question",
                allowOutsideClick: false,
                showConfirmButton: true,
                showDenyButton: true,
                confirmButtonText: "SIM",
                denyButtonText: "NO",
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.get(
                        `/forms/financeiro.transaction.actions.php?action=${action}&charge_id=${charge_id}&order_id=${order_id}`,
                        function (data) {
                            if (data.status === "success") {
                                toast(data.text, data.status, function () {
                                    $(tr_item).remove();
                                });
                            } else {
                                Swal.fire({
                                    title: "Atenção",
                                    icon: data.status,
                                    text: data.text,
                                });
                            }
                        }
                    );
                }
            });

            break;
        }
    }
}

function action_btn_form(btn) {
    let token = $(btn).data("token");
    let action = $(btn).data("action");
    preloader();
    $.get(`/formularios/financeiro.actions.php?action=${action}&token=${token}`)
        .done(function (data) {
            toast(data.text, data.status);

            $(`#${token}`).find(".td-status").text(data.result);

            switch (data.result) {
                case "CANCELADO": {
                    $(`#${token}`)
                        .find('button[data-action="payment-cancel"]')
                        .prop("disabled", true);
                    $(`#${token}`)
                        .find('button[data-action="payment-accept"]')
                        .prop("disabled", true);
                    break;
                }

                case "AGUARDANDO PAGAMENTO": {
                    $(`#${token}`)
                        .find('button[data-action="payment-cancel"]')
                        .prop("disabled", false);
                    $(`#${token}`)
                        .find('button[data-action="payment-accept"]')
                        .prop("disabled", false);
                    break;
                }

                case "PAGO": {
                    $(`#${token}`)
                        .find('button[data-action="payment-cancel"]')
                        .prop("disabled", false);
                    $(`#${token}`)
                        .find('button[data-action="payment-accept"]')
                        .prop("disabled", true);
                    break;
                }

                case "CANCELAMENTO PENDENTE": {
                    $(`#${token}`)
                        .find('button[data-action="payment-undo"]')
                        .prop("disabled", false);
                    $(`#${token}`)
                        .find('button[data-action="payment-accept"]')
                        .prop("disabled", false);
                    break;
                }
            }
        })
        .fail(function (error) {
            toast("Falha ao Atualizar", "error");
        });
}

function action_btn_form_agendamento(btn, h = null) {
    let token = $(btn).data("token");
    let action = $(btn).data("act");
    preloader();
    if (
        action !== "agenda-meet" &&
        action !== "agenda-edit" &&
        action !== "send-meet-link" &&
        action != "agenda-delete-item"
    ) {
        $.get(`/formularios/agenda.actions.php?action=${action}&token=${token}`)
            .done(function (data) {
                toast(data.text, data.status);

                $(`#${token}`).find(".td-status").text(data.result);

                switch (data.result) {
                    case "CANCELADO": {
                        $(`#${token}`)
                            .find('button[data-action="agenda-cancel"]')
                            .prop("disabled", true);
                        $(`#${token}`)
                            .find('button[data-action="agenda-accept"]')
                            .prop("disabled", true);
                        break;
                    }

                    case "AGUARDANDO PAGAMENTO": {
                        $(`#${token}`)
                            .find('button[data-action="agenda-cancel"]')
                            .prop("disabled", false);
                        $(`#${token}`)
                            .find('button[data-action="agenda-accept"]')
                            .prop("disabled", true);
                        break;
                    }

                    case "AGENDADO": {
                        $(`#${token}`)
                            .find('button[data-action="agenda-cancel"]')
                            .prop("disabled", false);
                        $(`#${token}`)
                            .find('button[data-action="agenda-accept"]')
                            .prop("disabled", false);
                        break;
                    }

                    case "EM CONSULTA": {
                        $(`#${token}`)
                            .find('button[data-action="agenda-cancel"]')
                            .prop("disabled", false);
                        $(`#${token}`)
                            .find('button[data-action="agenda-accept"]')
                            .prop("disabled", false);
                        $(`#${token}`)
                            .find('button[data-action="agenda-accept"]')
                            .attr("data-action", "agenda-finish");

                        $(`#${token}`)
                            .find('button[data-act="agenda-meet"]')
                            .removeAttr("disabled");

                        $(`#${token}`)
                            .find('button[data-act="send-meet-link"]')
                            .before(
                                `<button title="Editar Prescrição" class="btn-action" onclick="action_btn_form_agendamento(this, true)" data-token="${token}" data-act="agenda-edit"><img src="/assets/images/ico-edit.svg" height="28px"></button>`
                            );
                        $(`#${token}`).find('button[data-act="send-meet-link"]').remove();

                        let dt = getDateTime();
                        let doc1 = document.createElement("p");
                        let doc2 = document.createElement("small");
                        doc2.setAttribute("data-time", dt);

                        doc1.appendChild(doc2);

                        $(`#${token}`).find(".td-status").append(doc1);

                        setInterval(function () {
                            let date = countUp(dt);
                            doc2.innerText = date;
                        }, 30000);


                        start_meet(data.link, token);
                        break;
                    }

                    case "CANCELAMENTO PENDENTE": {
                        $(`#${token}`)
                            .find('button[data-action="agenda-cancel"]')
                            .prop("disabled", false);
                        $(`#${token}`)
                            .find('button[data-action="agenda-accept"]')
                            .prop("disabled", false);
                        break;
                    }

                    case "EFETIVADO": {
                        $(`#${token}`)
                            .find('button[data-action="agenda-cancel"]')
                            .prop("disabled", true);
                        $(`#${token}`)
                            .find('button[data-action="agenda-finish"]')
                            .prop("disabled", true);

                        break;
                    }
                }
            })
            .fail(function (error) {
                toast("Falha ao Atualizar", "error");
            });
    } else {
        switch (action) {
            case "agenda-meet": {
                Swal.close();
                window.open($(btn).data("room"), "meet_url");
                break;
            }

            case "agenda-edit": {
                window.location = `/agenda/prescricao/${token}`;
                Swal.close();
                break;
            }
            case "send-meet-link": {
                let tk = $(btn).attr("data-meet");

                $.get(`/forms/send_meet_link.php?token=${tk}`).done(function (data) {
                    Swal.fire(data);
                });

                break;
            }

            case "agenda-delete-item": {
                Swal.fire({
                    title: "Atenção",
                    icon: "question",
                    showConfirmButton: true,
                    showDenyButton: true,
                    confirmButtonText: "SIM",
                    denyButtonText: "NÃO",
                    html: `Deseja Cancelar este Agendamento?<p><small style="color: red">A Consulta <b>#${token}</b> será removida do Sistema<p></small>`,
                }).then(function (e) {
                    if (e.isConfirmed) {
                        preloader("Processando...");

                        $.get(
                            `/formularios/agenda.actions.php?action=${action}&token=${token}`
                        ).done(function (evt) {
                            Swal.fire(evt).then(function () {
                                window.location.reload();
                            });
                        });
                    }
                });
            }
        }
    }
}

function action_btn_pedidos(btn) {
    let token = $(btn).data("token");
    let action = $(btn).data("action");
    preloader();
    $.get(`/formularios/pedidos.actions.php?action=${action}&token=${token}`)
        .done(function (data) {
            toast(data.text, data.status);

            $(`#${token}`).find(".td-status").text(data.result);

            switch (data.result) {
                case "CANCELADO": {
                    $(`#${token}`)
                        .find('button[data-action="order-cancel"]')
                        .prop("disabled", true);
                    $(`#${token}`)
                        .find('button[data-action="order-accept"]')
                        .prop("disabled", true);
                    break;
                }

                case "AGUARDANDO PAGAMENTO": {
                    $(`#${token}`)
                        .find('button[data-action="order-cancel"]')
                        .prop("disabled", false);
                    $(`#${token}`)
                        .find('button[data-action="order-accept"]')
                        .prop("disabled", false);
                    break;
                }

                case "PAGO": {
                    prop;
                    $(`#${token}`)
                        .find('button[data-action="order-cancel"]')
                        .prop("disabled", false);
                    $(`#${token}`)
                        .find('button[data-action="order-accept"]')
                        .prop("disabled", true);
                    break;
                }

                case "CANCELAMENTO PENDENTE": {
                    $(`#${token}`)
                        .find('button[data-action="order-cancel"]')
                        .prop("disabled", true);
                    $(`#${token}`)
                        .find('button[data-action="order-accept"]')
                        .prop("disabled", true);
                    break;
                }
            }
        })
        .fail(function (error) {
            toast("Falha ao Atualizar", "error");
        });
}

function disconnect_wa(elem) {
    $(elem).text("Desconectando...");

    $.get("/perfil/wa-disconnect.php?disconnect=true").done(function (data) {
        $(elem).text("Desconectando...");
    });
}

function countUp(dt) {
    const d1 = new Date(dt).getTime();
    const d2 = Date.now();

    var ticks = (d2 - d1) / 1000;

    var sec_num = Math.floor(ticks);
    var hours = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - hours * 3600) / 60);
    var seconds = sec_num - hours * 3600 - minutes * 60;

    if (hours < 10) {
        hours = "0" + hours;
    }
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }

    return hours + ":" + minutes + ":" + seconds;
}

function toBinaryStr(str) {
    const encoder = new TextEncoder();
    // 1: split the UTF-16 string into an array of bytes
    const charCodes = encoder.encode(str);
    // 2: concatenate byte data to create a binary string
    return String.fromCharCode(...charCodes);
}

function newPrescFunc() {
    let select_html = `<div class="flex-col"><p>
     <table width="100%" data-list="none">
        <tr class="ap-1">
        <td><fieldset><legend>Selecionar um Produto</legend>
        <select id="produto_sa"></select></fieldset></td>
        <td width="180px"><fieldset><legend>Frascos</legend><input id="produto_frascos" type="number" min="1" max="100" value="1" data-arrows="false" placeholder="1"></fieldset></td>
        </tr>
     </table>
     </div></p>`;
    select_html += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%; height: 400px" rows="10">
    
    </textarea>
    </div>
          <div class="swal2-checks">
              <label for="prescricao_mod"><input type="radio" name="presc_mod" id="prescricao_mod" checked value="prescricao"> Prescrição de Medicamentos</label>
              <label for="acompanhamento_mod"><input type="radio" name="presc_mod" id="acompanhamento_mod" value="acompanhamento"> Acompanhamento Médico</label>
          </div>
          <div class="ld-modal"><img src="/assets/images/loading.gif" alt="Loading">
          <input type="hidden" id="product_ref" name="product_ref" value="PRODUTOS">
          </div>`;

    Swal.fire({
        title: "Nova Prescrição",
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: function () {
            let form_acompanhamento = '';
            let posologia_receita = '';

            let fist_run = true;

            tinymce.remove();

            tinymce
                .init({
                    selector: ".tiny-mce",
                    plugins: "",
                    autosave_restore_when_empty: false,
                    toolbar:
                        "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
                    tinycomments_mode: "embedded",
                    tinycomments_author: $('metas[name="user-name"]').attr('content'),
                    setup: function (editor) {
                        const savedContent = localStorage.getItem('tinymce-content-presc');
                        if (savedContent) {
                            editor.on('init', () => {
                                editor.setContent(savedContent);
                                $('span.tox-statusbar__branding').remove();
                            });
                        }


                        editor.on('input', () => {
                            const content = editor.getContent();
                            localStorage.setItem('tinymce-content-presc', content);
                        });
                    },
                })
                .then(function () {
                    $('div[role="application"]').attr(
                        "style",
                        "visibility: hidden; width: 100%; height: 202px;"
                    );

                    $("#produto_sa").bind("change", function () {
                        $("#product_ref").val(
                            $("#produto_sa option:selected").data("ref")
                        );
                        $("#produto_sa").select2("close");

                        if (this.value === "_new") {
                            newProductNotAssociated();
                        } else {
                            $("#produto_frascos").focus();
                            $("#produto_sa").trigger("change");
                        }
                    });

                    $(".swal2-actions").show();
                    $(".ld-modal").hide();
                });

            $.get('/form/medico.perfil.php?id=' + $('#tablePrescricao').data('medico')).done(function (data) {
                form_acompanhamento = data.medico.form_acompanhamento;
                posologia_receita = data.medico.posologia_receita;

                if (tinyMCE.activeEditor.getContent().length == 0) {
                    tinyMCE.activeEditor.setContent(posologia_receita, "user");
                }
            });

            $(".swal2-actions").hide();
            $('input[name="presc_mod"]').on("change", function () {
                $("#prescricao_mod").removeAttr("checked");
                $("#acompanhamento_mod").removeAttr("checked");

                $(this).attr("checked", "checked");

                if (this.value === "acompanhamento") {
                    $('.swal2-title').text('Novo Acompanhamento');
                    $(".swal2-html-container table").hide();

                    if (fist_run) {
                        tinyMCE.activeEditor.setContent(form_acompanhamento, "user");
                        fist_run = false;
                    }
                } else {
                    $('.swal2-title').text('Nova Prescrição');
                    $(".swal2-html-container table").show();
                }
            });

            if ($("#tablePrescricao").data("append") === "true" || $("#tablePrescricao").data("append") === 1 || $("#tablePrescricao").data("append") === true) {
                $("#produto_sa").append(
                    `<option value="_new">Criar um Novo Produto não Associado</option>`
                );

                $("#produto_sa").append(`<option value="_none" disabled> </option>`);
                $.post(
                    `/forms/fetch.tb.php?tb=MEDICAMENTOS&key=nome,unidade_medida,conteudo,tipo_conteudo,medico_token,id`
                ).done(function (medicamentos) {
                    $.each(medicamentos.results, function (i) {
                        let prod = medicamentos.results[i];
                        let medico_token = $('#tablePrescricao').data('medico');

                        if (medico_token === prod.medico_token) {
                            $("#produto_sa").append(
                                `<option value="${prod.id
                                }" data-ref="MEDICAMENTOS">${prod.nome.toUpperCase()} - ${prod.conteudo
                                } ${prod.unidade_medida}</option>`
                            );
                        }
                    });
                });
            }

            $("#produto_sa").append(`<option value="_none" disabled> </option>`);
            $("#produto_sa").append(
                `<option value="0" selected disabled>Selecionar um Produto</option>`
            );

            $.get(`/forms/fetch.tb.php?tb=PRODUTOS&key=nome,status`).done(function (
                produto
            ) {
                $.each(produto.results, function (i) {
                    let prod = produto.results[i];
                    if (prod.text === "ATIVO") {
                        $("#produto_sa").append(
                            `<option value="${prod.id
                            }" data-ref="PRODUTOS">${prod.nome.toUpperCase()}</option>`
                        );
                    }
                });

                $("#produto_sa").select2();

            });
        },
        preConfirm: function () {

            if ($('input[name="presc_mod"]:checked').val() === "prescricao") {
                if (
                    $("#produto_frascos").val() >= 1 &&
                    $("#produto_sa").val() !== null
                ) {
                    $(".swal2-confirm").text("Salvando...");
                    let data = {
                        produto: $("#produto_sa").val(),
                        produto_nome: $("#produto_sa").children("option:selected").text(),
                        frascos: $("#produto_frascos").val(),
                        reference: $("#product_ref").val(),
                        prescricao: window.btoa(
                            toBinaryStr(tinyMCE.activeEditor.getContent())
                        ),
                        agenda_token: $("#tablePrescricao").data("token"),
                        user_token: $('meta[name="user-id"]').attr("content"),
                        medico_token: $("#tablePrescricao").data("medico"),
                        paciente_token: $("#tablePrescricao").data("user"),
                    };

                    $.ajax({
                        type: "POST",
                        url: "/formularios/prescricao.add.php",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            $(".swal2-confirm").text("Salvar");
                            if (response.status === "success") {
                                let rows = $("#tablePrescricao").find("tbody").find("tr");

                                let ts = new Date();
                                let date_time = `${ts.getDate() < 10 ? "0" + ts.getDate() : ts.getDate()
                                    }/${ts.getMonth() < 10
                                        ? "0" + (ts.getMonth() + 1)
                                        : ts.getMonth() + 1
                                    }/${ts.getFullYear()}  ${ts.getHours()}:${ts.getMinutes() < 9 ? "0" + ts.getMinutes() : ts.getMinutes()
                                    }`;

                                let row = `<tr>
                                 <td>${rows.length + 1}</td>
                                 <td>${date_time}</td>
                                 <td>${tinyMCE.activeEditor.getContent()}</td>
                                 <td>${response.result.produto_nome}</td>
                                 <td>${response.result.frascos}</td>
                                 <td class="td-act">
                                    <div class="btns-act">
                                       <div class="btns-table">
                                          <button type="button" title="Cancelar Prescrição" class="btn-action" onclick="action_btn_presc(this)" data-token="" data-action="presc-delete">
                                             <img src="/assets/images/ico-delete.svg" height="28px">
                                          </button>
                                          <button type="button" title="Editar Prescrição" class="btn-action" oclick="action_btn_presc(this)" data-token="" data-action="presc-edit">
                                             <img src="/assets/images/ico-edit.svg" height="28px">
                                          </button>
                                       </div>
                                    </div>
                                 </td>
                              </tr>`;

                                $(".dataTables_empty").parent().remove();
                                $("#tablePrescricao").find("tbody").append(row);
                                localStorage.removeItem('tinymce-content-presc');
                                window.location.reload();
                            }
                        },
                    });

                    return false;
                } else {
                    if ($("#produto_sa").val() === null) {
                        $("#produto_sa").trigger("click");
                    } else {
                        $("#produto_frascos").focus();
                    }

                    return false;
                }
            } else {
                if (tinyMCE.activeEditor.getContent().length > 0) {
                    let data = {
                        periodo: 0,
                        semana: 0,
                        prescricao: window.btoa(
                            toBinaryStr(tinyMCE.activeEditor.getContent())
                        ),
                        funcionario_token: $('meta[name="user-id"]').attr("content"),
                        paciente_token: $("#tablePrescricao").data("user"),
                    };

                    $.ajax({
                        type: "POST",
                        url: "/formularios/acompanhamento.add.php",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            $(".swal2-confirm").text("Salvar");
                            if (response.status === "success") {
                                Swal.close();

                                window.location.reload();
                            } else {
                                Swal.fire({
                                    title: 'Atenção',
                                    text: 'Ocorreu um Erro ao Salvar.',
                                    icon: 'danger'
                                });
                            }
                        },
                    });

                    return true;
                } else {
                    $("textarea").focus();

                    return false;
                }
            }
        },
    }).then(function (result) {
        if (result.isConfirmed) {
        }
    });
}

function addPrescFunc2(show_acompanhamento = true, text = "Nova Observação", anexo = false) {
    let select_html = `
      <div class="wb-box">
      
      <div class="container">
      <div class="flex-col"><p>
     <table width="100%" data-list="none" class="dt">
        <tr${show_acompanhamento ? "" : ' style="display: none"'}>
        <td><fieldset><legend>Selecionar um Periodo</legend>
        <select id="periodo_item">
          <option value="1">1 Acompanhamento</option>
          <option value="2">2 Acompanhamento</option>
          <option value="3">3 Acompanhamento</option>
          <option value="4">4 Acompanhamento</option>
          <option value="5">5 Acompanhamento</option>
        </select>
        </fieldset></td>
        <td width="256px">
        <fieldset>
        <legend>Próximo Acompanhamento</legend>
        <input type="date" name="proximo_acompanhamento" id="proximo_acompanhamento">
        </fieldset></td>
        </tr>
        
        <tr>
          <td>
          <fieldset>
              <legend>Anexo</legend>
              <label for="anexo-acompanhamento" class="label-input" id="lb-anexo-acompanhamento">Escolher Arquivo</label>
              <input data-value="" type="file" id="anexo-acompanhamento" style="display: none">
          </fieldset>
          </td>
          <td>
              <fieldset id="fs-acompanhamento">
                  <legend>Tipo de Anexo</legend>
                  <select id="tipo_anexo">
                      <option value="" selected disabled>Selecione um Tipo</option>
                      <option value="RECEITA">Receita</option>
                      <option value="EXAME">Exame</option>
                      <option value="LAUDOME">Laudo</option>
                  </select>
              </fieldset>
          </td>
        </tr>
  
        <tr>
          <td colspan="2"${show_acompanhamento ? "" : ' style="display: none"'}>
              <fieldset>
                <legend>Título do Acompanhamento</legend>
                <input type="text" placeholder="Ex. Acompanhamento pós-cirurgico. (max. 25 caracteres)" name="titulo_acompanhamento" id="titulo_acompanhamento" maxlength="50">
              </fieldset>
          </td>
          <td></td>
        </tr>
     </table>
     </div></p>
  
     `;
    select_html += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4"></textarea></div></div>`;
    select_html += "</div>";
    Swal.fire({
        title: text,
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: function () {
            $("#anexo-acompanhamento").bind("change", function () {
                $("#lb-anexo-acompanhamento").text("Processando arquivo...");
                $("#fs-acompanhamento").removeClass("disabled");

                let xhr = new XMLHttpRequest();
                xhr.overrideMimeType("application/json");
                xhr.responseType = "json";

                let fd = new FormData();
                fd.append("file-doc", this.files[0], this.files[0].name);
                fd.append('tipo_anexo', $('#tipo_anexo').val());
                xhr.overrideMimeType = "application/json";
                xhr.onreadystatechange = function () {

                    if (xhr.readyState === 4 && xhr.status === 200) {
                        $("#lb-anexo-acompanhamento").text(xhr.response.filename);
                        $("#lb-anexo-acompanhamento").before(
                            `<input name="anexo_doc" type="hidden" value="${xhr.response.filename}">`
                        );
                    } else {
                        //$('#lb-anexo-acompanhamento').text('Falha ao Processar');
                    }
                };

                xhr.open("POST", "/form/doc.presc.php", true);
                xhr.send(fd);
            });

            tinymce
                .init({
                    selector: ".tiny-mce",
                    plugins: "",
                    autosave_restore_when_empty: true,
                    toolbar:
                        "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
                    tinycomments_mode: "embedded",
                    tinycomments_author: "",
                })
                .then(function () {
                    $('span.tox-statusbar__branding').remove();
                    $('div[role="application"]').attr(
                        "style",
                        "visibility: hidden; width: 100%; height: 202px;"
                    );

                    $("#produto_sa").bind("change", function () {
                        $("#produto_frascos").focus();
                        $("#produto_sa").trigger("change");
                    });
                });
        },
        preConfirm: function () {
            $(".swal2-confirm").text("Salvando...");

            let data = {
                periodo: $("#periodo_item").val(),
                proximo_acompanhamento: $("#proximo_acompanhamento").val(),
                prescricao: window.btoa(toBinaryStr(tinyMCE.activeEditor.getContent())),
                funcionario_token: $('meta[name="user-id"]').attr("content"),
                paciente_token:
                    $(".toolbar-rtl").data("user") ??
                    $("#tablePrescricao").data("user"),
                anexo_doc: $('input[name="anexo_doc"]').val(),
                anexo_tipo:
                    $("#tipo_anexo").val() == ""
                        ? "ACOMPANHAMENTO"
                        : $("#tipo_anexo").val(),
                titulo_acompanhamento: $("#titulo_acompanhamento").val(),
            };

            $.ajax({
                type: "GET",
                url: "/formularios/acompanhamento.add.php",
                data: data,
                dataType: "json",
                success: function (response) {
                    $(".swal2-confirm").text("Salvar");
                    if (response.status === "success") {
                        let rows = $("#tablePrescricao").find("tbody").find("tr");

                        let ts = new Date();
                        let date_time = `${ts.getDate() < 10 ? "0" + ts.getDate() : ts.getDate()
                            }/${ts.getMonth() < 10 ? "0" + (ts.getMonth() + 1) : ts.getMonth() + 1
                            }/${ts.getFullYear()}  ${ts.getHours()}:${ts.getMinutes() < 9 ? "0" + ts.getMinutes() : ts.getMinutes()
                            }`;

                        let row = `<tr>
                 <td>${rows.length + 1}</td>
                 <td>${date_time}</td>
                 <td>${tinyMCE.activeEditor.getContent()}</td>
                 <td>${response.result.produto_nome}</td>
                 <td>${response.result.frascos}</td>
                 <td class="td-act">
                    <div class="btns-act">
                       <div class="btns-table">
                          <button type="button" title="Cancelar Prescrição" class="btn-action" onclick="action_btn_presc(this)" data-token="" data-action="presc-delete">
                             <img src="/assets/images/ico-delete.svg" height="28px">
                          </button>
                          <button type="button" title="Editar Prescrição" class="btn-action" oclick="action_btn_presc(this)" data-token="" data-action="presc-edit">
                             <img src="/assets/images/ico-edit.svg" height="28px">
                          </button>
                       </div>
                    </div>
                 </td>
              </tr>`;

                        $(".dataTables_empty").parent().remove();
                        $("#tablePrescricao").find("tbody").append(row);

                        Swal.close();

                        window.location.reload();
                    }
                },
            });

            return false;
        },
    }).then(function (result) {
        tinymce.remove();
    });
}



function addPrescFunc3(show_acompanhamento = false, text = "Novo Anexo") {
    let select_html = `
      <div class="wb-box">
      
      <div class="container">
      <div class="flex-col"><p>
     <table width="100%" data-list="none" class="dt">
        <tr>
          <td>
          <fieldset>
              <legend>Anexo</legend>
              <label for="anexo-acompanhamento" class="label-input" id="lb-anexo-acompanhamento">Escolher Arquivo</label>
              <input data-value="" type="file" id="anexo-acompanhamento" style="display: none">
          </fieldset>
          </td>
          <td>
              <fieldset id="fs-acompanhamento">
                  <legend>Tipo de Anexo</legend>
                  <select id="tipo_anexo">
                      <option value="" selected disabled>Selecione um Tipo</option>
                      <option value="RECEITA">Receita</option>
                      <option value="EXAME">Exame</option>
                      <option value="LAUDO">Laudo</option>
                  </select>
              </fieldset>
          </td>
        </tr>
     </table>
     </div></p>
  
     `;
    select_html += `<input class="form-control" id="anexo_desc" placeholder="Descrição do Documento, max. 50 caracteres" maxlength="50"></div></div>`;
    select_html += "</div>";
    Swal.fire({
        title: text,
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: function () {
            $("#anexo-acompanhamento").bind("change", function () {
                $("#lb-anexo-acompanhamento").text("Processando arquivo...");
                $("#fs-acompanhamento").removeClass("disabled");

                let xhr = new XMLHttpRequest();
                xhr.overrideMimeType("application/json");
                xhr.responseType = "json";

                let fd = new FormData();
                fd.append("file-doc", this.files[0], this.files[0].name);
                xhr.overrideMimeType = "application/json";
                xhr.onreadystatechange = function () {

                    if (xhr.readyState === 4 && xhr.status === 200) {
                        $("#lb-anexo-acompanhamento").text(xhr.response.filename);
                        $("#lb-anexo-acompanhamento").before(
                            `<input name="anexo_doc" type="hidden" value="${xhr.response.filename}">`
                        );
                    } else {
                        $('#lb-anexo-acompanhamento').text('Falha ao Processar');
                    }
                };

                xhr.open("POST", "/form/doc.presc.php", true);
                xhr.send(fd);
            });
        },
        preConfirm: function () {
            $(".swal2-confirm").text("Salvando...");

            const reader = new FileReader(); // Create a new FileReader instance

            reader.onload = function (e) {
                const base64String = e.target.result;

                let data = {
                    details: window.btoa($('#anexo_desc').val()),
                    paciente_token: $('meta[name="user-id"]').attr("content"),
                    anexo_doc: $('input[name="anexo_doc"]').val(),
                    anexo_contents: base64String,
                    anexo_tipo: $("#tipo_anexo").val(),
                    titulo_acompanhamento: $("#titulo_acompanhamento").val(),
                };

                $.post('/form/user.anexo.php', data).then(function (result) {
                    Swal.fire(result).then(function (a) {
                        window.location.reload();
                    });
                });
            };

            reader.readAsDataURL(document.getElementById("anexo-acompanhamento").files[0]);
            return false;
        },
    }).then(function (result) {
        tinymce.remove();
    });
}


function editPrescFunc(elem, presc = false) {
    let product = $(elem).closest("tr").data("product");
    let frascos = $(elem).closest("tr").data("frascos");
    let prescricao = $(elem).closest("tr").data("prescricao");
    let produto_ref = $(elem).closest("tr").data("ref");

    let id = $(elem).closest("tr").data("id");
    let medico = $("#tablePrescricao").data("medico");

    let row = $(elem).closest("tr");

    let select_html = `<div class="flex-col"><p>
     <table width="100%" data-list="none">
        <tr class="ap-1">
        <td><fieldset><legend>Selecionar um Produto</legend><select id="produto_sa"></select></fieldset></td>
        <td width="100px"><fieldset><legend>Frascos</legend><input id="produto_frascos" type="number" min="1" max="100" value="1" data-arrows="false" placeholder="1"></fieldset></td>
        </tr>
     </table>
     </div></p>`;
    select_html += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4">${window.atob(
        prescricao
    )}</textarea></div>
          <div class="swal2-checks">
              <label for="prescricao_mod"><input type="radio" name="presc_mod" id="prescricao_mod" checked value="prescricao"> Prescrição de Médicamentos</label>
              <label for="acompanhamento_mod"><input type="radio" name="presc_mod" id="acompanhamento_mod" value="acompanhamento"> Acompanhamento Médico</label>
          </div>
          <input type="hidden" id="product_ref" name="product_ref" value="PRODUTOS">
          `;

    Swal.fire({
        title: presc ? "Nova Prescrição" : "Editar Prescrição",
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        allowOutSideClick: false,
        denyButtonText: "Cancelar",
        didOpen: function () {
            if ($("#tablePrescricao").data("append") === "true") {
                $("#produto_sa").append(
                    `<option value="_new">Criar um Novo Produto não Associado</option>`
                );
                $("#produto_sa").append(`<option value="_none" disabled> </option>`);
                $.get(
                    `/forms/fetch.tb.php?tb=MEDICAMENTOS&key=nome,unidade_medida,conteudo,tipo_conteudo,id`
                ).done(function (medicamentos) {
                    $.each(medicamentos.results, function (i) {
                        let prod = medicamentos.results[i];
                        $("#produto_sa").append(
                            `<option ${produto_ref === "MEDICAMENTOS" && product === prod.id
                                ? "selected"
                                : ""
                            } value="${prod.id
                            }" data-ref="MEDICAMENTOS">${prod.nome.toUpperCase()} - ${prod.text
                            }${prod.unidade_medida} - ${prod.tipo_conteudo}</option>`
                        );
                    });
                });

                $("#produto_sa").append(`<option value="_none" disabled> </option>`);
            }
            $("#produto_sa").append(
                `<option value="0" selected disabled>Selecionar um Produto</option>`
            );

            $.get(`/forms/fetch.tb.php?tb=PRODUTOS&key=nome,status`).done(function (
                produto
            ) {
                $.each(produto.results, function (i) {
                    let prod = produto.results[i];
                    if (prod.text === "ATIVO") {
                        $("#produto_sa").append(
                            `<option ${produto_ref === "PRODUTOS" && product === prod.id
                                ? "selected"
                                : ""
                            } value="${prod.id
                            }" data-ref="PRODUTOS">${prod.nome.toUpperCase()}</option>`
                        );
                    }
                });

                $("#produto_sa").select2();

                tinymce
                    .init({
                        selector: ".tiny-mce",
                        plugins: "",
                        autosave_restore_when_empty: true,
                        toolbar:
                            "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
                        tinycomments_mode: "embedded",
                        tinycomments_author: "",
                    })
                    .then(function () {
                        $('span.tox-statusbar__branding').remove();
                        $('div[role="application"]').attr(
                            "style",
                            "visibility: hidden; width: 100%; height: 202px;"
                        );

                        $("#produto_sa").bind("change", function () {
                            $("#product_ref").val(
                                $("#produto_sa option:selected").data("ref")
                            );
                            $("#produto_sa").select2("close");

                            if (this.value === "_new") {
                                newProductNotAssociated();
                            } else {
                                $("#produto_frascos").focus();
                                $("#produto_sa").trigger("change");
                            }
                        });

                        $(".swal2-actions").show();
                        $(".ld-modal").hide();
                    });
            });
        },
        preConfirm: function () {
            $(".swal2-confirm").text("Salvando...");

            let data = {
                produto: $("#produto_sa").val(),
                produto_nome: $("#produto_sa").children("option:selected").text(),
                frascos: $("#produto_frascos").val(),
                prescricao: window.btoa(toBinaryStr(tinyMCE.activeEditor.getContent())),
                agenda_token: $("#tablePrescricao").data("token"),
                user_token: $("#tablePrescricao").data("user"),
                medico_token: medico,
                id: id,
            };

            $.ajax({
                type: "GET",
                url: "/formularios/prescricao.edit.php",
                data: data,
                dataType: "json",
                success: function (response) {
                    $(".swal2-confirm").text("Salvar");
                    if (response.status === "success") {
                        $(row)
                            .find('td[data-set="prescricao"]')
                            .text(
                                tinyMCE.activeEditor.getContent({
                                    format: "text",
                                })
                            );
                        $(row).find('td[data-set="produto_nome"]').text(data.produto_nome);
                        $(row).find('td[data-set="frascos"]').text(data.frascos);

                        $(row).data("data-product", data.produto);
                        $(row).data("data-frascos", data.frascos);
                        $(row).data("data-prescricao", data.prescricao);

                        Swal.close();

                        window.location.reload();
                    }
                },
            });

            return false;
        },
    }).then(function (result) {
        tinymce.remove();
    });
}

function newTrackItem() {
    Swal.fire({
        title: "Novo Evento de Rastreio",
        html: `<div class="form-group"><label for="event_selector">Selecione um Evento</label><select class="swal2-select" name="event_selector" id="event_selector"></select></div>
          <div class="form-group"><label for="obs_adicional">Informaes Adicionais</label><input maxlength="40" class="swal2-input" name="obs_adicional" id="obs_adicional" placeholder="Ex. Cd de Rastreio PR59R98989BR"></div>`,
        allowOutsideClick: false,
        width: "650px",
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        didOpen: function () {
            $.get("/forms/fetch.tb.php?tb=RASTREIO_OPT&key=nome", function (data) {
                for (let i = 0; i < data.results.length; i++) {
                    let item = data.results[i];

                    if (item !== null) {
                        if ($('img[src="/assets/images/ico-doc-large.svg"]').length >= 1) {
                            if (
                                item.text === "Documento Auditado" ||
                                item.text === "Em Validaço Documental"
                            ) {
                                $(".swal2-select").append(
                                    `<option value="${item.id}" data-option="${i}">${item.text}</option>`
                                );
                            } else {
                                $(".swal2-select").append(
                                    `<option disabled value="${item.id}" data-option="${i}" style="color: red !important">${item.text}</option>`
                                );
                            }
                        } else {
                            $(".swal2-select").append(
                                `<option value="${item.id}" data-option="${i}">${item.text}</option>`
                            );
                        }
                    }
                }
            });
        },
    }).then(function (evt) {
        if (evt.isConfirmed) {
            $("#event_selector").trigger("change");
            let txt = $("#event_selector option:selected").text();
            let obs = $("#obs_adicional").val();
            let rows = $("#tableRastreio").find("tbody").find("tr").length + 1;

            let today = new Date().toLocaleString();

            let pl = {
                id: $('meta[name="track_id"]').attr("content"),
                token: $('meta[name="user-id"]').attr("content"),
                event: $("#event_selector option:selected").val(),
                desc: txt,
                dt: today,
                obs: obs,
                pid: $("#tableRastreio").data("token"),
                user: $("#tableRastreio").data("user"),
            };

            preloader();

            $.get("/forms/reastreio.add.php", pl, function (result) {
                toast(result.text, result.status);

                if (result.status === "success") {
                    $("#tableRastreio").find("tbody").append(`<tr>
                              <td>${rows}</td>
                              <td>${txt}${obs.length > 0 ? ` (${obs})` : ``}</td>
                              <td>${result.data}</td>
                              <td>${$('meta[name="user-name"]').attr(
                        "content"
                    )}</td>
                          </tr>`);
                }
            });
        }
    });
}

function defEndPadrao(item) {
    let token = $(item).data("token");
    let tb = $(item).data("table");

    let elem = $(`#${token}`);

    $(".street-item.selected").find(".default-street").text("");

    let json_data1 = $(".street-item.selected")
        .find('input[name="enderecos[]"]')
        .val()
        .replace("'isDefault':1,", "'isDefault':0,");

    $(".street-item.selected")
        .find('input[name="enderecos[]"]')
        .val()
        .replace("'isDefault': 1,", "'isDefault':0,");

    $("#atendimento_padrao").val(`{'token':'${token}','table':'${tb}'}`);

    $(".street-item.selected").removeClass("selected");

    $(elem).find(".default-street").text("(Padro)");

    $(elem).addClass("selected");

    let json_data2 = $(elem)
        .find('input[name="enderecos[]"]')
        .val()
        .replace("'isDefault':0,", "'isDefault':1,");
    $(elem).find(".input-data").val(json_data2);
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + "=; Max-Age=-99999999;";
}

function wa_send_message(btn) {
    let number = `55${$(btn).data("wa")}`;

    Swal.fire({
        title: "Enviar Mensagem",
        input: "url",
        html: `<textarea id="wa-msg" width="100%" height="128px" placeholder="Digite sua Mensagem aqui...</textarea>`,
        showCondirmButton: true,
        showCancelButton: false,
        showDenyButton: true,
        confirmButtonText: "Enviar",
        denyButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: function () {
            $("#swal2-textarea").show();
            $("#swal2-textarea").attr("placeholder", "Digite sua Mensagem aqui.");
            $(".swal2-input").attr("placeholder", "Insira um Link aqui.");

            $(".swal2-input").focus();
        },
    }).then(function (result) {
        if (result.isConfirmed) {
            let uri = `/forms/send.msg.php?number=55${$(btn).data("wa")}&msg=${$(
                "#swal2-textarea"
            ).val()}&linkUrl=${$(".swal2-input").val()}`;

            preloader();
            $.get(uri).done(function (data) {
                toast(data.text, data.status);
            });
        }
    });
}

function action_btn_presc(btn) {
    let id = $(btn).closest("tr").data("id");

    switch ($(btn).data("action")) {
        case "presc-delete": {
            Swal.fire({
                title: "Atenção",
                text: "Deseja Realmente Excluir este Registro?",
                icon: "question",
                showConfirmButton: true,
                showDenyButton: true,
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonText: "SIM",
                denyButtonText: "NO",
                didOpen: function () {
                    $(btn).closest("tr").css("filter", "grayscale(1)");
                },
            }).then(function (resp) {
                if (resp.isConfirmed) {
                    $.get(`/formularios/prescricao.delete.php?id=${id}`).done(function (
                        data
                    ) {
                        toast(data.text, data.status);

                        if (data.status === "success") {
                            $(btn).closest("tr").remove();
                        }
                    });
                } else {
                    $(btn).closest("tr").css("filter", "grayscale(0)");
                }
            });

            break;
        }

        case "presc-edit": {
            editPrescFunc(btn);
            break;
        }
    }
}

function printPrescFuncSigned() {
    preloader();
    let id = $("#formPrescricao").data("id");
    let file = $("#tablePrescricao.prescricao").data("file");

    let win = window.open(`/data/receitas/assinadas/${file}`);

    win.print();
    Swal.close();
}

function printPrescFunc2() {
    preloader();
    let id = $("#tablePrescricaoSR").data("token");
    let mt = $("#tablePrescricaoSR").data("medico");
    let values = [];

    $(".checkbox-item.presc-id:checked").each(function () {
        values.push(this.value);
    });

    if (values.length > 0) {
        let win = window.open(
            `/agenda/prescricao/receita/${id}?SR=true&medico_token=${mt}&items=${JSON.stringify(
                values
            )}`
        );

        win.print();
        Swal.close();
    } else {
        Swal.fire({
            title: "Atenção",
            text: "para poder Imprimir Você deve Selecionar os Medicamentos que foram prescrevidos fora da Agenda.",
            icon: "info",
            allowOutSideClick: false,
        });
    }
}

function getDateTime() {
    let date = new Date();
    return `${date.getDate()}/${date.getMonth() < 9 ? "0" + date.getMonth() : date.getMonth()
        }/${date.getFullYear()} ${date.getHours() < 9 ? "0" + date.getHours() : date.getHours()
        }:${date.getMinutes() < 9 ? "0" + date.getMinutes() : date.getMinutes()}:${date.getSeconds() < 9 ? "0" + date.getSeconds() : date.getSeconds()
        }`;
}

function unlock_upload(elem, container) {
    $(elem).removeAttr("onclick");
    $(elem).attr("onclick", "upload_docs_ped_async(this)");
    $(elem).html('<i class="fa fa-save"></i> Salvar');

    $("a.download-docs").css("pointer-events", "none");
    $("a.download-docs").css("filter", "grayscale(1)");

    $(`${container} label.anexo-item`).each(function () {
        $(this).off("click");

        $(this).append(
            `<input onchange="upload_docs_ped_async(this, '${$(this).attr(
                "for"
            )}')" type="file" accpet=".png,.jpg,.jpeg,.pdf" id="${$(this).attr(
                "for"
            )}" style="display: none">`
        );
    });
}

function upload_docs_ped_async(elem, id) {
    preloader("Enviando Documento...");
    let formData = new FormData();

    formData.append("file", elem.files[0]);
    formData.append(
        "tb",
        id === "doc_receita" ? "FARMACIA" : $("#tableProdutos").data("table")
    );
    formData.append("key", id);
    formData.append(
        "token",
        id === "doc_receita"
            ? $("#tableRastreio").data("token")
            : $("#tableProdutos").data("user")
    );

    fetch("/form/upload-docs.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                let im =
                    data.ext === "pdf"
                        ? "/assets/images/ico-doc-pdf.svg"
                        : "/assets/images/ico-doc-img.svg";
                $(elem).closest(".anexo-item").find("img").attr("src", im);

                if (
                    $(elem).closest(".anexo-item").find(`input[name="${id}"]`).length > 0
                ) {
                    $(elem)
                        .closest(".anexo-item")
                        .find(`input[name="${id}"]`)
                        .val(data.path);
                } else {
                    $(elem).after(
                        `<input type="hidden" name="${id}" value="${data.path}">`
                    );
                }

                $(elem).attr("disabled", true);

                Swal.close();
            } else {
                $(elem)
                    .closest(".anexo-item")
                    .find("img")
                    .attr("src", "/assets/images/ico-doc-large.svg");
                Swal.fire({
                    title: "Atenção",
                    text: data.message,
                    icon: "error",
                });
            }
        })
        .catch((error) => {
            Swal.fire({
                title: "Atenção",
                text: error,
                icon: "error",
            });
        });
}

function prescricao_window(elemn) {
    Swal.fire({
        title: "Atenção",
        text: "Prescrição no Disponvel no Momento",
        icon: "warning",
        allowOutsideClick: false,
    });
}

function newPrescFuncSR() {
    let select_html = `<div class="flex-col"><p>
     <table width="100%" data-list="none">
        <tr>
        <td><fieldset><legend>Selecionar um Produto</legend><select id="produto_sa"></select></fieldset></td>
        <td width="100px"><fieldset><legend>Frascos</legend><input id="produto_frascos" type="number" min="1" max="100" value="1" data-arrows="false" placeholder="1"></fieldset></td>
        </tr>
     </table>
     </div></p>`;
    select_html += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4"></textarea></div>
          <div class="swal2-checks">
              <label for="prescricao_mod"><input type="radio" name="presc_mod" id="prescricao_mod" checked value="prescricao"> Prescrição de Medicamentos</label>
              <label for="acompanhamento_mod"><input type="radio" name="presc_mod" id="acompanhamento_mod" value="acompanhamento"> Acompanhamento Médico</label>
          </div>`;

    Swal.fire({
        title: "Nova Prescrição",
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        didOpen: function () {
            $('input[name="presc_mod"]').on("change", function () {
                $("#prescricao_mod").removeAttr("checked");
                $("#acompanhamento_mod").removeAttr("checked");

                $(this).attr("checked", "checked");

                if (this.value === "acompanhamento") {
                    $(".swal2-html-container table").hide();
                } else {
                    $(".swal2-html-container table").show();
                }
            });

            $("#produto_sa").append(
                `<option value="" selected disabled>Selecionar um Produto</option>`
            );

            $.get(`/forms/fetch.tb.php?tb=PRODUTOS&key=id,nome,status`).done(
                function (produto) {
                    $.each(produto.results, function (i) {
                        let prod = produto.results[i];

                        if (prod.text === "ATIVO") {
                            $("#produto_sa").append(
                                `<option value="${prod.id}">${prod.nome}</option>`
                            );
                        }
                    });

                    $("#produto_sa").select2();

                    const tm = new Date().getTime();

                    tinymce.remove();

                    tinymce
                        .init({
                            language: "pt_BR",
                            selector: `#tmce_${tm}`,
                            plugins: "",
                            autosave_restore_when_empty: true,
                            toolbar:
                                "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
                            tinycomments_mode: "embedded",
                            tinycomments_author: "",
                        })
                        .then(function () {
                            $('div[role="application"]').attr(
                                "style",
                                "visibility: hidden; width: 100%; height: 202px;"
                            );

                            $("#produto_sa").bind("change", function () {
                                $("#produto_frascos").focus();
                                $("#produto_sa").trigger("change");
                            });
                        });
                }
            );
        },
        preConfirm: function () {
            if ($('input[name="presc_mod"]:checked').val() === "prescricao") {
                if (
                    $("#produto_frascos").val() >= 1 &&
                    $("#produto_sa").val() !== null
                ) {
                    $(".swal2-confirm").text("Salvando...");
                    let data = {
                        produto: $("#produto_sa").val(),
                        produto_nome: $("#produto_sa").children("option:selected").text(),
                        frascos: $("#produto_frascos").val(),
                        prescricao: window.btoa(tinyMCE.activeEditor.getContent()),
                        user_token: $('meta[name="user-id"]').attr("content"),
                        medico_token: $("#tablePrescricaoSR").data("medico"),
                        paciente_token: $("#tablePrescricaoSR").data("user"),
                    };

                    $.ajax({
                        type: "GET",
                        url: "/formularios/prescricao.add.sr.php",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            $(".swal2-confirm").text("Salvar");
                            if (response.status === "success") {
                                let rows = $("#tablePrescricaoSR").find("tbody").find("tr");

                                let ts = new Date();
                                let date_time = `${ts.getDate() < 10 ? "0" + ts.getDate() : ts.getDate()
                                    }/${ts.getMonth() < 10
                                        ? "0" + (ts.getMonth() + 1)
                                        : ts.getMonth() + 1
                                    }/${ts.getFullYear()}  ${ts.getHours()}:${ts.getMinutes() < 9 ? "0" + ts.getMinutes() : ts.getMinutes()
                                    }`;

                                let row = `<tr>
                                 <td>${rows.length + 1}</td>
                                 <td>${date_time}</td>
                                 <td>${tinyMCE.activeEditor.getContent({
                                    format: "text",
                                })}</td>
                                 <td>${response.result.produto_nome}</td>
                                 <td>${response.result.frascos}</td>
                                 <td class="td-act">
                                    <div class="btns-act">
                                       <div class="btns-table">
                                          <button type="button" title="Cancelar Prescrição" class="btn-action" onclick="action_btn_presc(this)" data-token="" data-action="presc-delete">
                                             <img src="/assets/images/ico-delete.svg" height="28px">
                                          </button>
                                          <button type="button" title="Editar Prescrição" class="btn-action" oclick="action_btn_presc(this)" data-token="" data-action="presc-edit">
                                             <img src="/assets/images/ico-edit.svg" height="28px">
                                          </button>
                                       </div>
                                    </div>
                                 </td>
                              </tr>`;

                                $(".dataTables_empty").parent().remove();
                                $("#tablePrescricaoSR").find("tbody").append(row);

                                Swal.close();

                                location.reload();
                            }
                        },
                    });

                    return false;
                } else {
                    if ($("#produto_sa").val() === null) {
                        $("#produto_sa").trigger("click");
                    } else {
                        $("#produto_frascos").focus();
                    }

                    return false;
                }
            } else {
                if (tinyMCE.activeEditor.getContent().length > 0) {
                    let data = {
                        periodo: 0,
                        semana: 0,
                        prescricao: window.btoa(tinyMCE.activeEditor.getContent()),
                        funcionario_token: $('meta[name="user-id"]').attr("content"),
                        paciente_token: $("#tablePrescricao").data("user"),
                    };

                    $.ajax({
                        type: "GET",
                        url: "/formularios/acompanhamento.add.php",
                        data: data,
                        dataType: "json",
                        success: function (response) {
                            $(".swal2-confirm").text("Salvar");
                            if (response.status === "success") {
                                Swal.close();

                                location.reload();
                            }
                        },
                    });

                    return true;
                } else {
                    $("textarea").focus();
                    return false;
                }
            }
        },
    }).then(function () {
        window.location.reload();
    });
}

function editPrescFuncSR(elem, presc = false) {
    let product = $(elem).closest("tr").data("product");
    let frascos = $(elem).closest("tr").data("frascos");
    let prescricao = $(elem).closest("tr").data("prescricao");
    let id = $(elem).closest("tr").data("id");
    let medico = $(elem).closest("tr").data("medico");

    let row = $(elem).closest("tr");

    let select_html = `<div class="flex-col"><p>
     <table width="100%" data-list="none">
        <tr>
        <td${presc == false ? ' style="display: none"' : ""
        }><fieldset><legend>Selecionar um Produto</legend><select id="produto_sa"></select></fieldset></td>
        <td width="100px"{presc == false ? ' style="display: none"':''}><fieldset><legend>Frascos</legend><input id="produto_frascos" type="text" data-arrows="false" placeholder="0"></fieldset></td>
        </tr>
     </table>
     </div></p>`;
    select_html += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4"></textarea></div>`;

    Swal.fire({
        title: presc ? "Nova Prescrição" : "Observação",
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        didOpen: function () {
            $("#produto_sa").append(
                `<option value="" selected disabled>Selecionar um Produto</option>`
            );

            $.get(`/forms/fetch.tb.php?tb=PRODUTOS&key=id,nome,status`).done(
                function (produto) {
                    $.each(produto.results, function (i) {
                        let prod = produto.results[i];

                        if (prod.text === "ATIVO") {
                            $("#produto_sa").append(
                                `<option value="${prod.id}">${prod.nome}</option>`
                            );
                        }
                    });

                    $("#produto_sa").val(product).trigger("change");
                    $("#produto_frascos").val(frascos);

                    $("#produto_sa").select2();

                    tinymce
                        .init({
                            selector: ".tiny-mce",
                            plugins: "",
                            autosave_restore_when_empty: true,
                            toolbar:
                                "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
                            tinycomments_mode: "embedded",
                            tinycomments_author: "",
                        })
                        .then(function () {
                            $('span.tox-statusbar__branding').remove();
                            $('div[role="application"]').attr(
                                "style",
                                "visibility: hidden; width: 100%; height: 202px;"
                            );

                            tinyMCE.activeEditor.setContent(window.atob(prescricao), {
                                format: "text",
                            });

                            $("#produto_sa").bind("change", function () {
                                $("#produto_frascos").focus();
                                $("#produto_sa").trigger("change");
                            });
                        });
                }
            );
        },
        preConfirm: function () {
            $(".swal2-confirm").text("Salvando...");

            let data = {
                produto: $("#produto_sa").val(),
                produto_nome: $("#produto_sa").children("option:selected").text(),
                frascos: $("#produto_frascos").val(),
                prescricao: window.btoa(tinyMCE.activeEditor.getContent()),
                agenda_token: $("#tablePrescricaoSR").data("token"),
                user_token: $("#tablePrescricaoSR").data("user"),
                medico_token: medico,
                id: id,
            };

            $.ajax({
                type: "GET",
                url: "/formularios/prescricao.edit.sr.php",
                data: data,
                dataType: "json",
                success: function (response) {
                    $(".swal2-confirm").text("Salvar");

                    if (response.status === "success") {
                        $(row)
                            .find('td[data-set="prescricao"]')
                            .text(
                                tinyMCE.activeEditor.getContent({
                                    format: "text",
                                })
                            );

                        $(row).find('td[data-set="produto_nome"]').text(data.produto_nome);
                        $(row).find('td[data-set="frascos"]').text(data.frascos);

                        $(row).data("data-product", data.produto);
                        $(row).data("data-frascos", data.frascos);
                        $(row).data("data-prescricao", data.prescricao);

                        Swal.close();

                        location.reload();
                    }
                },
            });

            return false;
        },
    }).then(function (result) {
        tinymce.remove();
    });
}

function addUnidade(elem = null) {
    Swal.fire({
        title: elem == null ? "Nova Unidade" : "Editar Unidade",
        width: "75%",
        html: `<form id="unidadeItem" style="width: 100%; padding: 0;">
                    <section class="form-grid area5">
                        <section class="form-group">
                            <label for="unidade_nome">Nome da Unidade</label>
                            <input autocomplete="off" type="text" id="unidade_nome" class="form-control" name="unidade_nome" placeholder="Digite o nome Da Unidade." />
                        </section>

                        <section class="form-group">
                            <label for="unidade_nome">Contato</label>
                            <input autocomplete="off" type="text" id="unidade_contato" class="form-control" name="unidade_contato" placeholder="Digite o Telefone Da Unidade." />
                        </section>

                        <section class="form-group">
                            <label for="unidade_image">Imagem</label>
                            <input autocomplete="off" type="file" id="unidade_image" class="form-control" name="unidade_image" accept="image/*" />
                        </section>
                    </section>

                    <section class="form-grid area13">
                        <section class="form-group">
                            <label for="unidade_cep">CEP</label> <input autocomplete="off" value="" type="text" id="unidade_cep" name="unidade_cep" class="form-control" placeholder="__.____-___" maxlength="10" required="required" />
                        </section>
                        <section class="form-group">
                            <label for="unidade_endereco">Endereo</label> <input autocomplete="off" value="" type="text" id="unidade_endereco" name="unidade_endereco" class="form-control" placeholder="Digite seu Endereo" required="required" />
                        </section>
                        <section class="form-group"><label for="unidade_numero">Número</label> <input autocomplete="off" value="" type="text" id="unidade_numero" name="unidade_numero" class="form-control" placeholder="N" required="required" /></section>
                        <section class="form-group">
                            <label for="unidade_complemento">Complemento</label> <input autocomplete="off" type="text" id="unidade_complemento" name="unidade_complemento" class="form-control" placeholder="Apto 13" value="" />
                        </section>
                    </section>

                    <section class="form-grid area5">
                        <section class="form-group">
                            <label for="unidade_cidade">Cidade</label>
                            <input autocomplete="off" value="" type="text" id="unidade_cidade" name="unidade_cidade" class="form-control" placeholder="Digite sua Cidade" required="required" />
                        </section>
                        <section class="form-group">
                            <label for="unidade_bairro">Bairro</label>
                            <input autocomplete="off" value="" type="text" id="unidade_bairro" name="unidade_bairro" class="form-control" placeholder="Digite seu Bairro" required="required" />
                        </section>
                        <section class="form-group">
                            <label for="unidade_uf">UF</label>
                            <select class="form-select form-control" id="unidade_uf" name="unidade_uf">
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Esprito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </section>
                    </section>

                    <section class="form-grid row-4">
                        <div class="form-group">
                            <label for="inicio_expediente">Inicio Atendimento</label>
                            <input autocomplete="off" type="time" name="inicio_expediente" id="inicio_expediente" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="fim_expediente">Fim Atendimento</label>
                            <input autocomplete="off" type="time" name="fim_expediente" id="fim_expediente" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="tipo_atendimento">Tipo de Atendimento</label>
                            <select class="form-select form-control" id="tipo_atendimento" name="tipo_atendimento">
                                <option value="ONLINE">ONLINE</option>
                                <option value="PRESENCIAL">PRESENCIAL</option>
                                <option value="TODOS">TODOS</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="unidade_status">Status</label>
                            <select class="form-select form-control" id="unidade_status" name="unidade_status">
                                <option value="ATIVO">ATIVO</option>
                                <option value="INATIVO">INATIVO</option>
                            </select>
                        </div>
                    </section>

                    <section class="form-grid area-full">
                        <section class="form-group">
                            <label for="medicos_combobox">Médicos</label>
                            <select class="form-control" name="unidade_medicos" id="medicos_combobox" multiple="multiple"></select>
                        </section>
                    </section>

                    <section class="form-grid area-full">
                        <label for="dias_semana">Selecione os dias da Semnana</label>
                    </section>

                    <div class="row-weeks">
                        <div class="week-item active">
                            <label for="dayofWeek_1">SEG</label>
                            <input name="dayofWeek[]" id="dayofWeek_1" type="checkbox" value="MO" checked="checked">
                        </div>

                        <div class="week-item active">
                            <label for="dayofWeek_2">TER</label>
                            <input name="dayofWeek[]" id="dayofWeek_2" type="checkbox" value="TU" checked="checked">
                        </div>

                        <div class="week-item active">
                            <label for="dayofWeek_3">QUA</label>
                            <input name="dayofWeek[]" id="dayofWeek_3" type="checkbox" value="WE" checked="checked">
                        </div>

                        <div class="week-item active">
                            <label for="dayofWeek_4">QUI</label>
                            <input name="dayofWeek[]" id="dayofWeek_4" type="checkbox" value="TH" checked="checked">
                        </div>

                        <div class="week-item active">
                            <label for="dayofWeek_5">SEX</label>
                            <input name="dayofWeek[]" id="dayofWeek_5" type="checkbox" value="FR" checked="checked">
                        </div>

                        <div class="week-item">
                            <label for="dayofWeek_6">SAB</label>
                            <input name="dayofWeek[]" id="dayofWeek_6" type="checkbox" value="SA" checked="checked">
                        </div>
                    </div>
                </form>
                `,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        allowOutsideClick: false,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        padding: "1rem",
        didOpen: function () {
            $('.row-weeks .week-item input').on('click', function () {
                if ($(this).is(':checked')) {
                    $(this).closest('.week-item').addClass('active');
                } else {
                    $(this).closest('.week-item').removeClass('active');
                }
            })
            $.get(
                "/forms/fetch.tb.php?tb=MEDICOS&key=nome_completo",
                function (medicos) {
                    $.each(medicos.results, function (i) {
                        let medico = medicos.results[i];
                        if (elem !== null) {
                            let data = JSON.parse(window.atob($(elem).attr("data-item")));
                            let items = JSON.parse(data.medicos);
                            let selected = false;

                            $(items).each(function () {
                                if (`${this}` === `${medico.id}`) {
                                    selected = true;
                                }
                            });

                            $("#medicos_combobox").append(
                                `<option value="${medico.id}"${selected ? " selected" : ""}>${medico.text
                                }</option>`
                            );
                        } else {
                            $("#medicos_combobox").append(
                                `<option value="${medico.id}">${medico.text}</option>`
                            );
                        }
                    });
                }
            );

            if (elem !== null) {
                let data = JSON.parse(window.atob($(elem).attr("data-item")));
                $("#unidade_nome").val(data.nome);
                $("#unidade_contato").val(data.contato);
                $("#unidade_cep").val(data.cep);
                $("#unidade_endereco").val(data.logradouro);
                $("#unidade_numero").val(data.numero);
                $("#unidade_complemento").val(data.complemento);
                $("#unidade_cidade").val(data.cidade);
                $("#unidade_bairro").val(data.bairro);
                $("#unidade_uf").val(data.uf);

                $('#inicio_expediente').val(data.inicio_expediente);
                $('#fim_expediente').val(data.fim_expediente);
                $('#tipo_atendimento').val(data.tipo_atendimento);
                $('#unidade_status').val(data.unidade_status);
            }

            $("#unidade_contato").mask("(00) 0000-0000", {
                placeholder: "(__) ____-____",
                clearIfNotMatch: true,
            });

            $("#medicos_combobox").select2({
                language: "pt",
                width: "resolve",
                height: "resolve",
            });

            $("#unidade_cep").mask("00.000-000", {
                placeholder: "__.____-___",
                clearIfNotMatch: true,
                onComplete: function (cep) {
                    cep = cep.replace(/\D/g, "");

                    if (cep.length == 8) {
                        $("#unidade_endereco").attr("placeholder", "Buscando dados...");
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then((response) => response.json())

                            .then((cep) => {
                                $("#unidade_endereco").val(cep.logradouro);

                                $("#unidade_cidade").val(cep.localidade);

                                $("#unidade_uf").val(cep.uf).trigger("change");

                                $("#unidade_bairro").val(cep.bairro);

                                $("#unidade_numero").focus();
                            })

                            .catch((error) => console.error("Error:", error))

                            .finally((x) =>
                                $("#unidade_endereco").attr("placeholder", "Digite Seu Endereo")
                            );
                    }
                },
            });
        },
    }).then(async function (result) {
        if (result.isConfirmed) {
            let items = $("#unidadeItem").serializeArray();

            let result = {};

            items.map(function (item) {
                result[item["name"]] = item["value"];
            });

            if (document.getElementById("unidade_image").files.length > 0) {
                let img = await getFile(
                    document.getElementById("unidade_image").files[0]
                );
                result["unidade_image"] = img["base64StringFile"];
            }

            result["unidade_medicos"] = JSON.stringify($("#medicos_combobox").val());
            await request("PUT", "/formularios/add_unidade.php", result).then(
                (data) => {
                    Swal.fire(data).then(function () {
                        window.location.reload();
                    });
                }
            );
        }
    });
}

async function request(method, url, data = {}) {
    return await fetch(url, {
        method: method,
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    }).then((response) => {
        return response.json();
    });
}

function addBlogPost(bid = null) {
    return Swal.fire({
        title: "Novo Post",
        width: "80%",
        height: "80%",
        html:
            bid == null
                ? `
      <form id="form-new-post" method="post">
          <div class="swal-row">
              <div class="swal-group">
                  <div class="swal-columns">
                      <div class="swal-column">
                          <label for="swal2-input1">Assunto</label>
                          <input class="swal-input" id="swal2-input1" placeholder"Assunto" name="subject">
                      </div>
  
                      <div class="swal-column">
                          <label for="swal2-input2">Imagem</label>
                          <input onclick="$('#swal2-input2').trigger('click')" readonly class="swal-input" id="swal2-input-file" placeholder"Image do Post">
                          <input name="image" class="file-upload" style="display: none" type="file" accept="image/png,image/jpg,image/jpeg" data-input="#swal2-input-file" id="swal2-input2" placeholder"Assunto">
                      </div>
                  </div>
              </div>
  
              <div class="swal-group">
                  <label for="swal2-input3">Descrição</label>
                  <input name="desc" class="swal-input" id="swal2-input3" placeholder"Escreva uma breve Descriço" maxlength="100">
              </div>
          </div>
          <input type="hidden" name="bid" value="${bid}">
          <input type="hidden" name="medico_token" value="${$(".blog-post").data(
                    "doctor"
                )}">
      </form>`
                : `<form id="form-new-post" method="post">
              <div class="swal-group">
                  <label for="swal2-inputfile2">Anexo</label>
                  <input onclick="$('#swal2-inputfile2').trigger('click')" readonly class="swal-input" id="swal2-input-filex" placeholder"Image do Post" data-input="#swal2-inputfile2">
                  <input name="anexo[]" class="file-upload" style="display: none" type="file" accept="image/png,image/jpg,image/jpeg, application/pdf" id="swal2-inputfile2" placeholder"Assunto" multiple  data-input="#swal2-input-filex">
              </div>
          </div>
          <br>
          <p>Conteúdo</p>
          <textarea id="post-txt" class="tinymce-blog-add-post"></textarea>
          <input type="hidden" name="bid" value="${bid}">
          <input type="hidden" name="medico_token" value="${$(".blog-post").data(
                    "doctor"
                )}">
      </form>`,
        allowOutsideClick: false,
        confirmButtonText: "POSTAR",
        cancelButtonText: "CANCELAR",
        showConfirmButton: true,
        showCancelButton: true,
        didOpen: function () {
            $('input[type="file"]').on("change", function () {
                let names = [];

                $(this.files).each(function () {
                    names.push(this.name);
                });

                $(`${this.dataset.input}`).val(names.join(", "));
            });

            tinymce.init({
                selector: ".tinymce-blog-add-post",
                height: 512,
            });
            //images_upload_handler: example_image_upload_handler
        },
    }).then(function (evt) {
        if (evt.isConfirmed) {
            formData = new FormData(document.getElementById("form-new-post"));
            if (bid !== null) {
                formData.append(
                    "content",
                    window.btoa(tinyMCE.activeEditor.getContent())
                );
            }

            formData.append(
                "medico_token",
                $('meta[name="user-id"]').attr("content")
            );

            send_form(
                "POST",
                bid == null
                    ? "/forms/blog.post.add.php"
                    : "/forms/blog.post.append.php",
                formData,
                function (resp) {
                    let response = JSON.parse(resp);
                    if (response.status === "success") {
                        toast("Salvo.", "success").then(function () {
                            window.location.reload();
                        });
                    } else {
                        toast("Erro ao Salvar.", "error");
                    }
                }
            );
        }

        tinymce.remove();
    });
}

function editarPost(elem) {
    let content = $(elem).closest(".blog-post").find(".blog-post-body").html();
    let post_id = $(elem).closest(".blog-post").attr("id");
    let post_item_id = $(elem).closest(".blog-post").attr("data-post-id");

    return Swal.fire({
        title: "Editar Post",
        width: "80%",
        height: "80%",
        html: `<textarea id="post-txt" class="tinymce-blog-edit-post">${content}</textarea>`,
        allowOutsideClick: false,
        confirmButtonText: "POSTAR",
        cancelButtonText: "CANCELAR",
        showConfirmButton: true,
        showCancelButton: true,
        didOpen: function () {
            const example_image_upload_handler = (blobInfo, progress) =>
                new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open("POST", "/forms/tmce_upload.php");
                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject({
                                message: "HTTP Error: " + xhr.status,
                                remove: true,
                            });
                            return;
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject("HTTP Error: " + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText);

                        if (!json || typeof json.location != "string") {
                            reject("Invalid JSON: " + xhr.responseText);
                            return;
                        }

                        resolve(json.location);
                    };

                    xhr.onerror = () => {
                        reject(
                            "Image upload failed due to a XHR Transport error. Code: " +
                            xhr.status
                        );
                    };

                    const formData = new FormData();
                    formData.append("file", blobInfo.blob(), blobInfo.filename());

                    xhr.send(formData);
                });

            tinymce.init({
                selector: ".tinymce-blog-edit-post",
                plugins: "image, code, media",
                images_upload_handler: example_image_upload_handler,
            });
        },
    }).then(function (evt) {
        if (evt.isConfirmed) {
            var myContent = tinymce.activeEditor.getContent();

            $(elem).closest(".blog-post").find(".blog-post-body").html(myContent);
            fetch("/forms/blog.post.update.php", {
                method: "PUT",
                headers: {
                    "Content-type": "application/json",
                },
                body: JSON.stringify({
                    id: post_id,
                    post_id: post_item_id,
                    content: window.btoa(myContent),
                }),
            })
                .then((resp) => resp.text())
                .then((resp) => { });
        }

        tinymce.remove();
    });
}

// Set a Cookie
function setCookie(cName, cValue, expDays) {
    let date = new Date();
    date.setTime(date.getTime() + expDays * 24 * 60 * 60 * 1000);
    const expires = "expires=" + date.toUTCString();
    document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
}

const send_form = async function (method, url, formData, callback) {
    Swal.fire({
        allowOutsideClick: false,
        showConfirmButton: false,
        showCancelButton: false,
        width: "auto",
        height: "auto",
        html: '<div class="swal2-preloader"><img src="/assets/images/loading.gif" title="Sending"> Enviando....</div>',
        didOpen: function () {
            let xhr = new XMLHttpRequest();

            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    Swal.close();
                    callback(xhr.response);
                }
            };

            xhr.open(method, url, true);
            xhr.send(formData);
        },
    });
};

function responderBlog(id) {
    let action = "ANSWER";
    let medico_token = $('meta[name="user-id"]').attr("content");
    let pid = $(".blog-post").attr("data-post-id");

    Swal.fire({
        title: "Responder Pergunta",
        input: "textarea",
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "ENVIAR",
        denyButtonText: "NO",
        didOpen: function () {
            $("#swal2-textarea").attr(
                "placeholder",
                action == "comment"
                    ? "Escreva aqui seu Comentrio...."
                    : "Escreva aqui sua pergunta...."
            );
        },
    }).then(function (evt) {
        if (evt.isConfirmed) {
            $.post("/forms/blog.post.answer.php", {
                post_id: pid,
                answer_ref: id,
                action: action,
                text: evt.value,
                medico_token: medico_token,
            }).done(function (resp) {
                toast(resp.text, resp.status).then(function () {
                    window.location.reload();
                });
            });
        }
    });
}

function downloadReceita(cid = null) {
    let id = $("#formPrescricao").data("id");
    let xfile = $("#tablePrescricao").data("file");

    if (cid === null) {
        cid = window.prompt("Insira o CID: ", "");
    }

    let a = document.createElement("a");

    let items = [];

    if ($('.presc-item:checked').length > 0) {
        $('.presc-item:checked').each(function () {
            items.push($(this).val());
        });
    }

    if (xfile !== null) {
        let qr = '';
        if (items.length > 0) {
            qr = `&include=${items.join(",")}`;
        } else {
            qr = '';
        }

        a.href = `/api/pdf/receita.php?token=${id}&cid=${cid}&dl=true${qr}`;
    } else {
        a.href = `/agenda/prescricao/receita/${id}&cid=${cid}`;
    }
    a.download = `RECITA-${id}.pdf`;
    a.click();
}

async function postData(url, formData, callback) {
    await fetch(url, {
        method: "POST",
        body: formData,
    })
        .then((resp) => resp.json())
        .then((resp) => {
            callback(resp);
        })
        .catch((error) => {
            callback(error);
        });
}

function update_cart() {
    Swal.fire({
        title: "Atualizando dados do Carrinho",
        text: "Aguarde.",
        imageUrl: "/assets/images/ico-cart.svg",
        imageHeight: 80,
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: function () {
            $.get("/carrinho/calcprodutos.php", function (product) {
                $("#valor-total").text(
                    Math.round(product.subtotal_produtos).toLocaleString("pt-br", {
                        style: "currency",
                        currency: "BRL",
                    })
                );

                $("#valor-total-prazo").text(
                    Math.round(product.subtotal).toLocaleString("pt-br", {
                        style: "currency",
                        currency: "BRL",
                    })
                );

                $("#valor-total-pix").text(
                    Math.round(product.total_pix).toLocaleString("pt-br", {
                        style: "currency",
                        currency: "BRL",
                    })
                );

                $('li[data-source="cart-items-count"]').attr(
                    "data-badge",
                    product.items.length
                );

                Swal.close();

                window.location.reload();
            });
        },
    });
}

let USDollar = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
});

let BRReal = new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
});

function formatMoney(valor, moeda) {
    if (moeda === "BRL") {
        return BRReal.format(valor.replace(/[^0-9]/g, "") / 100);
    } else {
        return USDollar.format(valor.replace(/[^0-9]/g, "") / 100);
    }
}

function upload_doc_ped(elem) {
    let id = $(elem).attr("id");

    var formData = new FormData();
    formData.append("doc", elem.files[0], elem.files[0].name);
    formData.append("name", elem.id);

    fetch("/forms/docs.upload.php", {
        method: "POST",
        credentials: "same-origin",
        body: formData,
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            if (result.success) {
                let im =
                    result.extension === "pdf"
                        ? "/assets/images/ico-doc-pdf.svg"
                        : "/assets/images/ico-doc-img.svg";
                $(elem).closest(".anexo-item").find("img").attr("src", im);

                if (
                    $(elem).closest(".anexo-item").find(`input[name="${id}"]`).length > 0
                ) {
                    $(elem)
                        .closest(".anexo-item")
                        .find(`input[name="${id}"]`)
                        .val(result.name);
                } else {
                    $(elem).after(
                        `<input type="hidden" name="${id}" value="${result.name}">`
                    );
                }

                $(elem).attr("disabled", true);
            }
        })
        .catch(function (error) { });
}

function serialize_ops(items) {
    return items["ops"];
}

function WaSendReceita() {
    preloader("Enviando Receita....");
    let paciente_token = $("#tablePrescricao").data("user");
    let receita_token = $("#tablePrescricao").data("file");

    $.get(
        `/form/send-receita.php?paciente_token=${paciente_token}&receita_token=${receita_token}`
    ).done(function (data) {
        Swal.fire(data);
    });
}

function getDatesInRange(startDate, endDate) {
    let dates = [];
    let currentDate = new Date(startDate);
    currentDate.setDate(currentDate.getDate());

    endDate = new Date(endDate);
    endDate.setDate(endDate.getDate() + 1);

    while (currentDate < endDate) {
        dates.push(new Date(currentDate).toISOString().split("T")[0]);
        currentDate.setDate(currentDate.getDate() + 1);
    }

    return dates;
}

function invoke_payment_reminder(payment_id) {
    return Swal.fire({
        title: "Reenviar Notificação de Pagamento",
        icon: "question",
        text: "Deseja Reenviar um Lembrete de Pagamento para Este Paciente?",
        showCondirmButton: true,
        showDenyButton: true,
        confirmButtonText: "SIM",
        denyButtonText: "NÃO",
    }).then(function (evt) {
        if (evt.isConfirmed) {
            preloader("Enviando Notificação....");
            $.get("/form/cobranca.notify.php", { token: payment_id }).done(function (
                data
            ) {
                Swal.fire(data);
            });
        }
    });
}

function tableToJson(tableId) {
    const table = document.getElementById(tableId);

    const data = [];

    const rows = table.querySelectorAll("tbody tr");

    const headers = Array.from(table.querySelectorAll("thead th")).map(
        (th) => th.dataset.name
    );

    rows.forEach((row) => {
        const cells = row.querySelectorAll("td");

        const rowData = {};
        rowData["index"] = row.dataset.index;

        cells.forEach((cell, index) => {
            if ("value" in cell.dataset) {
                rowData[headers[index]] = cell.dataset.value;
            }
        });

        data.push(rowData);
    });

    return data;
}

function removeDuplicates(array) {
    return array.filter((item, index) => array.indexOf(item) === index);
}

function filter_ag_req(input = null) {
    let result = [];

    const jsonData = tableToJson("tableAgendamento");
    const _filters =
        $("#agendamento-filters")
            .serializeArray()
            .filter((elem) => elem.value.length) ?? [];
    let filters = [];

    if (_filters.length > 0) {
        filters["data_agendamento"] = [];
        if ($("#date_start").val().length > 0 && $("#date_end").val().length > 0) {
            let dates = getDatesInRange($("#date_start").val(), $("#date_end").val());
            filters["data_agendamento"] = dates;
        } else {
            if ($("#date_start").val().length > 0) {
                filters["data_agendamento"].push($("#date_start").val());
                filters["method"] = ">=";
            } else {
                if ($("#date_end").val().length > 0) {
                    filters["data_agendamento"].push($("#date_end").val());
                    filters["method"] = "<=";
                }
            }
        }

        let filter = [];
        _filters.map((obj) => {
            if (obj.name !== "data_agendamento") {
                filter[obj.name] = obj.value;
            }
        });

        jsonData.forEach((item) => {
            if (
                Array.isArray(filters["data_agendamento"]) &&
                filters["data_agendamento"].length >= 1
            ) {
                if (filters["data_agendamento"].length > 1) {
                    if (filters["data_agendamento"].includes(item["data_agendamento"])) {
                        if (isSubset(filter, item)) {
                            result.push(item);
                        }
                    }
                } else {
                    if (
                        new Date(item["data_agendamento"]).getTime() >=
                        new Date(filters["data_agendamento"]).getTime() &&
                        filters["method"] === ">="
                    ) {
                        if (isSubset(filter, item)) {
                            result.push(item);
                        }
                    } else if (
                        new Date(item["data_agendamento"]).getTime() <=
                        new Date(filters["data_agendamento"]).getTime() &&
                        filters["method"] === "<="
                    ) {
                        if (isSubset(filter, item)) {
                            result.push(item);
                        }
                    } else {
                        if (
                            new Date(item["data_agendamento"]).getTime() ==
                            new Date(filters["data_agendamento"]).getTime()
                        ) {
                            if (isSubset(filter, item)) {
                                result.push(item);
                            }
                        }
                    }
                }
            } else {
                if (isSubset(filter, item)) {
                    if (isSubset(filter, item)) {
                        result.push(item);
                    }
                }
            }
        });

        $(`tr.ag_row`).hide();
        let rows = [];
        $(result).each(function () {
            let i = this.index;
            $(`tr.ag_row${i}`).show();
            rows.push($(`tr.ag_row${i}`));
        });
        pagination(rows);
    } else {
        $("tr").each(function () {
            $(this).show();
        });
    }
}

function isSubset(array1, array2) {
    let i = 0;

    Object.entries(array1).map(([key, val]) => {
        if (array1[key] === array2[key]) {
            i++;
        }
    });

    return i === Object.entries(array1).length;
}

function newProductNotAssociated() {
    Swal.fire({
        title: "Criar um novo Produto não Associado",
        width: "600px",
        height: "50%",
        html: `
              <div class="container-product">
                  <div class="row">
                      <div class="col-12">
                          <label for="product_name">Nome do Produto</label>
                          <input id="product_name" class="form-control" placeholder="Ex. Dipirona">
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-6">
                          <label for="product_medida">Medida</label>
                          <select id="product_medida" class="form-control">
                              <option value="ampola">Ampola</option>
                              <option value="caixa">Caixa</option>
                              <option value="frasco">Frasco</option>
                              <option value="g">G</option>
                              <option value="mg">MG</option>
                              <option value="ml">ML</opion>
                              <option value="ui">UI</option>
                               <option value="un">UN</option>
                              <option value="uso_continuo">Uso Contínuo</option>
                          </select>
                      </div>
  
                      <div class="col-6">
                          <label for="product_conteudo">Dosagem</label>
                          <input id="product_conteudo" class="form-control" placeholder="Ex. 10mg">
                      </div>
  
                      <div class="col-12"  style="display: none !important">
                          <label for="tipo_conteudo">Tipo de Conteúdo</label>
                          <select id="tipo_conteudo" class="form-control">
                              <option value="ui">UI</option>
                              <option value="frasco">Frasco</option>
                              <option value="ampola">Ampola</option>
                              <option value="caixa">Caixa</option>
                              <option value="uso_continuo">Uso Contínuo</option>
                          </select>
                      </div>
                  </div>
              </div>
          `,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: function () {
            $("#product_conteudo").bind("keyup", function (e) {
                setTimeout(function () {
                    $("#product_conteudo").val(
                        `${$("#product_conteudo")
                            .val()
                            .replace(/[^0-9]/g, "")} ${$("#product_medida").val()}`
                    );
                }, 1000);
            });

            $("#product_medida").bind("change", function () {
                $("#product_conteudo").attr(
                    "placeholder",
                    `Ex. 10 ${$("#product_medida").val()}`
                );
            });
        },
    }).then((result) => {
        if (result.isConfirmed) {
            let payload = {
                nome: $("#product_name").val().toUpperCase(),
                medico: $("table").data("medico"),
                medida: $("#product_medida").val(),
                conteudo: $("#product_conteudo")
                    .val()
                    .replace(/[^0-9]/g, ""),
                tipo_conteudo: $("#tipo_conteudo").val(),
            };

            preloader("Salvando...");

            $.post("/form/produtos.new.php", payload)
                .done(function (data) {
                    Swal.fire(data).then(() => {
                        window.location.reload();
                    });
                })
                .fail(function (data) {
                    Swal.fire(data.responseText);
                });
        }
    });
}


function editPrescFunc(elem) {
    let periodo = $(elem).data("periodo");
    let anexo = $(elem).data("anexo");
    let proximo_acompanhamento = $(elem).data("proximo");
    let anexo_tipo = $(elem).data("tipo");
    let texto = atob($(elem).data("content"));
    let titulo = $(elem).data("title");

    let select_html = `
      <div class="wb-box">
      
      <div class="container">
      <div class="flex-col"><p>
     <table width="100%" data-list="none" class="dt">
        <tr>
        <td><fieldset><legend>Selecionar um Periodo</legend>
        <select id="periodo_item">
          <option value="1"${periodo === '1' ? ' selected' : ''}>1 Acompanhamento</option>
          <option value="2"${periodo === '2' ? ' selected' : ''}>2 Acompanhamento</option>
          <option value="3"${periodo === '3' ? ' selected' : ''}>3 Acompanhamento</option>
          <option value="4"${periodo === '4' ? ' selected' : ''}>4 Acompanhamento</option>
          <option value="5"${periodo === '5' ? ' selected' : ''}>5 Acompanhamento</option>
        </select>
        </fieldset></td>
        <td width="256px">
        <fieldset>
        <legend>Próximo Acompanhamento</legend>
        <input type="date" name="proximo_acompanhamento" id="proximo_acompanhamento" value="${proximo_acompanhamento}">
        </fieldset></td>
        </tr>
  
        <tr>
          <td colspan="2">
              <fieldset>
                <legend>Título do Acompanhamento</legend>
                <input type="text" placeholder="Ex. Acompanhamento pós-cirurgico. (max. 25 caracteres)" name="titulo_acompanhamento" id="titulo_acompanhamento" maxlength="50" value="${titulo}">
              </fieldset>
          </td>
          <td></td>
        </tr>
     </table>
     </div></p>
  
     `;
    select_html += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4">${texto}</textarea></div></div>`;
    select_html += "</div>";
    Swal.fire({
        title: "Editar Acompanhamento",
        width: window.innerWidth >= 500 ? "60%" : "90%",
        height: "50%",
        html: select_html,
        showCancelButton: false,
        showConfirmButton: true,
        showDenyButton: true,
        confirmButtonText: "Salvar",
        denyButtonText: "Cancelar",
        allowOutsideClick: false,
        didOpen: function () {
            tinymce
                .init({
                    selector: ".tiny-mce",
                    plugins: "",
                    autosave_restore_when_empty: true,
                    toolbar:
                        "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
                    tinycomments_mode: "embedded",
                    tinycomments_author: "",
                })
                .then(function () {
                    $('span.tox-statusbar__branding').remove();
                    $('div[role="application"]').attr(
                        "style",
                        "visibility: hidden; width: 100%; height: 202px;"
                    );

                    $("#produto_sa").bind("change", function () {
                        $("#produto_frascos").focus();
                        $("#produto_sa").trigger("change");
                    });


                });
        },
        preConfirm: function () {
            $(".swal2-confirm").text("Salvando...");

            let data = {
                periodo: $("#periodo_item").val(),
                proximo_acompanhamento: $("#proximo_acompanhamento").val(),
                prescricao: window.btoa(toBinaryStr(tinyMCE.activeEditor.getContent())),
                funcionario_token: $('meta[name="user-id"]').attr("content"),
                paciente_token:
                    $(".toolbar-rtl").data("user") ??
                    $("#tablePrescricao").data("user"),
                anexo_doc: $('input[name="anexo_doc"]').val(),
                anexo_tipo:
                    $("#tipo_anexo").val() == ""
                        ? "ACOMPANHAMENTO"
                        : $("#tipo_anexo").val(),
                titulo_acompanhamento: $("#titulo_acompanhamento").val(),
            };

            $.ajax({
                type: "GET",
                url: "/formularios/acompanhamento.add.php",
                data: data,
                dataType: "json",
                success: function (response) {
                    $(".swal2-confirm").text("Salvar");
                    if (response.status === "success") {
                        window.location.reload();
                    }
                },
            });

            return false;
        },
    }).then(function (result) {
        tinymce.remove();
    });
}


async function start_meet(link, token) {
    let _window = null;
    let _current = window;

    const pipWindow = await documentPictureInPicture.requestWindow({
        width: 600,
        height: 580,
        disableAutoFullscreen: true,
        disableAutoPIP: true,
        disablePipScaling: true,
        disablePipControls: true,
        disablePipAutoClose: true,
        disablePipAutoCloseOnBlur: true,
        disablePipAutoCloseOnEscape: true,
        disallowReturnToOpener: false,
    });

    pipWindow.addEventListener('unload', () => {
        _current.self.close();
        _window.location = '/agenda/prescricao/' + token;
    });

    let frame = document.createElement("iframe");
    frame.setAttribute("allow", "camera; microphone; fullscreen; display-capture; autoplay; compute-pressure");
    frame.src = link;
    frame.style.width = "100%";
    frame.style.height = "550px";
    pipWindow.document.body.append(frame);


    setInterval(function () {
        const element = document.querySelector('iframe');
        if (element) {
            const elementBanner = element.contentWindow.document.querySelector('[class^="WatermarkBanner-"]');
            if (elementBanner)
                elementBanner.remove();
        }
    }, 1000);

    document.title = "Teleconsulta";

    _window = window.open('/agenda/prescricao/' + token + '?h=1');
}
