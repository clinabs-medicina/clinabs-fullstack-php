<?php
error_reporting(E_ALL);
@ini_set('display_errors', 1);

$events = [];

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if (isset($_REQUEST['events'])) {
    foreach ($_REQUEST['events'] as $event) {
        if (validateDate($event) && date('Y-m-d', strtotime($event)) == sprintf('%04d-%02d-%02d', $year, $month, $day)) {
            $events[] = $event;
        }
    }
}

// Função para formatar o mês como nome e número
function getMonthDetails($month, $year, $events)
{
    $months = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ];

    return [
        'month' => $months[$month],
        'year' => $year,
        'month_number' => $month
    ];
}

// Função para gerar a lista de dias do mês
function generateMonthDays($month, $year, $events)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $dates = [];

    // Gerar datas do mês atual
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dates[] = [
            'date' => sprintf('%04d-%02d-%02d', $year, $month, $day),
            'day' => date('d', strtotime(sprintf('%04d-%02d-%02d', $year, $month, $day))),
            'enabled' => in_array(sprintf('%04d-%02d-%02d', $year, $month, $day), $events)
        ];
    }

    return $dates;
}

// Função para gerar os dias da última semana do mês anterior
function generatePrevMonthDays($month, $year, $events)
{
    $prevMonth = $month - 1;
    $prevYear = $year;
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }

    // Obter o último dia do mês anterior
    $lastDayOfPrevMonth = strtotime("$prevYear-$prevMonth-" . cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear));
    $lastDayOfWeek = date('w', $lastDayOfPrevMonth);

    $prevMonthDays = [];

    // Adicionar apenas os dias da última semana do mês anterior
    for ($day = $lastDayOfWeek; $day >= 0; $day--) {
        $prevMonthDays[] = [
            'date' => sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, date('d', strtotime("-$day day", $lastDayOfPrevMonth))),
            'day' => date('d', strtotime("-$day day", $lastDayOfPrevMonth)),
            'enabled' => in_array(sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, date('d', strtotime("-$day day", $lastDayOfPrevMonth))), $events)
        ];
    }

    return array_reverse($prevMonthDays);  // Reverter para a ordem correta
}

// Função para gerar os primeiros dias da primeira semana do próximo mês
function generateNextMonthDays($month, $year, $events)
{
    $nextMonth = $month + 1;
    $nextYear = $year;

    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    // Obter o primeiro dia do próximo mês
    $firstDayOfNextMonth = strtotime("$nextYear-$nextMonth-01");
    $nextMonthDays = [];

    // Obter o dia da semana do primeiro dia do próximo mês
    $firstDayOfWeek = date('w', $firstDayOfNextMonth);

    // Adicionar os primeiros dias da semana do próximo mês
    for ($day = 1; $day <= (7 - $firstDayOfWeek); $day++) {
        $nextMonthDays[] = [
            'date' => sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day),
            'day' => date('d', strtotime(sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day))),
            'enabled' => in_array(sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day), $events)
        ];
    }

    return $nextMonthDays;
}

// Defina o mês e o ano atual
$month = $_REQUEST['month'];
$year = $_REQUEST['year'];
// Obter as informações do mês atual
$monthDetails = getMonthDetails($month, $year, $events);

// Gerar as listas de dias para o mês anterior, mês atual e mês seguinte
$prevMonthDays = generatePrevMonthDays($month, $year, $events);
$currentMonthDays = generateMonthDays($month, $year, $events);
$nextMonthDays = generateNextMonthDays($month, $year, $events);

// Ordenar as listas de dias
sort($prevMonthDays);
sort($nextMonthDays);
sort($currentMonthDays);

// Criar a estrutura final para o JSON
$result = [
    'month' => $monthDetails['month'],
    'year' => $monthDetails['year'],
    'prev' => $prevMonthDays,
    'current' => $currentMonthDays,
    'next' => $nextMonthDays
];

file_put_contents('calendar.json', json_encode($_REQUEST, JSON_PRETTY_PRINT));
// Retornar o JSON
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>
