<?php
error_reporting(1);

enum DateFormat: string {
  case DATETIME = "Y-m-d H:i:s";
  case DATE = "Y-m-d";
  case TIME = "H:i:s";
}


class GoogleCalendarSync {
  private string $link;
  private string $raw;
  private int $interval;
  private string $inicio;
  private string $fim;

  public function setLink(string $link, string $inicio, string $fim,int $interval = 30) {
    $this->link = $link;
    $this->interval = $interval;
    $this->inicio = $inicio;
    $this->fim = $fim;
  }

  public function fetch():void {
    $this->raw = file_get_contents($this->link);
  }

  public function parse():array {
    return $this->parseICS($this->raw);
  }

  private function parseICS($string) {
    $lines = explode("\n", $string);
    $events = [];
    $event = [];

    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === 'BEGIN:VEVENT') {
        $event = [];
      } elseif ($line === 'END:VEVENT') {
        $events[] = $event;
      } else {
        $parts = explode(':', $line, 2);
        if(count($parts) >= 1) {
          $key = $parts[0];
          $value = $parts[1];

          $event[explode(';', $key)[0]] = $value ?? null;
        }
      }
    }

    $result = [];

    foreach($events as $evt) {
      $result[] = $this->parseEvent($evt);
    }
    
    return $result;
  }

  public function find(string $key, string $value):array {
    $events = $this->parse();
    $results = [];

    foreach ($events as $event) {
      if (isset($event[$key]) && $event[$key] === $value) {
        $results[] = $event;
      }
    }

    return $results;
  }

  public function kind(string $key):array {
    $events = $this->parse();
    $results = [];

    foreach ($events as $event) {
      if (isset($event[$key])) {
        $results[] = $event;
      }
    }

    return $results;
  }

  public function parseEvent(array $event):array {

    if(isset($event['RRULE'])) {
      $objects = [];

      $rules = explode(';', $event['RRULE']);

      foreach ($rules as $rule) {
        $parts = explode('=', $rule, 2);
        $key = $parts[0];
        $value = trim($parts[1]);

        if($key === 'BYDAY') {
          $objects[$key] = array_map(function($item) {
            return trim(preg_replace('/[0-9]+/', '', $item));
          }, explode(',' , $value));
        } else {
          $objects[$key] = $value;
        }
      }

     // $objects['DTEND'] = $this->parseDateTime($event['DTSTART']);

      $event['RRULE'] = $objects;
    }

    if(isset($event['DTSTART'])) {
      $event['DTSTART'] = $this->parseDateTime(string: $event['DTSTART']);
    }
    
    if(isset($event['DTEND'])) {
      $event['DTEND'] = $this->parseDateTime(string: $event['DTEND']);
    }

    if(isset($event['DTSTAMP'])) {
      $event['DTSTAMP'] = $this->parseDateTime(string: $event['DTSTAMP']);
    }

    if(isset($event['CREATED'])) {
      $event['CREATED'] = $this->parseDateTime(string: $event['CREATED']);
    }

    if(isset($event['LAST-MODIFIED'])) {
      $event['LAST-MODIFIED'] = $this->parseDateTime(string: $event['LAST-MODIFIED']);
    }

    $event['DAYS'] = $this->getDays(event: $event);
    
    return $event;
  }

  public function parseDateTime(string $string, DateFormat $format = DateFormat::DATETIME):string {
    if($format == DateFormat::DATE) {
      return date('Y-m-d', strtotime($string));
    } 
    else if($format == DateFormat::TIME) {
      return date('H:i:s', strtotime($string));
    } else {
      return date('Y-m-d H:i:s', strtotime($string));
    }
  }

  public function getWeekDay(string $date):string {
    $week = date('w', strtotime($date));
    
    $weekDays =  [
          0 => 'SU',
          1 => 'MO',
          2 => 'TU',
          3 => 'WE',
          4 => 'TH',
          5 => 'FR',
          6 => 'SA'
        ];

      return $weekDays[$week];
  }

  public function getDays(array $event) {
    $startDate = $this->parseDateTime($event['DTSTART'], DateFormat::DATE);
    $endDate = $this->parseDateTime($event['DTEND'], DateFormat::DATE);

    $startTime = $this->parseDateTime($event['DTSTART'], DateFormat::TIME);
    $endTime = $this->parseDateTime($event['DTEND'], DateFormat::TIME);

    $daysOfWeek = $event['RRULE']['BYDAY'];
    $method = $event['RRULE']['FREQ'];
      
    $days = [];


    $weekNames =  [
      0 => 'sunday',
      1 => 'monday',
      2 => 'tuesday',
      3 => 'wednesday',
      4 => 'thursday',
      5 => 'friday',
      6 => 'saturday'
    ];

    $weekDays =  [
      'SU' => 'sunday',
      'MO' => 'monday',
      'TU' => 'tuesday',
      'WE' => 'wednesday',
      'TH' => 'thursday',
      'FR' => 'friday',
      'SA' => 'saturday'
    ];

    $currentDate = $startDate;

    if($method === 'WEEKLY') {
      $start = new DateTime($startDate);
      $end = new DateTime($endDate);

      //$days[$startDate] = $this->IntervalTime($startTime, $this->interval, $endTime);
      
        $interval = new DateInterval('P1W');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
          $date = new DateTime($dt->format('Y-m-d'));
          $days_week = [];
          foreach($daysOfWeek as $w) {
            $week = $weekDays[$w];

            $date->modify($week.' this week ');

            $times =  $this->IntervalTime($this->inicio, $this->interval, $this->fim);

            $days[$date->format('Y-m-d')] = [];
            foreach($times as $time) {
              if(strtotime($startDate.' '.$time) >= strtotime($startDate.' '.$startTime) && strtotime($startDate.' '.$time) <= strtotime($startDate.' '.$endTime)) {
                $days[$date->format('Y-m-d')][] = $time;
              }
            }

          }
        }

        ksort($days);

    } else if($method === 'MONTHLY') {
      $start = new DateTime($startDate);
      $end = new DateTime($endDate);
      $interval = new DateInterval('P1M');
      $period = new DatePeriod($start, $interval, $end);
      foreach ($period as $dt) {
        $days[$dt->format('Y-m-d')] = $this->IntervalTime($startTime, $this->interval, $endTime);
      }
    } else if($method === null) {
      $date = $startDate;
      
      $startDate = $this->parseDateTime($event['DTSTART'], DateFormat::DATETIME);
      $endDate = $this->parseDateTime($event['DTEND'], DateFormat::DATETIME);
      $values = $this->IntervalTime($startDate, $this->interval, $endDate);
      $days[$date] = $this->IntervalTime($startDate, $this->interval, $endDate);
    }

    return $days;
  }

  public function getEvents():array {
    $events = [];

    $items = $this->parse();

    foreach($items as $item) {
      $id = str_replace('@google.com', '', $item['UID']);
      unset($item['UID']);
      unset($item['SEQUENCE']);
      unset($item['DTSTAMP']);
      unset($item['TRANSP']);
      unset($item['LAST-MODIFIED']);
      
      $events[$id] = [];

      foreach($item as $key => $value) {
        $events[$id][$key] = $value;
      }
    }

    return $events;
  }

  private function IntervalTime(string $startTime, int $interval, string $endTime):array {
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    
    $intv = new DateInterval('PT'.$interval.'M');

    $times = [];
    
    for ($time = $start; $time <= $end; $time->add($intv)) {
        $times[] = $time->format('H:i');
    }

    return $times;
  }
}