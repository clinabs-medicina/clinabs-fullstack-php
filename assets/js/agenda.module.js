document.addEventListener('DOMContentLoaded', function () {
  $('.week-head, button.btn-button1.ag-config').on('click', function () {
    configAgenda(
      $(this).data('date'),
      $(this).data('timestart'),
      $(this).data('timeend'),
      $(this).data('duration'),
      $(this).data('week'));
  });
})
function getDates(startDate, endDate, daysOfWeek, startTime, endTime, duration) {
  const start_date = new Date(startDate);
  const end_date = new Date(endDate);
  end_date.setDate(end_date.getDate() + 1);

  const week_names = {
    '0': 'SU',
    '1': 'MO',
    '2': 'TU',
    '3': 'WE',
    '4': 'TH',
    '5': 'FR',
    '6': 'SA'
  };

  let results = [];



  start_date.setDate(start_date.getDate() + 1);

  while (start_date <= end_date) {
    const date = new Date(start_date);
    const day = date.getDate() > 9 ? date.getDate() : "0" + date.getDate();
    const month = date.getMonth() + 1 > 9 ? date.getMonth() + 1 : "0" + (date.getMonth() + 1);
    const year = date.getFullYear();
    const formatted_date = `${day}/${month}/${year}`;
    const week = date.getDay();

    if (daysOfWeek.includes(week_names[week])) {
      results.push({ "date": formatted_date, "week": week_names[week], "times": getTimeSlots(startTime, endTime, duration) });
    }

    start_date.setDate(start_date.getDate() + 1);
  }


  return results;
}

function getTimeSlots(start, end, interval) {
  const slots = [];
  const startTime = new Date(`1970-01-01T${start}:00`);
  const endTime = new Date(`1970-01-01T${end}:00`);

  for (let time = startTime; time <= endTime; time.setMinutes(time.getMinutes() + interval)) {
    slots.push(time.toTimeString().slice(0, 5));
  }

  return slots;
}



function configAgenda(dateStart, startTime, endTime, duration, w) {
  Swal.fire({
    title: 'Configuração de Agenda',
    allowOutsideClick: false,
    html: `
          <div class="row" id="row-unidade">
                <div class="col-12">
                    <div class="form-group">
                      <label for="unidade_atendimento">Unidade de Atendimento</label>
                      <select name="unidade_atendimento" id="unidade_atendimento">
                        <option disabled selected>Selecione uma Opção</option>
                      </select>
                    </div>
                </div>
          </div>
      <div class="row row-config-agenda hide">
         <br>
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="start_date">Data de Início</label>
              <input disabled name="start_date" id="start_date" type="date" value="${dateStart}">
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="end_date">Data de Fim</label>
              <input disabled name="end_date" id="end_date" type="date" value="${dateStart}">
            </div>
          </div>
        </div>
        <br>
         <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="start_time">Hora de Início</label>
              <select disabled name="start_time" id="start_time" type="time" value="${startTime}"></select>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="end_time">Hora de Fim</label>
              <select disabled name="end_time" id="end_time" type="time" value="${endTime}"></select>
            </div>
          </div>
        </div>
        <br>
        <div class="row-title">Repetir em:</div>
        <br>
        <div class="row-weeks">
            <div class="week-item${w == 1 ? ' active' : ''}">
              <label for="dayofWeek_1">SEG</label>
              <input disabled name="dayofWeek[]" id="dayofWeek_1" type="hidden" value="MO">
            </div>

            <div class="week-item${w == 2 ? ' active' : ''}">
              <label for="dayofWeek_2">TER</label>
              <input disabled name="dayofWeek[]" id="dayofWeek_2" type="hidden" value="TU">
            </div>

            <div class="week-item${w == 3 ? ' active' : ''}">
              <label for="dayofWeek_3">QUA</label>
              <input disabled name="dayofWeek[]" id="dayofWeek_3" type="hidden" value="WE">
            </div>

            <div class="week-item${w == 4 ? ' active' : ''}">
              <label for="dayofWeek_4">QUI</label>
              <input disabled name="dayofWeek[]" id="dayofWeek_4" type="hidden" value="TH">
            </div>

            <div class="week-item${w == 5 ? ' active' : ''}">
              <label for="dayofWeek_5">SEX</label>
              <input disabled name="dayofWeek[]" id="dayofWeek_5" type="hidden" value="FR">
            </div>

            <div class="week-item${w == 6 ? ' active' : ''}">
              <label for="dayofWeek_6">SAB</label>
              <input disabled name="dayofWeek[]" id="dayofWeek_6" type="hidden" value="SA">
            </div>
          </div>

          <br>

           <div class="row">
                <div class="col-12 col-md-6 row-center">
                    <div class="form-group-checkbox">
                    <input disabled name="modalidade[]" id="modalidade_online" type="checkbox" checked value="ONLINE">
                    <label for="modalidade_online">ONLINE</label>
                    </div>
                </div>

                <div class="col-12 col-md-6 row-center">
                    <div class="form-group-checkbox">
                    <input disabled name="modalidade[]" id="modalidade_presencial" type="checkbox" checked value="PRESENCIAL">
                    <label for="modalidade_presencial">PRESENCIAL</label>
                </div>
            </div>
        </div>
          <br>
        </div>
      `,
    showCancelButton: true,
    confirmButtonText: 'Confirmar',
    cancelButtonText: 'Cancelar',
    didOpen: () => {
      $('.row-weeks .week-item').on('click', function () {
        $(this).toggleClass('active');
      });

      $('#modalidade_presencial').on('click', function () {
        if ($(this).is(':checked')) {
          $('#row-unidade').fadeIn(1000);
        } else {
          $('#row-unidade').fadeOut(1000);
        }
      });


      $.get('/forms/fetch.unidades.php', {
        medico_id: $('.calendar-slide').data('id'),
        medico_token: $('.calendar-slide').data('token')
      }, function (data) {
        for (let i = 0; i < data.length; i++) {
          let unidade = data[i];
          let option = document.createElement('option');
          option.value = unidade.token;
          option.textContent = `${unidade.nome}`;
          option.dataset.inicio = unidade.inicio_expediente;
          option.dataset.fim = unidade.fim_expediente;
          document.getElementById('unidade_atendimento').appendChild(option);
        }

        $('#unidade_atendimento').on('change', function () {

          let selected_item = $('#unidade_atendimento option:selected');

          let times = getTimeSlots($(selected_item).data('inicio'), $(selected_item).data('fim'), duration);
          let select_start = document.getElementById('start_time');
          let select_end = document.getElementById('end_time');
          for (let i = 0; i < times.length; i++) {
            let option1 = document.createElement('option');
            option1.value = times[i];
            option1.text = times[i];
            select_start.appendChild(option1);


            let option2 = document.createElement('option');
            option2.value = times[i];
            option2.text = times[i];
            select_end.appendChild(option2);
          }


          select_start.options[0].selected = true;
          select_end.options[select_end.options.length - 1].selected = true;


          $('.row.row-config-agenda').find('[disabled]').removeAttr('disabled');
          $('.row.row-config-agenda').css('display', 'flex');
        });
      });
    },
    preConfirm: () => {
      let start_date = document.getElementById('start_date').value;
      let end_date = document.getElementById('end_date').value;

      let start_time = document.getElementById('start_time').value;
      let end_time = document.getElementById('end_time').value;

      let daysOfWeek = [];
      $('.row-weeks .week-item.active').each(function () {
        daysOfWeek.push($(this).find('input').val());
      });

      let dates = getDates(start_date, end_date, daysOfWeek, start_time, end_time, duration);

      let daysitems = [];

      $(dates).each(function () {
        let date = this.date;
        let times = this.times;

        let modalidade = [];
        $('.row .form-group-checkbox input:checked').each(function () {
          modalidade.push($(this).val());
        });

        let week = [];
        $('.row-weeks .week-item.active > input').each(function () {
          week.push($(this).val());
        });

        for (let i = 0; i < times.length; i++) {
          $(`label.week-time.week-schedule.listmedic-box-dir-time[data-obj="${date} ${times[i]}"]`).addClass('active');
          $(`label.week-time.week-schedule.listmedic-box-dir-time[data-obj="${date} ${times[i]}"]`).data('presencial', modalidade.includes('PRESENCIAL'));
          $(`label.week-time.week-schedule.listmedic-box-dir-time[data-obj="${date} ${times[i]}"]`).data('online', modalidade.includes('ONLINE'));

          $(`label.week-time.week-schedule.listmedic-box-dir-time[data-obj="${date} ${times[i]}"]`).find('i[name="presencial"]').toggleClass('icon-disabled', !modalidade.includes('PRESENCIAL'));
          $(`label.week-time.week-schedule.listmedic-box-dir-time[data-obj="${date} ${times[i]}"]`).find('i[name="online"]').toggleClass('icon-disabled', !modalidade.includes('ONLINE'));
        }
      });

      if ($('#modalidade_presencial').is(':checked') && !$('#unidade_atendimento').val()) {
        Swal.showValidationMessage(`Selecione uma Unidade de Atendimento.`);
        return false;
      }
    }
  });
}


function clearAgenda() {
  Swal.fire({
    icon: 'question',
    title: 'Atenção',
    width: 'auto',
    html: `Deseja Limpar a Sua Agenda?<br><br><small style="color: red;font-size: 16px;font-weight: bold">Essa Operação Não Podera Ser Desfeita após Ser Concluída.<br>Os horários já agendados não serão excluídos.</small>`,
    showCancelButton: true,
    confirmButtonText: 'Sim',
    cancelButtonText: 'Cancelar',
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      $('.week-time.week-schedule.listmedic-box-dir-time').attr('data-presencial', true);
      $('.week-time.week-schedule.listmedic-box-dir-time').attr('data-online', true);

      $('.week-time.week-schedule.listmedic-box-dir-time').find('i[name="presencial"]').removeClass('icon-disabled');
      $('.week-time.week-schedule.listmedic-box-dir-time').find('i[name="online"]').removeClass('icon-disabled');

      $('.week-time.week-schedule.listmedic-box-dir-time').removeClass('active');
    }
  });
}
