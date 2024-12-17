$("document").ready(function () {
     $("*").scrollTop(0),
          setInterval(function () {
               calendarAgUpdate();
          }, 1e3),
          $("label.week-time.week-schedule.listmedic-box-dir-time").each(function () {
               $(this).off("dblclick");
          }),
          $("label.week-time.week-schedule.listmedic-box-dir-time.active").each(function () {
               let e = $(this);
               $(e)
                    .find('i[name="online"]')
                    .on("click", function () {
                         $(this).hasClass("icon-disabled") ? ($(this).removeClass("icon-disabled"), $(e).attr("data-online", !0)) : ($(this).addClass("icon-disabled"), $(e).attr("data-online", !1)),
                              !1 === $(e).data("presencial") && !1 === $(e).data("online") && ($(e).removeClass("active"), $(e).find("i[name]").off("click"), $(e).find("i[name]").removeClass("icon-disabled"));
                    }),
                    $(e)
                         .find('i[name="presencial"]')
                         .on("click", function () {
                              $(this).hasClass("icon-disabled") ? ($(this).removeClass("icon-disabled"), $(e).attr("data-presencial", !0)) : ($(this).addClass("icon-disabled"), $(e).attr("data-presencial", !1)), calendarAgUpdate();
                         });
          }),
          $(".btn-modalidade").on("change", function () {
               "presencial" == this.value ? $(this).closest(".box-mediclist").find(".location-clinic").show() : $(this).closest(".box-mediclist").find(".location-clinic").hide();
               let e = $(this).data("value"),
                    t = $(this).data("duration");
               $(this)
                    .closest(".box-mediclist")
                    .find(".listmedic-values")
                    .html(`<img src="/assets/images/ico-money.svg"><p>${e.toLocaleString("pt-br", { style: "currency", currency: "BRL" })} - <img src="/assets/images/ico-agenda-clock.svg" height="25px"> ${t} minutos</p>`);
          }),
          $(".ag-item-time-btn").on("click", function () {
               let e = $(this).data();
               e.presencial
                    ? $(this).closest(".box-mediclist").find('.listmedic-boxlink[data-value="presencial"]').removeClass("btn-disabled")
                    : $(this).closest(".box-mediclist").find('.listmedic-boxlink[data-value="presencial"]').addClass("btn-disabled"),
                    e.online
                         ? $(this).closest(".box-mediclist").find('.listmedic-boxlink[data-value="online"]').removeClass("btn-disabled")
                         : $(this).closest(".box-mediclist").find('.listmedic-boxlink[data-value="online"]').addClass("btn-disabled"),
                    $(this).closest(".box-mediclist").find('.listmedic-boxlink[data-value="online"]').prop(":disabled", e.online),
                    !0 === e.presencial || "true" === e.presencial
                         ? $(this).closest(".box-mediclist").find(".street-info.street-item-info").text($(this).data("street"))
                         : $(this).closest(".box-mediclist").find(".street-info.street-item-info").text("");
               let t = $(this),
                    i = $(this).text(),
                    a = $(this).closest(".box-mediclist").find('input[name="duracao_consulta"]').val();
               Swal.fire({
                    title: "Agendamento de Consulta",
                    html: `<hr>
             <div class="swal-dflex-start">
             <p><small><b>Data:</b> ${$(t).attr("data-date")}</small></p>
             <p><small><b>Hor\xe1rio: </b> ${$(t).text()}</small></p>
             <p><small><b>Modalidade: </b> ${$(t).closest(".box-mediclist").find(".listmedic-boxlink[checked]").attr("data-value").toUpperCase()}</small></p>
             <p><small><b>Unidade: </b> ${$(t).attr("data-street-name").toUpperCase()}</small></p>
             <p><small><b>Endere\xe7o: </b> ${$(t).attr("data-street")}</small></p>
             <p><small><b>Dura\xe7\xe3o: </b> ${a} Minutos</small></p>
             <p><small><b>Dr(a): </b> ${$(t).closest(".box-mediclist").find(".listmedic-box-dir-user").find("h3").text()}</small></p>
             <p><small><b>CRM: </b> ${$(t).closest(".box-mediclist").find(".listmedic-box-dir-user").find(".crm-bg").text().replace("CRM ", "")}</small></p>
             <p><small><b>Especialidade: </b> ${$(t).closest(".box-mediclist").find(".listmedic-box-dir-subtitle").text()}</small></p>
             <p><small><b>Valor: </b> ${$(t).closest(".box-mediclist").find(".btn-modalidade:checked").data("value").toLocaleString("pt-br", { style: "currency", currency: "BRL" })}</small></p>
             </div>`,
                    width: "auto",
                    showConfirmButton: !0,
                    showDenyButton: !0,
                    showCancelButton: !1,
                    allowOutsideClick: !1,
                    confirmButtonText: "CONTINUAR",
                    denyButtonText: "FECHAR",
               }).then(function (e) {
                    if (e.isConfirmed) {
                         let a = $(t).closest(".box-mediclist").find('input[name="data_agendamento"]').val();
                         $(t).closest(".box-mediclist").find('input[name="data_agendamento"]').val(`${a} ${i}`),
                              $(t).closest(".box-mediclist").find('input[name="valor_consulta"]').val($(t).closest(".box-mediclist").find(".btn-modalidade:checked").data("value")),
                              $(t).closest(".box-mediclist").submit();
                    }
               });
          }),
          $(".listmedic-boxlink").on("click", function () {
               $(".listmedic-box-dir-boxtime.item-disabled").removeClass("item-disabled"),
                    "online" === $(this).attr("data-value")
                         ? ($(this).closest(".box-mediclist").find(".listmedic-box-dir-boxtime.item-disabled").removeClass(".item-disabled"),
                              $(this)
                                   .closest(".box-mediclist")
                                   .find(".listmedic-box-dir-boxtime")
                                   .find(".ag-item-time-btn")
                                   .each(function () {
                                        "true" === $(this).attr("data-online") ? $(this).show() : $(this).hide();
                                   }))
                         : ($(this).closest(".box-mediclist").find(".listmedic-box-dir-boxtime.item-disabled").removeClass(".item-disabled"),
                              $(this)
                                   .closest(".box-mediclist")
                                   .find(".listmedic-box-dir-boxtime")
                                   .find(".ag-item-time-btn")
                                   .each(function () {
                                        "true" === $(this).attr("data-presencial") ? $(this).show() : $(this).hide();
                                   })),
                    $(this).closest(".box-mediclist").find(".street-info.street-item-info").text("");
          }),
          $(".prev-week, .next-week").on("click", function () {
               let e = $(".calendar-slide"),
                    t = $(".calendar-slide.active").data("index") ?? 0,
                    i = e.length,
                    a = t < i - 1;
               if (($(".next-week").css("filter", a ? "grayscale(0)" : "grayscale(1)"), $(".next-week").css("visibility", a ? "visible" : "hidden"), $(this).hasClass("next-week"))) {
                    let s = t + 1;
                    $(".calendar-slide").removeClass("active"),
                         s > i - 1
                              ? ($(".calendar-slide").first().addClass("active"), $(".next-week").css("filter", "grayscale(1)"), s > 0 ? $(".prev-week").css("visibility", "visible") : $(".prev-week").css("visibility", "hidden"))
                              : ($(".calendar-slide").eq(s).addClass("active"),
                                   $(".next-week").removeAttr("disabled"),
                                   $(".prev-week").removeAttr("disabled"),
                                   s > 0 ? $(".prev-week").css("visibility", "visible") : $(".prev-week").css("visibility", "hidden"));
               } else {
                    e.length;
                    let d = t - 1;
                    d < 1
                         ? ($(".calendar-slide").each(function () {
                              $(this).removeClass("active");
                         }),
                              $(".calendar-slide").first().addClass("active"),
                              $(".prev-week").attr("disabled", !0),
                              $(".prev-week").css("visibility", "hidden"))
                         : ($(".calendar-slide").each(function () {
                              $(this).removeClass("active");
                         }),
                              $(".calendar-slide").eq(d).addClass("active"),
                              $(".prev-week").removeAttr("disabled"));
               }
          }),
          $("label.week-schedule").on("click", function () {
               let e = {};
               $(this).hasClass("active") ? $(this).removeClass("active") : $(this).addClass("active"),
                    $("label.week-schedule").each(function () {
                         this.classList.contains("active")
                              ? ((e[$(this).attr("data-day")] = [].concat(e[$(this).attr("data-day")], $(this).attr("data-time")).filter(function (e) {
                                   return null != e;
                              })),
                                   $(this).find("img").attr("src", "/assets/images/ico-clock-b.svg"))
                              : $(this).find("img").attr("src", "/assets/images/ico-clock-p.svg");
                    }),
                    $("#agenda_dados").val(JSON.stringify(e));
          }),
          $(".week-schedule-ag").on("click", function () {
               let e = {};
               $("label.week-schedule-ag").each(function () {
                    this.classList.contains("active") &&
                         ((e[$(this).attr("data-day")] = [].concat(e[$(this).attr("data-day")], $(this).attr("data-time")).filter(function (e) {
                              return null != e;
                         })),
                              $(this).removeClass("active"));
               }),
                    $(this).hasClass("active") ? $(this).removeClass("active") : $(this).addClass("active"),
                    $("#ag_data_agendamento").val(`${$(this).attr("data-day")} ${$(this).attr("data-time")}`);
          }),
          $("label.week-schedule").each(function () {
               this.classList.contains("active") ? $(this).find("img").attr("src", "/assets/images/ico-clock-b.svg") : $(this).find("img").attr("src", "/assets/images/ico-clock-p.svg");
          }),
          $("label.week-schedule.week-time").each(function () {
               $(this).off("click");
          }),
          $("label.week-schedule.week-time").on("dblclick", function () {
               let e = $(this);
               $(this).hasClass("active")
                    ? ($(this).removeClass("active"), $(this).find("i[name]").off("click"))
                    : ($(this).addClass("active"),
                         $(this)
                              .find('i[name="online"]')
                              .on("click", function () {
                                   $(this).hasClass("icon-disabled") ? ($(this).removeClass("icon-disabled"), $(e).attr("data-online", !0)) : ($(this).addClass("icon-disabled"), $(e).attr("data-online", !1)),
                                        !1 === $(e).data("presencial") && !1 === $(e).data("online") && ($(e).removeClass("active"), $(e).find("i[name]").off("click"), $(e).find("i[name]").removeClass("icon-disabled"));
                              }),
                         $(this)
                              .find('i[name="presencial"]')
                              .on("click", function () {
                                   $(this).hasClass("icon-disabled") ? ($(this).removeClass("icon-disabled"), $(e).attr("data-presencial", !0)) : ($(this).addClass("icon-disabled"), $(e).attr("data-presencial", !1));
                              }),
                         $(this)
                              .find('i[name="config"]')
                              .on("click", function () {
                                   Swal.fire({
                                        title: "Configurar Agendamento",
                                        html: `
                    <div class="swal2-row">
                         <div class="swal2-input-group">
                             <input type="checkbox" id="modalidade_online" checked>
                             <label for="modalidade_online">ONLINE</label>
                         </div>
                         
                         <div class="swal2-input-group">
                             <input type="checkbox" id="modalidade_pres" checked>
                             <label for="modalidade_pres">PRESENCIAL</label>
                         </div>
                    </div>
                    <div class="swal2-row swal2-df-column">
                         <label for="enderecos_ag">Endere\xe7o de Atendimento (Presencial)</label>
                         <select class="swal2-select" id="enderecos_ag"></select>
                    </div>
                    
                    <div class="swal2-row swal2-df-row">
                         <input type="checkbox" id="check_all_after_this">
                         <label for="check_all_after_this">Copiar para os demais</label>
                    </div>
                    `,
                                        showConfirmButton: !0,
                                        showCancelButton: !0,
                                        allowOutsideClick: !1,
                                        confirmButtonText: "SALVAR",
                                        cancelButtonText: "CANCELAR",
                                        didOpen: function () {
                                             $(".street-object").each(function () {
                                                  let t = JSON.parse(this.value.replaceAll("'", '"'));
                                                  $("#modalidade_online").prop(":checked", "true" === $(e).attr("data-online")),
                                                       $("#modalidade_pres").prop(":checked", "true" === $(e).attr("data-presencial")),
                                                       $("#enderecos_ag").append(`<option value="${t.id}" data-item="${this.value}" data-desc="${t.token}">${t.nome}</option>`);
                                             });
                                        },
                                        preConfirm: function () {
                                             let t = $(e).data("date"),
                                                  i = $(e).index(),
                                                  a = $("#check_all_after_this").is(":checked") ? $(`.week-time[data-date="${t}"]`).length : $(e).index();
                                             $(e).attr("data-atendimento-desc", $("#enderecos_ag").find("option:selected").attr("data-desc")),
                                                  $(e).attr("data-atendimento-name", $("#enderecos_ag").find("option:selected").text()),
                                                  $(e).attr("data-atendimento-id", $("#enderecos_ag").val());
                                             for (let s = i + 1; s < a + 1; s++)
                                                  $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`).trigger("dblclick"),
                                                       $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`).prop("online", $("#modalidade_online").is(":checked")),
                                                       $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`).prop("presencial", $("#modalidade_pres").is(":checked")),
                                                       $("#modalidade_online").is(":checked")
                                                            ? $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`)
                                                                 .find('i[name="online"]')
                                                                 .removeClass("icon-disabled")
                                                            : $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`)
                                                                 .find('i[name="online"]')
                                                                 .addClass("icon-disabled"),
                                                       $("#modalidade_pres").is(":checked")
                                                            ? $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`)
                                                                 .find('i[name="presencial"]')
                                                                 .removeClass("icon-disabled")
                                                            : $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`)
                                                                 .find('i[name="presencial"]')
                                                                 .addClass("icon-disabled"),
                                                       $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`).attr("data-atendimento-desc", $("#enderecos_ag").find("option:selected").attr("data-desc")),
                                                       $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`).attr("data-atendimento-name", $("#enderecos_ag").find("option:selected").text()),
                                                       $(`.week-time[data-date="${t}"]:nth-child(${s + 1})`).attr("data-atendimento-id", $("#enderecos_ag").val());
                                        },
                                   }).then(function (e) {
                                        e.isConfirmed;
                                   });
                              }));
          });
});
let appendTo = function (e, t, i, a) {
     e[t][i] = a;
};
function calendarAgUpdate() {
     let e = {};
     $("label.week-time.week-schedule.listmedic-box-dir-time.active").each(function () {
          (e[$(this).data("date")] = {}), $(this).attr("data-presencial", 0 == $(this).find("i.fa.fa-home.icon-disabled").length), $(this).attr("data-online", 0 == $(this).find("i.fa.fa-globe.icon-disabled").length);
     }),
          $("label.week-time.week-schedule.listmedic-box-dir-time.active").each(function () {
               let t = {
                    endereco: $(this).attr("data-atendimento"),
                    online: $(this).attr("data-online"),
                    presencial: $(this).attr("data-presencial"),
                    date: $(this).attr("data-date"),
                    time: $(this).attr("data-time"),
               };
               appendTo(e, $(this).data("date"), $(this).data("time"), t);
          }),
          $("#agenda_dados").val(JSON.stringify(e).replaceAll('"', "'"));
}
function removeA(e) {
     for (var t, i, a = arguments, s = a.length; s > 1 && e.length;) for (t = a[--s]; -1 !== (i = e.indexOf(t));) e.splice(i, 1);
     return e;
}
function schedule_item(e) {
     let t = {};
     $("button.week-schedule-ag").each(function () {
          this.classList.contains("active") &&
               (t[$(this).attr("data-day")] = [].concat(t[$(this).attr("data-day")], $(this).attr("data-time")).filter(function (e) {
                    return null != e;
               }));
     }),
          $("button.week-schedule-ag").each(function () {
               $(this).removeClass("active");
          }),
          $(e).hasClass("active") ? $(e).removeClass("active") : $(e).addClass("active"),
          $("#ag_data_agendamento").val(`${$(e).attr("data-day")} ${$(e).attr("data-time")}`);
}
Array.prototype.remove = function () {
     for (var e, t, i = arguments, a = i.length; a && this.length;) for (e = i[--a]; -1 !== (t = this.indexOf(e));) this.splice(t, 1);
     return this;
};
