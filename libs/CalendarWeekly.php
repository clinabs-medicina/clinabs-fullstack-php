<?php

class WeeklyCalendar {
  private $week;
  public $weekDays;
  public $months;
  private $firstDay;
  private $lastDay;

  public function displayCalendar() {
      echo '<pre>'; 
        print_r(['days' => $this->weekDays, 'firstDay' => $this->firstDay, 'lastDay' => $this->lastDay]);
      echo '<pre>';
    }

  public function array_week($start_date, $end_date) {
    $daysArray = [];


    $start_date = new DateTime($start_date);
    $end_date = new DateTime($end_date);

    $current_date = clone $start_date;
    while ($current_date <= $end_date) {
      $daysArray[] = $current_date->format('Y-m-d');
      $current_date->modify('+1 day');
    }

    return $daysArray;
  }

  public function array_week_month($week = 12, $start = 'monday', $end = 'saturday') {
    $weeks = [];

    $firstDay = date('Y-m-d', strtotime($start.' this week'));
    $lastDay = date('Y-m-d', strtotime($end.' this week'));


    for( $i = 0; $i <= $week; $i++ ) {
      $startDate = new DateTime($firstDay);
      $currentDate = new DateTime($firstDay);
      $endDate = new DateTime($lastDay);
      
      $weeks[$i] = $this->getWeekDays($firstDay, $lastDay);
        
      $firstDay = date('Y-m-d', strtotime('+7 days', strtotime($firstDay)));
      $lastDay = date('Y-m-d', strtotime('+7 days', strtotime($lastDay)));

      $currentDate->modify('+1 day');
    }

    
    return $weeks;
  }

  public function getWeekDays($firstDay, $lastDay){
    $days =  $this->array_week($firstDay, $lastDay);

    $result = [];

    $weeks = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'];
    $months = ['Jan' => 'Jan', 'Feb' => 'Fev', 'Mar' => 'Mar', 'Apr' => 'Abr', 'May' => 'Mai', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' =>'Ago', 'Sep' => 'Set', 'Oct' => 'Out', 'Nov' => 'Nov', 'Dec' => 'Dez'];

    foreach($days as $day){
      $result[] = ['name' => $weeks[date('w', strtotime($day))], 'day' => $day, 'month' => strtr(date('M', strtotime($day)), $months) ];
    }

    return $result;
  }

  public function calculateHoursInterval($start, $end, $div = 3600) {
    $horarios = [];
    
    $startTimestamp = strtotime($start);
    $endTimestamp = strtotime($end);
    $interval = abs($endTimestamp - $startTimestamp) / $div;
        for ($i = 0; $i <= $interval; $i++) {
          $horarios[] = date('H:i', $startTimestamp + ($i * $div));
        }
    return $horarios;
  }
}