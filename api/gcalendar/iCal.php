<?php
class GoogleCalendar {
  private string $url;
  private int $interval;
  public function __construct($url, $interval) {
    $this->url = $url;
    $this->interval = $interval;
  }

  public function setUrl($url) {
    $this->url = $url;
  }

  public function parse() {
    $ical = file_get_contents($this->url);
    return $this->parseICal($ical);
  }

  function filter_by($events, $key, $value) {
    return array_filter($events, function($event) use ($key, $value) {
      return $event[$key] == $value;
    });
  }

  public function getEvents() {
    $ical = file_get_contents($this->url);

    $eventos = [];
    $remove = [];

    $events = array();

    $evts = $this->parseICal($ical);

    foreach ($evts as $evt) {
      if(isset($evt['RRULE'])) {
          if($evt['TRANSP'] == 'TRANSPARENT' && isset($event['CLASS'])) {
          $evts = array_merge($events, $this->getEventsFromRRule($evt));

          foreach($evts as $ex) {
            $events[] = $this->parseEvent($ex);
          }
        } else {
          $events[] = $this->parseEvent($evt);
        }
      } else {
        $d1 = date('H:i:s', strtotime(explode('T', $evt['DTSTART'])[1]));
        $d2 = date('H:i:s', strtotime(explode('T', $evt['DTEND'])[1]));

        $hrs = [];

        foreach($this->divideTimeIntoIntervals($d1, $d2, $this->interval) as $h) {
          $hrs[] = $h;
        }

        $remove[date('Y-m-d', strtotime(explode('T', $evt['DTSTART'])[0]))] = $hrs;
      }
    }

    foreach($events as $event) {
        foreach($event['DATES'] as $k => $v) {
            foreach($v as $t) {
              $time = date('H:i', strtotime($t));
              $eventos[$k][$time] = [
                'endereco' => str_replace("\\", "",  $event['LOCATION']),
                'description' => $event['SUMMARY'] ?? $vent['DESCRIPTION']
              ];

              if($event['DESCRIPTION'] != '') {
                $modalidades = explode("\\n",  $event['DESCRIPTION']);

                $eventos[$k][$time]['online'] = in_array('ONLINE', explode("\\n",  $event['DESCRIPTION']));
                $eventos[$k][$time]['presencial'] = in_array('PRESENCIAL', explode("\\n",  $event['DESCRIPTION']));
                $eventos[$k][$time]['date'] = $k;
                $eventos[$k][$time]['time'] = $time;
              } else {
                $modalidades = ['ONLINE', 'PRESENCIAL'];

                $eventos[$k][$time]['online'] = true;
                $eventos[$k][$time]['presencial'] = true;
                $eventos[$k][$time]['date'] = $k;
                $eventos[$k][$time]['time'] = $time;
            }

            $eventos[$k][$time]['available'] = $event['TRANSP'] == 'TRANSPARENT';
          }
        }
    }
    

    foreach($remove as $d => $hrs) {
      foreach($hrs as $h) {
        unset($eventos[$d][$h]);
      }
    }
   
    return $eventos;
  }

  private function getEventsFromRRule($evt) {
    $events = array();

    $rules = array_map(function($rule) {
        $r = explode('=', $rule);
        return array($r[0] => $r[1]);
      }, explode(';', $evt['RRULE']));

    $rule = [];

    foreach ($rules as $r) {
      $rule = array_merge($rule, $r);
      $rule['DAYS'] = explode(',', $rule['BYDAY']);
    }
    
    $evt['RULES'] = $rule;

    $evt['DTSTART'] = $this->parseDate($evt[array_keys($evt)[0]]);
    $evt['DTEND'] = $this->parseDate($evt[array_keys($evt)[1]]);
    $evt['UNTIL'] = trim(explode(' ', $this->parseDate($rule['UNTIL']))[0]);

    unset($rule['UNTIL']);
    unset($rule['BYDAY']);
    unset($evt['RRULE']);
    unset($evt["DTSTART;TZID=America/Sao_Paulo"]);
    unset($evt["DTEND;TZID=America/Sao_Paulo"]);

    $dates = $this->dateBetweens(trim(explode(' ', $evt['DTSTART'])[0]), $evt['UNTIL']);

    $dts = [];

    foreach($dates as $date) {
        $week = strtoupper(substr(date('l', strtotime($date)), 0, 2));

        if(in_array($week, $evt['RULES']['DAYS'])) {
          foreach($this->divideTimeIntoIntervals(trim(explode(' ', $evt['DTSTART'])[1]), trim(explode(' ', $evt['DTEND'])[1]), $this->interval) as $d) {
            $dts[$date][] = $d;
          }
      }
    }

    $evt['DATES'] = $dts;
    
    ksort($evt);
    
    $events[] = $evt;

    return $events;
  }
  
  private function parseICal($icalString) {
      $lines = explode("\n", $icalString);
      $events = [];
      $event = null;
      $inEvent = false;

      foreach ($lines as $line) {
          $line = trim($line);

          if ($line === "BEGIN:VEVENT") {
              $inEvent = true;
              $event = [];
          } elseif ($line === "END:VEVENT") {
              $inEvent = false;
              $events[] = $event;
          } elseif ($inEvent) {
              list($key, $value) = explode(":", $line, 2);
              $event[$key] = $value;
          }
      }

      return $events;
  }

  public function divideTimeIntoIntervals($startTime, $endTime, $intervalMinutes) {
      $start = new DateTime($startTime);
      $end = new DateTime($endTime);
      $interval = new DateInterval('PT' . $intervalMinutes . 'M');
      $times = [];

      while ($start <= $end) {
          $times[] = $start->format('H:i');
          $start->add($interval);
      }

      $times[] = $end->format('H:i');

      return $times;
  }


  private function parseDate($date) {
    $dt = explode("T", $date);
    $date = date('Y-m-d', strtotime($dt[0]));
    $time = date('H:i:s', strtotime($dt[1]));
    
    return "{$date} {$time}";
  }

  private function parseEvent($event) {
    $event['DATETIME_START'] = $this->parseDate($event['DTSTART']);
    $event['DATETIME_END'] = $this->parseDate($event['DTEND']);
    $event['INTERVALS'] = $this->divideTimeIntoIntervals(
      date('H:i:s', strtotime($this->parseDate($event['DTSTART']))), 
      date('H:i:s', strtotime($this->parseDate($event['DTEND']))), 30);
    
    $event['TRANSP'] == 'OPAQUE';
    return $event['TRANSP'] == 'OPAQUE' ? false : $event;
  }

  private function dateBetweens($start, $end) {
    $start = new DateTime($start);
    $end = new DateTime($end);
    
    $interval = new DateInterval('P1D');

    $period = new DatePeriod($start, $interval, $end);

    $dates = [];
    foreach ($period as $date) {
      $dates[] = $date->format('Y-m-d');
    }

    return $dates;
  }

}


