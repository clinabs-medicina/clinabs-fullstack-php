var month = [
        "Janeiro",
        "Fevereiro",
        "Mar\xe7o",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro"
    ],
    weekday = [
        "Domingo",
        "Segunda-feira",
        "Ter\xe7a-feira",
        "Quarta-feira",
        "Quinta-feira",
        "Sexta-feira",
        "S\xe1bado"
    ],
    weekdayShort = [
        "sun",
        "mon",
        "tue",
        "wed",
        "thu",
        "fri",
        "sat"
    ],
    monthDirection = 0;
function getNextMonth() {
    monthDirection++;
    var e,
        t = new Date;
    e = 12 == t.getMonth() ? new Date(t.getFullYear() + monthDirection, 0, 1) : new Date(t.getFullYear(), t.getMonth() + monthDirection, 1),
    $(".calendar-main .calendar-header span .month").html(month[e.getMonth()]),
    $(".calendar-main .calendar-header span .year").html(e.getFullYear()),
    initCalender(getMonth(e))
}
function getPrevMonth() {
    monthDirection--;
    var e,
        t = new Date;
    e = 12 == t.getMonth() ? new Date(t.getFullYear() + monthDirection, 0, 1) : new Date(t.getFullYear(), t.getMonth() + monthDirection, 1),
    $(".calendar-main .calendar-header span .month").html(month[e.getMonth()]),
    $(".calendar-main .calendar-header span .year").html(e.getFullYear()),
    initCalender(getMonth(e))
}
function getMonth(e) {
    var t = new Date,
        a = month[e.getMonth()],
        n = [];
    for (i = 1 - e.getDate(); i < 31; i ++) {
        var r = new Date(e);
        if (r.setDate(e.getDate() + i), a !== month[r.getMonth()]) 
            break;
        
        n.push({
            date: {
                weekday: weekday[r.getDay()],
                weekday_short: weekdayShort[r.getDay()],
                day: r.getDate(),
                month: month[r.getMonth()],
                monthNum: r.getMonth() + 1,
                year: r.getFullYear(),
                current_day: !! t.isSameDateAs(r),
                date_info: r
            }
        })
    }
    return n
}
function clearCalender() {
    $("table#calendarAgendamento tbody tr").each(function () {
        $(this).find("td").removeClass("active selectable currentDay between hover").html("")
    }),
    $("td").each(function () {
        $(this).unbind("mouseenter").unbind("mouseleave")
    }),
    $("td").each(function () {
        $(this).unbind("click")
    }),
    clickCounter = 0
}
function initCalender(e) {
    var t = 0,
        a = "",
        n = "",
        r = new Date;
    clearCalender(),
    $.each(e, function (e, o) {
        var d = o.date.weekday_short,
            l = o.date.day,
            h = 0;
        o.date.current_day && (n = "currentDay", a = "selectable", $(".right-wrapper .calendar-header span").html(o.date.weekday), $(".right-wrapper .day").html(o.date.day), $(".right-wrapper .month").html(o.date.month), $(".calendar-main .calendar-header span .month").html(o.date.month), $(".calendar-main .calendar-header span .year").html(o.date.year)),
        r.getTime() < o.date.date_info.getTime() && (a = "selectable"),
        $("tr.weedays th").each(function () {
            var e = $(this);
            if (e.data("weekday") === d) {
                h = e.data("column");
                return
            }
        }),
        $($($($("tr.days").get(t)).find("td").get(h)).addClass(a + " " + n).html(l)).attr(
            "data-date",
            `${
                o.date.year
            }-${
                (o.date.monthNum < 10 ? "0" : "") + o.date.monthNum
            }-${
                (o.date.day < 10 ? "0" : "") + o.date.day
            }`
        ),
        n = "",
        6 == h && t++
    }),
    $("td.selectable").click(function () {
        dateClickHandler($(this))
    })
}
Date.prototype.isSameDateAs = function (e) {
    return this.getFullYear() === e.getFullYear() && this.getMonth() === e.getMonth() && this.getDate() === e.getDate()
};
var clickCounter = 0;
function dateClickHandler(e) {
    parseInt($(e).html()),
    $("td.selectable").each(function () {
        $(this).removeClass("active between hover")
    });
    let t = $(e).attr("data-date").split("-");
    t[0];
    let a = t[1] - 1,
        n = t[2],
        r = new Date(t).getDay();
    $(".right-wrapper .week").html(weekday[r]),
    $(".right-wrapper .day").html(n),
    $(".right-wrapper .month").html(month[a]),
    calendarClickEvent(e, $(e).attr("data-date"), window.location.pathname)
}
$(".fa-angle-double-right").click(function () {
    $(".right-wrapper").toggleClass("is-active"),
    $(this).toggleClass("is-active")
}),
$(".fa-angle-left").click(function () {
    getPrevMonth(),
    $(".main-calendar").addClass("is-rotated-left"),
    setTimeout(function () {
        $(".main-calendar").removeClass("is-rotated-left")
    }, 195)
}),
$(".fa-angle-right").click(function () {
    getNextMonth(),
    $(".main-calendar").addClass("is-rotated-right"),
    setTimeout(function () {
        $(".main-calendar").removeClass("is-rotated-right")
    }, 195)
});
