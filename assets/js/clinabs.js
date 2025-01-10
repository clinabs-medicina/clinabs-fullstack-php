let tabControl = function (e) {
        let t = document.getElementById(e),
            n = t.querySelectorAll(`.tab-toolbar span[data-tab="${e}"]`),
            a = t.querySelectorAll(`.tab[data-tab="${e}"]`);
        n.forEach(function (e) {
            e.removeEventListener("click", function () {}),
            e.addEventListener("click", function (e) {
                let t = e.target.dataset.tab;
                n.forEach(e => {
                    e.classList.remove("active")
                }),
                a.forEach(e => {
                    e.classList.remove("active")
                }),
                e.target.classList.add("active"),
                a.forEach(function (e) {
                    e.classList.remove("active")
                }),
                $(`.tab.active[data-tab="${t}"]`).removeClass("active"),
                $(`.tab[data-tab="${t}"][data-index="${
                    e.target.dataset.index
                }"]`).addClass("active")
            })
        })
    },
    Slider = function (e, t = 3e3) {
        $(e);
        let n = Array.from($(".slider-box")),
            a = $(".nav-manual"),
            o = $(".slider-content"),
            s = $(".nav-auto"),
            r = $(".btns-slider"),
            l = 0,
            c = n.length,
            d = document.createElement("button");
        d.textContent = "❮";
        let u = document.createElement("button");
        u.textContent = "❯",
        d.classList.add("btn-prev"),
        u.classList.add("btn-next"),
        $(r).append(d),
        $(r).append(u),
        $(d).on("click", function () {
            n[l].classList.remove("active"),
            n[l = l - 1 < 0 ? c - 1 : l - 1].classList.add("active"),
            (document && document.querySelector(".manual-btn.active") && (document.querySelector(".manual-btn.active")).classList) ? (document.querySelector(".manual-btn.active")).classList.remove("active") : null,
            (document && document.querySelectorAll(".manual-btn") && (document.querySelectorAll(".manual-btn")).querySelectorAll(".manual-btn")[l].classList) ? (document.querySelectorAll(".manual-btn")).querySelectorAll(".manual-btn")[l].classList.add("active") : null
        }),
        $(u).on("click", function () {
            g()
        });
        for (let f = 0; f < n.length; f++) {
            let m = document.createElement("label");
            m.setAttribute("for", `slider${
                f + 1
            }`),
            m.classList.add("manual-btn"),
            $(m).on("click", function () {
                clearInterval("timer"),
                (document.querySelector(".manual-btn.active")) ? document.querySelector(".manual-btn.active").classList.remove("active") : null,
                m.classList.add("active"),
                n[l].classList.remove("active"),
                n[l = f].classList.add("active")
            }),
            0 == f && m.classList.add("active");
            let p = document.createElement("input");
            p.setAttribute("id", `slider${
                f + 1
            }`),
            p.setAttribute("type", "radio"),
            p.setAttribute("name", "btn-radio");
            p.setAttribute("placeholder", "slider");
            let h = document.createElement("div");
            h.setAttribute("class", `auto-btn${
                f + 1
            }`),
            $(o).append(p),
            $(a).append(m),
            $(s).append(h)
        }
        function g() {
            (n[l] && n[l].classList) ? n[l].classList.remove("active") : null,
            (n[l = l = l + 1 > c - 1 ? 0 : l + 1] && n[l = l = l + 1 > c - 1 ? 0 : l + 1].classList) ? n[l = l = l + 1 > c - 1 ? 0 : l + 1].classList.add("active") : null,
            (document && document.querySelector(".manual-btn.active") && document.querySelector(".manual-btn.active").classList) ? document.querySelector(".manual-btn.active").classList.remove("active") : null,
            (document && document.querySelectorAll(".manual-btn") && document.querySelectorAll(".manual-btn")[l] && document.querySelectorAll(".manual-btn")[l].classList) ? document.querySelectorAll(".manual-btn")[l].classList.add("active") : null
        }
        if ($(e)) 
            $(e).length > 0 && setInterval(g, t)


        


    };
function isNull(e) {
    return 0 === e.length && "Selecione uma Op\xe7\xe3o" == e && "undefined" == e
}
function isNotNull(e) {
    return 0 !== e.length && "Selecione uma Op\xe7\xe3o" !== e && "undefined" !== e
}
function FromHex() {
    for (var e = this.toString(), t = "", n = 0; n < e.length; n += 2) 
        t += String.fromCharCode(parseInt(e.substr(n, 2), 16));
    


    return t
}
function ToHex() {
    for (var e = "", t = 0; t < this.length; t++) 
        e += "" + this.charCodeAt(t).toString(16);
    


    return e
}
function convertDataURIToBinary(e) {
    var t = ";base64,",
        n = e.indexOf(t) + t.length,
        a = e.substring(n),
        o = window.atob(a),
        s = o.length,
        r = new Uint8Array(new ArrayBuffer(s));
    for (i = 0; i < s; i ++) 
        r[i] = o.charCodeAt(i);
    


    return r
}
function getFile(e) {
    let t = new FileReader;
    return new Promise((n, a) => {
        t.onerror = () => {
            t.abort(),
            a(Error("Error parsing file"))
        },
        t.onload = function () {
            let t = btoa(Array.from(new Uint8Array(this.result)).map(e => String.fromCharCode(e)).join(""));
            n({base64StringFile: t, fileName: e.name, fileType: e.type})
        },
        t.readAsArrayBuffer(e)
    })
}
function getFileBase64(e, t) {
    let n = e.files[0],
        a = new FileReader;
    a.onload = function (e) {
        let n = e.target.result;
        t(n)
    },
    a.readAsDataURL(n)
}
let fetchBlob = function (e, t, n = {}, a = "GET") {
        let o = new XMLHttpRequest;
        o.open(a.toUpperCase(), e, !0),
        o.onloadend = function () {
            200 !== o.status ? t(null) : t(o.response)
        },
        o.responseType = "blob",
        o.send(new URLSearchParams(n).toString())
    },
    fetchJSON = function (e, t) {
        let n = new XMLHttpRequest;
        n.open("GET", e, !0),
        n.onloadend = function () {
            if (200 !== n.status) 
                t(null);
             else 
                try {
                    t(JSON.parse(n.response))
                }
             catch {}},
        n.send()
    };
function makeid(e) {
    let t = "",
        n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",
        a = n.length,
        o = 0;
    for (; o < e;) 
        t += n.charAt(Math.floor(Math.random() * a)),
        o += 1;
    


    return t
}
let wizardModal = function (e, t = {
    btnSubmitText : "ENVIAR",
    validate : !0,
    autoSave : !1,
    autoFill : !1,
    onFinish,
    onValidate,
    onStep,
    onUpload,
    autoHeight : !0,
    onBuild
}, n = function () {}) {
    let a = $(`${e}`),
        o = 0,
        s = a.find(".stepIndicator"),
        r = a.find(".step");
    for (let l = 0; l < r.length; l++) 
        $(r[l]).attr("data-step", l + 1);
    


    $(r[0]).addClass("active"),
    $(s[0]).addClass("active"),
    $(`${e} .form-footer button[name]`).on("click", function (n) {
        let l = 0,
            c = 0,
            d = [];
        if (t.validate && "btn-step-prev" !== this.name) {
            if ($(r[o]).find("*[required]").each(function () {
                0 === this.value.length ? ($(this).addClass("input-error"), "select" == this.tagName ? ($(`span[aria-controls="select2-${
                    this.id
                }-container]`).addClass("input-error"), d.push($(`span[aria-controls="select2-${
                    this.id
                }-container]`)), t.onValidate($(`span[aria-controls="select2-${
                    this.id
                }-container]`), "error")) : (d.push($(this)), t.onValidate(this, "error"))) : ($(this).removeClass("input-error"), t.onValidate(this, "success"))
            }), 0 == d.length) {
                if (o !== r.length) {
                    $(this).hasClass("btn-step-next") ? ($(r[o]).removeClass("active"), $(s[o]).removeClass("active"), $(r[o = (o + 1) % r.length]).addClass("active"), $(s[o]).addClass("active"), l =( o + 1) % r.length, c =( o - 1) % r.length) : ($(r[o]).removeClass("active"), $(s[o]).removeClass("active"), $(r[o = (o - 1) % r.length]).addClass("active"), $(s[o]).addClass("active"), l =( o + 1) % r.length, c =( o - 1) % r.length),
                    $(s).each(function (e) {
                        $(this).removeClass("active"),
                        $(this).removeClass("finish")
                    });
                    for (let u = 0; u < o + 1; u++) 
                        $(s[u]).addClass("active"),
                        $(s[u - 1]).addClass("finish")


                    


                }
            } else 
                d[0].focus()


            


        } else 
            o !== r.length && ($(this).hasClass("btn-step-next") ? ($(r[o]).removeClass("active"), $(s[o]).removeClass("active"), $(r[o = (o + 1) % r.length]).addClass("active"), $(s[o]).addClass("active"), l =( o + 1) % r.length, c =( o - 1) % r.length) : ($(r[o]).removeClass("active"), $(s[o]).removeClass("active"), $(r[o = (o - 1) % r.length]).addClass("active"), $(s[o]).addClass("active"), l =( o + 1) % r.length, c =( o - 1) % r.length));
        


        let f = {
            form: a,
            currentStep: o + 1,
            prevStep: c + 1,
            nextStep: l + 1,
            maxSteps: r.length,
            currentStepTitle: $(s[o]).text(),
            canNext: o !== r.length - 1,
            canBack: 0 !== o,
            btnPrev: $(`${e} .form-footer .btn-step-prev`),
            btnNext: $(`${e} .form-footer .btn-step-next`),
            btnFinish: $(`${e} .form-footer .btn-step-submit`)
        };
        $(`${e} .form-footer .btn-step-prev`).attr("disabled", ! f.canBack),
        f.canNext ? $(`${e} .form-footer .btn-step-next`).show() : $(`${e} .form-footer .btn-step-next`).hide(),
        t.onStep(f)
    }),
    t.autoSave && ($(".form-footer .btn-step-submit").hide(), $(a).find("input").on("input", function () {
        "file" == this.type ? getBase64File(this, function (e, t) {
            localStorage.setItem(t.id, e)
        }) : localStorage.setItem(this.name, this.value)
    }), $(a).find("select").on("change", function () {
        localStorage.setItem(this.name, this.value)
    })),
    t.autoFill && ($(a).find("input").each(function () {
        null !== localStorage.getItem(this.name) && ("file" == this.type ? $(`input[name="${
            this.id
        }"]`).val(localStorage.getItem(this.name)) : this.value = localStorage.getItem(this.name))
    }), $(a).find("select").each(function () {
        null !== localStorage.getItem(this.name) && $(this).find(`option[value=${
            localStorage.getItem(this.name)
        }]`).prop("selected", !0).val(localStorage.getItem(this.name))
    }));
    let c;
    n({
        form: a,
        btnPrev: $(`${e} .form-footer .btn-step-prev`),
        btnNext: $(`${e} .form-footer .btn-step-next`),
        btnFinish: $(`${e} .form-footer .btn-step-submit`)
    }),
    $(a).css("height", "auto"),
    $(`${e} .form-footer .btn-step-prev`).attr("disabled", 0 === o),
    $(`${e} .form-footer .btn-step-next`).attr("disabled", o === r.length - 1)
};
function getBase64File(e, t) {
    var n = new FileReader;
    n.readAsDataURL(e.files[0]),
    n.onload = function () {
        t(n.result, e)
    },
    n.onerror = function (e) {}
}
function setCookie(e, t, n) {
    var a = "";
    if (n) {
        var o = new Date;
        o.setTime(o.getTime() + 864e5 * n),
        a = "; expires=" + o.toUTCString()
    }
    document.cookie = e + "=" + (
        t || ""
    ) + a + "; path=/"
}
function getCookie(e) {
    for (var t = e + "=", n = document.cookie.split(";"), a = 0; a < n.length; a++) {
        for (var o = n[a]; " " == o.charAt(0);) 
            o = o.substring(1, o.length);
        


        if (0 == o.indexOf(t)) 
            return o.substring(t.length, o.length)


        


    }
    return null
}
function eraseCookie(e) {
    document.cookie = e + "=; Max-Age=-99999999;"
}
class Modal {
    static dialog(e, t = "Aten\xe7\xe3o", n = "info", a = !1, o = !1, s = !1) {
        let r = document.createElement("section"),
            l = document.createElement("div"),
            c = document.createElement("div"),
            d = document.createElement("div"),
            u = document.createElement("span"),
            f = document.createElement("div"),
            m = document.createElement("div"),
            p = document.createElement("img"),
            h = document.createElement("div"),
            g = document.createElement("div"),
            v = document.createElement("button"),
            b = document.createElement("button"),
            w = document.createElement("span");
        $(r).append(l),
        $(l).append(c),
        $(c).append(d),
        $(c).append(f),
        $(d).append(u),
        $(f).append(m),
        $(m).append(p),
        $(f).append(h),
        a && $(g).append(v),
        o && $(g).append(b),
        a || o ? (c.classList.add("modal-dialog"), $(c).append(g)) : c.classList.add("modal-dialog-nfooter"),
        r.classList.add("modal-overlay"),
        l.classList.add("modal"),
        d.classList.add("modal-header"),
        f.classList.add("modal-body"),
        g.classList.add("modal-footer"),
        u.classList.add("modal-title"),
        w.classList.add("modal-close"),
        m.classList.add("modal-img"),
        h.classList.add("modal-content"),
        p.src = `assets/images/ico-${n}.svg`,
        u.textContent = t,
        h.innerHTML = e,
        $(w).html("&times;"),
        d.classList.add(`modal-${n}`),
        $(w).on("click", function () {
            $(r).fadeOut(700, function () {
                $(r).remove(),
                $("body").css("overflow", "auto")
            })
        }),
        $("html").find(".modal-overlay").length > 0 ? ($("bohtmldy").find(".modal-overlay").remove(), $("html").append(r).css("overflow", "hidden")) : ($(d).append(w), $("html").append(r), $("html").css("overflow", "hidden"))
    }
    static toast(e, t = "info", n, a = 5e3) {
        let o = document.createElement("section"),
            s = document.createElement("div"),
            r = document.createElement("div"),
            l = document.createElement("div"),
            c = document.createElement("div"),
            d = document.createElement("img"),
            u = document.createElement("div");
        $(o).append(s),
        $(s).append(r),
        $(r).append(l),
        $(l).append(c),
        $(c).append(d),
        $(l).append(u),
        o.classList.add("modal-overlay-toast"),
        s.classList.add("modal-toast"),
        l.classList.add("modal-body-toast"),
        c.classList.add("modal-img-toast"),
        u.classList.add("modal-content-toast"),
        d.src = "https://clinabs.com.br/assets/images/ico-smile.svg",
        u.innerText = e,
        $("body").find(".modal-overlay-toast").length > 0 && $("body").find(".modal-overlay-toast").remove(),
        $("body").append(o),
        modal_header.classList.add(`modal-${t}`),
        setTimeout(function () {
            $("body").find(".modal-overlay-toast").fadeOut(700, function () {
                $("body").find(".modal-overlay-toast").remove()
            })
        }, a)
    }
}
let fetchForm = function (e, t, n) {
    $(e).on("submit", function (a) {
        a.preventDefault(),
        $(e).find("type[submit]").attr("disabled", !0);
        let o = new FormData;
        $(this).find("[name]").each(function () {
            o.append(this.name, this.value)
        });
        let s = $(e).attr("action");
        Swal.fire({
            title: "",
            html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Aguarde...</span></div>",
            width: "256px",
            showCancelButton: !1,
            showConfirmButton: !1,
            allowOutsideClick: !1,
            didOpen: function () {
                fetch(s, {
                    method: "POST",
                    body: o
                }).then(e => {
                    if (200 != e.status) 
                        throw Error("Bad Server Response");
                    


                    return e.json()
                }).then(e => t(e)).catch(e => n(e)). finally(function (t) {
                    $(e).find("type[submit]").removeAttr("disabled")
                })
            }
        })
    })
};
function enableAll() {
    $("input, select, textarea").each(function () {
        $(this).removeAttr("disabled")
    })
}
function disableAll() {
    $("input, select").each(function () {
        $(this).removeAttr("disabled")
    })
}
function thread(e) {
    return new Promise(t => {
        t(e)
    })
}
function setDefault(e, t, n) {
    $(".grid-image-item").each(function () {
        $(this).css("border", "10px"),
        $(this).find(".sd-grid-image-item").removeAttr("selected")
    }),
    $(t).css("border", "5px dotted green"),
    $(e).val(n),
    $(t).attr("selected", "true")
}
function passwordStrength(e, t = 8, n = 3) {
    $(e).on("input", function () {
        let n = $(e).val(),
            a = 0,
            o = "";
        n.length < t ? o += "Torne a senha mais longa. " : a += 1,
        n.match(/[a-z]/) && n.match(/[A-Z]/) ? a += 1 : o += "Use letras min\xfasculas e mai\xfasculas. ",
        n.match(/\d/) ? a += 1 : o += "Inclua pelo menos um n\xfamero. ",
        n.match(/[^a-zA-Z\d]/) ? a += 1 : o += "Inclua pelo menos um caractere especial. ";
        let s = $("#password-strength");
        a >= 2 && n.length >= t ? $('input[type="submit"], button[type="submit"]').removeAttr("disabled") : ($('input[type="submit"], button[type="submit"]').attr("disabled", !0), o +=
            `Tamanho M\xednimo de ${t} caracteres`),
        a < 2 ? (s.text("F\xe1cil de adivinhar. " + o), s.css("color", "red")) : 2 === a && (s.text("Dificuldade m\xe9dia. " + o), s.css("color", "orange")),
        3 === a ? (s.text("Dif\xedcil. " + o), s.css("color", "black")) : a > 3 && (s.text("Extremamente dif\xedcil. " + o), s.css("color", "green"))
    })
}
function doAction(e, t) {
    let n = $(e).data("token");
    switch (t) {
        case "cart-products":
            Swal.fire({
                title: "",
                html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Verificando Paciente.</span></div>",
                width: "350px",
                showCancelButton: !1,
                showConfirmButton: !1,
                allowOutsideClick: !1,
                didOpen: function () {
                    $.getJSON(`/forms/login.paciente.php?token=${n}`).done(function (e) {
                        "success" === e.status ? (localStorage.setItem("clinabs_visited", !0), Swal.fire({
                            width: "450px",
                            html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Iniciando Sess\xe3o..</span></div>",
                            width: "256px",
                            showCancelButton: !1,
                            showConfirmButton: !1,
                            timer: 3e3,
                            timerProgressBar: !0,
                            allowOutsideClick: !1,
                            didClose: function () {
                                window.location = "/produtos"
                            }
                        })) : Swal.fire({
                            title: "Aten\xe7\xe3o",
                            icon: "warning",
                            text: e.text,
                            allowOutsideClick: !1
                        })
                    })
                }
            });
            break;
        case "agendar-consulta":
            localStorage.setItem("paciente_token", n),
            Swal.fire({
                title: "",
                html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Verificando Paciente.</span></div>",
                width: "450px",
                showCancelButton: !1,
                showConfirmButton: !1,
                allowOutsideClick: !1,
                didOpen: function () {
                    $.getJSON(`/forms/login.paciente.php?token=${n}`).done(function (e) {
                        "success" === e.status ? Swal.fire({
                            title: "",
                            width: "350px",
                            html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Iniciando Sess\xe3o..</span></div>",
                            width: "256px",
                            showCancelButton: !1,
                            showConfirmButton: !1,
                            timer: 3e3,
                            timerProgressBar: !0,
                            allowOutsideClick: !1,
                            didClose: function () {
                                if (null !== localStorage.getItem("url_redirect")) {
                                    let e = localStorage.getItem("url_redirect");
                                    localStorage.removeItem("url_redirect"),
                                    window.location = e
                                } else 
                                    window.location = "/"


                                


                            }
                        }) : Swal.fire({
                            title: "Aten\xe7\xe3o",
                            text: e.text,
                            icon: "error",
                            allowOutsideClick: !1
                        })
                    })
                }
            });
            break;
        case "aceitar-consulta":
        case "recusar-consulta":
            localStorage.setItem("paciente_token", n),
            window.location = "/agenda/consulta";
            break;
        case "ag-action":
            {
                let a = $(e).data("action-event");
                preloader(),
                "" === a ? $.get(`/forms/agenda.actions.php?action=${a}&token=${n}`, function (e) {
                    let t = $(`#${n}`);
                    $(t).find(".col-sb").text("success" == e.status ? e.newStatus : $(t).find(".col-sb").text()),
                    toast(e.text, e.status)
                }) : $.get(`/forms/agenda.actions.php?action=${a}&token=${n}`, function (e) {
                    let t = $(`#${n}`);
                    $(t).find(".col-sb").text("success" == e.status ? e.newStatus : $(t).find(".col-sb").text()),
                    toast(e.text, e.status)
                });
                break
            }
        case "editar-perfil":
            window.location = `/perfil/${n}`;
            break;
        case "editar-produto":
            window.location = `/produtos/editar/${n}`;
            break;
        case "agenda-edit":
            window.location = `/agenda/editar/${n}`;
            break;
        case "agenda-accept":
            Swal.fire({
                showCancelButton: !1,
                showConfirmButton: !1,
                width: 300,
                allowOutSideClick: !1,
                html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Processando...</span></div>",
                didOpen: function () {
                    $.getJSON(`/forms/agenda.actions.php?paciente_token=${
                        $(e).data("paciente")
                    }&action=${t}&token=${n}&ref=${
                        $(e).data("status")
                    }`, function (t) {
                        if ("success" === t.status) {
                            let a = $(`#${n}`);
                            $(a).find(".column-status").text("success" == t.status ? t.newStatus : $(a).find(".column-status").text()),
                            $(e).attr("data-action", t.action),
                            $(e).attr("data-status", t.newStatus)
                        }
                        toast(t.text, t.status)
                    })
                }
            });
            break;
        case "agenda-payment-accept":
            Swal.fire({
                showCancelButton: !1,
                showConfirmButton: !1,
                width: 300,
                allowOutSideClick: !1,
                html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Processando...</span></div>",
                didOpen: function () {
                    $.getJSON(`/forms/agenda.actions.php?paciente_token=${
                        $(e).data("paciente")
                    }&action=${t}&token=${n}&ref=${
                        $(e).data("status")
                    }`, function (t) {
                        if ("success" === t.status) {
                            let a = $(`#${n}`);
                            $(a).find(".column-status").text("success" == t.status ? t.newStatus : $(a).find(".column-status").text()),
                            $(e).attr("data-action", t.action),
                            $(e).attr("data-status", t.newStatus)
                        }
                        toast(t.text, t.status)
                    })
                }
            });
            break;
        case "agenda-cancel":
            Swal.fire({
                title: "Aten\xe7\xe3o",
                text: "Deseja Cancelar Consulta?",
                icon: "question",
                showCancelButton: !0,
                showConfirmButton: !0,
                confirmButtonText: "SIM",
                cancelButtonText: "N\xc3O",
                allowOutSideClick: !1
            }).then(function (t) {
                if (t.isConfirmed) {
                    let a = $(`#${n}`),
                        o = $(e).data("action");
                    $(a).find(".paciente-nome").text(),
                    $(a).find(".datetime-column").text(),
                    $.get("formularios/agenda.actions.php", {
                        token: n,
                        action: o
                    }, function (e) {
                        Swal.fire({title: "Aten\xe7\xe3o", icon: e.status, text: e.text})
                    })
                }
            });
            break;
        case "agenda-doc":
            {
                let o = document.createElement("a");
                o.href = `/api/pdf/?token=${n}`,
                o.setAttribute("download", "PRONTUARIO_MEDICO.pdf"),
                o.click(),
                window.location = "/agenda";
                break
            }
        case "agenda-meet":
            window.open(`${
                $(e).data("room")
            }}`, "CLINABS_MEET");
            break;
        case "agenda-pix":
            {
                let s = $(e).data("money");
                Swal.fire({
                    text: "Gerando PIX...",
                    imageUrl: "/assets/images/loading.gif",
                    imageWidth: 64,
                    imageHeight: 64,
                    shoeCancelButton: !1,
                    showConfirmButton: !1,
                    didOpen: function () {
                        $.getJSON(`/api/pix/index.php?valor=${s}&identificador=5656565&descricao=CONSULTA MEDICA CLINABS`, function (e) {
                            Swal.fire({
                                    confirmButtonText: "Fechar Janela", imageUrl: "/api/pix/logo_pix.png", html: `<p>Use seu App de Pagamento para Escanear o QrCode para Pagamento</p><img height="256px" src="${
                                    e.imageString
                                }" alt=""><p><b>Valor:</b>  R$ ${
                                    e.valor.toLocaleString("pt-br", {
                                        style: "currency",
                                        currency: "BRL"
                                    })
                                }<p><b>Beneficiario:</b> ${
                                    e.beneficiario
                                }`
                            })
                        })
                    },
                    allowOutSideClick: !1,
                    timer: 5e3
                }).then(function () {
                    window.location = "/agenda"
                });
                break
            }
        case "delete-medico":
            Swal.fire({
                title: "Aten\xe7\xe3o",
                text: "Deseja Deletar este M\xe9dico?",
                icon: "question",
                showCancelButton: !0,
                showConfirmButton: !0,
                confirmButtonText: "SIM",
                cancelButtonText: "N\xc3O",
                allowOutSideClick: !1
            }).then(function (e) {
                e.isConfirmed && $.getJSON(`/forms/medicos.actions.php?action=${t}&token=${n}`, function (e) {
                    "success" == e.status && $(`#${n}`).remove(),
                    toast(e.text, e.status)
                })
            })
    }
}
function actionBtn(e) {
    $(e).attr("data-token");
    let t = $(e).attr("data-action");
    return doAction(e, t)
}
function import_wa(e) {
    let t = $(e).data("cell");
    Swal.fire({
        title: "Aten\xe7\xe3o",
        text: `Deseja Solicitar ao WhatsApp a Imagem de Perfil do N\xfamero ${t} para esta Conta?`,
        icon: "question",
        showConfirmButton: !0,
        showDenyButton: !0,
        confirmButtonText: "SIM",
        denyButtonText: "N\xc3O"
    }).then(function (e) {
        e.isConfirmed && $.get(`/forms/wa-img.php?number=${t}`).done(function () {
            toast("Solicita\xe7\xe3o Realizada com Sucesso!", "success")
        }).fail(function () {
            toast("Erro ao Solicitar!", "error")
        })
    })
}
function onSubmit(e) {
    let t = $("form").attr("id");
    $(`#${t}`).attr("id", `${t}-secure`),
    $("#form-login-secure").on("submit", function (e) {
        e.preventDefault(),
        $(this).serialize(),
        Swal.fire({
            title: "",
            html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Fazendo Login...</span></div>",
            width: "350px",
            showCancelButton: !1,
            showConfirmButton: !1,
            allowOutsideClick: !1,
            didOpen: function () {
                $.get(`/forms/login.form.php?usuario=${
                    $("#user-login").val()
                }&password=${
                    $("#user-password").val()
                }&redirect=${
                    $("#redirect").val()
                }`).done(function (e) {
                    "success" === e.status ? Swal.fire({
                        title: "",
                        width: "350px",
                        html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Iniciando Sess\xe3o..</span></div>",
                        width: "256px",
                        showCancelButton: !1,
                        showConfirmButton: !1,
                        timer: 3e3,
                        timerProgressBar: !0,
                        allowOutsideClick: !1,
                        didClose: function () {
                            if (null !== localStorage.getItem("url_redirect")) {
                                var e = localStorage.getItem("url_redirect");
                                localStorage.removeItem("url_redirect"),
                                window.location = e
                            } else 
                                window.location = "/"


                            


                        }
                    }) : Swal.fire({
                        title: "Aten\xe7\xe3o",
                        text: e.text,
                        icon: "error",
                        allowOutsideClick: !1
                    })
                })
            }
        })
    }),
    $("#form-login-recovery-secure").on("submit", function (e) {
        e.preventDefault(),
        $(this).serialize(),
        Swal.fire({
            title: "",
            html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Verificando seu Cadastro...</span></div>",
            width: "450px",
            showCancelButton: !1,
            showConfirmButton: !1,
            allowOutsideClick: !1,
            didOpen: function () {
                $.post(`/forms/login.form.recovery.php?token=${
                    $('input[name="token"]').val()
                }&usuario=${
                    $("#user-login").val() || $("#token").val()
                }&password=${
                    $("#user-password").val()
                }&confirmPassword=${
                    $("#user-password-confirm").val()
                }&action=${
                    $('input[name="action"]').val()
                }`).done(function (e) {
                    "success" === e.status ? Swal.fire({
                        title: "",
                        width: "450px",
                        html: "<div style='display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;justify-content: flex-start;align-items: center;gap: 1rem;'><img src='/assets/images/loading.gif' height='64px'><span>Validando nova Senha..</span></div>",
                        width: "450px",
                        showCancelButton: !1,
                        showConfirmButton: !1,
                        timer: 3e3,
                        timerProgressBar: !0,
                        allowOutsideClick: !1,
                        didClose: function () {
                            Swal.fire({
                                title: "Aten\xe7\xe3o",
                                text: e.text,
                                icon: e.status,
                                allowOutsideClick: !1
                            }).then(function () {
                                if (null !== localStorage.getItem("redirect_uri")) {
                                    let e = localStorage.getItem("redirect_uri");
                                    localStorage.removeItem("redirect_uri"),
                                    window.location = e
                                } else 
                                    window.location = "/"


                                


                            })
                        }
                    }) : Swal.fire({
                        title: "Aten\xe7\xe3o",
                        text: e.text,
                        icon: "error",
                        allowOutsideClick: !1
                    })
                })
            }
        })
    }),
    $(`#${t}-secure`).submit()
}
function addStreet() {
    $(".street-container").hide(),
    $(".street-editor").show(),
    $(".street-editor").find("input#endereco_nome.form-control").attr("required", !0),
    $(".street-editor").find("select#tipo_endereco").attr("required", !0),
    $(".street-editor").find("input#cep.form-control").attr("required", !0),
    $(".street-editor").find("input#endereco.form-control").attr("required", !0),
    $(".street-editor").find("input#numero.form-control").attr("required", !0),
    $(".street-editor").find("input#cidade.form-control").attr("required", !0),
    $(".street-editor").find("input#bairro.form-control").attr("required", !0),
    $(".street-editor").find("select#uf")
}
function addAddress() {
    if (0 === $(".street-editor").find("[name][required]").filter(function () {
        return 0 === this.value.length
    }).length) {
        preloader();
        let e = {};
        $(".street-editor").find("[name][required]").each(function () {
            e[this.name] = this.value
        }),
        0 == $(".street-item").length ? e.isDefault = !0 : e.isDefault = !1;
        let t = $(".street-item").length + 1;
        $.get("/forms/uniqid.php", function (n) {
            let a = `
        <div class="street-item" id="${n}">
            <div class="street-info">
                <b>${
                $("#endereco_nome").val()
            } (${
                $("select#tipo_endereco").val()
            })</b>
                <span>${
                $("#endereco").val()
            }, ${
                $("#numero").val()
            }</span>
                <span>${
                $("#cidade").val()
            }/${
                $("#uf").val()
            }</span>
                <span>${
                $("#cep").val()
            }</span>
            </div>
            <div class="street-btns">
                <div class="btns-info">
                <label for="address-${t}"></label>
                </div>

                <div class="btns-street">
                <legend data-action="editar" data-token="${n}">Editar</legend>
                <legend data-action="excluir" data-token="${n}">Excluir</legend>
                <legend data-action="padrao" data-token="${n}">Deixar Padr\xe3o</legend>
                </div>
            </div>

            <input type="hidden" name="enderecos[]" data-token="${n}" value="${
                JSON.stringify(e).replace(/"/g, "'")
            }">
    </div>`;
            $(".street-item").length > 0 ? $(".street-container p").before(a) : $(".street-container").append(a),
            $(".street-editor").find("[name]").each(function () {
                $(this).removeAttr("required"),
                $(this).val("")
            }),
            Swal.close()
        }),
        $(".street-container").show(),
        $(".street-editor").hide()
    } else 
        $(".street-editor").find("[name][required]").filter(function () {
            return 0 === this.value.length
        })[0].focus()


    


}
function addAddress2() {
    preloader();
    let e = {};
    $(".street-editor").find("[name]").each(function () {
        e[this.name] = this.value
    }),
    0 == $(".street-item").length ? e.isDefault = !0 : e.isDefault = !1;
    let t = $(".street-item").length + 1;
    $.get("/forms/uniqid.php", function (n) {
        let a = `
        <div class="street-item" id="${n}">
            <input type="radio" class="form-control" name="endereco" value="${n}">
            <div class="street-info">
                <b>${
            $("#endereco_nome").val()
        }</b>
                <span>${
            $("#endereco").val()
        }, ${
            $("#numero").val()
        }</span>
                <span>${
            $("#cidade").val()
        }/${
            $("#uf").val()
        }</span>
                <span>${
            $("#cep").val()
        }</span>
            </div>
            <div class="street-btns">
                <div class="btns-info">
                <label for="address-${t}"></label>
            </div>
            </div>

            <input type="hidden" name="enderecos[]" data-token="${n}" value="${
            JSON.stringify(e).replace(/"/g, "'")
        }">
    </div>`;
        $(".street-item").length > 0 ? $(".street-item").after(a) : $(".street-container").append(a),
        $(".street-editor").find("[name]").each(function () {
            this.value = ""
        }),
        Swal.close()
    }),
    $(".street-container").show(),
    $(".street-editor").hide()
}
$("document").ready(function () {
    $('form[validation="recaptcha"]').on("submit", function (e) {
        e.preventDefault(),
        grecaptcha.execute()
    })
}),
passwordStrength($('#form-login-recovery input[name="password"]')),
passwordStrength($('#form-login-recovery-secure input[name="password"]')),
$("document").ready(function () {
    $("i[data-download]").on("click", function () {
        let e = document.createElement("a"),
            t = $(this).data("download");
        e.setAttribute("href", `/data/docs/${t}.pdf`),
        e.setAttribute("download", `${t}.pdf`),
        e.click()
    }),
    $("#certificado").bind("change", function (e) {
        let t = this.files[0];
        Swal.fire({
            title: "Autentica\xe7\xe3o de Certificado",
            input: "password",
            width: 500,
            showCancelButton: !1,
            showConfirmButton: !0,
            showDenyButton: !0,
            closeOnConfirm: !1,
            confirmButtonText: "VALIDAR",
            denyButtonText: "CANCELAR",
            animation: "slide-from-top",
            inputPlaceholder: "Digite a Senha do Certificado",
            allowOutSideClick: !1
        }).then(function (e) {
            if (e.isConfirmed) {
                let n = e.value;
                if ("" === n) 
                    return swal.showInputError("Digite sua Senha!"),
                    !1;
                


                var a = new FileReader;
                a.onload = function (e) {
                    Swal.fire({
                        showCancelButton: !1,
                        showConfirmButton: !1,
                        allowOutsideClick: !1,
                        text: "Processando Certificado...",
                        didOpen: function () {
                            $.post("/api/certificado/index.php", {
                                cert: btoa(e.target.result),
                                pwd: n
                            }, function (e) {
                                if ("error" in e) 
                                    Swal.fire({
                                        title: "Aten\xe7\xe3o",
                                        icon: "warning",
                                        allowOutSideClick: !1,
                                        text: e.error
                                    }).then(function () {
                                        window.location.reload()
                                    });
                                 else {
                                    let t = $("#nome_completo").val().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/^\s+|\s+$/gm, "");
                                    $("#cert_common_name").val(e.name),
                                    $("#issuer_standard").val(e.signer),
                                    $("#issuer").val(e.organization),
                                    $("#serial_number").val(e.serial),
                                    $("#pfx_path").val(e.path),
                                    $("#pfx_passwd").val(btoa(e.passwd)),
                                    $("#certificate").val(window.btoa(JSON.stringify(e))),
                                    e.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/^\s+|\s+$/gm, "").trim() === t.replace(/^\s+|\s+$/gm, "").trim() ? Swal.close() : Swal.fire({
                                        title: "Aten\xe7\xe3o",
                                        icon: "warning",
                                        allowOutSideClick: !1,
                                        html: `Esta Assinatura Digital n\xe3o pode Ser Utilizada por <b>${t}</b>! <br><br>Esta Assinatura Pertence a <b style="color: red !important"><p>${
                                            e.name
                                        }</p>.</b>`
                                    }).then(function () {
                                        window.location.reload()
                                    })
                                }
                            })
                        }
                    })
                },
                a.readAsBinaryString(t)
            }
        })
    })
});
