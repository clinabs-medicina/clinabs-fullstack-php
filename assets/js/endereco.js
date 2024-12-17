$(function () {
    $('.btns-street label[data-action="editar"]').on("click", function () {
        let endereco_token = $(this).data('id');

        let e = $(this).closest(".street-item"); Swal.fire({
            title: "Editar Endere\xe7o",
            width: "75%",
            allowOutsideClick: !1,
            html: `<div class="street-editor" style="display: block;">
    <section class="form-grid area1">
        <section class="form-group">
            <label for="endereco_nome">Nome</label>
            <input autocomplete="off" value="" type="text" id="endereco_nome" name="endereco_nome" class="form-control" maxlength="100" required="required">
        </section>

        <section class="form-group">
            <label for="endereco_nome">Tipo de Endere\xe7o</label>
            <select id="tipo_endereco" name="tipo_endereco" class="form-control" required="required">
                <option value="CASA" data-select2-id="select2-data-15-rgw4" selected="selected">CASA</option>
                <option value="ATENDIMENTO">ATENDIMENTO</option>
                <option value="RESPONSAVEL">RESPONSAVEL LEGAL</option>
            </select>
        </section>
    </section>
        <section class="form-grid area13">
                <section class="form-group">
                    <label for="cep">CEP</label>
                    <input autocomplete="off" value="" type="text" id="cep" name="cep" class="form-control" placeholder="__.____-___" maxlength="10" required="required">
                </section>
                <section class="form-group">
                    <label for="endereco">Endere\xe7o</label>
                    <input autocomplete="off" value="" type="text" id="endereco" name="endereco" class="form-control" placeholder="Digite seu Endere\xe7o" required="required">
                </section>

                <section class="form-group">
                    <label for="numero">N\xfamero</label>
                    <input autocomplete="off" value="" type="text" id="numero" name="numero" class="form-control" placeholder="N\xba" required="required">
                </section>

                <section class="form-group">
                    <label for="complemento">Complemento</label>
                    <input autocomplete="off" type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto 13" value="">
                </section>
        </section>

        <section class="form-grid area5">
                <section class="form-group">
                    <label for="cidade">Cidade</label>
                    <input autocomplete="off" value="" type="text" id="cidade" name="cidade" class="form-control" placeholder="Digite sua Cidade" required="required">
                </section>

                <section class="form-group">
                    <label for="bairro">Bairro</label>
                    <input autocomplete="off" value="" type="text" id="bairro" name="bairro" class="form-control" placeholder="Digite seu Bairro" required="required">
                </section>

                <section class="form-group">
                    <label for="uf">UF</label>
                    <select class="form-select form-control" id="uf" name="uf">
                        <option value="AC" data-select2-id="select2-data-17-rwem" selected="selected">Acre</option>
                        <option value="AL">Alagoas</option>
                        <option value="AP">Amap\xe1</option>
                        <option value="AM">Amazonas</option>
                        <option value="BA">Bahia</option>
                        <option value="CE">Cear\xe1</option>
                        <option value="DF">Distrito Federal</option>
                        <option value="ES">Esp\xedrito Santo</option>
                        <option value="GO">Goi\xe1s</option>
                        <option value="MA">Maranh\xe3o</option>
                        <option value="MT">Mato Grosso</option>
                        <option value="MS">Mato Grosso do Sul</option>
                        <option value="MG">Minas Gerais</option>
                        <option value="PA">Par\xe1</option>
                        <option value="PB">Para\xedba</option>
                        <option value="PR">Paran\xe1</option>
                        <option value="PE">Pernambuco</option>
                        <option value="PI">Piau\xed</option>
                        <option value="RJ">Rio de Janeiro</option>
                        <option value="RN">Rio Grande do Norte</option>
                        <option value="RS">Rio Grande do Sul</option>
                        <option value="RO">Rond\xf4nia</option>
                        <option value="RR">Roraima</option>
                        <option value="SC">Santa Catarina</option>
                        <option value="SP">S\xe3o Paulo</option>
                        <option value="SE">Sergipe</option>
                        <option value="TO">Tocantins</option>
                    </select>
                </section>
        </section>

        <section class="form-grid area-x3">
                        <section class="form-group"> 
                            <label for="inicio_expediente">Inicio Expediente</label>
                            <input autocomplete="off" type="time" id="inicio_expediente" class="form-control" name="inicio_expediente" placeholder="">
                        </section>

                        <section class="form-group">
                            <label for="fim_expediente">Fim de Expediente</label>
                            <input autocomplete="off" type="time" id="fim_expediente" class="form-control" name="fim_expediente" placeholder="">
                        </section>

                        <section class="form-group">
                            <label for="tipo_atendimento">Tipo de Atendimento</label>
                            <select class="form-select form-control" id="tipo_atendimento" name="tipo_atendimento">
                                <option value="ONLINE">ONLINE</option>
                                <option value="PRESENCIAL">PRESENCIAL</option>
                                <option value="ALL">ONLINE e PRESENCIAL</option>
                            </select>
                        </section>

                        <section class="form-group">
                            <label for="unidade_status">Status</label>
                            <select class="form-select form-control" id="unidade_status" name="unidade_status">
                                <option value="ATIVO">ATIVO</option>
                                <option value="INATIVO">INATIVO</option>
                            </select>
                        </section>
                      </section>
        <input autocomplete="off" type="hidden" name="isDefault" value="false">
   </div>`,
            showConfirmButton: !0,
            showCancelButton: !1,
            showDenyButton: !0,
            confirmButtonText: "SALVAR",
            denyButtonText: "cancelar",
            didOpen: function () {
                let o = JSON.parse($(e).find(".input-data").val().replaceAll("'", '"'));
                $(".swal2-html-container").find("#endereco_nome").val(o.nome), $
                    (".swal2-html-container").find("#tipo_endereco").val(o.tipo_endereco).trigger("change"),
                    $(".swal2-html-container").find("#cep").val(o.cep),
                    $(".swal2-html-container").find("#endereco").val(o.logradouro),
                    $(".swal2-html-container").find("#numero").val(o.numero),
                    $(".swal2-html-container").find("#complemento").val(o.complemento),
                    $(".swal2-html-container").find("#cidade").val(o.cidade),
                    $(".swal2-html-container").find("#bairro").val(o.bairro),
                    $(".swal2-html-container").find("#uf").val(o.uf).trigger("change"),
                    $(".swal2-html-container").find("#cep").mask("00.000-000", {
                        placeholder: "__.____-___", clearIfNotMatch: !0,
                        onComplete: function (e) {
                            $.get(`/forms/fetch.endereco.php?token=${endereco_token}`, function () {
                                $(".swal2-html-container").find("#cidade").val(e.localidade);
                                $(".swal2-html-container").find("#uf").val(e.uf).trigger("change");
                                $(".swal2-html-container").find("#bairro").val(e.bairro);
                                $(".swal2-html-container").find("#numero").val(e.numero);

                                $(".swal2-html-container").find("#inicio_expediente").val(e.inicio_expediente);
                                $(".swal2-html-container").find("#fim_expediente").val(e.fim_expediente);
                                $(".swal2-html-container").find("#tipo_atendimento").val(e.tipo_atendimento);
                                $(".swal2-html-container").find("#unidade_status").val(e.unidade_status);
                                $(".swal2-html-container").find("#numero").val(e.numero);
                            });
                        }
                    })
            }
        }).then(function (o) {
            if (o.isConfirmed) {
                let t = JSON.parse($(e).find(".input-data").val().replaceAll("'", '"')), n = { nome: $(".swal2-html-container").find("#endereco_nome").val(), cep: $(".swal2-html-container").find("#cep").val(), logradouro: $(".swal2-html-container").find("#endereco").val(), numero: $(".swal2-html-container").find("#numero").val(), complemento: $(".swal2-html-container").find("#complemento").val(), cidade: $(".swal2-html-container").find("#cidade").val(), bairro: $(".swal2-html-container").find("#bairro").val(), uf: $(".swal2-html-container").find("#uf").val(), tipo_endereco: $(".swal2-html-container").find("#tipo_endereco").val(), id: t.id, user_token: t.user_token, isDefault: t.isDefault, token: t.token }; console.log(n), $(e).find(".input-data").val(JSON.stringify(n).replaceAll('"', "'"))
            }
        })
    }), $('.btns-street label[data-action="excluir"]').on("click", function () { let e = $(this).closest(".street-item"); Swal.fire({ title: "Aten\xe7\xe3o", text: "Deseja Excluir Este Endere\xe7o?", showConfirmButton: !0, showCancelButton: !1, showDenyButton: !0, confirmButtonText: "SIM", denyButtonText: "N\xc3O", icon: "question", allowOutsideClick: !1 }).then(function (o) { if (o.isConfirmed) { let t = JSON.parse($(e).find(".input-data").val().replaceAll("'", '"')); t.delete = !0, $(e).find(".input-data").val(JSON.stringify(t).replaceAll('"', "'")), $(e).fadeOut() } }) }), $('.btns-street label[data-action="def"]').on("click", function () { let e = $(this).closest(".street-item"); $(".street-item").each(function () { let e = JSON.parse($(this).find(".input-data").val().replaceAll("'", '"')); e.isDefault = !0, $(this).find(".default-street").text(" "), $(this).find(".input-data").val(JSON.stringify(e).replaceAll('"', "'")) }); let o = JSON.parse($(e).find(".input-data").val().replaceAll("'", '"')); o.isDefault = !0, $(e).find(".input-data").val(JSON.stringify(o).replaceAll('"', "'")), $(e).find(".default-street").text("(Padr\xe3o)") })
});