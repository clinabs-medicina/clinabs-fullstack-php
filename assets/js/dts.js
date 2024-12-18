$(".table").each(function () {
    $(this).dataTable({
        dom: "Bfrtip",
        stateSave: true,
        responsive: true,
        processing: false,
        retrieve: true,
        serverSide: false,
        buttons: ["colvis"],
    });
});



$(".table-default").each(function () {
    $(this).dataTable({
        dom: "Bfrtip",
        stateSave: true,
        responsive: true,
        processing: false,
        retrieve: true,
        serverSide: false,
        paging: false,
        searching: false,
    });
});


$("#tablePrescricao.prescricao").dataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    buttons: ["colvis"],
});



$("#tablePrescricaoWb").dataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    buttons: ["colvis"],
});

$("#tablePrescricaoSR.prescricao").dataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    buttons: ["colvis"],
});


$("#tablePrescricao.acompanhamento").dataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    buttons: ["colvis"],
});


$("#tableRastreio").dataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    paging: true,
    buttons: ["colvis"],
});

new DataTable("#presc-wb");


addTableButton("#tableRastreio", {
    icon: "plus",
    hint: "Novo Evento",
    click: "newTrackItem",
});

addTableButton("#tablePrescricao.prescricao", {
    icon: "plus",
    hint: "Nova Prescrição/Acompanhamento Médico",
    click: "newPrescFunc",
});

addTableButton("#tablePrescricao", {
    icon: "signature-pen",
    hint: "Assinar com Certificado Digital",
    click: "certificado",
});


if ($("#tablePrescricao.prescricao").data("doc")) {
    addTableButton("#tablePrescricao.prescricao", {
        icon: "download",
        hint: "Baixar Receita para Assinar",
        click: "downloadReceita",
    });

    addTableButton("#tablePrescricao.prescricao", {
        icon: "upload",
        hint: "Atualizar Receita Assinada",
        click: "uploadReceita",
    });

    addTableButton("#tablePrescricao.prescricao", {
        icon: "print",
        hint: "Imprimir Receita",
        click: "printPrescFuncSigned",
    });

    addTableButton("#tablePrescricao.prescricao", {
        icon: "whatsapp",
        hint: "Enviar Receita para seu WhatsApp para Assinar",
        click: "WaSendReceita",
    });
} else if ($("#tablePrescricao.prescricao").data("upld")) {

} else {
    addTableButton("#tablePrescricao.prescricao", {
        icon: "download",
        hint: "Baixar Receita para Assinar",
        click: "downloadReceita",
    });

    addTableButton("#tablePrescricao.prescricao", {
        icon: "upload",
        hint: "Enviar Receita Assinada",
        click: "uploadReceita",
    });
}

addTableButton("#tablePrescricaoSR.prescricao", {
    icon: "plus",
    hint: "Nova Prescrição/Acompanhamento Médico",
    click: "newPrescFuncSR",
});

addTableButton("#tablePrescricao.prescricao", {
    icon: "file-pdf",
    hint: "Enviar Receita,Exame e Laudo",
    click: "addPrescFunc2(false, 'Enviar Anexo ao Prontuário')",
});

addTableButton("#tablePrescricao.prescricao", {
    icon: "file-medical",
    hint: "Atestado",
    click: "newAtestado()",
});

addTableButton("#tablePrescricaoSR.prescricao", {
    icon: "print",
    hint: "Imprimir Receita",
    click: "printPrescFunc2",
});



$("#tablePedidos").DataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    buttons: [
        "colvis",
        "pageLength",
        $('meta[name="user"]').attr("content") === "FUNCIONARIO" ? "new" : "",
    ],
});

$("#tablePedidos_length > label").css("font-size", "0px !important");


$("#tableMedicos").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:first-child)",
    },

    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO MÉDICO",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/medico";
            },
        },
    ],
});

$("#tablePacientes").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:last-child)",
    },
    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO PACIENTE",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/cadastro-paciente";
            },
        },
    ],
});

$("#faturamento-lf").DataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    autoFill: {
        columns: ":not(:last-child)",
    },
    columnDefs: [
        {
            target: 0,
            visible: false,
            searchable: false,
        },
    ],
    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "EXTRATO",
            action: function (e, dt, node, config) {
                window.location = "?extrato";
            },
        },
    ],
});

$("#tableFinanceiro").DataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    autoFill: {
        columns: ":not(:last-child)",
    },
    columnDefs: [
        {
            target: 0,
            visible: false,
            searchable: false,
        },
    ],
    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "RELATÓRIO",
            action: function (e, dt, node, config) {
                window.location = "/";
            },
        },
    ],
});

$("#tableProdutos").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:first-child)",
    },

    buttons: ["colvis", "pageLength", "print"],
});

$("#tableFuncionarios").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:last-child)",
    },

    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO FUNCIONARIO",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/funcionario";
            },
        },
    ],
});

$("#tableUsuarios").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:last-child)",
    },

    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO USURIO",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/usuario";
            },
        },
    ],
});


if ($("#tablePedidos").length > 0) {
    $.fn.dataTable.ext.buttons.new = {
        text: "Novo",
        action: function (e, dt, node, config) {
            window.location = "/produtos";
        },
    };

    $("#tablePedidos").DataTable({
        dom: "Bfrtip",
        stateSave: true,
        responsive: true,
        processing: false,
        retrieve: true,
        serverSide: false,
        buttons: [
            "colvis",
            "pageLength",
            $('meta[name="user"]').attr("content") === "FUNCIONARIO" ? "new" : "",
        ],
    });

    $("#tablePedidos_length > label").css("font-size", "0px !important");
}

$("#tableMedicos").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:first-child)",
    },

    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO MÉDICO",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/medico";
            },
        },
    ],
});

$("#tablePacientes").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:last-child)",
    },
    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO PACIENTE",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/cadastro-paciente";
            },
        },
    ],
});

$("#faturamento-lf").DataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    autoFill: {
        columns: ":not(:last-child)",
    },
    columnDefs: [
        {
            target: 0,
            visible: false,
            searchable: false,
        },
    ],
    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "EXTRATO",
            action: function (e, dt, node, config) {
                window.location = "?extrato";
            },
        },
    ],
});

$("#tableFinanceiro").DataTable({
    dom: "Bfrtip",
    stateSave: true,
    responsive: true,
    processing: false,
    retrieve: true,
    serverSide: false,
    autoFill: {
        columns: ":not(:last-child)",
    },
    columnDefs: [
        {
            target: 0,
            visible: false,
            searchable: false,
        },
    ],
    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "RELATÓRIO",
            action: function (e, dt, node, config) {
                window.location = "/";
            },
        },
    ],
});

$("#tableProdutos").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:first-child)",
    },

    buttons: ["colvis", "pageLength", "print"],
});

$("#tableFuncionarios").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:last-child)",
    },

    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO FUNCIONARIO",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/funcionario";
            },
        },
    ],
});

$("#tableUsuarios").DataTable({
    dom: "Bfrtip",

    stateSave: true,

    responsive: true,

    processing: false,

    retrieve: true,

    serverSide: false,

    autoFill: {
        columns: ":not(:last-child)",
    },

    buttons: [
        "colvis",
        "pageLength",
        "print",
        {
            text: "NOVO USURIO",
            action: function (e, dt, node, config) {
                window.location = "/cadastro/usuario";
            },
        },
    ],
});






function addTableButton(elem, btnInfo) {
    if (elem) {
        let btn = document.createElement("button");
        btn.type = "button";
        btn.title = btnInfo.hint;
        btn.className = "btn-table-item";
        btn.innerHTML = `<img src="/assets/images/icons?icon=${btnInfo.icon}&color=white" class="icon-cli"></i>`;

        if (btnInfo.click.indexOf("(") !== -1) {
            btn.setAttribute("onclick", `${btnInfo.click}`);
        } else {
            btn.setAttribute("onclick", `${btnInfo.click}()`);
        }

        let id = $(`${elem}`).attr("id");

        $(`#${id}_filter`).append(btn);
    }
}