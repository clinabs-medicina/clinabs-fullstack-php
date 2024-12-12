
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
global $pdo;
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if(isset($_SESSION['user'])) {
  try {
     $user = (object) $_SESSION['user'];
 } catch (PDOException $e) {

 }
}

$token = $_SESSION['token'];

$dados = $pdo->query("SELECT calendario FROM `AGENDA_MEDICA` WHERE medico_token = '$token'")->fetch(PDO::FETCH_OBJ);
$ags = [];

$ag_med = $pdo->query("SELECT data_agendamento as dt FROM `AGENDA_MED` WHERE medico_token = '$token'")->fetchAll(PDO::FETCH_ASSOC);

foreach($ag_med as $h) {
    $ags[date('Y-m-d', strtotime($h['dt']))][] = date('H:i', strtotime($h['dt']));
}

$calendario = json_decode($dados->calendario, true);
$weekCalendar = new WeeklyCalendar($calendario);



                                        $first = true;
                                        $last = false;
                                        $i = 0;


                                        foreach($weekCalendar->array_week_month(48) as $weekDay)
                                        {
                                            echo '<div class="calendar-slide'.($first ? ' active':'').'" data-index="'.$i.'">';
                                            foreach($weekDay as $item) {
                                                echo '<div class="week-item">
                                                   <div class="week-head">
                                                     <img src="https://cdn1.iconfinder.com/data/icons/website-internet/48/website_-_calendar_schedule-32.png">
                                                     <div class="week-name">
                                                       <span>'.$item['name'].'</span>
                                                       <span>'.date('d', strtotime($item['day'])).' '.$item['month'].'</span>
                                                     </div>
                                                   </div>';

                                                foreach($calendario[date('Y-m-d', strtotime($item['day']))] as $h) {
                                                    $checked = in_array($h, $ags[date('Y-m-d', strtotime($item['day']))]  ?? []);
                                                    echo '<button onclick="schedule_item(this)" type="button" '.($checked ? ' disabled':'').' data-day="'.$item['day'].'" data-time="'.$h.'" class="week-schedule-ag'.($checked ? ' active':'').'" type="checkbox" checked="false">
                                            <i class="fa fa-clock-o"></i> '.$h.'H</button>';

                                                }

                                                echo '</div>';

                                                $first = false;
                                            }
                                            echo '</div>';

                                            $i++;
                                        }
                                        ?>