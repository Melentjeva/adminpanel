<?php

function getRow($row,$parity){
    if($row['lecturer_1_1']==$_GET['lec_id'] and $row['type_of_week_1']==$parity){
        $lec_row='1_1';
    }
    elseif($row['lecturer_1_2']==$_GET['lec_id'] and $row['type_of_week_1']==$parity){
        $lec_row='1_2';
    }
    elseif($row['lecturer_2_1']==$_GET['lec_id'] and $row['type_of_week_2']==$parity){
        $lec_row='2_1';
    }
    elseif($row['lecturer_2_2']==$_GET['lec_id'] and $row['type_of_week_2']==$parity){
        $lec_row='2_2';
    }
    return $lec_row;
}
function getCountOfWeeks_1($link, $row, $emptyvar){
    if(!empty($row['count_of_weeks_1'])){
        $weeks=getValueByForeignKey($link,$row,'count_of_weeks_1', 'raspisanie.count_of_lessons','count');
    }
    else {$weeks=$emptyvar;}
    return $weeks;
}
function getCountOfWeeks_2($link, $row, $emptyvar){
    if(!empty($row['count_of_weeks_2'])){
        $weeks=getValueByForeignKey($link,$row,'count_of_weeks_2', 'raspisanie.count_of_lessons','count');
    }
    else {$weeks=$emptyvar;}
    return $weeks;
}
function getVars($link, $row, $lec_row, $emptyvar){
    if($lec_row=='1_1' or $lec_row=='1_2'){
        $subject = getValueByForeignKey($link, $row, 'subject_1', 'raspisanie.subjects', 'subject');
        $hall = getValueByForeignKey($link, $row, 'hall_1', 'raspisanie.halls', 'hall');
        $les_type = getValueByForeignKey($link, $row, 'lesson_type_1', 'raspisanie.lesson_type', 'type');
        $count_of_weeks = getCountOfWeeks_1($link, $row, $emptyvar);
        $lecturer_n_1=getValueByForeignKey($link, $row, 'lecturer_1_1', 'raspisanie.lecturers', 'lecturer');
        if(!empty($row['lecturer_1_2'])){
            $lecturer_n_2=getValueByForeignKey($link, $row, 'lecturer_1_2', 'raspisanie.lecturers', 'lecturer');
        }
        else { $lecturer_n_2=''; }
    }
    if($lec_row=='2_1' or $lec_row=='2_2'){
        $subject = getValueByForeignKey($link, $row, 'subject_2', 'raspisanie.subjects', 'subject');
        $hall = getValueByForeignKey($link, $row, 'hall_2', 'raspisanie.halls', 'hall');
        $les_type = getValueByForeignKey($link, $row, 'lesson_type_2', 'raspisanie.lesson_type', 'type');
        $count_of_weeks = getCountOfWeeks_2($link, $row, $emptyvar);
        $lecturer_n_1=getValueByForeignKey($link, $row, 'lecturer_2_1', 'raspisanie.lecturers', 'lecturer');
        if(!empty($row['lecturer_2_2'])){
            $lecturer_n_2=getValueByForeignKey($link, $row, 'lecturer_1_2', 'raspisanie.lecturers', 'lecturer');
        }
        else { $lecturer_n_2=''; }
    }
    return array($subject, $hall, $les_type, $count_of_weeks, $lecturer_n_1, $lecturer_n_2);
}
function getGroups($link, $row){
    if (!empty($row['group_3'])) {
        $group_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
    } else {$group_3='';}
    if (!empty($row['group_2'])) {
        $group_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
    } else {$group_2='';}
    return array($group_2, $group_3);
}
function getParity(){
    if($_POST['week_radio']=='all'){ $parity=1; }
    elseif($_POST['week_radio']=='par') { $parity=2; }
    elseif($_POST['week_radio']=='notpar') { $parity=3; }
    return $parity;
}

function notEmptyPost(){
    if (!empty($_POST["Subject"]) and !empty($_POST["Lesson_Type"]) and !empty($_POST["Group_1"]) and !empty($_POST["Hall"])) {
        return true;
    }
}
function getCountOfWeeksPost($link){
    if (!empty($_POST['Count_of_Weeks'])) {
        $weeks = getFKeyByValue($link, $_POST['Count_of_Weeks'], 'raspisanie.count_of_lessons', 'count');
    } else { $weeks = 'NULL';}
    return $weeks;
}
function getGroups23Post($link){
    if (!empty($_POST['Group_3'])) {
        if ($_POST['Group_3'] != $_POST['Group_2'] and $_POST['Group_2'] != $_POST['Group_1']) {
            $gr_2 = getFKeyByValue($link, $_POST['Group_2'], 'raspisanie.groups', 'group_number');
            $gr_3 = getFKeyByValue($link, $_POST['Group_3'], 'raspisanie.groups', 'group_number');
        } else {
            echo 'Группы 1, 2 и 3 должны быть уникальными (группы 2 и 3 не были добавлены в таблицу).<br>';
            $gr_2 = 'NULL';
            $gr_3 = 'NULL';
        }
    }
    elseif (!empty($_POST['Group_2'])) {
        if ($_POST['Group_2'] != $_POST['Group_1'])
        {
            $gr_2 = getFKeyByValue($link, $_POST['Group_2'], 'raspisanie.groups', 'group_number');
            $gr_3 = 'NULL';
        } else {
            echo 'Группы 1 и 2 должны быть уникальными (группа 2 не добавлена в таблицу).<br>';
            $gr_2 = 'NULL';
            $gr_3 = 'NULL';
        }
    }
    else{
        $gr_2 = 'NULL';
        $gr_3 = 'NULL';
    }
    return array($gr_2, $gr_3);
}
function getLecturer2Post($link, $coincide_text){
    if (!empty($_POST['Lecturer_2']))
    {
        if ($_POST['Lecturer_2'] != $_GET['lec'])
        {
            $lecturer_2 = getFKeyByValue($link, $_POST['Lecturer_2'], 'raspisanie.lecturers', 'lecturer');
        } else {
            echo $coincide_text;
            $lecturer_2 = 'NULL';
        }
    } else {$lecturer_2 = 'NULL';}
    return $lecturer_2;
}

function getMatchRowGroupId($link,$day, $time, $gr_1,$gr_2,$gr_3, $parity){
    if($gr_3!='NULL') {
        $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
        WHERE day=" . $day . " and time=" . $time . " and group_1=" . $gr_1 . " and group_2=" . $gr_2 . " and group_3=" . $gr_3 . "");
        while ($row = pg_fetch_array($result)) {
            if ($row['type_of_week_1'] != $parity and $row['type_of_week_1'] != 1 and empty($row['type_of_week_2'])) {
                $schedule_id=$row['id'];
            }
        }
    }
    elseif($gr_2!='NULL'){
        $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
        WHERE day=" . $day . " and time=" . $time . " and group_1=" . $gr_1 . " and group_2=" . $gr_2 . "");
        while ($row = pg_fetch_array($result)) {
            if ($row['type_of_week_1'] != $parity and $row['type_of_week_1'] != 1 and empty($row['type_of_week_2'])) {
                $schedule_id=$row['id'];
            }
        }
    }
    else{
        $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
        WHERE day=" . $day . " and time=" . $time . " and group_1=" . $gr_1 . "");
        while ($row = pg_fetch_array($result)) {
            if ($row['type_of_week_1'] != $parity and $row['type_of_week_1'] != 1 and empty($row['type_of_week_2'])) {
                $schedule_id=$row['id'];
            }
        }
    }
    return $schedule_id;
}

function isLecBusy($parity, $link, $lec_id){
    if($parity==1){
        if(isLecBusyAll($link, $lec_id))
        {
            return true;
        }
    }
    elseif ($parity==2){
        if(isLecBusyPair($link, $lec_id))
        {
            return true;
        }
    }
    elseif ($parity==3){
        if(isLecBusyNotPair($link, $lec_id))
        {
            return true;
        }
    }
}
function isLecBusyAll($link, $lec_id){
    $a=0;
    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
    WHERE (lecturer_1_1=".$lec_id." or lecturer_1_2=".$lec_id." or lecturer_2_1=".$lec_id." or lecturer_2_2=".$lec_id.")
    AND day=".$day_id." AND time=".$time_id."");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}
/* function isLecBusyPair($link, $lec_id, $lec_row)
{
    $a = 0; $b = 0;
    $day_id = getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id = getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    if($lec_row=='2_1')
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule
    WHERE (lecturer_1_1=" . $lec_id . " or lecturer_1_2=" . $lec_id . ")
    AND (type_of_week_1=1 or type_of_week_1=2)
    AND day=" . $day_id . " AND time=" . $time_id . "");
    while ($row = pg_fetch_array($result))
    {
        $a=$a+1;
    }
    if($a>0)
    {$a='busy';}
    else {$a='not busy';}

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule
    WHERE (lecturer_2_1=" . $lec_id . " or lecturer_2_2=" . $lec_id . ")
    AND (type_of_week_2=2)
    AND day=" . $day_id . " AND time=" . $time_id . "");

    while ($row = pg_fetch_array($result))
    {
        $b=$b+1;
    }
    if($b>0)
    {$b='busy';}
    else {$b='not busy';}

    if($a=='buzy' or $b=='buzy') {return true;}
    else {return false;}
} */
function isLecBusyPair($link, $lec_id)
{
    $a = 0; $b = 0;
    $day_id = getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id = getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result_1 = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_1_1=" . $lec_id . " or lecturer_1_2=" . $lec_id . ") 
    AND (type_of_week_1=1 or type_of_week_1=2) 
    AND day=" . $day_id . " AND time=" . $time_id . "");
    while ($row = pg_fetch_array($result_1))
    {
        $a=$a+1;
    }
    if($a>0)
    {$a='busy';}
    else {$a='not busy';}

    $result_2 = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_2_1=" . $lec_id . " or lecturer_2_2=" . $lec_id . ")
    AND (type_of_week_2=2) 
    AND day=" . $day_id . " AND time=" . $time_id . "");

    while ($row = pg_fetch_array($result_2))
    {
        $b=$b+1;
    }
    if($b>0)
    {$b='busy';}
    else {$b='not busy';}

    if($a=='buzy' or $b=='buzy') {return true;}
    else {return false;}
}
function isLecBusyNotPair($link, $lec_id){
    $a=0; $b=0;
    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result_1 = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_1_1=".$lec_id." or lecturer_1_2=".$lec_id.")
    AND (type_of_week_1=1 or type_of_week_1=3) 
    AND day=".$day_id." AND time=".$time_id."");

    while ($row = pg_fetch_array($result_1))
    {$a=$a+1; }
    if($a>0)
    {$a='busy';}
    else {$a='not busy';}

    $result_2 = pg_query($link, "SELECT * FROM raspisanie.schedule  
    WHERE (lecturer_2_1=".$lec_id." or lecturer_2_2=".$lec_id.")
    AND (type_of_week_2=3) 
    AND type_of_week_2=3 AND day=".$day_id." AND time=".$time_id."");

    while ($row = pg_fetch_array($result_2))
    {
        $b=$b+1;
    }
    if($b>0)
    {$b='busy';}
    else {$b='not busy';}

    if($a=='buzy' or $b=='buzy') {return true;}
    else {return false;}


}
function isGroupBusyAll($link, $group_id){
    $a=0;
    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
    WHERE (group_1=".$group_id." or group_2=".$group_id." or group_3=".$group_id.")
    AND day=".$day_id." AND time=".$time_id."");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}
function isGroupBusyPair($link, $group_id){
    $a=0;
    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
    WHERE (group_1=".$group_id." or group_2=".$group_id." or group_3=".$group_id.")
    AND (type_of_week_1=1 or type_of_week_1=2 or type_of_week_2=2)
    AND day=".$day_id." AND time=".$time_id."");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}
function isGroupBusyNotPair($link, $group_id){
    $a=0;
    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
    WHERE (group_1=".$group_id." or group_2=".$group_id." or group_3=".$group_id.")
    AND (type_of_week_1=1 or type_of_week_1=3 or type_of_week_2=3)
    AND day=".$day_id." AND time=".$time_id."");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}

function isLecBusyConsultations($link, $lec_id)
{
    $a=0;
    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');

    $result = pg_query($link, "SELECT * FROM raspisanie.consultations 
    WHERE lecturer=".$lec_id."
    AND (day_1=".$day_id." and lesson_number_1=".$time_id.") OR (day_2=".$day_id." and lesson_number_2=".$time_id.")");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}

?>