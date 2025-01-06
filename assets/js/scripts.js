$(document).ready(function () {
  $("img.ico-hover.user").on("click", function (e) {
    e.preventDefault();
  });

  $('.anexo-item-view').on('click', function () {
    let src = $(this).data('file');

    window.open(`/data/docs/${src}`, '_pdfViwer', 'width=800,height=600');
  });

  $('.box-mediclist').each(function () {
    if ($(this).find('input').val() === 'hidden') {
      $(this).remove();
    }
  });


  $('#responsavel_cpf, #cpf').mask('000.000.000-00', {
    placeholder: '___.___.___-__',
    clearIfNotMatch: true,
    onComplete: function (cpf) {
      if (!validateCPF(cpf)) {
        Swal.fire({
          title: 'Atenção',
          icon: 'warning',
          text: 'CPF Inválido',
          allowOutSideClick: false
        }).then(function () {
          $('#cpf').val('').focus();
        })
      }
    }
  });

  $('.btn-play-wb').on('click', function () {
    //$('.btn-play-wb').hide();
    //$('#wb-view').attr('src', $('#wb-view').data('src'));
    //$('#wb-view').show();

  });

  $('.btn-play-wb').on('click', async () => {
    const player = document.querySelector("#wb-view");
    $('#wb-view').attr('src', $('#wb-view').data('src'));
    $('#wb-view').css('width', '99.95%');
    $('#wb-view').css('height', '580px !important');

    const pipWindow = await documentPictureInPicture.requestWindow({
      width: 600,
      height: 580,
      disableAutoFullscreen: true,
      disableAutoPIP: true,
      disablePipScaling: true,
      disablePipControls: false,
      disablePipAutoClose: true,
      disablePipAutoCloseOnBlur: false,
      disablePipAutoCloseOnEscape: false,
      disallowReturnToOpener: true,
    });

    pipWindow.addEventListener("pagehide", (event) => {
      const playerContainer = document.querySelector("#wb-play");
      const pipPlayer = event.target.querySelector("#wb-view");
      $("#wb-view").css('height', '580px');
      playerContainer.parentNode.append(pipPlayer);
    });

    pipWindow.document.body.append(player);
  });



  if ($('textarea[name="descricao_html"]').length > 0) {
    quill_editor(
      $('textarea[name="descricao_html"]'),
      $("#descricao_completa")
    );
  }

  $(".fwz-btn, .btn-edit-form, .paginate_button").on("click", function () {
    $("html, body").animate(
      {
        scrollTop: 180,
      },
      "1000"
    );
  });

  $("#moeda").on("change", function () {
    let moeda = this.value;

    $('input[mask-money="true"]').each(function () {
      $(this).attr("data-currency", moeda);
      $(this).attr("data-value", this.value.replace(/[^0-9]/g, ""));
      $(this).val(formatMoney(this.value, moeda));
    });

    $('input[mask-money="true"]').on("input", function () {
      $(this).val(formatMoney(this.value, moeda));
    });
  });

  $("button.btn-table i.fa.fa-whatsapp").on("click", function () {
    // Enviar Receita via WhatsApp
  });

  $(".blog-user-info a").on("click", function (evt) {
    evt.preventDefault();

    let uri = $(this).attr("href");
    /*
    Swal.fire({
      title: 'Visualizar Anexo',
      width: '70%',
      height: '90%',
      backgroundColor: '#f0f0f0',
      html: uri.toLowerCase().indexOf('.pdf') !== -1 ? `<embed src="${uri}#toolbar=0" type="application/pdf" width="100%" height="90%"></embed>`: `<img src="${uri}" height="350px">`,
      allowOutsideClick: false
    });
    */

    window.open(uri);
  });

  $(".estrelas")
    .find('input[type="radio"]')
    .on("click", function () { });

  $('input[type="search"][data-search]').on("input", function () {
    let selector = $(this).data("search");
    let val = this.value.toLowerCase();

    $(`${selector}`).each(function () {
      if (this.textContent.toLowerCase().indexOf(val) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  $(".blog-btns a[data-post-action]").on("click", function () {
    let id = $(".blog-posts").attr("data-id");
    let action = $(this).attr("data-post-action");
    let medico_token = $('meta[name="user-id"]').attr("content");

    Swal.fire({
      title: action == "comment" ? "Comentar Post" : "Fazer uma Pergunta",
      input: "textarea",
      showConfirmButton: true,
      showDenyButton: true,
      confirmButtonText: "ENVIAR",
      denyButtonText: "NÃO",
      didOpen: function () {
        $("#swal2-textarea").attr(
          "placeholder",
          action == "comment"
            ? "Escreva aqui seu Comentário...."
            : "Escreva aqui sua pergunta...."
        );
      },
    }).then(function (evt) {
      if (evt.isConfirmed) {
        $.post(
          action == "comment"
            ? "/forms/blog.post.comment.php"
            : "/forms/blog.post.question.php",
          {
            post_id: id,
            action: action,
            text: evt.value,
            medico_token: medico_token,
          }
        ).done(function (resp) {
          toast(resp.text, resp.status).then(function () {
            window.location.reload();
          });
        });
      }
    });
  });

  $(".blog-btns small a[data-item]").on("click", function () {
    $("fieldset").hide();
    $(".blog-btns small a[data-item]").removeClass("active");
    $(`${$(this).data("item")}`).show();

    $([document.documentElement, document.body]).animate(
      {
        scrollTop: $(`${$(this).data("item")}`).offset().top - 150,
      },
      2000
    );

    $(this).addClass("active");
  });

  $(".payment-button").bind("click", function () {
    $("#valorTotal").val($(this).find(".payment-value").val());

    $("b.valor-total").text($(this).find(".payment-value").data("total"));
    $("span.subtotal").text($(this).find(".payment-value").data("value"));
    $("span.frete").text($(this).find(".payment-value").data("frete"));
    $("span.desconto").text($(this).find(".payment-value").data("desc"));

    $(".payment-button.btn-payment-active").each(function () {
      $(this).removeClass("btn-payment-active");
    });

    $(this).addClass("btn-payment-active");
  });

  $(".form-search").on("keyup", function () {
    let finder = $(".page-box");

    let val = this.value.toLowerCase();

    $(finder).each(function () {
      if ($(this).text().toLowerCase().indexOf(val) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });

    if (this.value.length === 0) {
      $(finder).each(function () {
        $(this).show();
      });
    }
  });

  if (localStorage.getItem("visited_clinabs") === null) {
    localStorage.setItem("visited_clinabs", 1);

    if (localStorage.getItem("redirect_uri") !== null) {
      window.location = localStorage.getItem("redirect_uri");
    } else {
      //window.location = localStorage.getItem('redirect_uri') ?? '/';
    }
  }

  //$('#nacionalidade').trigger('change').val($(this).data('cell'));

  $("#cpf, #doc_cpf").mask("000.000.000-00", {
    placeholder: "___.___.___-__",
    clearIfNotMatch: true,
    onComplete: function (cpf) {
      console.log(cpf);

      if (!validateCPF(cpf)) {
        Swal.fire({
          title: 'Atenção',
          icon: 'warning',
          text: 'CPF Inválido',
          allowOutSideClick: false
        }).then(function () {
          $("#cpf, #doc_cpf").val('').focus();
        })
      }
    }
  });

  $("#doc_validation").on("change", function () {
    if ($(this).is(":checked")) {
      $('label[for="doc_rg_frente"]').find("strong").text("Anexo CNH (Frente)");
      $('label[for="doc_rg_verso"]').parent().hide();
      $('label[for="doc_cpf_frente"]').parent().hide();
      $('label[for="doc_cpf_verso"]').parent().hide();
    } else {
      $('label[for="doc_rg_frente"]').find("strong").text("Anexo RG (Frente)");
      $('label[for="doc_rg_verso"]').parent().show();
      $('label[for="doc_cpf_frente"]').parent().show();
      $('label[for="doc_cpf_verso"]').parent().show();
    }
  });

  $(".btn-cupom").on("click", function () {
    $.get(
      `/formularios/carrinho_desconto.php?cupom=${$(
        "#cupomDesconto"
      ).val()}&valor_total=${$("#valorTotal").val()}`
    ).done(function (evt) {
      if (evt.status === "error") {
        Swal.fire(evt);
      } else {
        $("#cupomDesconto").attr("readonly", true);
        $(".btn-cupom").attr("disabled", true);
        $("#valorTotal").val(
          evt.tipo === "PORCENTAGEM" ? evt.valor_total * 100 : evt.valor_total
        );
        $(".subtotal").text(`R$ ${evt.valor_pedido_formatado}`);
        $(".desconto").text(
          evt.tipo === "PORCENTAGEM"
            ? `(-${evt.desconto}%) = R$ ${evt.desconto_formatado}`
            : `-R$ ${evt.desconto_formatado}`
        );
        $(".valor-total").text(`R$ ${evt.valor_total_formatado}`);
        $(".payment_valor_pix").text(`R$ ${evt.valor_total_formatado}`);
        $(".payment_valor_cc").text(`R$ ${evt.valor_total_formatado}`);
      }
    });
  });

  $(".pacientes-doc .download-anexo-item").on("click", function () {
    if ($(this).data("file").length > 5) {
      let ftype = $(this).data("file");
      let token = $(this).data("token");
      let fname = $(this).data("attachment");

      let a = document.createElement("a");
      a.setAttribute("download", "");
      a.setAttribute(
        "href",
        `/forms/download-docs.php?token=${token}&type=${ftype}&filename=${fname}`
      );

      a.click();
    } else {
      Swal.fire({
        title: "Atenão",
        text: "Documento não foi Enviado pelo usuário!.",
        icon: "warning",
        allowOutSideClick: false,
      });
    }
  });











  $("small[data-time]").each(function () {
    let elem = this;

    setInterval(function () {
      let date = countUp($(elem).data("time"));
      $(elem).text(date);
    }, 10000);
  });

  $("table").each(function () {
    let pages = $(".paginate_button").length;

    if (pages < 2) {
      $('a[data-dt-idx="previous"]').prop("disabled", true);
      $('a[data-dt-idx="next"]').prop("disabled", true);
    }
  });

  setInterval(function () {
    $.get("/perfil/wa-status.php").done(function (data) {
      if ($("#wa-status").html() !== data) {
        $("#wa-status").html(data);
      }
    });
  }, 60000);



  $('input[name="filter_ag"]').on("change", function () {
    $('select[name="select_filter"]').select2({
      placeholder: "Selecione uma Opção...",
      sorter: (data) => data.sort((a, b) => a.text.localeCompare(b.text)),
      language: "pt",
      ajax: {
        url: `/forms/fetch.tb.php?tb=${this.value}&key=${$(this).data("key")}`,
        success: function (data) { },
      },
    });

    //$('select[name="select_filter"]').select2('open');
  });

  $('label[data-action="padrao"]').on("click", function () {
    let id = $(this).data("token");
    let elem = $(`#${id}`);

    $(".street-item").each(function () {
      $(this).removeClass("selected");
    });

    $(`#${id}`).addClass("selected");
  });

  $(".listmedic-boxlink").on("click", function () {
    if ($(this).text() == "PRESENCIAL") {
      $('label[data-modalidade="presencial"]').each(function () {
        $(this).hide();
      });
      $('label[data-modalidade="online"]').each(function () {
        $(this).show();
      });
    } else {
      $('label[data-modalidade="presencial"]').each(function () {
        $(this).hide();
      });
      $('label[data-modalidade="online"]').each(function () {
        $(this).show();
      });
    }
  });

  $("#telefone").mask("(00) 0000-0000", {
    placeholder: "(__) ____-____",
    clearIfNotMatch: true,
  });

  Slider("#slider1", 10000);

  $("textarea").on("input", function () {
    let rem = $(this).attr("maxlength") - $(this).val().length;

    $(`small[data-area="${this.name}"]`).html(`${rem} caracteres restantes.`);
  });

  $("input[data-form]").on("click", function () {
    let form = $(this).data("form");
    let token = $(this).data("token");
    let dt = $(this).val();
    let modalidade = $('input[name="atendimento"]').val();

    if ($(`#${form} input[name="atendimento"]:checked`).length == 0) {
      Swal.fire({
        title: "Atenção",
        html: "Por favor, selecione como gostaria de ser atendido se  <b>presencial ou online</b>",
        imageUrl: "/assets/images/warning.svg",
        imageWidth: "120px",
        confirmButtonText: "Fechar Janela",
        showConfirmButton: true,
      });
    } else {
      let url = `/agenda/consulta?dt=${dt}&${location.href.split("?")[1]
        }&medico_token=${token}&atendimento=${modalidade}`;

      localStorage.setItem("url_redirect", url);

      $(`#${form}`).submit();
    }
  });

  $("label[data-form]").on("click", function () {
    let form = $(this).data("form");

    let input = $(this).attr("for");

    let valor = $(`#${form} input[name="atendimento"]:checked`).val();

    $(`#${form} label[for]`).each(function () {
      $(this).removeAttr("checked");
    });

    $(`#${form} label[for="${input}"]`).attr("checked", true);
  });

  setInterval(function () {
    $("#data_agendamento").attr("disabled", $("#nome_medico").val() == null);

    $("#hora_agendamento").attr("disabled", $("#nome_medico").val() == null);

    $("#duracao_agendamento").attr("disabled", $("#nome_medico").val() == null);
  }, 30000);

  if (window.location.pathname === "/agenda/consulta") {
    // alert(`${localStorage.getItem('agendamento_data')} - ${localStorage.getItem('agendamento_token')}`);

    $("#ag_data_agendamento").val(localStorage.getItem("agendamento_data"));

    $("#nome_medico")
      .val(localStorage.getItem("agendamento_token"))
      .trigger("change");

    $("#nome_medico").attr("disabled", true);
  }

  $('#nome_medico[data-schedule="calendar"]').on("change", function () {
    $.get(
      `/api/agenda/calendario_medico.php?medico_token=${this.value}`,
      function (resp) {
        $("#calendar_schedule").html(resp);
      }
    );
  });

  $("#nome_medico").on("change", function () {
    $("#data_agendamento").attr("disabled", $("#nome_medico").val() == null);

    $("#hora_agendamento").attr("disabled", $("#nome_medico").val() == null);

    $("#duracao_agendamento").attr("disabled", $("#nome_medico").val() == null);
  });

  $("button[data-act]").on("click", function () {
    let editor = $(this).data("item");

    let act = $(this).data("act");

    if (act === "edit") {
      $(`#${editor}`).removeAttr("readonly");

      $(`#${editor}`).css("width", "90% !important");

      $(this).attr("disabled", true).css("filter", "grayscale(1)");
    } else {
      $(`fieldset[data-editor="${editor}"]`).remove();
    }
  });

  $("#hora_agendamento").on("change", function () {
    $("#hora_agendamento_input").val(this.value);
  });

  $("#data_agendamento").on("change", function () {
    let medico_token = $("#nome_medico").val().split("|")[0];

    if (!isNaN(Date.parse(this.value))) {
      $.getJSON(
        `/forms/data.agendamento.php?date=${this.value}&token=${medico_token}`,
        function (obj) {
          $("#hora_agendamento").html("");

          let s = $("#hora_agendamento_input").val();

          Object.keys(obj).forEach(function (k) {
            $("#hora_agendamento").append(
              `<option${obj[k] ? " disabled" : ""}${obj[k] ? ' style="color: red"' : ""
              }${s == k ? " selected" : ""} value="${k}">${k}</option>`
            );
          });
        }
      );
    } else {
      Swal.fire({
        title: "Atenção",

        icon: "warning",

        text: "Data Inválida!",
      }).then(function () {
        $("#data_agendamento").val("");
      });
    }
  });

  $("input[data-ico]").each(function () {
    let ico = $(this).data("ico");

    if (this.tagName.toLowerCase() === "select") {
      $(`#${this.id}`)
        .parent()
        .find(".select2")
        .attr(
          "style",
          `background-repeat: no-repeat !important;background-size: 22px 22px !important;background-position: left !important;background-position-x: 10px !important;text-indent: 20px !important;background-image: url('/assets/images/ico-${ico}.svg') !important;backround-repeat: no-repeat !important;`
        );

      $(this).removeAttr("data-ico");
    } else {
      $(this).attr(
        "style",
        `background-repeat: no-repeat !important;background-size: 22px 22px !important;background-position: left !important;background-position-x: 10px !important;text-indent: 20px !important;background-image: url('/assets/images/ico-${ico}.svg') !important;backround-repeat: no-repeat !important;`
      );

      $(this).removeAttr("data-ico");
    }
  });

  $("select").on("change", function () {
    for (let i = 0; i < this.options.length; i++) {
      let opt = this.options[i];

      if (this.options.selectedIndex == i) {
        $(opt).attr("selected", true);
      } else {
        $(opt).removeAttr("selected");
      }
    }
  });

  $("i[data-text]").on("click", function () {
    let text = $(this).data("text");

    Swal.fire({
      icon: "info",

      text: text,

      allowOutsideClick: false,
    });
  });

  $('input[name="agendamento"]').on("change", function () {
    let id = this.id;

    $("#agendamento").css(
      "visibility",
      this.id == "agendamento-on" ? "visible" : "hidden"
    );

    $("#agendamento")
      .find("input, select")

      .each(function () {
        if (id === "agendamento-on") {
          $(this).attr("required", "required");
        } else {
          $(this).removeAttr("required");
        }
      });
  });

  $("#prazo_entrega").mask("## dia(s)", {
    reverse: true,
    placeholder: "3 dia(s)",
    clearIfNotMatch: true,
  });

  $("input[data-money]").mask("#.##0,00", {
    reverse: true,
    placeholder: "0.00",
    clearIfNotMatch: true,
  });

  $('input[name="capacidade"]').mask(`### ${$("#unidade_medida").val()}`, {
    placeholder: "",
    clearIfNotMatch: true,
  });

  $('.estrelas input[type="radio"]').on("change", function () {
    let stars = $(this).val();
    /*
        $.getJSON(`/forms/form.stars.php?product_id=${$(this).data("product")}`).done(function (data) {
            toast("Produto Adicionado como Favorito!", "success");
        });
        */
  });

  $("#nome_completo").on("input", function () {
    this.value = this.value.toUpperCase();
  });

  $("#email").on("input", function () {
    this.value = this.value.toLowerCase();
  });

  $(".btn-edit-form").on("click", function () {
    $(this).attr("disabled", true);

    $(".tabControl.locked").removeClass("locked");

    $(".tab-disabled").each(function () {
      $(".tab-disabled").removeClass("tab-disabled");
    });

    $(".btn-save-form").removeAttr("disabled");

    enableAll();
  });

  $(".ico-product-fav").on("click", function () {
    let product_id = $(this).data("product");

    $.getJSON(
      `/forms/form.favoritos.php?product_id=${$(this).data("product")}`
    ).done(function (data) {
      toast("Produto Adicionado como Favorito!", "success");
    });
  });

  $("#unidade_medida").on("change", function () {
    $("#capacidade").mask(`### ${this.value}`, {
      reverse: true,
      placeholder: `0.00 ${this.value.toLowerCase()}`,
      clearIfNotMatch: true,
    });

    $("#capacidade").removeAttr("disabled");

    $("#capacidade").removeAttr("title");
  });

  $("*[data-icon]").each(function () {
    $(this).css({
      "background-image": `url("/assets/images/ico-${$(this).data(
        "icon"
      )}.svg")`,

      "background-repeat": "no-repeat",

      "background-size": "32px 32px",

      "text-indent": "28px",

      "background-position": "left 8px center",
    });
  });

  $("#nfe_xml").on("change", function (evt) {
    let myFile = this.files[0];

    let reader = new FileReader();

    reader.readAsText(myFile);

    reader.onload = function () {
      let xml = parseXmlToJson(reader.result);

      let emitente = xml.nfeProc.NFe.infNFe.emit;

      let destinatario = xml.nfeProc.NFe.infNFe.dest;

      let faturas = xml.nfeProc.NFe.infNFe.cobr;

      let nfe = xml.nfeProc.NFe.infNFe.ide;

      let produtos = xml.nfeProc.NFe.infNFe.det;

      $("#codigo_produto").val(produtos.prod.cEAN);

      $("#nome_produto").val(produtos.prod.cProd);

      $("#fornecedor").val(emitente.xNome);

      $("#valor_compra").val(produtos.prod.vProd);

      $("#valor_frete").val("");

      $("#nfe").val(nfe.nNF);

      $("#data_emissao_nfe").val(
        new Date(nfe.dhEmi.split("T")[0]).toLocaleDateString("pt-BR")
      );

      $("#data_recebimento_nfe").val(
        new Date(nfe.dhSaiEnt.split("T")[0]).toLocaleDateString("pt-BR")
      );
    };
  });

  /* Procurar Produtos */

  $("#produtoSearch").on("input", function () {
    let selector = $($(this).data("search"));

    let val = this.value;

    $(".produto-flex").each(function () {
      let item = this;

      $(item)
        .find("h2")

        .filter(function () {
          return this.innerText.toLowerCase().indexOf(val) !== -1;
        })

        .each(function () {
          $(item).show();
        });

      $(item)
        .find("h2")

        .filter(function () {
          return this.innerText.toLowerCase().indexOf(val) == -1;
        })

        .each(function () {
          $(item).hide();
        });
    });
  });

  $("#product-new-btn").on("click", function () {
    $.get("novo.php", function (data) {
      Swal.fire({
        html: data,

        width: "80%",

        height: "50%",

        allowOutsideClick: false,

        didOpen: function () {
          $(".swal2-html-container #signUpForm").css({
            padding: 0,

            margin: 0,
          });
        },
      });
    });
  });

  /* Editar Perfil */

  $("#image-file-input").on("change", function (e) {
    var file = e.target.files[0];

    var reader = new FileReader();

    reader.onload = function (e) {
      var data = e.target.result;

      let cropper = new Croppie(
        document.getElementById("image-cropper-profile"),
        {
          viewport: {
            width: 280,
            height: 280,
            type: "circle",
          },

          boundary: {
            width: 300,
            height: 300,
          },

          showZoomer: true,

          enableResize: false,

          enableOrientation: true,

          mouseWheelZoom: "ctrl",
        }
      );

      cropper.bind({
        url: data,
      });

      $(".modal-image-editor-cancel-btn").on("click", function (e) {
        $("#image-editor").hide();
      });

      $(".modal-image-editor-save-btn").on("click", function (e) {
        cropper

          .result({
            type: "blob",
            size: {
              width: 512,
              height: 512,
              type: "square",
            },

            format: "png",
            backgroundColor: "#f4f2f5",
            circle: false,
            quality: 10,
          })

          .then(function (blob) {
            let readerx = new FileReader();

            readerx.onload = function (event) {
              $(".profile-image").css(
                "background-image",
                `url("data:image/jpeg;base64,${window.btoa(
                  event.target.result
                )}")`
              );

              $("#profileImage").val(
                `data:image/jpeg;base64,${window.btoa(event.target.result)}`
              );

              $("#image-editor").hide();
            };

            readerx.readAsBinaryString(blob);
          });
      });
    };

    $("#image-editor").show();

    reader.readAsDataURL(file);
  });
  /*
    $(".download-anexo-item").on("click", function() {
        let doc = $(this).data("file");
 
        let id = $(this).attr("for");
 
        if ($("#btn-save-profile").is(":disabled")) {
            if (doc.length > 0) {
                $.get(`/data/images/docs/${doc}`).done(function() {
                    Swal.fire({
                        width: "600px",
                        height: "512px",
                        html: doc.indexOf(".pdf") !== -1 ?
                            `<iframe class="doc-viewer" src="/data/images/docs/${doc}#toolbar=0&navpanes=0&scrollbar=0" width="512px" height="400px"></iframe>` :
                            `<img src="/data/images/docs/${doc}" width="100%">`,
                        showConfirmButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Baixar",
                        cancelButtonText: "Fechar",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            let link = document.createElement("a");
 
                            link.setAttribute("href", `/forms/doc.php?doc=/data/images/docs/${doc}`);
 
                            link.setAttribute("download", doc);
 
                            link.click();
                        }
                    });
                });
            } else {
                Swal.fire({
                    title: "Atenço",
                    text: "Voc No Enviou Este Documento Ainda.",
                    icon: "warning",
                });
            }
        }
    });
*/
  $("#mobile-toggle-menu").on("click", function () {
    if ($(".menu-header").css("display") == "none") {
      $(".menu-header").show();
    } else {
      $(".menu-header").hide();
    }
  });

  $("#user-link").on("click", function () {
    if (confirm("Deseja Encerrar a Sessão Atual?")) {
      window.location = "/logout";
    }
  });

  // Formatar Inputs

  $("#cep").mask("00.000-000", {
    placeholder: "__.____-___",
    clearIfNotMatch: true,
    onComplete: function () {
      let cep = $("#cep").val().replace(/\D/g, "");

      if (cep.length == 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
          .then((response) => response.json())

          .then((cep) => {
            $("#endereco").val(cep.logradouro);

            $("#cidade").val(cep.localidade);

            $("#uf").val(cep.uf).trigger("change");

            $("#bairro").val(cep.bairro);

            $("#numero").focus();
          })

          .catch((error) => console.error("Error:", error))

          .finally((x) => Swal.close());
      }
    },
  });

  $(".wizard-form #cpf").mask("000.000.000-00", {
    placeholder: "___.___.___-__",
    clearIfNotMatch: true,
    onComplete: function (cpf) {
      $.get(
        `/forms/paciente.dados.php?paciente_cpf=${cpf}`,
        function (paciente) {
          if (paciente.status === "warning") {
            Swal.fire({
              title: "Atenção",
              icon: "question",
              text: paciente.text,
              didOpen: function () { },
              showConfirmButton: true,
              showDenyButton: true,
              confirmButtonText: "SIM",
              denyButtonText: "NÃO",
              allowOutsideClick: false,
            }).then(function (result) {
              if (result.isConfirmed) {
                $("#cpf").before(
                  '<input type="hidden" name="action" value="update">'
                );
                $("#cadastroPaciente").attr(
                  "action",
                  "/forms/form.cadastro.update.php"
                );
                $(".btn-step-prev ").before(
                  '<input type="hidden" name="tabela" value="PACIENTES">'
                );
                $(".btn-step-prev ").before(
                  `<input type="hidden" name="profileImage" id="profileImage" value="${paciente.data["profile_image"]}">`
                );

                Object.keys(paciente.data).forEach(function (k, v) {
                  if (k.startsWith("doc_")) {
                    if ($(`input[name="${k}"]`).length > 0) {
                      $(`input[name="${k}"]`).val(paciente.data[k]);
                      $(`label[for="${k}"]`)
                        .find("img")
                        .attr("src", `/data/images/docs/${paciente.data[k]}`);
                    } else {
                      $(`label[for="${k}"]`).append(
                        `<input type="hidden" name="${k}" value="${paciente.data[k]}">`
                      );
                      $(`label[for="${k}"]`)
                        .find("img")
                        .attr("src", `/data/images/docs/${paciente.data[k]}`);
                    }
                  } else {
                    $(`#${k}`)
                      .val(paciente.data[k])
                      .trigger("change")
                      .removeAttr("disabled");
                  }
                });
              }
            });
          } else {
            Swal.close();
          }
        }
      );
    },
  });

  // Preencher o Cliente Referncia

  if (localStorage.key("cli_fid") !== null) {
    $(".product-grid-item-button").each(function () {
      $(this).attr("data-ref", localStorage.getItem("cli_fid"));
    });
  }

  // Remover item do Carrinho

  $('a[data-action="remove"]').on("click", function () {
    let pid = $(this).data("item");

    let item = $(`#${$(this).data("item")}`);

    if (pid == "all") {
      Swal.fire({
        title: "Atenção",

        text: "Tem certeza que Deseja remover todos os produtos do Carrinho?",

        icon: "question",

        showConfirmButton: true,

        showDenyButton: true,

        showCancelButton: false,

        confirmButtonText: "SIM",

        denyButtonText: "NÃO",
      }).then(function (result) {
        if (result.isConfirmed) {
          Swal.fire({
            title: "",

            html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Removendo...</span></div>",

            width: "256px",

            showCancelButton: false,

            showConfirmButton: false,

            closeOnClickOutside: false,

            didOpen: function () {
              preloader("Atualizando Carrinho...");
              $.getJSON(`/forms/form.carrinho.php?remove=true&pid=all`).done(
                function (result) {
                  if (result.status !== "success") {
                    $(".product-carrinho-flex").show();

                    toast("Falha ao Remover", "error");
                  } else {
                    $(".product-carrinho-flex").each(function () {
                      $(this).css("filter", "grayscale(1)");

                      update_cart();

                      $(this).fadeOut(700, function () {
                        $(this).remove();

                        toast("Produtos Removido(s) do Carrinho!", "success");
                      });
                    });
                  }
                }
              );
            },
          });
        }
      });
    } else {
      $(item).css("filter", "grayscale(1)");

      $(item).fadeOut(700, function () {
        $.getJSON(`/forms/form.carrinho.php?remove=true&pid=${pid}`).done(
          function (result) {
            if (result.status !== "success") {
              $(item).show();
              update_cart();
            } else {
              $(item).remove();
              update_cart();
            }
          }
        );
      });
    }
  });

  //Adicionar Produto no Carrinho

  $(".product-add-cart, .product-grid-item-button").on("click", function () {
    if ($(this).data("add") !== 1) {
      Swal.fire({
        title: "Atenção",
        icon: "warning",
        html: 'Você no tem permisses para Adicionar Produtos no Carrinho!<p"><br><b>Selecione um Paciente para Continuar</b></p>',
        confirmButtonText: "Pacientes",
        denyButtonText: "Fechar",
        showConfirmButton: true,
        showDenyButton: true,
      }).then(function (result) {
        if (result.isConfirmed) {
          window.location = "/cadastros/pacientes";
        }
      });
    } else {
      $.getJSON(
        `/forms/form.carrinho.php?product_id=${$(this).data("product")}&pid=${$(
          this
        ).data("id")}&user_ref=${$(this).data("ref")}`
      ).done(function (data) {
        toast("Produto Adicionado ao Carrinho", "success");
      });

      Swal.close();
    }
  });

  // Incrementar Produtos no Carrinho ( + ou -)

  $(".product-qtde input").on("blur", function () {
    let vp = $(this).closest(".product-carrinho-flex");
    let id = $(this).attr("id").replace("id_", "");

    $.getJSON(
      `/carrinho/calc.php?pid=${$(this)
        .attr("id")
        .replace("id_", "")}&qtde=${parseInt(this.value)}`,
      function (product) {
        $(vp)
          .find("div > div.product-carrinho-price > p.text1")
          .html(
            Math.round(product.current_item.subtotal).toLocaleString("pt-br", {
              style: "currency",
              currency: "BRL",
            })
          );

        update_cart();
      }
    );
  });

  $(".product-qtde button").on("click", function () {
    let action = $(this).data("action");
    let frete = $('input[name="valor_frete"]').val();

    let id = $(this).data("for");

    let input = document.getElementById(id);

    if (this.tagName == "BUTTON") {
      if (action === "+") {
        $(input).val(parseInt(input.value) + 1);
      } else {
        if (parseInt(input.value) > 0) {
          input.value = parseInt(input.value) - 1;
        }
      }
    }

    let vp = $(this).closest(".product-carrinho-flex");

    $.getJSON(
      `/carrinho/calc.php?pid=${$(this)
        .data("for")
        .replace("id_", "")}&qtde=${parseInt(input.value)}`,
      function (product) {
        $(vp)
          .find("div > div.product-carrinho-price > p.text1")
          .html(
            Math.round(product.current_item.subtotal).toLocaleString("pt-br", {
              style: "currency",
              currency: "BRL",
            })
          );

        update_cart();
      }
    );

    Swal.close();
  });

  // Aceitar Cookies

  if (localStorage.getItem("clinabs_accept_cookies") !== null) {
    $(".cmplz-cookiebanne").hide();
    $(".cmplz-cookiebanne").remove();
  } else {
    $(".cmplz-cookiebanne").show();
  }

  $("#btn-accept-cookies").on("click", function () {
    localStorage.setItem("clinabs_accept_cookies", "ok");

    $(".cmplz-cookiebanne").remove();
  });

  // Links em Div

  $("*[data-link]").each(function () {
    let link = $(this).data("link");

    $(this).on("click", function () {
      window.location = link;
    });

    $(this).removeAttr("data-link");
  });

  // Menu link

  $("li[data-ref]").each(function () {
    if (
      location.pathname.split("/").filter((p) => p !== "")[0] ===
      $(this).data("ref")
    ) {
      this.classList.add("active");
    }
  });

  // Selecionar Documento no Formulário

  $("#cadastroFuncionarioBasico").on("submit", function (e) {
    e.preventDefault();

    let fd = $(this).serialize();

    let id = $(this).attr("id");

    let fdx = $(this).serializeArray();

    preloader();

    $.post($(this).attr("action"), fdx)
      .done(function (result, textStatus, jqXHR) {
        if (result.status == "success") {
          Swal.fire({
            title: "Atenão",
            text: result.text,
            icon: "success",
          }).then(function () {
            window.location = "/cadastros/funcionarios";
          });
        } else {
          Swal.fire({
            title: "Atenção",
            text: result.text,
            icon: result.status,
          }).then(function () {
            if (result.action === "focus") {
              var filteredList = $("input[name]").filter(function () {
                return $(this).val() == "";
              });

              if (filteredList.length > 0) {
                filteredList.css("border", "2px solid red");
                return false;
              }
            }
          });
        }
      })

      .fail(function (result, textStatus, jqXHR) {
        Swal.fire({
          title: "Atenção",
          text: textStatus,
          icon: "warning",
        });
      });
  });

  // Atualizar Perfil

  $("#formUpdateCadastro").on("submit", function (e) {
    e.preventDefault();

    let url_action = $(this).attr("action");

    let input = $("[data-required]").filter(function (elem) {
      return elem.value === "";
    });

    if ($("input.pwd-alter:first").val() !== $("input.pwd-alter:last").val()) {
      Swal.fire({
        title: "Atenão",
        text: "Senhas no Conferem!",
        showCancelButton: false,
        showConfirmButton: true,
        allowOutsideClick: false,
      });
    } else {
      if (input.length) {
        let name = $(`label[for="${input[0].name}"]`).text();

        Swal.fire({
          title: "Atenção",
          html: `Voc não Preencheu o Campo <b style="color: red">${name}</a>`,
          icon: "warning",
        });
      } else {
        let fd = $(this).serialize();

        let id = $(this).attr("id");

        let fdx = $(this).serializeArray();

        Swal.fire({
          html: "<div style='display: flex;align-items: center;justify-content: start;gap: 1rem;padding: 1rem;'><img src='/assets/images/loading.gif' height='48px'><strong>Processando...</strong></div>",
          showCancelButton: false,
          showConfirmButton: false,
          allowOutsideClick: false,
          didOpen: function () {
            let api_keys = {};
            $("#tabControl2")
              .find("[name]")
              .map(function () {
                return (api_keys[this.name] = this.value);
              });

            let sfd = {};

            $.post("/forms/form.cadastro.update.php", fd)
              .done(function (result) {
                if (result.status == "success") {
                  if ($("#agenda_dados").length > 0) {
                    if ($("#agenda_dados").val().length > 0) {
                      const requestOptions = {
                        method: "PUT",
                        headers: {
                          "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                          calendario: window.btoa(
                            $("#agenda_dados").val().replaceAll("'", '"')
                          ),
                          token: $("#user_token").val(),
                        }),
                      };

                      fetch("/forms/schedule.calendar.php", requestOptions).then(() => {

                      });
                    }
                  }

                  Swal.fire({
                    title: "Atenção",
                    text: result.text,
                    icon: "success",
                  }).then(function () {
                    $.post("/form/form.update.image.php", {
                      image: $("#profileImage").val(),
                      token: $("#user_token").val(),
                    })
                      .done(function (resp) {

                      })
                      .always(function () {
                        window.location.reload();
                      });
                  });


                } else {
                  Swal.fire({
                    title: "Atenção",
                    text: result.text,
                    icon: result.status,
                  }).then(function () {
                    if (result.action === "focus") {
                      var filteredList = $("input[name]").filter(function () {
                        return $(this).val() == "";
                      });

                      if (filteredList.length > 0) {
                        filteredList.css("border", "2px solid red");
                        return false;
                      }
                    }
                  });
                }
              })
              .fail(function (error) {
                Swal.fire({
                  allowOutSideClick: false,
                  title: "Atenção",
                  text: error.statusText,
                  icon: "error",
                });
              });

            fetch("/forms/form.cadastro.update.php", {
              method: "PUT",
              body: sfd,
              headers: {},
            })
              .then((response) => {
                if (response.ok) {
                  return response.json();
                } else {
                  throw new Error("Erro na requisição");
                }
              })
              .then((data) => { })
              .catch((error) => { });
          },
        });
      }
    }
  });

  //Cadastro de Produtos

  wizardModal("#cadastroProduto", {
    validate: true,

    onFinish: function (info) { },

    onValidate: function (element, status) {
      if (element.type === "file" && status === "error") {
        $(`label[for='${element.id}']`).addClass("anexo-item-error");

        $(`label[for='${element.id}']`).removeClass("anexo-item-success");
      } else if (element.type === "file" && status === "success") {
        $(`label[for='${element.id}']`).addClass("anexo-item-success");

        $(`label[for='${element.id}']`).removeClass("anexo-item-error");
      }
    },

    onStep: function (step) {
      if (step.currentStep === 2) {
        step.btnFinish.show();

        step.btnNext.hide();

        step.btnPrev.show();

        fetchForm(
          step.form,

          function (data) {
            if (data.status === "success") {
              toast(data.text, "success");

              setTimeout(function () {
                window.location = "/cadastros/produtos";
              });
            } else {
              toast(data.text, "warning");
            }
          },

          function (resp) {
            toast(resp, "error");
          }
        );
      }
    },

    autoFill: false,

    autoSave: false,
  });

  // Update Produto

  wizardModal("#updateProduto", {
    validate: true,
    onFinish: function (info) { },
    onValidate: function (element, status) {
      if (element.type === "file" && status === "error") {
        $(`label[for='${element.id}']`).addClass("anexo-item-error");
        $(`label[for='${element.id}']`).removeClass("anexo-item-success");
      } else if (element.type === "file" && status === "success") {
        $(`label[for='${element.id}']`).addClass("anexo-item-success");
        $(`label[for='${element.id}']`).removeClass("anexo-item-error");
      }
    },

    onStep: function (step) {
      if (step.currentStep === 2) {
        step.btnFinish.show();
        step.btnNext.hide();
        step.btnPrev.show();
        fetchForm(
          step.form,

          function (data) {
            if (data.status === "success") {
              toast(data.text, "success");

              setTimeout(function () {
                window.location = "/produtos";
              });
            } else {
              toast(data.text, "warning");
            }
          },

          function (resp) {
            toast(resp, "error");
          }
        );
      }
    },

    autoFill: false,

    autoSave: false,
  });

  //Cadastro de Agendamento

  wizardModal("#cadastroAgendamento", {
    validate: true,

    onFinish: function (info) { },

    onValidate: function (element, status) { },

    onStep: function (step) {
      if (step.currentStep === 2) {
        step.btnNext.hide();

        $(step.btnFinish).text("REALIZAR PAGAMENTO");

        step.btnFinish.show();

        step.btnPrev.show();

        $(step.form).off("submit");
        $(step.form).on("submit", function (e) {
          e.preventDefault();
          if ($("#payment_credit").is(":checked")) {
            preloader("Processando Pagamento...");
          } else {
            preloader("Enviando Formulário...");
          }

          let data = $(this).serialize();

          $.post(`${$(this).attr("action")}?${data}`)
            .done(function (result, textStatus, jqXHR) {
              if (result.status === "error") {
                try {
                  xhtml = "";

                  for (let i = 0; i < result.data.length; i++) {
                    xhtml += `<p><strong style="color: red">${result.data[i].description}</strong></p>`;
                  }

                  Swal.fire({
                    title: "Atenção",
                    icon: "error",
                    html: xhtml,
                    allowOutSideClick: false,
                  });
                } catch (error) {
                  Swal.fire({
                    title: "Atenção",
                    icon: "error",
                    text: result.text,
                    allowOutSideClick: false,
                  });
                }
              } else if (result.status === "warning") {
                Swal.fire(result);
              } else {
                Swal.fire(result).then(function () {
                  if (result.status === "success") {
                    if (result.paymentLink) {
                      localStorage.setItem("redirect_uri", result.link);
                      window.location = result.link;
                    } else {
                      if (result.createPwd) {
                        localStorage.setItem("redirect_uri", result.link);
                      }

                      window.location = result.linkUrl;
                    }
                  }
                });
              }
            })
            .fail(function (data, textStatus, jqXHR) {
              console.log(data, textStatus);
            });
        });
      } else {
        step.btnNext.show();

        step.btnFinish.hide();
      }
    },

    autoFill: false,

    autoSave: false,
  });

  // Vendas

  wizardModal("#cadastroCompraMedicamento", {
    validate: true,
    onFinish: function (info) { },
    onValidate: function (element, status) {
      if (element.type === "file" && status === "error") {
        $(`label[for='${element.id}']`).addClass("anexo-item-error");

        $(`label[for='${element.id}']`).removeClass("anexo-item-success");
      } else if (element.type === "file" && status === "success") {
        $(`label[for='${element.id}']`).addClass("anexo-item-success");

        $(`label[for='${element.id}']`).removeClass("anexo-item-error");
      }
    },
    onStep: function (step) {
      if (step.currentStep === 4) {
        step.btnNext.hide();
        $(step.btnFinish).text("REALIZAR PAGAMENTO");
        step.btnFinish.show();
        step.btnPrev.show();
        fetchForm(
          step.form,
          function (data) {
            if (data.status === "success") {
              if (data.method === "pix") {
                Swal.fire({
                  html: "<div style='display: flex; gap: 1rem;padding: 1rem;'><img src='/assets/images/loading.gif' height='48px'><strong>Gerando PIX...</strong></div>",
                  showCancelButton: false,
                  showConfirmButton: false,

                  didOpen: function () {
                    $.get(
                      `/api/pix/index.php?paciente_token=${data.paciente_token
                      }&valor=${data.amount.replace(",", ".")}&identificador=${data.token
                      }&descricao=CONSULTA MEDICA CLINABS`,
                      function (dataX) {
                        Swal.fire({
                          confirmButtonText: "Fechar Janela",
                          imageUrl: "/api/pix/logo_pix.png",
                          allowOutSideClick: false,
                          html: `<p>Use seu App de Pagamento para Escanear o QrCode para Pagamento</p><img height="256px" src="${dataX.imageString}"><p><b>Valor:</b>  R$ ${dataX.valor}<p><b>Beneficiario:</b> ${dataX.beneficiario}`,
                          didClose: function () {
                            window.location = "/pedidos";
                          },
                        });
                      }
                    );
                  },

                  allowOutSideClick: false,
                });
              } else {
                Swal.fire({
                  title: "Atenção",
                  title:
                    "Você Receber um link pela nossa Equipe para realizar o Pagamento!",
                  icon: "info",
                  didClose: function () {
                    window.location = "/pedidos";
                  },
                });
              }
            } else {
              toast(data.text, "warning");
            }
          },

          function (resp) {
            toast(resp, "error");
          }
        );
      } else {
        step.btnNext.show();
        step.btnPrev.show();

        step.btnFinish.hide();
      }
    },

    autoFill: false,

    autoSave: false,
  });

  // Cadastro de Funcionarios

  wizardModal("#cadastroFuncionario", {
    validate: true,

    onFinish: function (info) { },

    onValidate: function (element, status) {
      if (element.type === "file" && status === "error") {
        $(`label[for='${element.id}']`).addClass("anexo-item-error");

        $(`label[for='${element.id}']`).removeClass("anexo-item-success");
      } else if (element.type === "file" && status === "success") {
        $(`label[for='${element.id}']`).addClass("anexo-item-success");

        $(`label[for='${element.id}']`).removeClass("anexo-item-error");
      }
    },

    onStep: function (step) {
      if (step.currentStep === step.maxSteps) {
        step.btnFinish.show();
        step.btnNext.hide();
        fetchForm(
          step.form,
          function (data) {
            if (data.status === "success") {
              toast(data.text, "success");
              setTimeout(function () {
                window.location = "/cadastros/funcionarios";
              });
            } else {
              toast(data.text, "warning");
            }
          },
          function (resp) {
            toast(resp, "error");
          }
        );
      }
    },
    autoFill: false,
    autoSave: false,
  });

  // Cadastro de Pacientes (Completo)

  wizardModal("#cadastroPaciente", {
    validate: true,

    onFinish: function (info) { },

    onValidate: function (element, status) {
      if (element.type === "file" && status === "error") {
        $(`label[for='${element.id}']`).addClass("anexo-item-error");

        $(`label[for='${element.id}']`).removeClass("anexo-item-success");
      } else if (element.type === "file" && status === "success") {
        $(`label[for='${element.id}']`).addClass("anexo-item-success");

        $(`label[for='${element.id}']`).removeClass("anexo-item-error");
      }
    },

    onStep: function (step) {
      if (step.currentStep > 1) {
        $(".btn-step-prev").show();
      } else {
        $(".btn-step-prev").hide();
      }

      if (step.currentStep === 3) {
        step.btnFinish.show();

        step.btnNext.hide();

        $(step.btnFinish).text("CONCLUIR");

        fetchForm(
          step.form,
          function (data) {
            if (data.status === "success") {
              toast(data.text, "success");

              setTimeout(function () {
                window.location = data.linkUrl;
              });
            } else {
              toast(data.text, "warning");
            }
          },

          function (resp) {
            toast(resp, "error");
          }
        );
      } else {
        $(step.btnFinish).off();
        step.btnFinish.hide();
        step.btnNext.show();
        $(step.btnNext).text("PROXIMO");
      }
    },

    autoFill: false,

    autoSave: false,
  });

  // Cadastro de Pacientes (Bsico)

  wizardModal(
    "#cadastroPacienteBasico",
    {
      validate: true,
      onFinish: function (info) { },
      onValidate: function (element, status) { },
      onStep: function (step) { },
      autoFill: false,
      autoSave: false,
    },
    function (step) {
      step.btnFinish.show();

      step.btnNext.hide();

      $(step.btnFinish).text("CONCLUIR");

      fetchForm(
        step.form,
        function (data) {
          if (data.status === "success") {
            toast(data.text, "success");

            setTimeout(function () {
              window.location = data.linkUrl;
            });
          } else {
            toast(data.text, "warning");
          }
        },

        function (resp) {
          toast(resp, "error");
        }
      );
    }
  );

  // Cadastro de Usurios

  wizardModal(
    "#cadastroUsuarioBasico",
    {
      validate: true,
      onFinish: function (info) { },
      onValidate: function (element, status) { },
      onStep: function (step) { },
      autoFill: false,
      autoSave: false,
    },
    function (step) {
      step.btnFinish.show();

      step.btnNext.hide();

      $(step.btnFinish).text("CONCLUIR");

      fetchForm(
        step.form,
        function (data) {
          if (data.status === "success") {
            toast(data.text, "success");

            setTimeout(function () {
              if ("redirect" in data && data.redirect !== "none") {
                window.location = data.redirect;
              } else {
                window.location = "/cadastros/usuarios";
              }
            });
          } else {
            toast(data.text, "warning");
          }
        },

        function (resp) {
          toast(resp, "error");
        }
      );
    }
  );

  // Cadastro Mdico

  wizardModal("#cadastroMedico", {
    validate: true,

    onFinish: function (info) { },

    onValidate: function (element, status) {
      if (element.type === "file" && status === "error") {
        $(`label[for='${element.id}']`).addClass("anexo-item-error");

        $(`label[for='${element.id}']`).removeClass("anexo-item-success");
      } else if (element.type === "file" && status === "success") {
        $(`label[for='${element.id}']`).addClass("anexo-item-success");

        $(`label[for='${element.id}']`).removeClass("anexo-item-error");
      }
    },

    onStep: function (step) {
      if (step.currentStep > 1) {
        $(".btn-step-prev").show();
      } else {
        $(".btn-step-prev").hide();
      }

      if (step.currentStep === step.maxSteps) {
        step.btnFinish.show();

        step.btnNext.hide();

        fetchForm(
          step.form,

          function (data) {
            if (data.status === "success") {
              toast(data.text, "success");

              setTimeout(function () {
                window.location = "/cadastros/medicos";
              });
            } else {
              toast(data.text, "warning");
            }
          },

          function (resp) {
            toast(resp, "error");
          }
        );
      }
    },

    autoFill: false,

    autoSave: false,
  });

  fetchForm(
    $("#editarConsulta"),

    function (data) {
      if (data.status === "success") {
        toast(data.text, "success");

        setTimeout(function () {
          window.location = "/agenda";
        });
      } else {
        toast(data.text, "warning");
      }
    },

    function (resp) {
      toast(resp, "error");
    }
  );


  if ($("#tableAgendamento").length > 0) {
    //$('select.filter-element').off('all');

    $("i.fa-solid.fa-filter-circle-xmark.filter-clear").on(
      "click",
      function () {
        $("#tableAgendamento")
          .find("tbody")
          .find("tr")
          .each(function () {
            $(this).show();
          });

        $(this)
          .closest("fieldset")
          .find("input")
          .each(function () {
            this.value = "";
          });

        $(this)
          .closest("fieldset")
          .find(".select-selected")
          .each(function () {
            this.textContent = "Selecione uma Opção";
          });

        filter_ag_req();
      }
    );

    /*
    $('#filter_modalidade').on('change', function() {
        let filter = $('#tableAgendamento').find('tbody').find('tr');
        let v =  this.value;
 
        $(filter).each(function() {
            let k = $($(this).find('td')[5]).text().trim().toUpperCase();
 
            if(k === v) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
 
    $('#filter_medico').on('change', function() {
        let filter = $('#tableAgendamento').find('tbody').find('tr');
        let v =  this.value;
 
        $(filter).each(function() {
            let k = $($(this).find('td')[3]).text().trim().toUpperCase();
 
            if(k === v) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
 
    $('#filter_status').on('change', function() {
        let filter = $('#tableAgendamento').find('tbody').find('tr');
        let v =  this.value;
 
        $(filter).each(function() {
            let k = $($(this).find('td')[4]).text().trim().toUpperCase();
 
            if(k === v) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
 
    $('#date_start, #date_end').on('change', function() {
        let filter = $('#tableAgendamento').find('tbody').find('tr');
        let date_start = $('#date_start').val();
        let date_end = $('#date_end').val();
 
        if(date_start.length > 0 && date_end.length == 0) {
            $(filter).each(function() {
                if(new Date($(this).data('date')).getTime() >= new Date(date_start).getTime()) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else if(date_end.length > 0 && date_start.length == 0) {
            $(filter).each(function() {
                if(new Date(date_end).getTime() <= new Date($(this).data('date')).getTime()) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            let dates = getDatesInRange(date_start, date_end);
 
            $(filter).each(function() {
                let column = new Date($(this).data('date')).toLocaleDateString();
 
                if(new Date(date_start).getTime() === new Date(date_end).getTime()) {
                    if($(this).data('date') == date_start) {
                        $(this).show();
                    }
                } else {
                    $(this).hide();
                }
            });
        }
    });
 
*/

    $("#agendamento-filters").on("submit", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });

    $(".filter-element").off("change");

    $(".filter-element").on("change", function () {
      if (this.value !== "") {
      }
    });
  }



  $('input[type="search"]').attr("placeholder", "Pesquisar....");
  $("#cadastroConsulta").find(".btn-step-submit").show();
  $("#editarConsulta")
    .find(".btn-step-submit")
    .show()
    .text("ATUALIZAR CONSULTA");

  // tinymce.init({
  //     language: "pt_BR",
  //     selector: ".tinymce",
  //     plugins: "",
  //     toolbar: "undo redo | bold italic underline strikethrough | link image media table mergetags | align lineheight | checklist numlist bullist indent outdent | removeformat",
  //     tinycomments_mode: "embedded",
  //     tinycomments_author: "",
  // });

  $('a[rel="noopener"]').html('<i class="fa fa-save"></i> Salvar');

  $("input").trigger("input");



  $("#medico_token").on("change", function () {
    let opts = document.querySelector("#medico_token").options;
    $("#crm").val(
      opts[document.querySelector("#medico_token").selectedIndex].getAttribute(
        "data-crm"
      )
    );
  });

  $("#doc_cpf").on("blur", function () {
    if (this.value.length === 14) {
      $.get(
        `/forms/paciente.dados.php?paciente_cpf=${this.value}`,
        function (paciente) {
          $("#cpf").val(paciente.cpf);
          $("#nome_completo").val(paciente.nome_completo);
          $("#rg").val(paciente.rg);
          $("#nacionalidade").val(paciente.nacionalidade).trigger("change");
          $("#nome_preferencia").val(paciente.nome_preferencia);
          $("#identidade_genero")
            .val(paciente.identidade_genero)
            .trigger("change");
          $("#data_nascimento").val(paciente.data_nascimento).trigger("change");
          $("#email").val(paciente.email);
          $("#anamnese").val(paciente.anamnese);
          $("#celular").val(paciente.celular);
          $("#paciente_token").val(paciente.token);
        }
      );
    }
  });

  $("input.form-control.cpf-auto-fill").mask("000.000.000-00", {
    placeholder: "___.___.___-__",
    clearIfNotMatch: true,
    onComplete: function (cpf) {
      preloader("Verificando Paciente...");
      $.get(
        `/forms/paciente.dados.php?paciente_cpf=${cpf.replace(
          /\D/g,
          ""
        )}&autologin=false`,
        function (paciente) {
          $("#cpf").val(paciente.cpf);
          $("#nome_completo").val(paciente.nome_completo);
          $("#rg").val(paciente.rg);
          $("#nacionalidade").val(paciente.nacionalidade).trigger("change");
          $("#nome_preferencia").val(paciente.nome_preferencia);
          $("#identidade_genero")
            .val(paciente.identidade_genero)
            .trigger("change");
          $("#data_nascimento").val(paciente.data_nascimento).trigger("change");
          $("#email").val(paciente.email);
          $("#anamnese").val(paciente.anamnese);
          $("#celular").val(paciente.celular);

          $('input[name="paciente_token"]').val(paciente.token);

          // $('div[data-step="1"]').removeClass("active");
          //$('div[data-step="2"]').addClass("active");

          // $("button.btn-step-next.btn-button1").hide();
          //$("button.btn-step-submit.btn-button1").show();

          //window.location.reload();

          Swal.close();
        }
      );
    },
  });

  $('input[type="checkbox"],input[type="radio"]').each(function () {
    $(this).trigger("change");
  });

  $("#nacionalidade").on("change", function () {
    $("#celular, #wa_notificacao").unmask();

    switch (this.value) {
      case "US": {
        $("#celular, #wa_notificacao").mask("+1 000-0000", {
          placeholder: "+1 ___-____",
          clearIfNotMatch: true,
        });
        break;
      }

      case "BR": {
        $("#celular, #wa_notificacao").mask("+55 (00) 0 0000-0000", {
          placeholder: "+55 (__) _ ____-____",
          clearIfNotMatch: true,
        });
        break;
      }

      case "UY": {
        $("#celular, #wa_notificacao").mask("+598 0000-0000", {
          placeholder: "+598 ____-____",
          clearIfNotMatch: true,
        });
        break;
      }
    }

    $("#celular").removeAttr("disabled");
  });

  if (document.querySelectorAll("#tabControl1").length > 0) {
    tabControl("tabControl1");
  }

  if (document.querySelectorAll("#tabControl2").length > 0) {
    tabControl("tabControl2");
  }

  $('select:not([no-trigger="true"])').each(function () {
    $(this).trigger("change");
  });

  $("#age-min").on("focus", function () {
    let val = this.value.replace(/\D/g, "");

    this.value = val;
  });

  $("#age-max").on("focus", function () {
    let val = this.value.replace(/\D/g, "");

    this.value = val;
  });

  $("#age-min").on("blur", function () {
    let val = this.value.replace(/\D/g, "");

    this.value = `${val} Ano(s)`;
  });

  $("#age-max").on("blur", function () {
    let val = this.value.replace(/\D/g, "");

    this.value = `${val} Ano(s)`;
  });

  // End

  setInterval(function () {
    $.get('/api/server/index.php').done(function (data) {
      $('.dashboard-conter-ram').html(`${data.memory.percentage}%`);
    });
  }, 15000);


  $(".preloader-container").fadeOut(600, function () {
    $(".preloader-container").hide();

    setTimeout(() => {
      $('#row-btn-banner').before(`
        <img width="100%" class="img-desktop" src="/assets/images/banner-home.png" alt="slider1" preload>
        <img width="100%"  class="img-mobile" src="/assets/images/banner-home-mobile.jpg" alt="slider1" preload>
      `);
    }, 3500);
  });
});



function validateCPF(cpf) {
  // Remove non-numeric characters
  cpf = cpf.replace(/[^\d]+/g, '');

  // Check if CPF length is 11
  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
    return false; // CPF is invalid if it's too short or has all identical digits
  }

  // Validate first check digit
  let sum = 0;
  for (let i = 0; i < 9; i++) {
    sum += parseInt(cpf.charAt(i)) * (10 - i);
  }
  let firstCheckDigit = 11 - (sum % 11);
  if (firstCheckDigit === 10 || firstCheckDigit === 11) {
    firstCheckDigit = 0;
  }

  // Validate second check digit
  sum = 0;
  for (let i = 0; i < 10; i++) {
    sum += parseInt(cpf.charAt(i)) * (11 - i);
  }
  let secondCheckDigit = 11 - (sum % 11);
  if (secondCheckDigit === 10 || secondCheckDigit === 11) {
    secondCheckDigit = 0;
  }

  // Check if the calculated check digits match the ones in the CPF
  return cpf.charAt(9) == firstCheckDigit && cpf.charAt(10) == secondCheckDigit;
}


function refund_payment(id, value) {
  const vl = (value * 100);

  Swal.fire({
    title: 'Estornar Pagamento',
    width: 350,
    allowOutSideClick: false,
    showConfirmButton: true,
    showDenyButton: true,
    confirmButtonText: 'ESTORNAR',
    denyButtonText: 'CANCELAR',
    html: `
    <div class="rowcol">
      <div class="col-12">
        <div class="form-group">
          <label for="valor_estorno">Valor a Devolver</label>
          <input id="valor_estorno" value="${vl}" data-mask="#.###0.00">
        </div>
      </div>

      <div class="col-12">
        <div class="form-group">
          <label for="desc_estorno">Descrição</label>
          <textarea id="desc_estorno"></textarea>
        </div>
      </div>
    </div>`,
    didOpen: function () {
      $("#valor_estorno").mask("#.##0,00", {
        reverse: true,
        placeholder: "0.00",
        clearIfNotMatch: true
      });
    },
    preConfirm: function () {
      return ($('#valor_estorno').val().length > 0);
    }
  }).then(function (e) {
    if (e.isConfirmed) {
      valor = $('#valor_estorno').val();
      let desc = $('#desc_estorno').val();

      preloader('Estornando Pagamento...');

      $.get('/forms/payment.refund.php', {
        payment_id: id,
        valor: valor,
        description: desc
      }).done(function (resp) {
        console.log(resp);

        if ('code' in resp) {
          Swal.fire({
            title: 'Atenção',
            text: resp.description,
            icon: 'warning'
          });
        } else {
          Swal.fire({
            title: 'Atenção',
            text: 'Estorno Solicitado com Sucesso!',
            icon: 'success'
          });
        }
      }).fail(function (error) {
        Swal.fire({
          title: 'Atenção',
          text: 'Falha ao Estornar Pagamento',
          icon: 'error'
        });
      });
    }
  });
}