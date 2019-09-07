<?php
/**
 * Created by PhpStorm.
 * User: Maria_Melentyeva
 * Date: 12.06.2018
 * Time: 04:07
 */

include '../db_connection.php';


function isNewField($my_link, $column_name, $table, $user_data){
    // проверяем, не совпадает ли новая запись с уже существующей
    $count=0;
    $res = pg_query($my_link,"SELECT (" . $column_name . ") FROM $table"); //запрос к БД
    while($row = pg_fetch_array($res))
    {
        if ($user_data == $row[$column_name]) {
            $count = 1;
        }
    }
    pg_free_result($res);
    if($count==0){ return true; } else { return false; }
}

function isLecturer($con, $user_data){
    $link = pg_connect($con);
    $sql = "SELECT lecturer FROM raspisanie.lecturers";
    $result = pg_query($link, $sql);
    while ($row = pg_fetch_array($result)) {
        if ($user_data == $row['lecturer']) {
            $count = 1;
        }
    }
    pg_free_result($result);
    if($count>0){ return true; } else { return false; }

}

function isDay($con, $user_data){
    $link = pg_connect($con);
    $sql = "SELECT day FROM raspisanie.days_of_week";
    $result = pg_query($link, $sql);
    while ($row = pg_fetch_array($result)) {
        if ($user_data == $row['day']) {
            $count = 1;
        }
    }
    pg_free_result($result);
    if($count>0){ return true; } else { return false; }

}

function isHall($con, $user_data){
    $link = pg_connect($con);
    $sql = "SELECT hall FROM raspisanie.halls";
    $result = pg_query($link, $sql);
    while ($row = pg_fetch_array($result)) {
        if ($user_data == $row['hall']) {
            $count = 1;
        }
    }
    pg_free_result($result);
    if($count>0){ return true; } else { return false; }

}

function isLessonNumber($con, $user_data){
    $link = pg_connect($con);
    $sql = "SELECT number_of_lesson FROM raspisanie.time";
    $result = pg_query($link, $sql);
    while ($row = pg_fetch_array($result)) {
        if ($user_data == $row['number_of_lesson']) {
            $count = 1;
        }
    }
    pg_free_result($result);
    if($count>0){ return true; } else { return false; }

}

function isInTablesConsultation1($conn){
    if(isLecturer($con=$conn, $user_data=$_POST["Lecturer"])==true and isDay($con=$conn, $user_data=$_POST["Day_1"])==true
        and isHall($con=$conn, $user_data=$_POST["Hall_1"])==true and IsLessonNumber($con=$conn, $user_data=$_POST["Lesson_Num_1"])==true){
        return true;
    } else { return false; }
}
function isInTablesConsultation2($conn){
    if(isDay($con=$conn, $user_data=$_POST["Day_2"])==true and isHall($con=$conn, $user_data=$_POST["Hall_2"])==true
        and IsLessonNumber($con=$conn, $user_data=$_POST["Lesson_Num_2"])==true){
        return true;
    } else { return false; }
}
function isNewConsultation(){
    if($_POST["Day_1"]==$_POST["Day_2"] and $_POST["Lesson_Num_1"]==$_POST["Lesson_Num_2"])
    {
        return false;
    }
    else
    {
        return true;
    }
}

function replace_quotes($str){
    $str2 = str_replace("'", "`", (string)$str);
    $str3 = str_replace('"', "`", $str2);
    return $str3;
}

function getFKeyByValue($link, $value, $table, $table_column){
    $new_value=replace_quotes($value);
    $row = pg_fetch_row(pg_query($link,"SELECT id FROM ".$table." WHERE ".$table_column."='".$new_value."'"));
    $id=$row[0];
    return $id;

}
?>