function wb_fs() { document.fullscreenElement ? (document.exitFullscreen(), $(".breadcrumbs").show(), $("header.header").show(), $("section.footer-1").show(), $("section.footer-2").show()) : (document.body.requestFullscreen(), $(".breadcrumbs").hide(), $("header.header").hide(), $("section.footer-1").hide(), $("section.footer-2").hide()) } function newPrescFuncWb() {
    let t = `<div class="flex-col"><p>
    <table width="100%" data-list="none">
       <tr class="gap-1">
       <td><fieldset><legend>Selecionar um Produto</legend><select id="produto_sa"></select></fieldset></td>
       <td width="100px"><fieldset><legend>Frascos</legend><input id="produto_frascos" type="number" min="1" max="100" value="1" data-arrows="false" placeholder="1"></fieldset></td>
       </tr>
    </table>
    </div></p>`; t += `<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4"></textarea></div>
         <div class="swal2-checks">
             <label for="prescricao_mod"><input type="radio" name="presc_mod" id="prescricao_mod" checked value="prescricao"> Prescri\xe7\xe3o de M\xe9dicamentos</label>
             <label for="acompanhamento_mod"><input type="radio" name="presc_mod" id="acompanhamento_mod" value="acompanhamento"> Acompanhamento M\xe9dico</label>
         </div>`, Swal.fire({
        title: "Nova Prescri\xe7\xe3o", width: window.innerWidth >= 500 ? "60%" : "90%", height: "50%", html: t, showCancelButton: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "Salvar", denyButtonText: "Cancelar", allowOutsideClick: !1, customClass: "swal-right", didOpen: function () { $(".swal2-popup.swal2-modal").css("transform", "translateX(25%) !important"), $('input[name="presc_mod"]').on("change", function () { $("#prescricao_mod").removeAttr("checked"), $("#acompanhamento_mod").removeAttr("checked"), $(this).attr("checked", "checked"), "acompanhamento" === this.value ? $(".swal2-html-container table").hide() : $(".swal2-html-container table").show() }), $("#produto_sa").append('<option value="" selected disabled>Selecionar um Produto</option>'), $.get("/forms/fetch.tb.php?tb=PRODUTOS&key=nome,status").done(function (t) { $.each(t.results, function (e) { let o = t.results[e]; "ATIVO" === o.text && $("#produto_sa").append(`<option value="${o.id}">${o.nome}</option>`) }), tinymce.init({ language: "pt_BR", selector: ".tiny-mce", plugins: "", autosave_restore_when_empty: !0, toolbar: "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat", tinycomments_mode: "embedded", tinycomments_author: "" }).then(function () { $('div[role="application"]').attr("style", "visibility: hidden; width: 100%; height: 202px;"), $("#produto_sa").bind("change", function () { $("#produto_frascos").focus(), $("#produto_sa").trigger("change") }) }) }) }, preConfirm: function () {
            if ("prescricao" === $('input[name="presc_mod"]:checked').val()) {
                if (!($("#produto_frascos").val() >= 1) || null === $("#produto_sa").val()) return null === $("#produto_sa").val() ? $("#produto_sa").trigger("click") : $("#produto_frascos").focus(), !1; {
                    $(".swal2-confirm").text("Salvando..."); let t = { produto: $("#produto_sa").val(), produto_nome: $("#produto_sa").children("option:selected").text(), frascos: $("#produto_frascos").val(), prescricao: window.btoa(tinyMCE.activeEditor.getContent()), agenda_token: $("#tablePrescricaoWb").data("token"), user_token: $('meta[name="user-id"]').attr("content"), medico_token: $("#tablePrescricaoWb").data("medico"), paciente_token: $("#tablePrescricaoWb").data("user") }; return $.ajax({
                        type: "GET", url: "/formularios/prescricao.add.php", data: t, dataType: "json", success: function (t) {
                            if ($(".swal2-confirm").text("Salvar"), "success" === t.status) {
                                let e = $("#tablePrescricaoWb").find("tbody").find("tr"), o = new Date, a = `<tr>
                                <td>${e.length + 1}</td>
                                <td>${10 > o.getDate() ? "0" + o.getDate() : o.getDate()}/${10 > o.getMonth() ? "0" + (o.getMonth() + 1) : o.getMonth() + 1}/${o.getFullYear()}  ${o.getHours()}:${9 > o.getMinutes() ? "0" + o.getMinutes() : o.getMinutes()}</td>
                                <td>${tinyMCE.activeEditor.getContent()}</td>
                                <td>${t.result.produto_nome}</td>
                                <td>${t.result.frascos}</td>
                                <td class="td-act">
                                   <div class="btns-act">
                                      <div class="btns-table">
                                         <button type="button" title="Cancelar Prescri\xe7\xe3o" class="btn-action" onclick="action_btn_presc_wb(this)" data-token="" data-action="presc-delete">
                                            <img src="/assets/images/ico-delete.svg" height="28px">
                                         </button>
                                         <button type="button" title="Editar Prescri\xe7\xe3o" class="btn-action" oclick="action_btn_presc_wb(this)" data-token="" data-action="presc-edit">
                                            <img src="/assets/images/ico-edit.svg" height="28px">
                                         </button>
                                      </div>
                                   </div>
                                </td>
                             </tr>`; $(".dataTables_empty").parent().remove(), $("#tablePrescricaoWb").find("tbody").append(a), Swal.close()
                            }
                        }
                    }), !1
                }
            } if (!(tinyMCE.activeEditor.getContent().length > 0)) return $("textarea").focus(), !1; { let e = { periodo: 0, semana: 0, prescricao: window.btoa(tinyMCE.activeEditor.getContent()), funcionario_token: $('meta[name="user-id"]').attr("content"), paciente_token: $("#tablePrescricaoWb").data("user") }; return $.ajax({ type: "GET", url: "/formularios/acompanhamento.add.php", data: e, dataType: "json", success: function (t) { $(".swal2-confirm").text("Salvar"), "success" === t.status && (Swal.close(), location.reload()) } }), !0 }
        }
    }).then(function (t) { t.isConfirmed })
} function wa_notify(t, e, o) { Swal.fire({ title: "Aten\xe7\xe3o", icon: "question", html: 0 == o ? `Deseja Enviar um Lembrete de Pagamento para <b>${e}</b> ?` : `Deseja Enviar o Comprovante de Pagamento para <b>${e}</b> ?`, allowOutsideClick: !1, showCancelButton: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "SIM", denyButtonText: "N\xc3O" }).then(function (e) { e.isConfirmed && (preloader("Enviando..."), $.get("/form/cobranca.notify.php", { token: t }).done(function (t) { Swal.fire(t) }).fail(function (t) { }).always(function (t) { })) }) } function payment_by_money(t, e, o = !1) { Swal.fire({ title: "Aten\xe7\xe3o", icon: "question", html: o ? `Deseja Desfazer o Recebimento em Dinheiro para  <b>${e}</b> ?` : `Deseja Confirmar o Recebimento em Dinheiro para  <b>${e}</b> ?`, allowOutsideClick: !1, showCancelButton: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "SIM", denyButtonText: "N\xc3O" }).then(function (e) { e.isConfirmed && (preloader("Processando..."), $.get("/form/cobranca.cash.php", { token: t, mode: o ? "undo" : "" }).done(function (t) { Swal.fire(t).then(function (e) { "success" == t.status && window.location.reload() }) }).fail(function (t) { }).always(function (t) { })) }) } function action_btn_presc_wb(t) { let e = $(t).closest("tr").data("id"); switch ($(t).data("action")) { case "presc-delete": Swal.fire({ title: "Aten\xe7\xe3o", text: "Deseja Realmente Excluir este Registro?", icon: "question", showConfirmButton: !0, showDenyButton: !0, showCancelButton: !1, allowOutsideClick: !1, confirmButtonText: "SIM", denyButtonText: "NO", didOpen: function () { $(t).closest("tr").css("filter", "grayscale(1)") } }).then(function (o) { o.isConfirmed ? $.get(`/formularios/prescricao.delete.wb.php?id=${e}`).done(function (e) { toast(e.text, e.status), "success" === e.status && $(t).closest("tr").remove() }) : $(t).closest("tr").css("filter", "grayscale(0)") }); break; case "presc-edit": editPrescFuncWb(t) } } function editPrescFuncWb(t, e = !1) {
    let o = $(t).closest("tr").data("product"), a = $(t).closest("tr").data("frascos"), n = $(t).closest("tr").data("prescricao"), i = $(t).closest("tr").data("id"); $(t).closest("tr").data("medico"); let s = $(t).closest("tr"), r = `<div class="flex-col"><p>
    <table width="100%" data-list="none">
       <tr>
       <td${!1 == e ? ' style="display: none"' : ""}><fieldset><legend>Selecionar um Produto</legend><select id="produto_sa"></select></fieldset></td>
       <td width="100px"{presc == false ? ' style="display: none"':''}><fieldset><legend>Frascos</legend><input id="produto_frascos" type="text" data-arrows="false" placeholder="0"></fieldset></td>
       </tr>
    </table>
    </div></p>`; r += '<textarea id="swal-textarea" class="tiny-mce" name="descricao_html" style="width: 100%;" rows="4">Carregando...</textarea></div>', Swal.fire({ title: e ? "Nova Prescri\xe7\xe3o/Acompanhamento" : "Editar Prescri\xe7\xe3o/Acompanhamento", width: window.innerWidth >= 500 ? "60%" : "90%", height: "50%", html: r, showCancelButton: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "Salvar", allowOutSideClick: !1, denyButtonText: "Cancelar", didOpen: function () { $("#produto_sa").append('<option value="" selected disabled>Selecionar um Produto</option>'), $.get("/forms/fetch.tb.php?tb=PRODUTOS&key=id,nome,status").done(function (t) { $.each(t.results, function (e) { let o = t.results[e]; "ATIVO" === o.text && $("#produto_sa").append(`<option value="${o.id}">${o.nome}</option>`) }), $("#produto_sa").val(o).trigger("change"), $("#produto_frascos").val(a), $("#produto_sa").select2(), tinymce.init({ language: "pt_BR", selector: ".tiny-mce", entity_encoding: "raw", plugins: "", autosave_restore_when_empty: !0, toolbar: "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat", tinycomments_mode: "embedded", tinycomments_author: "" }).then(function () { $('div[role="application"]').attr("style", "visibility: hidden; width: 100%; height: 202px;"), tinyMCE.activeEditor.setContent(window.atob(n), { format: "text" }), $("#produto_sa").bind("change", function () { $("#produto_frascos").focus(), $("#produto_sa").trigger("change") }) }) }) }, preConfirm: function () { $(".swal2-confirm").text("Salvando..."); let t = { produto: $("#produto_sa").val(), produto_nome: $("#produto_sa").children("option:selected").text(), frascos: $("#produto_frascos").val(), prescricao: window.btoa(tinyMCE.activeEditor.getContent()), agenda_token: $("#tablePrescricaoWb").data("token"), user_token: $("#tablePrescricaoWb").data("user"), medico_token: $("#tablePrescricaoWb").data("medico"), id: i }; return $.ajax({ type: "GET", url: "/formularios/prescricao.edit.wb.php", data: t, dataType: "json", success: function (e) { $(".swal2-confirm").text("Salvar"), "success" === e.status && ($(s).find('td[data-set="prescricao"]').text(tinyMCE.activeEditor.getContent({ format: "text" })), $(s).find('td[data-set="produto_nome"]').text(t.produto_nome), $(s).find('td[data-set="frascos"]').text(t.frascos), $(s).data("data-product", t.produto), $(s).data("data-frascos", t.frascos), $(s).data("data-prescricao", t.prescricao), Swal.close(), location.reload()) } }), !1 } }).then(function (t) { tinymce.remove() })
} function action_btn_form_payment(t) { let e = $(t).data("token"), o = $(t).data("act"); "delete_payment" === o && Swal.fire({ title: "Aten\xe7\xe3o", text: "Deseja cancelar este Pagamento?", icon: "question", allowOutsideClick: !1, showCancelButton: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "SIM", denyButtonText: "N\xc3O" }).then(function (t) { t.isConfirmed && (preloader("Processando...."), $.get("/form/faturamento.actions.php", { action: o, token: e }).done(function (t) { Swal.fire(t).then(function () { "success" == t.status && (preloader("Atualizando..."), window.location.reload()) }) })) }) } $("document").ready(function () {
    if ($('input[type="radio"]:checked').trigger("change"), $("#google-sync").on("click", function (t) {
        let e = $(this).data("token"); Swal.fire({
            title: "Sincronizar Agenda", html: `<b style="color: red">Aten\xe7\xe3o: </b> Voc\xea est\xe1 prestes a Sincronizar sua agenda com a Agenda do Google abaixo est\xe1 os dados que vai ser coletados da Agenda
             <br>
             <br>
             <ul>
                <li>=> Hor\xe1rios Livres</li>
             </ul>
             `, imageUrl: "/assets/images/google_calendar.svg", imageHeight: 64, allowOutSideClick: !1, showCancelButton: !1, showConfirmButton: !0, showDenyButton: !0, confirmButtonText: "Sincronizar", denyButtonText: "Cancelar"
        }).then(function (t) { t.isConfirmed && (preloader("Sincronizando..."), $.get("/cron/google-sync.php", { token: $('#google-sync').data('token') }).done(function (t) { Swal.fire(t).then(() => { "success" === t.status && window.location.reload() }) }).fail(function (t) { Swal.fire({ title: "Aten\xe7\xe3o", text: "Ocorreu um Erro" }) })) })
    }), window.addEventListener("pageshow", function (t) { (t.persisted || void 0 !== window.performance && 2 === window.performance.navigation.type) && window.location.reload() }), $('input[type="email"]').on("blur", function (t) { let e = $(this); /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value) || Swal.fire({ title: "Aten\xe7\xe3o", html: "E-mail Inv\xe1lido<br>Verifique o E-mail Digitado!<br><br> - Verifique se h\xe1 espa\xe7os<br> - Verifique se digitou o dom\xednio Ex. @clinabs.com", icon: "warning" }).then(function (t) { t.isConfirmed && ($(e).val(e.value.replaceAll(" ", "")), $(e).focus()) }) }), $(".listmedic-boxlink").length > 0) { let t = $('.ag-item-time-btn[data-presencial="true"]').length, e = $('.ag-item-time-btn[data-online="true"]').length; 0 === e && $('.listmedic-boxlink[data-value="online"]').hide(), 0 === t && $('.listmedic-boxlink[data-value="presencial"]').hide(), 0 == t && e > 0 ? $('.listmedic-boxlink[data-value="online"]').trigger("click") : t > 0 && 0 == e && $('.listmedic-boxlink[data-value="presencial"]').trigger("click") }
});





$('document').ready(async function () {

    if (window.location.pathname.trim() === '/dashboard/') {
        preloader('Carregando dashboard...');

        await update_dashboard();

        setInterval(function () {
            update_dashboard();
        }, Math.max(5000, 15000));
    }


    $('[delete-acompanhamento]').on('click', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Atenção',
            text: 'Deseja excluir o Acompanhamento?',
            icon: 'warning',
            allowOutsideClick: false,
            showConfirmButton: true,
            showDenyButton: true,
            confirmButtonText: 'SIM',
            denyButtonText: 'NÃO'
        }).then(function (evt) {
            if (evt.isConfirmed) {

                preloader('Excluindo Acompanhamento...');
                $.get(`/formularios/acompanhamento.delete.php?id=${id}`).done(function (evt) {
                    if (evt.status === 'success') {
                        Swal.fire({
                            title: 'Aten\xe7\xe3o',
                            text: 'Acompanhamento excluído com sucesso!',
                            icon: 'success',
                            allowOutsideClick: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Aten\xe7\xe3o',
                            text: evt.msg,
                            icon: 'warning',
                            allowOutsideClick: false
                        });
                    }
                }).fail(function (evt) {
                    Swal.fire({
                        title: 'Aten\xe7\xe3o',
                        text: 'Ocorreu um erro ao excluir o acompanhamento',
                        icon: 'warning',
                        allowOutsideClick: false
                    });
                }).always(function (evt) {

                });
            }
        });
    });



});



async function update_dashboard() {
    const response = await fetch('/api/v4/dashboard');
    const evt = await response.json();

    let current_date = new Date().toLocaleDateString();
    let current_time = new Date().toLocaleTimeString();

    $('#medicos_count').text(evt.counter.medicos);
    $('#pacientes_count').text(evt.counter.pacientes);
    $('#pagamentos_count').text(evt.counter.vendas);
    $('#agendamentos_count').text(evt.counter.agendamentos);
    $('#visitantes_count').text(evt.counter.acessos);

    // Agendamentos

    $('#novosAgendamentos').find('.kanban-card').remove();

    Object.entries(evt.agendamentos).forEach(([key, item]) => {
        let date = new Date(key).toLocaleDateString();
        let hour = new Date(key).toLocaleTimeString();

        if ($(`#kanban-item-${item.id}`).length === 0) {
            let htm = `<div class="kanban-card${item.status === 'AGUARDANDO PAGAMENTO' || item.status === 'PAGAMENTO PENDENTE' ? ' kanban-badge-danger' : ' kanban-badge-success'}" id="kanban-item-${item.id}" data-id="${item.id}" title="Paciente: ${item.paciente_nome}\nMédico: ${item.medico_nome}\nData/Hora: ${date} as ${hour}\n Status: ${item.status}">
                               <img src="${item.img}" alt="${item.nome_completo}" class="kanban-img" />
                               <div class="text-container">
                                   <p><strong>${item.paciente_nome}</strong></p>
                                   <p>Médico: ${item.medico_nome}</p>
                                   <p>Modalidade: ${item.modalidade}</p>
                                   <p>Data/Hora: ${date} as ${hour.substring(0, 5)}</p>
                                   <p class="kanban-btns">`;

            if (item.status === 'AGUARDANDO PAGAMENTO' || item.status === 'PAGAMENTO PENDENTE') {
                htm += (evt.perms.agendamentos.dashboard_new_agendamentos_enviar_whatsapp === 1 ? `<button class="btn-action">
                                                    <img class="kanban-action-btn" 
                                                        data-act="wa_notify" 
                                                        data-token="${item.token}" 
                                                        data-patient="${item.paciente_nome}" 
                                                        title="Enviar Lembrete de Cobrança via WhatsApp"
                                                        src="/assets/images/icon-whatsapp.svg" height="22px">
                                                </button>`: ``);
            } else if (item.status === 'AGENDADO') {
                htm += (evt.perms.agendamentos.dashboard_new_agendamentos_editar === 1 ? `<button title="Editar Prescrição" class="btn-action" onclick="action_btn_form_agendamento(this)" data-token="${item.token}" data-act="agenda-edit" data-status="EFETIVADO"><img src="/assets/images/ico-edit.svg" height="28px"></button>` : ``);
                htm += (evt.perms.agendamentos.dashboard_new_agendamentos_alterar === 1 ? `<button title="Alterar Agendamento" class="btn-action" onclick="alterar_agendamento(this)" data-token="${item.token}" data-medico="${item.medico_nome}" data-status="AGENDADO"><img src="/assets/images/ico-alter.svg" height="28px"></button>` : ``);
            }

            htm += (evt.perms.agendamentos.dashboard_new_agendamentos_cancelar === 1 ? `<button title="Cancelar Consulta" class="btn-action" onclick="action_btn_form_agendamento(this)" data-token="${item.token}" data-act="agenda-cancel" data-status="AGENDADO"><img src="/assets/images/ico-delete.svg" height="28px"></button>` : ``);


            htm += `</p>
                               </div>
                           </div>`;

            $('#novosAgendamentos').append(htm);
        }
    });

    // Pacientes
    $('#novosPacientes').find('.kanban-card').remove();
    Object.entries(evt.pacientes).forEach(([key, item]) => {
        let date = new Date(item.creation_date).toLocaleDateString();
        let time = new Date(item.creation_date).toLocaleTimeString();

        if ($(`#kanban-item-${item.id}`).length === 0) {
            let pacientes_item = `<div class="kanban-card" id="kanban-item-${item.id}" data-id="${item.id}">
                               <img src="${item.img}" alt="${item.nome_completo}" class="kanban-img" />
                               <div class="text-container">
                                   <p><strong>${item.nome_completo}</strong></p>
                                   <p>Queixa: ${item.queixa_principal}</p>
                                   <p>Data de Criação: ${date} as ${time}</p>
                                   <p>Idade: ${item.age} anos</p>
                                   <p>`;

            pacientes_item += (evt.perms.pacientes.dashboard_new_pacientes_carrinho == 1 ? `<button $ class="btn-action" onclick="actionBtn(this)" data-token="${item.token}" data-action="cart-products"><img src="/assets/images/ico-cart-btn.svg" height="32px" title="Comprar Medicamentos"></button>` : ``);
            pacientes_item += (evt.perms.pacientes.dashboard_new_pacientes_ag_consulta === 1 ? `<button class="btn-action" onclick="actionBtn(this)" data-token="${item.token}" data-action="agendar-consulta"><img src="/assets/images/ico-doctor-btn.svg" height="32px" title="Agendar Consulta"></button>` : ``);
            pacientes_item += (evt.perms.pacientes.dashboard_new_pacientes_perfil === 1 ? `<button class="btn-action" onclick="actionBtn(this)" data-token="${item.token}" data-action="editar-perfil"><img src="/assets/images/ico-edit.svg" height="32px title=" editar="" perfil"=""></button>` : ``);
            pacientes_item += (evt.perms.pacientes.dashboard_new_pacientes_msg_wa === 1 ? `<button class="btn-action" onclick="wa_send_message(this)" data-wa="${item.celular}"><img src="/assets/images/ico-wa-btn.svg" height="36px" title="Enviar Notificação/Mensagem"></button>` : ``);
            pacientes_item += (evt.perms.pacientes.dashboard_new_pacientes_prontuario === 1 ? `<a class="btn-action" href="/cadastros/pacientes/acompanhamento/${item.token}" data-token="${item.token}"><img src="/assets/images/ico-presc.svg" height="32px" title="Acompanhamento de Paciente"></a>` : ``);

            pacientes_item += `</p>
                               </div>
                           </div>`;

            $('#novosPacientes').append(pacientes_item);
        }
    });

    // Medicos
    $('#novosMedicos').find('.kanban-card').remove();
    Object.entries(evt.medicos).forEach(([key, item]) => {
        let date = new Date(item.creation_date).toLocaleDateString();

        if ($(`#kanban-id-${item.id}`).length === 0) {
            let medicos_item = `<div class="kanban-card" id="kanban-id-${item.id}" data-id="${item.id}">
                               <img src="${item.img}" alt="${item.nome_completo}" class="kanban-img" />
                               <div class="text-container">
                                   <p><strong>${item.nome_completo}</strong></p>
                                   <p>Queixa: ${item.especialidade}</p>
                                   <p>Data de Criação: ${date}</p>
                                   <p>Idade: ${item.age} anos</p>
                                   <p>`;
            medicos_item += (evt.perms.medicos.dashboard_new_medicos_perfil == 1 ? `<button class="btn-action" onclick="actionBtn(this)" data-token="${item.token}" data-action="editar-perfil"><img src="/assets/images/ico-edit.svg" height="32px title=" editar="" perfil"=""></button>` : ``);
            `</p>
                               </div>
                           </div>`;
            $('#novosMedicos').append(medicos_item);
        }
    });

    // Acompanhamentos
    $('#acompanhamentosMedicos').find('.kanban-card').remove();
    Object.entries(evt.acompanhamentos).forEach(([key, item]) => {

        let badge, date;

        const xtoday = new Date(item.proximo_acompanhamento);
        const xyesterday = new Date(xtoday);
        xyesterday.setDate(xtoday.getDate() - 1);
        const yesterdayDate = xyesterday.toLocaleDateString();

        if (item.proximo_acompanhamento == 'Hoje') {
            date = item.proximo_acompanhamento;
            badge = ' kanban-badge-danger danger';
        } else {
            if (item.proximo_acompanhamento == 'Amanhã') {
                badge = ' kanban-badge-warning warning';
                date = 'Amanhã';
            } else {
                date = parse_date(item.proximo_acompanhamento);
                badge = '';
            }
        }



        if ($(`#kanban-item-${item.id}`).length === 0) {
            let acompanhamento_item = `<div class="kanban-card${badge}" id="kanban-item-${item.id}" data-id="${item.id}">
                               <img src="${item.img}" alt="${item.paciente_nome}" class="kanban-img" />
                               <div class="text-container">
                                   <p><strong>Paciente: ${item.paciente_nome}</strong></p>
                                   <p>Descrição: ${item.titulo_acompanhamento}</p>
                                   <p>Data Próximo Acompanhamento: ${date}</p>
                                   <p>Acompanhante: ${item.funcionario_nome}</p>
                                   <p>`;
            acompanhamento_item += (evt.perms.acompanhamento.dashboard_proximos_acompanhamentos_prontuario == 1 ? `<a class="btn-action" href="/cadastros/pacientes/acompanhamento/${item.paciente_token}" data-token="${item.paciente_token}"><img src="/assets/images/ico-presc.svg" height="32px" title="Acompanhamento de Paciente"></a>` : ``);
            acompanhamento_item += `</p>
                               </div>
                           </div>`;
            $('#acompanhamentosMedicos').append(acompanhamento_item);
        }
    });


    // Contas a Receber
    $('#contasReceber').find('.kanban-card').remove();
    Object.entries(evt.cobrancas).forEach(([key, item]) => {
        let date = new Date(item.dueDate).toLocaleDateString();
        let valor = item.value.toLocaleString("pt-br", { style: "currency", currency: "BRL" });
        let days_due = dateDiff1(new Date(), new Date(item.dueDate));

        if (days_due === 0) {
            days_due = 'hoje';
        } else {
            days_due = `em ${days_due} dia(s)`;
        }

        if ($(`#kanban-item-${item.id}`).length === 0) {
            $('#contasReceber').append(`<div class="kanban-card" id="kanban-item-${item.id}" data-id="${item.id}">
                               <img src="${item.img}" alt="${item.paciente_nome}" class="kanban-img" />
                               <div class="text-container">
                                   <p><strong>Paciente: ${item.paciente_nome}</strong></p>
                                   <p>Valor: ${valor}</p>
                                   <p>Data de Vencimento: ${date}</p>
                                   <p>Vence:  ${days_due}</p>
                                   <p>
                                        <a target="_blank" href="${item.invoiceUrl}"><img title="Ver Fatura" src="/assets/images/icon-doc.svg" height="22px"></a>
                                        <img onclick="wa_notify('${item.id}', '${item.paciente_nome}', 0)" title="Enviar Lembrete de Cobrança via WhatsApp" src="/assets/images/wa.svg" height="22px">
                                        <img onclick="caixa_recebimento_exec('${item.id}', (${item.value} * 100), '${item.paciente_nome}')" title="Confirmar Recebimnento" src="/assets/images/ico-money.svg" height="22px">
                                   </p>
                               </div>
                           </div>`);
        }
    });

    $('.kanban-action-btn').on('click', function () {
        switch ($(this).data('act')) {
            case 'wa_notify': {
                wa_notify($(this).data('token'), $(this).data('patient'), 0);
                break;
            }
        }
    });


    const medicos_count = $('#novosMedicos').find('.kanban-card').length;
    const pacientes_count = $('#novosPacientes').find('.kanban-card').length;
    const pagamentos_count = $('#novosPagamentos').find('.kanban-card').length;
    const agendamentos_count = $('#novosAgendamentos').find('.kanban-card').length;
    const acompanhamentos_count = $('#acompanhamentosMedicos').find('.kanban-card').length;


    $('.dashboard_last_update').text(`Atualizado em ${current_date} às ${current_time}`);

    $('#novosAgendamentos > div.column-header > small').text(`${agendamentos_count} registro(s)`);
    $('#novosPacientes > div > small').text(`${pacientes_count} registro(s)`);
    $('#novosMedicos > div > small').text(`${medicos_count} registro(s)`);
    $('#acompanhamentosMedicos > div > small').text(`${acompanhamentos_count} registro(s)`);
    $('#contasReceber > div > small').text(`${pagamentos_count} registro(s)`);

    Swal.close();
}


function dateDiff1(startDate, endDate) {
    var ms = Math.abs(endDate - startDate);
    return Math.floor(ms / 1000 / 60 / 60 / 24);
}

function parse_date(date) {
    const year = date.substring(0, 4);
    const month = date.substring(5, 7);
    const day = date.substring(8, 10);

    return `${day}/${month}/${year}`;
}




function show_logs_details(elem) {
    const item = atob($(elem).data('content'));

    Swal.fire({
        title: 'Detalhes do Log',
        html: `<div class="box-view">${item}</div>`,
        allowOutSideClick: false
    });
}


function syncAgenda() {
    preloader('Sincronizando...');
    $.get('/api/google_calendar/index.php', function () {
        Swal.close();
        window.location.reload();
    });
}