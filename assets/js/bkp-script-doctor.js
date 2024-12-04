function newAtestado() {
    Swal.fire({
        title: "Emitir Atestado M\xe9dico", allowOutsideClick: !1, html: `
    <div class="row-elements">
        <div class="form-group-item-7">
            <label for="motivo">Motivo</label>
            <select name="motivo" id="motivo">
                <option value="TRABALHISTA">TRABALHISTA</option>
            </select>
        </div>

        <div class="form-group-item-3">
            <label for="dias_afastamento">Afastamento</label>
            <input name="dias_afastamento" id="dias_afastamento" type="number" value="1">
        </div>
    </div>

    <div class="row-elements">
        <div class="form-group-item-7">
            <label for="motivo">Data</label>
            <input name="data-afastamento" id="data-afastamento" type="date" value="">
        </div>

        <div class="form-group-item-3">
            <label for="hora-afastamento">Hora</label>
            <input name="hora-afastamento" id="hora-afastamento" type="time">
        </div>
    </div>

    <div class="row-elements">
        <div class="form-group-item-7">
            <label for="motivo">Local de Atendimento</label>
            <select name="local_atendimento" id="local_atendimento"></select>
        </div>

        <div class="form-group-item-3">
            <label for="cid">CID</label>
            <input name="cid" id="cid" type="text">
        </div>

    </div>
`, didOpen: function () { $("#data-afastamento").val(new Date().toUTCString()), $("#dias_afastamento").mask("00 dia(s)"), $.get(`/form/endereco.medico.php?medico_token=${$('meta[name="user-id"]').attr("content")}`).done(function (e) { if ("data" in e) for (let a = 0; a < e.data.length; a++) { let t = e.data[a]; null !== t.token && $("#local_atendimento").append(`<option value="${t.token}">${t.nome}</option>`) } }) }, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "Emitir", denyButtonText: "Cancelar"
    }).then(function (e) { if (e.isConfirmed) { let a = document.createElement("a"); a.setAttribute("target", "_atestado"), a.setAttribute("href", `/api/pdf/atestado.php?date=${$("#data-afastamento").val()}&time=${$("#hora-afastamento").val()}&paciente_token=${$("table[data-user]").data("user")}&medico_token=${$('meta[name="user-id"]').attr("content")}&motivo=${$("#motivo").val()}&cid=${$("#cid").val()}&afastamento=${$("#dias_afastamento").val()}&local=${$("#local_atendimento").val()}`), a.click() } })
} function calculateAge(e) { var a = new Date, t = new Date(e), o = a.getFullYear() - t.getFullYear(), i = a.getMonth() - t.getMonth(); return (i < 0 || 0 === i && a.getDate() < t.getDate()) && o--, o } function invoke_payment_link(e) { window.open(`/payment/link.php?payment_id=${e}`) } function newMailAccount() {
    Swal.fire({
        title: "Nova Conta de E-mail", html: `<div class="row">
            <div class="form-group">
                <label for="nomecompleto">Nome do Usu\xe1rio</label>
                <input class="swal2-input" id="nomecompleto" placeholder="John Doe" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="mailaccount">E-mail do Usu\xe1rio</label>
                <input class="swal2-input" type="email" id="mailaccount" placeholder="john.doe@clinabs.com" autocomplete="off>
            </div>

            <div class="form-group">
                <label for="mailpwd">Senha</label>
                <input class="swal2-input" id="mailpwd" type="password" placeholder="john.doe@clinabs.com" autocomplete="off">
            </div>
        </div>`, showCancelButtom: !1, showDenyButton: !0, showConfirmButton: !0, confirmButtonText: "CRIAR", denyButtonText: "CANCELAR", allowOutsideClick: !1
    }).then(function (e) { if (e.isConfirmed) { let a = $("#nomecompleto").val(), t = $("#mailaccount").val(), o = window.btoa($("#mailpwd").val()); preloader("Criando Conta de E-mail..."), $.get(`/form/newmail.php?mail=${t}&password=${o}&nome=${a}`).done(function (e) { Swal.fire(e).then(function (a) { "success" === e.status && window.location.reload() }) }) } })
} function alterMailAccount(e = "", a = "") {
    Swal.fire({
        title: "Editar Conta de E-mail", html: `<div class="row">
            <div class="form-group">
                <label for="nomecompleto">Nome do Usu\xe1rio</label>
                <input class="swal2-input" id="nomecompleto" placeholder="John Doe" value="${e}">
            </div>

            <div class="form-group">
                <label for="mailaccount">E-mail do Usu\xe1rio</label>
                <input class="swal2-input" disabled type="email" id="mailaccount" placeholder="john.doe@clinabs.com" value="${a}">
            </div>

            <div class="form-group">
                <label for="mailpwd">Senha</label>
                <input class="swal2-input" id="mailpwd" type="password" placeholder="Insira uma Nova Senha">
            </div>
        </div>`, showCancelButtom: !1, showDenyButton: !0, showConfirmButton: !0, confirmButtonText: "ATUALIZAR", denyButtonText: "CANCELAR", allowOutsideClick: !1
    }).then(function (e) { if (e.isConfirmed) { let a = $("#nomecompleto").val(), t = $("#mailaccount").val(), o = window.btoa($("#mailpwd").val()); preloader("Atualizando Conta de E-mail..."), $.get(`/form/updatemail.php?mail=${t}&password=${o}&nome=${a}&alterpwd=true`).done(function (e) { Swal.fire(e).then(function (a) { "success" === e.status && window.location.reload() }) }) } })
}

function alterar_agendamento(e) {
    preloader("Carregando Agendamento..."), $.get("/form/ag-info.php", { token: $(e).data("token"), mode: "fetch:ag" }).done(function (a) {
        console.log(a);
        Swal.fire({
            title: "Alterar Agendamento", imageUrl: "/assets/images/ico-calendar.svg", imageHeight: 50, html: `
    <div class="row">
        <div class="form-group">
            <label for="medico-id">M\xe9dico</label>
            <select id="medico-id" disabled name="medico-id" class="swal2-input"></select>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="data-agendamento">Data de Agendamento</label>
                <input data-mask="00/00/0000" id="data-agendamento" name="data-agendamento" type="text" class="swal2-input">
            </div>
        </div>

        <div class="col-6">
            <div class="form-group">
                <label for="hora-agendamento">Hora</label>
                <input data-mask="00:00" id="hora-agendamento" name="hora-agendamento" type="text">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="valor_consulta">Valor da Consulta</label>
                <input readonly id="valor_consulta" name="valor_consulta" type="text" class="swal2-input" value="R$ ${a.valor}">
            </div>
        </div>

        <div class="col-6">
            <div class="form-group">
                <label for="valor_restante">Valor a Pagar/Devolver</label>
                <input readonly id="valor_restante" name="valor_restante" type="text" value="R$ 0,00">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <label for="modalidade-id">Modalidade</label>
            <select id="modalidade-id" name="modalidade-id" class="swal2-input">
                <option value="ONLINE">ONLINE</option>
                <option value="PRESENCIAL">PRESENCIAL</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <label for="desc-id">Justificativa</label>
            <textarea id="desc-id"></textarea>
        </div>
    </div>

    <div class="row" id="payment_option">
        <div class="col-4">
            <div class="form-group form-flex">
                <input readonly id="valor_pagar1" name="valor_pagar" type="radio" value="abonar">
                <label for="valor_pagar">Abonar</label>
            </div>
        </div>

        <div class="col-4">
            <div class="form-group form-flex">
                <input readonly id="valor_pagar2" name="valor_pagar" type="radio" value="cobrar">
                <label for="valor_pagar">Cobrar</label>
            </div>
        </div>

        <div class="col-4">
            <div class="form-group form-flex">
                <input readonly id="valor_pagar3" name="valor_pagar" type="radio" value="devolver">
                <label for="valor_pagar">Devolver</label>
            </div>
        </div>
    </div>

    <input id="valor_a_cobrar" type="hidden">
    `, allowOutsideClick: !1, showCancelButtom: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "CONFIRMAR", denyButtonText: "CANCELAR",
            didOpen: async function () {
                await new Promise((e, t) => {
                    $("#medico-id").select2(),
                        $("#select2-medico-id-container").text("Carregando...."),
                        $.get("/forms/fetch.tb.php?tb=MEDICOS&key=valor_consulta_online,valor_consulta,nome_completo").done(function (t) {
                            var lx = new Option(`Selecione uma Opção`, 0);
                            lx.setAttribute("selected", true);
                            lx.setAttribute("disabled", true);

                            $("#medico-id").append(lx);

                            for (let o = 0; o < t.results.length; o++) {
                                let i = t.results[o], n = "MASCULINO" === i.sexo ? "Dr." : "Dra.";
                                if (i.id == a.mid) var l = new Option(`${n} ${i.text}`, i.id, !1, !0);
                                else var l = new Option(`${n} ${i.text}`, i.id, !1, !1); l.setAttribute("data-online", i.valor_consulta_online), l.setAttribute("data-presencial", i.valor_consulta), $("#medico-id").append(l)
                            } $("#medico-id").removeAttr("disabled"), e("success")
                        }),
                        $("#data-agendamento").mask("00/00/0000", { clearIfNotMatch: !0, placeholder: "00/00/0000" }),
                        $("#hora-agendamento").mask("00:00", { clearIfNotMatch: !0, placeholder: "00:00" }), $("#select2-medico-id-container").text("Carregando...."),
                        $("#modalidade-id").select2(), $("#data-agendamento").val(a.data_formated).trigger("keyup"),
                        $("#hora-agendamento").val(a.hora).trigger("change"),
                        $("#modalidade-id").val(a.modalidade).trigger("change")
                });
                $("#medico-id").on('change', function (e) {
                    let valor = parseInt($('#valor_consulta').val().replace(/\D/g, ""));
                    let valor2 = $("#modalidade-id") == "PRESENCIAL" ? $("#medico-id").find(":selected").data("presencial") : $("#medico-id").find(":selected").data("online");
                    let diff = (valor - valor2);

                    console.log({
                        id: $(this).val(),
                        modalidade: $("#modalidade-id").val(),
                        valor_consulta: valor,
                        novo_valor: valor2,
                        diff: diff
                    });

                    $('#valor_restante').val(diff.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                    $('#valor_a_cobrar').val(diff);

                    if (diff < 0) {
                        $('#valor_restante').css('color', 'red');
                        $('#valor_pagar3').prop('disabled', true);
                        $('#valor_pagar2').prop('disabled', false);
                        $('#valor_pagar1').prop('disabled', false);

                        $('#valor_pagar2').prop('checked', true);
                    } else {
                        $('#valor_restante').css('color', 'green');
                        $('#valor_pagar3').prop('disabled', false);
                        $('#valor_pagar2').prop('disabled', true);
                        $('#valor_pagar1').prop('disabled', true);

                        $('#valor_pagar3').prop('checked', true);
                    }

                    $('#payment_option').css('display', 'flex');
                });
            }, preConfirm: function () {
                let a = {
                    medico_id: $("#medico-id").val(),
                    data_agendamento: $("#data-agendamento").val(),
                    hora_agendamento: $("#hora-agendamento").val(),
                    modalidade: $("#modalidade-id").val(),
                    code: $(e).data("token"),
                    description: $("#desc-id").val(),
                    alter_ag: !0,
                    valor_restante: $('#valor_a_cobrar').val(),
                    acao: $('input[name="valor_pagar"]:checked').val()
                };
                preloader("Validando Solicita\xe7\xe3o...."),
                    $.post("/form/form.ag.alter.php", a).done(function (e) {
                        "success" === e.status ? Swal.fire({
                            title: "Aten\xe7\xe3o",
                            text: "Sua Solicita\xe7\xe3o de Altera\xe7\xe3o do Agendamento foi Enviado com sucesso!\n\nAguarde a Confirma\xe7\xe3o da Altera\xe7\xe3o.",
                            icon: "success",
                            allowOutsideClick: !1
                        }).then(function () {
                            window.location.reload();
                        }) : Swal.fire({
                            title: "Aten\xe7\xe3o",
                            text: e.text,
                            icon: "error"
                        })
                    }).fail(function () {
                        Swal.fire({ title: "Aten\xe7\xe3o", text: "Erro ao Enviar a Solicita\xe7\xe3o, tente novamente", icon: "error" })
                    })
            }
        })
    })
}

function confirm_agendamento(e) {
    preloader("Carregando Agendamento..."), $.get("/form/ag-info.php", { token: $(e).data("token"), mode: "fetch:alter" }).done(function (a) {
        console.log(a);

        Swal.fire({
            title: "Confirmar Altera\xe7\xf5es de Agendamento", imageUrl: "/assets/images/ico-calendar.svg", imageHeight: 50, html: `
    <div class="row">
        <div class="form-group">
            <label for="medico-id">M\xe9dico</label>
            <select disabled id="medico-id" disabled name="medico-id" class="swal2-input">
                <option value="${a.medico_id}" selected>${a.medico_nome}</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <label for="data-agendamento">Data de Agendamento</label>
            <input disabled data-mask="00/00/0000" id="data-agendamento" name="data-agendamento" value="${a.data_alterada}" type="text" class="swal2-input">
        </div>

        <div class="form-group">
            <label for="hora-agendamento">Hora</label>
            <input disabled data-mask="00:00" id="hora-agendamento" name="hora-agendamento" type="text" value="${a.hora_agendamento}">
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label for="modalidade-id">Modalidade</label>
            <select disabled id="modalidade-id" name="modalidade-id" class="swal2-input">
                <option value="${a.modalidade}" selected>${a.modalidade}</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <label for="desc-id">Justificativa</label>
            <textarea disabled id="desc-id">${a.description}</textarea>
        </div>
    </div>

    <div class="row" id="payment_option">
        <div class="form-group">
            <label for="desc-id">Forma de Pagamento</label>
            <select id="payment_method">
                <option disabled selected>Selecione uma Opção</option>
                <option value="PIX">PIX</option>
                <option value="CREDIT_CARD">Cartão de Crédito ou Débito</option>
            </select>
        </div>
    </div>

    <input type="hidden" id="medico_token" value="${a.medico_token}">
    `,
            didOpen: function () {
                $('#payment_method').select2();

                if (a.acao == 'cobrar') {
                    $("#payment_option").css('display', 'flex');
                } else {
                    $("#payment_option").css('display', 'none');
                }
            }, allowOutsideClick: !0, showCancelButtom: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "CONFIRMAR", denyButtonText: "REJEITAR", cancelButtonText: "FETCHAR"
        }).then(function (t) { let o = { payment_method: $('#payment_method').val(), medico_id: $("#medico-id").val(), data_agendamento: $("#data-agendamento").val(), hora_agendamento: $("#hora-agendamento").val(), modalidade: a.modalidade, code: $(e).data("token"), description: $("#desc-id").val(), confirm_ag: !0, status: t.isConfirmed ? "CONFIRMADO" : "IGNORADO", medico_token: $("#medico_token").val() }; console.log(o), (t.isConfirmed || t.isDanied) && (preloader("Validando Solicita\xe7\xe3o...."), $.post("/form/form.ag.alter.php", o).done(function (e) { "success" === e.status ? Swal.fire({ title: "Aten\xe7\xe3o", text: e.text, icon: "success", allowOutsideClick: !1 }).then(function () { window.location.reload() }) : Swal.fire({ title: "Aten\xe7\xe3o", text: e.text, icon: "error" }); console.log(e) }).fail(function () { Swal.fire({ title: "Aten\xe7\xe3o", text: "Erro ao Enviar a Solicita\xe7\xe3o, tente novamente", icon: "error" }) })) })
    })
} function wbfs() { document.getElementById("wb-container").requestFullscreen() } function showPrescForm() { $("#x-form-presc").fadeIn(500), $("section#tabControl1").fadeOut(500), tinymce.init({ language: "pt_BR", selector: ".tiny-mce", plugins: "", autosave_restore_when_empty: !0, toolbar: "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat", tinycomments_mode: "embedded", tinycomments_author: "" }).then(function () { $('div[role="application"]').attr("style", "visibility: hidden; width: 100%; height: 202px;"), $("#produto_sa_x").bind("change", function () { $("#produto_frascos_x").focus(), $("#produto_sa_x").trigger("change") }) }) } function presc_form() { let e = { tipo: $(".presc_mod:checked").val(), produto: $("#produto_sa_x").val(), produto_nome: $("#produto_sa_x").children("option:selected").text(), frascos: $("#produto_frascos_x").val(), prescricao: tinyMCE.activeEditor.getContent({ format: "text" }), agenda_token: $("#presc-wb").data("token"), user_token: $('meta[name="user-id"]').attr("content"), medico_token: $("#presc-wb").data("medico"), paciente_token: $("#presc-wb").data("paciente") }; $.ajax({ type: "GET", url: "/formularios/prescricao.add.php", data: e, dataType: "json", success: function (e) { toast(e.text, e.status), "success" == e.status && ($("#x-form-presc").fadeOut(500), $("section#tabControl1").fadeIn(500), $.get(`/form/form.acompanhamento.list.php?token=${$("#presc-wb").data("paciente")}`).done(function (e) { $("#acompanhamentos-list").html(e) })) } }) } function calcAge(e) { let a = e.split("/"), t = parseInt(a[0]), o = parseInt(a[1]), i = parseInt(a[2]), n = new Date(i, o - 1, t), l = new Date, d = l.getFullYear() - n.getFullYear(), s = l.getMonth() - n.getMonth(); return (s < 0 || 0 === s && l.getDate() < n.getDate()) && d--, d } $("document").ready(function () { window.matchMedia("(max-width: 767px)").matches && $("img.ico-hover.user").bind("click", function () { "none" == $('meta[name="user-name"]').attr("content") ? window.location = "/login" : $(this).off("click") }), $(".presc_mod").on("click", function () { "acompanhamento" == this.value ? $(".row-presc").hide() : $(".row-presc").show() }), $("#presc-wb").length > 0 && $.get(`/form/form.acompanhamento.list.php?token=${$("#presc-wb").data("paciente")}`).done(function (e) { $(".acompanhamentos-list").html(e) }), $.get("/forms/fetch.tb.php?tb=PRODUTOS&key=nome,status").done(function (e) { $.each(e.results, function (a) { let t = e.results[a]; "ATIVO" === t.text && $("#produto_sa_x").append(`<option value="${t.id}">${t.nome.toUpperCase()}</option>`), $("#produto_sa_x").select2() }) }), $(".ql-snow .ql-tooltip a").each(function () { $(this).remove() }), $(".ql-snow .ql-tooltip input").each(function () { $(this).remove() }), $(".ql-snow .ql-tooltip").append('<a class="link-action" data-action="save">Salvar</a>'), $(".ql-snow .ql-tooltip").append('<a class="link-action" data-action="save">Atualizar</a>'), $(".listmedic-box-dir-boxtime.item-disabled").on("click", function (e) { let a = $(this).closest(".box-mediclist").find(".listmedic-boxlink-box"); !1 === $(a).find(".btn-modalidade").is(":checked") && (e.preventDefault(), Swal.fire({ title: "Aten\xe7\xe3o", icon: "info", text: "Voc\xea Deve Selecionar uma Modalidade de Consulta Primeiro.!", allowOutsideClick: !1 }).then(function () { $(a).css("border", "2px solid #009688"), $(a).css("padding", "5px") })) }), $("#nacionalidade").val("BR").trigger("change"), $("#data_nascimento.validate-age").on("blur", function () { let e = calcAge(this.value); e < 18 ? Swal.fire({ text: "Voc\xea n\xe3o tem idade igual ou superior a 18 anos, portanto deve preencher os dados do respons\xe1vel legal por voc\xea.", icon: "warning", allowOutsideClick: !1 }).then(function () { $("#responsavel-legal").find("input").each(function () { $(this).attr("required", "required") }), $("#responsavel-legal").show() }) : isNaN(e) && $(this).trigger("blur") }), $(".fill-form").mask("000.000.000-00", { placeholder: "___.___.___-__", onComplete: function (e) { preloader("Processando..."), $.get(`/forms/paciente.exists.php?cpf=${e}`, function (e) { if (e.exists) { let a = e.data.data_nascimento.split("-"); $("#paciente_token").val(e.data.token), $("#cpf").val(e.data.cpf), $("#nome_completo").val(e.data.nome_completo), $("#rg").val(e.data.rg), $("#nacionalidade").val(e.data.nacionalidade).trigger("change"), $("#nome_preferencia").val(e.data.nome_preferencia), $("#identidade_genero").val(e.data.identidade_genero).trigger("change"), $("#data_nascimento").off("change").val(`${a[2]}/${a[1]}/${a[0]}`).trigger("change"), $("#email").val(e.data.email), $("#anamnese").val(e.data.queixa_principal), $("#celular").val(e.data.celular), $('input[name="paciente_token"]').val(e.data.token), $("#add_new").val(e.data.cpf), $("select").each(function () { $(this).trigger("change") }) } else $("#add_new").val(""); Swal.close() }) } }), $("#celular").mask("+55 (00) 0 0000-0000", { clearIfNotMatch: !0, placeholder: "+55 (00) 0 0000-0000" }), $("#anamnese[data-selected]").each(function () { let e = $(this).data("selected").split(","); $(this).val(e), $(this).trigger("change") }), $(".input-upload-doc").off("change"), $(".input-upload-doc").on("change", function () { let e = $(this).attr("id"), a = $(this); preloader("Enviando Arquivo..."); let t = new FormData; t.append("file", this.files[0], this.files[0].name), t.append("tb", $('input[name="tabela"]').val()), t.append("key", this.id), t.append("token", $("#user_token").val()), fetch("/form/upload-docs.php", { method: "POST", body: t }).then(e => e.json()).then(t => { if ("success" === t.status) { let o = "pdf" === t.ext ? "/assets/images/ico-doc-pdf.svg" : "/assets/images/ico-doc-img.svg"; $(a).closest(".anexo-item").find("img").attr("src", o), $(a).closest(".anexo-item").find(`input[name="${e}"]`).length > 0 ? $(a).closest(".anexo-item").find(`input[name="${e}"]`).val(t.path) : $(a).after(`<input type="hidden" name="${e}" value="${t.path}">`), $(a).attr("disabled", !0), Swal.close() } else $(a).closest(".anexo-item").find("img").attr("src", "/assets/images/ico-doc-large.svg"), Swal.fire({ title: "Aten\xe7\xe3o", text: t.message, icon: "error" }) }).catch(e => { Swal.fire({ title: "Aten\xe7\xe3o", text: e, icon: "error" }) }) }) });





function relatorioAcompanhamento(classItem, patientToken) {
    let items = [];

    $(classItem).each(function () {
        items.push($(this).val());
    });

    window.open('/cadastros/pacientes/relatorio/?paciente_token=' + patientToken + '&items=' + btoa(JSON.stringify(items)), '_relatorio_acompanhamento', 'width=800,height=600');
}
