<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><head><meta http-equiv="refresh" content="20"></head>
<?php
include 'func.php';

function getGroup($link,$row)
{
    $gr_1 = getValueByForeignKey($link, $row, 'group_1', 'raspisanie.groups', 'group_number');
    if(!empty($row['group_3'])) {
        $gr_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
        $gr_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
        $group='<td>' . $gr_1 .', '. $gr_2 .','.$gr_3. '</td>'; }
    elseif(!empty($row['group_2'])) {
        $gr_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
        $group='<td>' . $gr_1 .', '. $gr_2 .'</td>'; }
    else {$group='<td>' . $gr_1 .'</td>'; }
    return $group;
}

function resultScheduleAll($link, $row_day, $row_time){
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_1_1=".$_GET['lec_id']." or lecturer_1_2=".$_GET['lec_id'].") 
    AND type_of_week_1=1 AND day=".$row_day['id']." AND time=".$row_time['id']." 
     ORDER by day"); //запрос к БД
    return $result;
}
function resultSchedulePair($link, $row_day, $row_time){
    $result_schedule = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE ((lecturer_1_1=".$_GET['lec_id']." or lecturer_1_2=".$_GET['lec_id'].") AND type_of_week_1=2 
    AND day=".$row_day['id']." AND time=".$row_time['id'].")
    or ((lecturer_2_1=".$_GET['lec_id']." or lecturer_2_2=".$_GET['lec_id'].") AND type_of_week_2=2
      AND day=".$row_day['id']." AND time=".$row_time['id'].")
       ORDER by day"); //запрос к БД
    return $result_schedule;
}
function resultScheduleNotPair($link, $row_day, $row_time){
    $result_schedule = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE ((lecturer_1_1=".$_GET['lec_id']." or lecturer_1_2=".$_GET['lec_id'].") AND type_of_week_1=3 
    AND day=".$row_day['id']." AND time=".$row_time['id'].")
    or ((lecturer_2_1=".$_GET['lec_id']." or lecturer_2_2=".$_GET['lec_id'].") AND type_of_week_2=3
      AND day=".$row_day['id']." AND time=".$row_time['id'].")
       ORDER by day"); //запрос к БД
    return $result_schedule;
}

function lecturerIsBusyAll($link, $row_day, $row_time){
    $a=0;
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_1_1=".$_GET['lec_id']." or lecturer_1_2=".$_GET['lec_id']."
    or lecturer_2_1=".$_GET['lec_id']." or lecturer_2_2=".$_GET['lec_id'].") 
    AND (type_of_week_1=2 or type_of_week_1=3) AND day=".$row_day['id']." AND time=".$row_time['id'].""); //запрос к БД
    while ($row = pg_fetch_array($result))
    {
        $a=$a+1;
    }
    if($a>0){return true;}
    else {return false;}
}


function lecturerIsBusyPNP($link, $row_day, $row_time){
    $a=0;
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_1_1=".$_GET['lec_id']." or lecturer_1_2=".$_GET['lec_id'].")
    AND type_of_week_1=1 AND day=".$row_day['id']." AND time=".$row_time['id'].""); //запрос к БД
    while ($row = pg_fetch_array($result))
    {
        $a=$a+1;
    }
    if($a>0){return true;}
    else {return false;}
}


#---------------------------------------------------------------------------------------------------------

function sendScheduleAll($link, $row_day, $row_time)
{
    $lec_id=$_GET['lec_id'];
    $lec=$_GET['lec'];
    $time=$row_time['number_of_lesson'];
    $day = $row_day['day'];

    $result_schedule = resultScheduleAll($link, $row_day, $row_time);
    while ($row_schedule = pg_fetch_array($result_schedule))
    {
        $id_schedule = $row_schedule['id'];
    }

    if(!empty($id_schedule))
    {
        $result_schedule = resultScheduleAll($link, $row_day, $row_time);
        while ($row_schedule = pg_fetch_array($result_schedule))
        {
            $lec_row = getRow($row_schedule, 1);
            $subject = getValueByForeignKey($link, $row_schedule, 'subject_1', 'raspisanie.subjects', 'subject');
            $hall = getValueByForeignKey($link, $row_schedule, 'hall_1', 'raspisanie.halls', 'hall');
            $les_type = getValueByForeignKey($link, $row_schedule, 'lesson_type_1', 'raspisanie.lesson_type', 'type');
            $count_of_weeks = getCountOfWeeks_1($link, $row_schedule, '-');
            $group = getGroup($link, $row_schedule);
            if($lec_row=='1_1')
            {
                $a_href_edit="row_edit_all.php?lec=".$lec."&lid=".$lec_id."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;

                echo '<tr><td>' . $time . '</td><td>'. $subject . '</td>' . $group .'<td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_edit.'" target="_blank">Редакт.</a></td><td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td></tr>';
            }
            elseif ($lec_row=='1_2')
            {
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;
                $lecturer_1_1=getValueByForeignKey($link, $row_schedule, 'lecturer_1_1', 'raspisanie.lecturers', 'lecturer');

                echo '<tr><td>' . $time . '</td><td>'. $subject . '<FONT size=2><br>Преподаватель 1: '.$lecturer_1_1.'</FONT>
                    ' . $group .'</td><td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td><td>(все)</td></tr>';

            }
        }
    }
    else{
        if(lecturerIsBusyAll($link, $row_day, $row_time)==false)
        {
            $a_href_add="new_row_add.php?lec=".$lec."&lid=".$lec_id. "&day=".$day."&time=".$time."&week=все&id=";
            echo '<tr><td>' . $row_time['number_of_lesson'] . '</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>'.
                '<td><a href="'.$a_href_add.'" target="_blank">Добавить</a></td><td>(все)</td></tr>';
        }
        else{
            echo '<tr><td>' . $row_time['number_of_lesson'] . '</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>'.
                '<td>занято</td><td>(все)</td></tr>';
        }
    }
}

#---------------------------------------------------------------------------------------------------------

function sendSchedulePair($link, $row_day, $row_time)
{
    $lec = $_GET['lec'];
    $lec_id=$_GET['lec_id'];
    $time=$row_time['number_of_lesson'];
    $day = $row_day['day'];

    $result_schedule =resultSchedulePair($link, $row_day, $row_time);
    while ($row_schedule = pg_fetch_array($result_schedule))
    {
        $id_schedule = $row_schedule['id'];
        $lecturer_2_1 = $row_schedule['lecturer_2_1'];
    }
    #if(!empty($id_schedule) and empty($lecturer_2_1))
    if(!empty($id_schedule))
    {
        $result_schedule =resultSchedulePair($link, $row_day, $row_time);
        while ($row_schedule = pg_fetch_array($result_schedule))
        {
            $lec_row = getRow($row_schedule, 2);
            $subject = getVars($link, $row_schedule, $lec_row, '-')['0'];
            $hall = getVars($link, $row_schedule, $lec_row, '-')['1'];
            $les_type = getVars($link, $row_schedule, $lec_row, '-')['2'];
            $count_of_weeks = getVars($link, $row_schedule, $lec_row, '-')['3'];
            $group = getGroup($link, $row_schedule);
            if($lec_row=='1_1' or $lec_row=='2_1')
            {
                $a_href_edit="row_edit_pairnotpair.php?lec=".$lec."&lid=".$lec_id."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=пар&id=".$id_schedule;
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=пар&id=".$id_schedule;

                echo '<tr><td>' . $time . '</td><td>'. $subject . '</td>' . $group .'<td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_edit.'" target="_blank">Редакт.</a></td><td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td></tr>';
            }
            elseif ($lec_row=='1_2')
            {
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;
                $lecturer_1_1=getValueByForeignKey($link, $row_schedule, 'lecturer_1_1', 'raspisanie.lecturers', 'lecturer');

                echo '<tr><td>' . $time . '</td><td>'. $subject . '<FONT size=2><br>Преподаватель 1: '.$lecturer_1_1.'</FONT>
                    ' . $group .'</td><td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td><td>(все)</td></tr>';

            }
            elseif ($lec_row=='2_2')
            {
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;
                $lecturer_2_1=getValueByForeignKey($link, $row_schedule, 'lecturer_2_1', 'raspisanie.lecturers', 'lecturer');

                echo '<tr><td>' . $time . '</td><td>'. $subject . '<FONT size=2><br>Преподаватель 1: '.$lecturer_2_1.'</FONT>
                ' . $group .'</td><td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td><td>(все)</td></tr>';

            }

        }

    }
    else {
        if(lecturerIsBusyPNP($link, $row_day, $row_time)==false) {
            $a_href_add = "new_row_add.php?lec=" . $lec . "&lid=" . $lec_id . "&day=" . $day . "&time=" . $time . "&week=пар&id=";

            echo '<tr><td>' . $row_time['number_of_lesson'] . '</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>' .
                '<td><a href="' . $a_href_add . '" target="_blank">Добавить</a></td><td>(пар)</td></tr>';
        }
        else{
            echo '<tr><td>' . $row_time['number_of_lesson'] . '</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>' .
                '<td>занято</td><td>(пар)</td></tr>';
        }
    }
}

#---------------------------------------------------------------------------------------------------------

function sendScheduleNotPair($link, $row_day, $row_time)
{
    $lec = $_GET['lec'];
    $lec_id=$_GET['lec_id'];
    $time=$row_time['number_of_lesson'];
    $day = $row_day['day'];

    $result_schedule =resultScheduleNotPair($link, $row_day, $row_time);
    while ($row_schedule = pg_fetch_array($result_schedule))
    {
        $id_schedule = $row_schedule['id'];
        $lec_row = getRow($row_schedule, 3);
    }
    if(!empty($id_schedule))
    {
        $result_schedule =resultScheduleNotPair($link, $row_day, $row_time);
        while ($row_schedule = pg_fetch_array($result_schedule))
        {
            $subject = getVars($link, $row_schedule, $lec_row,'-')['0'];
            $hall = getVars($link, $row_schedule, $lec_row,'-')['1'];
            $les_type = getVars($link, $row_schedule, $lec_row,'-')['2'];
            $count_of_weeks = getVars($link, $row_schedule, $lec_row,'-')['3'];
            $group = getGroup($link, $row_schedule);

            if($lec_row=='1_1' or $lec_row=='2_1')
            {
                $a_href_edit="row_edit_pairnotpair.php?lec=".$lec."&lid=".$lec_id."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=н/п&id=".$id_schedule;
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=н/п&id=".$id_schedule;

                echo '<tr><td>' . $time . '</td><td>'. $subject . '</td>' . $group .'<td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_edit.'" target="_blank">Редакт.</a></td><td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td></tr>';

            }
            elseif ($lec_row=='1_2')
            {
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;
                $lecturer_1_1=getValueByForeignKey($link, $row_schedule, 'lecturer_1_1', 'raspisanie.lecturers', 'lecturer');

                echo '<tr><td>' . $time . '</td><td>'. $subject . '<FONT size=2><br>Преподаватель 1: '.$lecturer_1_1.'</FONT>
                    ' . $group .'</td><td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td><td>(все)</td></tr>';

            }
            elseif ($lec_row=='2_2')
            {
                $a_href_delete="row_delete.php?lec=".$lec."&lec_row=".$lec_row."&day=".$day."&time=".$time."&week=все&id=".$id_schedule;
                $lecturer_2_1=getValueByForeignKey($link, $row_schedule, 'lecturer_2_1', 'raspisanie.lecturers', 'lecturer');

                echo '<tr><td>' . $time . '</td><td>'. $subject . '<FONT size=2><br>Преподаватель 1: '.$lecturer_2_1.'</FONT>
                ' . $group .'</td><td>'.$hall.'</td><td>'.$les_type.'</td><td>'.$count_of_weeks.'</td>'.
                    '<td><a href="'.$a_href_delete.'" target="_blank">Удалить</a></td><td>(все)</td></tr>';

            }

        }

    }
    else {
        if(lecturerIsBusyPNP($link, $row_day, $row_time)==false) {

            $a_href_add = "new_row_add.php?lec=" . $lec . "&lid=" . $lec_id . "&day=" . $day . "&time=" . $time . "&week=н/п&id=" . $row_schedule['id'];

            echo '<tr><td>' . $row_time['number_of_lesson'] . '</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>' .
                '<td><a href="' . $a_href_add . '" target="_blank">Добавить</a></td><td>(н/п)</td></tr>';
        }
        else{
            echo '<tr><td>' . $row_time['number_of_lesson'] . '</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>' .
                '<td>занято</td><td>(н/п)</td></tr>';
        }
    }
}
?>

<?
endif;
?>
