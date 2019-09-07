<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?><?php
include '../db_connection.php';
include '../function.php';
include 'new_row_add_hat.php';
include 'func.php';

function sendUpdSql($link, $subj,$less_type,$weeks,$hall,$lecturer_1, $lecturer_2, $parity, $id){
    if(isLecBusyConsultations($link, $lecturer_1)==false and isLecBusyConsultations($link, $lecturer_2)==false)
    {
        $upd_sql = pg_query($link, "UPDATE raspisanie.schedule SET subject_2=" . $subj . ", lesson_type_2=" . $less_type . ",  
        count_of_weeks_2=" . $weeks . ", hall_2=" . $hall . ", lecturer_2_1=" . $lecturer_1 . ", lecturer_2_2=" . $lecturer_2 . ", type_of_week_2=" . $parity . " 
         WHERE id=" . $id . "");

        if ($upd_sql) {
            echo "Запись добавлена!!!<br>";
            ?>
            <script type="text/javascript">
                ID = window.setTimeout("Update();", 6000);

                function Update() {
                    javascript:window.close();
                }
            </script>
            <?
        } else { echo "Изменения не добавлены. Error"; }
    }else {echo "Изменения не добавлены. У преподавателя консультация в этот день и время.";}
}
function sendAddSql($link, $day, $time, $subj,$less_type,$weeks,$gr_1,$gr_2,$gr_3,$hall,$lecturer_1, $lecturer_2, $parity)
{
    if(isLecBusyConsultations($link, $lecturer_1)==false and isLecBusyConsultations($link, $lecturer_2)==false) {
        $add_sql = pg_query($link, "INSERT INTO raspisanie.schedule(day, time, subject_1, lesson_type_1,count_of_weeks_1,group_1,
                group_2, group_3, hall_1,lecturer_1_1, lecturer_1_2, type_of_week_1) VALUES ($day, $time, $subj,$less_type,$weeks,$gr_1,$gr_2,$gr_3,$hall,
                $lecturer_1, $lecturer_2, $parity)");

        if ($add_sql) {
            echo "Запись добавлена!<br>";
            ?>
            <script type="text/javascript">
                ID = window.setTimeout("Update();", 6000);

                function Update() {
                    javascript:window.close();
                }
            </script>
            <?
        } else { echo "Запись не добавлена. Ошибка."; }
    } else {echo "Изменения не добавлены. У преподавателя консультация в этот день и время.";}
}

#-------------------------------------------------------------------------------------------------------

$link = pg_connect($connection);

if(isset($_POST['submit']) and isset($_POST['week_radio']))
{
    $subj = getFKeyByValue($link, $_POST['Subject'], 'raspisanie.subjects', 'subject');
    $less_type = getFKeyByValue($link, $_POST['Lesson_Type'], 'raspisanie.lesson_type', 'type');
    $weeks=getCountOfWeeksPost($link);
    $gr_1 = getFKeyByValue($link, $_POST['Group_1'], 'raspisanie.groups', 'group_number');
    $gr_2=getGroups23Post($link)[0];
    $gr_3=getGroups23Post($link)[1];
    $hall = getFKeyByValue($link, $_POST['Hall'], 'raspisanie.halls', 'hall');
    $lecturer_2=getLecturer2Post($link,$coincide_text='Преподаватели 1 и 2 не должны совпадать (преподаватель 2 не добавлен в таблицу).<br>');

    if (isset($_GET['id']) and isset($_GET['lid'])) {
        if (notEmptyPost())
        {
            $lecturer_1=$_GET['lid'];
            $day = getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
            $time = getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');
            $parity=getParity();

            if(isLecBusy($parity, $link, $lecturer_1)==false)
            {
                $id=getMatchRowGroupId($link,$day, $time, $gr_1,$gr_2,$gr_3, $parity);
                if(!empty($id)){
                    sendUpdSql($link, $subj,$less_type,$weeks,$hall,$lecturer_1, $lecturer_2, $parity, $id);
                }
                else
                {
                    sendAddSql($link, $day, $time, $subj, $less_type, $weeks, $gr_1, $gr_2, $gr_3, $hall, $lecturer_1, $lecturer_2, $parity);
                }
            } else {echo "В этот день и время у преподавателя уже есть пара!<br>";}
        }
        else {echo "Поля со '*' должны быть заполнены!<br>";}
    }
}

pg_close($link);


#-------------------------------------------------------------------------------------------------------

function sendSelect($name, $name_select, $link, $table, $column)
{
    echo $name;
    echo '<select name='.$name_select.'>';
    echo '<option selected="selected"></option>';
    $result = pg_query($link, "SELECT ".$column." FROM ".$table."");
    while ($row = pg_fetch_array($result))
    {
        echo '<option>' . $row[$column] . '</option>';
    }
    echo '</select>';
}
function sendSelectGroup($name, $name_select, $link,$table, $column, $func){
    echo $name;
    echo '<select name='.$name_select.'>';
    echo '<option selected="selected"></option>';
    $result = pg_query($link, "SELECT * FROM ".$table."");
    while ($row = pg_fetch_array($result))
    {
        if($func($link, $group_id=$row['id'])==false)
        {
            echo '<option>' . $row[$column] . '</option>';
        }
    }
    echo '</select>';
}

#-------------------------------------------------------------------------------------------------------

function AddBlanksAll($con){
    $link = pg_connect($con);

    sendSelect('*Дисциплина:', 'Subject', $link, 'raspisanie.subjects', 'subject');
    sendSelect('*Тип занятия:', 'Lesson_Type', $link, 'raspisanie.lesson_type', 'type');
    sendSelect('Число недель:', 'Count_of_Weeks', $link, 'raspisanie.count_of_lessons', 'count');
    echo '<br><br>';
    sendSelectGroup('*Группа 1:','Group_1',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyAll');
    sendSelectGroup('Группа 2:','Group_2',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyAll');
    sendSelectGroup('Группа 3:','Group_3',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyAll');
    echo '<br><br>';
    sendSelect('*Аудитория:', 'Hall', $link, 'raspisanie.halls', 'hall');
    ?>
    Преподаватель 2:
    <select name="Lecturer_2">
        <?php
        echo '<option selected="selected"></option>';
        $result = pg_query($link, "SELECT * FROM raspisanie.lecturers");
        while ($row = pg_fetch_array($result)) {
            if(isLecBusyAll($link, $lec_id=$row['id'])==false and $row['id'] !=$_GET['lid'])
            {
                echo '<option>' . $row['lecturer'] . '</option>';
            }
        }
        ?>
    </select>
    <br><br>

    <?php
    pg_close($link);

}

function AddBlanksPair($con){
    $link = pg_connect($con);

    sendSelect('*Дисциплина:', 'Subject', $link, 'raspisanie.subjects', 'subject');
    sendSelect('*Тип занятия:', 'Lesson_Type', $link, 'raspisanie.lesson_type', 'type');
    sendSelect('Число недель:', 'Count_of_Weeks', $link, 'raspisanie.count_of_lessons', 'count');
    echo '<br><br>';
    sendSelectGroup('*Группа 1:','Group_1',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyPair');
    sendSelectGroup('Группа 2:','Group_2',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyPair');
    sendSelectGroup('Группа 3:','Group_3',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyPair');
    echo '<br><br>';
    sendSelect('*Аудитория:', 'Hall', $link, 'raspisanie.halls', 'hall');

    ?>
    Преподаватель 2:
    <select name="Lecturer_2">
        <?php
        echo '<option selected="selected"></option>';
        $result = pg_query($link, "SELECT * FROM raspisanie.lecturers");
        while ($row = pg_fetch_array($result)) {
            if(isLecBusyPair($link, $lec_id=$row['id'])==false and $row['id'] !=$_GET['lid'])
            {
                echo '<option>' . $row['lecturer'] . '</option>';
            }
        }
        ?>
    </select>
    <br><br>

    <?php
    pg_close($link);

}

function AddBlanksNotPair($con){
    $link = pg_connect($con);

    sendSelect('*Дисциплина:', 'Subject', $link, 'raspisanie.subjects', 'subject');
    sendSelect('*Тип занятия:', 'Lesson_Type', $link, 'raspisanie.lesson_type', 'type');
    sendSelect('Число недель:', 'Count_of_Weeks', $link, 'raspisanie.count_of_lessons', 'count');
    echo '<br><br>';
    sendSelectGroup('*Группа 1:','Group_1',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyNotPair');
    sendSelectGroup('Группа 2:','Group_2',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyNotPair');
    sendSelectGroup('Группа 3:','Group_3',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyNotPair');
    echo '<br><br>';
    sendSelect('*Аудитория:', 'Hall', $link, 'raspisanie.halls', 'hall');

    ?>
    Преподаватель 2:
    <select name="Lecturer_2">
        <?php
        echo '<option selected="selected"></option>';
        $result = pg_query($link, "SELECT * FROM raspisanie.lecturers");
        while ($row = pg_fetch_array($result)) {
            if(isLecBusyNotPair($link, $lec_id=$row['id'])==false and $row['id'] !=$_GET['lid'])
            {
                echo '<option>' . $row['lecturer'] . '</option>';
            }
        }
        ?>
    </select>
    <br><br>

    <?php
    pg_close($link);

}

#-------------------------------------------------------------------------------------------------------

function formAction($value, $parity_text, $func, $con){
    echo '<form action method="post">';
    #echo '<form action="new_row_add_action.php?lec=' .$_GET['lec'] .'&lid='.$_GET['lid'].'&day='.$_GET['day'].'&time='.$_GET['time'].'&week=все&id='.$_GET['id'].'" method="post">';
    echo '<label><input type="radio" name="week_radio" value='.$value.' checked>'.$parity_text.'<br><br>';
    $func($con);
    echo '</label><br><label><input type="submit" name="submit" value="OK"></a></label></form>';
}

if(isset($_GET['week'])){
    if($_GET['week']=='все')
    {
        formAction('all','ВCЕ (Предмет идет и в пар, и в н/п недели)', 'AddBlanksAll', $connection);
    }
    elseif ($_GET['week']=='пар'){
        formAction('par','ПАР', 'AddBlanksPair', $connection);
    }
    elseif ($_GET['week']=='н/п'){
        formAction('notpar','Н/П', 'AddBlanksNotPair', $connection);
    }
}
?>


<?
endif;
?>