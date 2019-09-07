<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><?php
include '../db_connection.php';
include "row_delete_hat.php";

$link = pg_connect($connection);

function getVars2($link, $id){
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule WHERE id=".(int)$id);
    while ($row = pg_fetch_array($result))
    {
        $subject_2=$row['subject_2'];
        $lesson_type_2=$row['lesson_type_2'];
        if(!empty($row['count_of_weeks_2'])){$count_of_weeks_2=$row['count_of_weeks_2'];} else {$count_of_weeks_2='NULL';}
        $hall_2=$row['hall_2'];
        $lecturer_2_1=$row['lecturer_2_1'];
        if(!empty($row['lecturer_2_2'])){$lecturer_2_2=$row['lecturer_2_2'];} else {$lecturer_2_2='NULL';}
        $type_of_week_2=$row['type_of_week_2'];

        $day=$row['day'];
        $time=$row['time'];

        $gr_1=$row['group_1'];
        if(!empty($row['group_3'])) {
            $gr_2=$row['group_2'];
            $gr_3=$row['group_3'];
        }
        elseif(!empty($row['group_2'])){
            $gr_2=$row['group_2'];
            $gr_3='NULL';
        }
        else{
            $gr_2='NULL';
            $gr_3='NULL';
        }
    }
    return array($subject_2, $lesson_type_2, $count_of_weeks_2, $hall_2, $lecturer_2_1, $lecturer_2_2, $type_of_week_2, $day, $time, $gr_1, $gr_2, $gr_3);
}
function clearPart2Sql($link){
    $upd_sql2=pg_query($link, "UPDATE raspisanie.schedule SET subject_2=NULL, lesson_type_2=NULL,  
            count_of_weeks_2=NULL, hall_2=NULL, lecturer_2_1=NULL, lecturer_2_2=NULL, type_of_week_2=NULL
             WHERE id=".$_GET["id"]."");

    if ($upd_sql2) {
        echo "Запись удалена!!!<br><br>";
        ?>
        <script type="text/javascript">
            ID=window.setTimeout("Update();",6000);
            function Update(){ javascript:window.close(); }
        </script>
        <?
    } else {
        echo "Запись не удалена. Error.<br><br>";
    }
}
function clearAllSql($link){
    $sql = pg_query($link, "DELETE FROM raspisanie.schedule WHERE id = " . $_GET['id']); //удаляем строку из таблицы
    if ($sql) {
        echo "Запись успешно удалена!<br><br>";
        ?>
        <script type="text/javascript">
        ID=window.setTimeout("Update();",6000);
            function Update(){ javascript:window.close(); }
        </script>
        <?
    } else {
        echo "Запись не удалена. Ошибка.<br><br>";
    }
}
function Part2NotEmpty($link, $id){
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
        WHERE id=" . $id);
    while ($row = pg_fetch_array($result)) {
        if(!empty($row['lecturer_2_1']))
        {
            return true;
        }
    }
}

if(!empty($_GET['id']) and !empty($_GET["id"])) {
    if ($_GET['week'] == 'все') {
        if($_GET['lec_row']=='1_1' or $_GET['lec_row']=='2_1')
        {
            clearAllSql($link);
        }
        elseif($_GET['lec_row']=='1_2')
        {
            $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET lecturer_1_2=NULL");
            if ($upd_sql) {
                echo "Преподаватель №2 ".$_GET['lec']." удален из записи!<br>";
            } else {
                echo "Преподаватель №2 ".$_GET['lec']." не удален из записи. Ошибка.";
            }
        }
        elseif($_GET['lec_row']=='2_2')
        {
            $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET lecturer_2_2=NULL");
            if ($upd_sql) {
                echo "Преподаватель №2 ".$_GET['lec']." удален из записи!<br>";
            } else {
                echo "Преподаватель №2 ".$_GET['lec']." не удален из записи. Ошибка.";
            }
        }


    }
    elseif (($_GET['week'] == 'пар' or $_GET['week'] == 'н/п') and !empty($_GET['lec_row'])) {
        if(Part2NotEmpty($link, $_GET["id"]))
        {
            if($_GET['lec_row']=='1_1')
            {
                $subject_2= getVars2($link, $_GET["id"])[0];
                $lesson_type_2=getVars2($link, $_GET["id"])[1];
                $count_of_weeks_2=getVars2($link, $_GET["id"])[2];
                $hall_2=getVars2($link, $_GET["id"])[3];
                $lecturer_2_1=getVars2($link, $_GET["id"])[4];
                $lecturer_2_2=getVars2($link, $_GET["id"])[5];
                $type_of_week_2=getVars2($link, $_GET["id"])[6];
                $day=getVars2($link, $_GET["id"])[7];
                $time=getVars2($link, $_GET["id"])[8];
                $gr_1=getVars2($link, $_GET["id"])[9];
                $gr_2=getVars2($link, $_GET["id"])[10];
                $gr_3=getVars2($link, $_GET["id"])[11];

                clearAllSql($link);

                $add_sql=pg_query($link, "INSERT INTO raspisanie.schedule(day, time, subject_1, lesson_type_1,count_of_weeks_1,group_1,
                group_2, group_3, hall_1,lecturer_1_1, lecturer_1_2, type_of_week_1) VALUES ($day, $time, $subject_2,$lesson_type_2,
                $count_of_weeks_2,$gr_1,$gr_2,$gr_3,$hall_2,$lecturer_2_1, $lecturer_2_2, $type_of_week_2)");

                if ($add_sql) {
                    echo "Запись удалена.<br>";
                } else {
                    echo "ОШИБКА. ЛИШНЕЕ УДАЛЕНИЕ!!!";
                }
            }
            elseif($_GET['lec_row']=='1_2')
            {
                $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET lecturer_1_2=NULL");
                if ($upd_sql) {
                    echo "Преподаватель №2 ".$_GET['lec']." удален из записи!<br>";
                } else {
                    echo "Преподаватель №2 ".$_GET['lec']." не удален из записи. Ошибка.";
                }
            }
            elseif($_GET['lec_row']=='2_1')
            {
                clearPart2Sql($link);

            }
            elseif($_GET['lec_row']=='2_2')
            {
                $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET lecturer_2_2=NULL");
                if ($upd_sql) {
                    echo "Преподаватель №2 ".$_GET['lec']." удален из записи!<br>";
                } else {
                    echo "Преподаватель №2 ".$_GET['lec']." не удален из записи. Ошибка.";
                }
            }

        }
        else{
            if($_GET['lec_row']=='1_1' or $_GET['lec_row']=='2_1')
            {
                clearAllSql($link);
            }
            elseif($_GET['lec_row']=='1_2')
            {
                $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET lecturer_1_2=NULL");
                if ($upd_sql) {
                    echo "Преподаватель №2 ".$_GET['lec']." удален из записи!<br>";
                } else {
                    echo "Преподаватель №2 ".$_GET['lec']." не удален из записи. Ошибка.";
                }
            }
            elseif($_GET['lec_row']=='2_2')
            {
                $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET lecturer_2_2=NULL");
                if ($upd_sql) {
                    echo "Преподаватель №2 ".$_GET['lec']." удален из записи!<br>";
                } else {
                    echo "Преподаватель №2 ".$_GET['lec']." не удален из записи. Ошибка.";
                }
            }
        }

    }
    ?>
    <script type="text/javascript">
    ID=window.setTimeout("Update();",6000);
        function Update(){ javascript:window.close(); }
    </script>
<?
}
pg_close($link);
echo '</body></html>';
?>
<?
endif;
?>
