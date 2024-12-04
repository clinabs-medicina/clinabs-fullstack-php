<?php
use Sabre\VObject\Reader;
use Sabre\VObject\Component\VCalendar;

class AgendaGoogle {
    private array $events = [];
    
    public function __construct(string $icsFile, $interval = 30) {
      $calendar = Reader::read(file_get_contents($icsFile));
      
      foreach ($calendar->VEVENT as $event) {
        $evt = [
          'uid' => (string)$event->UID,
          'dtstart' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', (string)$event->DTSTART))),
          'dtend' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', (string)$event->DTEND))),
          'summary' => (string)$event->SUMMARY,
          'status' => (string)$event->STATUS,
          'rule' => (string)$event->RRULE,
          'dtstamp' => date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', (string)$event->DTSTAMP))),
          'location' => (string)$event->LOCATION
        ];

        file_put_contents('events.txt', print_r($dt, true), FILE_APPEND);
  
          if(!empty($event->RRULE)) {
            $rule = $this->parse_rule($evt['rule'], $evt['dtstart'], $evt['dtend']);
            file_put_contents('test2.txt', print_r($rule, true));
            $this->parse_ag(
              days: explode(',', $rule['rule']['BYDAY']),
              ag: [],
              duration: 30, 
              min: $rule['dtstart'], 
              max: $rule['dtend'],
              location: $evt['location'],
              summary: $evt['summary']
            );
          } else {
            $dt = date('Y-m-d H:i:s', strtotime($evt['dtstart']));

            
            if(!isset($this->events[date('Y-m-d', strtotime($evt['dtstart']))])) {
              $this->events[date('Y-m-d', strtotime($evt['dtstart']))] = [];
              
                foreach($this->timeInterval(date('H:i', strtotime($evt['dtstart'])), date('H:i', strtotime($evt['dtend'])), $interval) as $hh) {
                  $this->events[date('Y-m-d', strtotime($dt))][$hh] = [
                    'endereco' => $evt['location'],
                    'online' => empty($evt['location']),
                    'presencial' => !empty($evt['location']),
                ];
              }
            } else {
              foreach($this->timeInterval(date('H:i', strtotime($evt['dtstart'])), date('H:i', strtotime($evt['dtend'])), $interval) as $hh) {
                  $this->events[date('Y-m-d', strtotime($dt))][$hh] = [
                    'endereco' => $evt['location'],
                    'online' => empty($evt['location']),
                    'presencial' => !empty($evt['location']),
                ];
              }
            }
          }
      }
  
  
      ksort($this->events);
    }
  
    public function getEvents() {
      return $this->events;
    }
  
  
    public function parse_ag(array $days, array $ag, int $duration, string $min, string $max, string $location, string $summary) {
      $dates = $this->dateBetween($min, $max);
  
      foreach($dates as $date) {
        $dt = new DateTime($date);
  
        if(in_array(substr(strtoupper($dt->format('D')), 0, 2), $days)) {
          foreach($this->timeInterval(date('H:i', strtotime($min)), date('H:i', strtotime($max)), 30) as $timer) {
            if(empty($summary)) {
                $this->events[$dt->format('Y-m-d')][$timer] = [
                    'endereco' => $location,
                    'online' => empty($location),
                    'presencial' => !empty($location),
                ];
            } else {
                $this->events[$dt->format('Y-m-d')][$timer] = [
                    'endereco' => $location,
                    'online' => strtolower($summary) == 'online',
                    'presencial' => strtolower($summary) == 'presencial',
                ];
            }
            
          }
        }
      }
    }
  
    private function parse_rule($rule, $start, $end) {
      $rule = $this->parse_rule_to_array($rule);
      
      $item = [
        'dtstart' => $start,
        'dtend' => date('Y-m-d', strtotime(str_replace('T', ' ', $rule['UNTIL']))).' '.date('H:i:s', strtotime($end)),
        'summary' => '',
        'status' => 'CONFIRMED',
        'rule' => $rule,
      ];
      
      unset($item['rule']['UNTIL']);
      unset($item['rule']['WKST']);
  
      return $item;
    }
  
    private function dateBetween($startDate, $endDate) {
      $startDate = new DateTime($startDate);
      $endDate = new DateTime($endDate);
      
      $currentDate = $startDate;
      while ($currentDate <= $endDate) {
          $dates[] = $currentDate->format('Y-m-d');
          $currentDate->modify('+1 day');
      }
  
      return $dates;
    }
  
    private function parse_rule_to_array($rule) {
      $map = array_map(function($item) {
        $r = explode('=', $item);
        return [$r[0] => $r[1]];
      }, explode(';', $rule));
  
      $result = [];
  
      foreach($map as $item) {
        $result = array_merge($result, $item);
      }
  
      return $result;
    }
  
    private function timeInterval(string $start, string $end, int $interval) {
      $start = new DateTime($start);
      $end = new DateTime($end);
  
      $currentDate = $start;
      while ($currentDate <= $end) {
          $dates[] = $currentDate->format('H:i');
          $currentDate->modify('+'.$interval.' minutes');
      }
  
      return $dates;
    }
  }