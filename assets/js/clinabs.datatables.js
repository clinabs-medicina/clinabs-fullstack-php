$(document).ready(function () {
    var table = $('#tableAgendamento').DataTable();

    // Custom filter function
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var minDate = $('#min-date').val();
            var maxDate = $('#max-date').val();
            var date = data[0].trim().split(' ')[0].trim();

            if (
                (minDate === '' && maxDate === '') ||
                (minDate === '' && date <= maxDate) ||
                (minDate <= date && maxDate === '') ||
                (minDate <= date && date <= maxDate)
            ) {
                return true;
            }
            return false;
        }
    );

    // Event listener for the range inputs
    $('#min-date, #max-date').change(function () {
        table.draw();
    });
});