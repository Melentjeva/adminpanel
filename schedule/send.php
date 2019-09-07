<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><?php
include '../db_connection.php';
include 'send_hat.php';
include 'send_Schedule.php';


function sendDay($link, $parity)
{
    $res = pg_query($link, "SELECT * FROM raspisanie.days_of_week"); //запрос к БД
    while ($row_day = pg_fetch_array($res))
    {
        sendHat($parity, $day=$row_day['day']);

        $result_time = pg_query($link,"SELECT * FROM raspisanie.time ORDER by number_of_lesson"); //запрос к БД
        while($row_time = pg_fetch_array($result_time))
        {
            if ($parity=='все')
            {
                echo '<div class="tbl-content"><table cellpadding="0" cellspacing="0" border="0"><col span="1" class="col_time"><col span="1" class="col_subject"><col span="1" class="col_group"><col span="4" class="col_nnn">';
                sendScheduleAll($link, $row_day, $row_time);
                echo '</table></div>';
            }
            elseif($parity=='пар')
            {
                echo('<div class="tbl-content"><table cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(to right, #D98E46, #A1A6C5)"><col span="1" class="col_time"><col span="1" class="col_subject"><col span="1" class="col_group"><col span="5" class="col_nnn">');
                sendSchedulePair($link,$row_day, $row_time);
                echo('</table></div>');
            }
            elseif($parity=='н/п')
            {
                echo('<div class="tbl-content"><table cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(to right, #CC9286, #B586CF)"><col span="1" class="col_time"><col span="1" class="col_subject"><col span="1" class="col_group"><col span="5" class="col_nnn">');
                sendScheduleNotPair($link, $row_day, $row_time);
                echo('</table></div>');
            }
        }
    }
}

?>

<?
endif;
?>
