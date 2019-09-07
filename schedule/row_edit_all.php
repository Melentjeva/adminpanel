<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><?php
include "row_edit_hat.php";
include '../db_connection.php';
include '../function.php';
include 'func.php';



function sendUpdSqlEditAll($link, $group_2, $group_3, $subj,$less_type,$weeks,$hall,$lecturer_1, $lecturer_2, $parity, $id){
    $upd_sql=pg_query($link, "UPDATE raspisanie.schedule SET group_2=".$group_2.", group_3=".$group_3.", subject_1=".$subj.", lesson_type_1=".$less_type.",  
    count_of_weeks_1=".$weeks.", hall_1=".$hall.", lecturer_1_1=".$lecturer_1.", lecturer_1_2=".$lecturer_2.", type_of_week_1=".$parity." 
     WHERE id=".$id."");

        if ($upd_sql) {
            echo "Изменения добавлены<br>";
            ?>
            <script type="text/javascript">
                ID=window.setTimeout("Update();",20000);
                function Update(){ javascript:window.close(); }
            </script>
            <?
        } else {echo "Изменения не добавлены. Error";
        }
}

#-----------------------------------------------------------------------------------------

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

    if (isset($_GET['id']) and isset($_GET['lid']))
    if (notEmptyPost()) {
        $lecturer_1 = $_GET['lid'];
        $day = getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
        $time = getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');
        $parity = getParity();

        sendUpdSqlEditAll($link, $gr_2, $gr_3, $subj,$less_type,$weeks,$hall,$lecturer_1, $lecturer_2, $parity, $_GET['id']);
    }
    else {echo "Поля со '*' должны быть заполнены!<br>";}

}

pg_close($link);

#-----------------------------------------------------------------------------------------

function sendSelectEdit($name, $name_select, $link, $table, $column, $var)
{
    echo $name;
    echo '<select name='.$name_select.'>';
    echo '<option selected="selected">'.$var.'</option>';
    if (!empty($var)) {
        echo '<option></option>';
    }
    $result = pg_query($link, "SELECT ".$column." FROM ".$table."");
    while ($row = pg_fetch_array($result))
    {
        if($row[$column] != $var) {
            echo '<option>' . $row[$column] . '</option>';
        }
    }
    echo '</select>';
}
function sendSelectGroupEdit($name, $name_select, $link,$table, $column, $func, $var)
{
    echo $name;
    echo '<select name=' . $name_select . '>';
    if (!empty($var)) {
        echo '<option></option>';
    }
    echo '<option selected="selected">' . $var . '</option>';
    $result = pg_query($link, "SELECT * FROM " . $table . "");
    while ($row = pg_fetch_array($result)) {
        if ($func($link, $group_id = $row['id']) == false) {
            if ($row[$column] != $var) {
                echo '<option>' . $row[$column] . '</option>';
            }
        }
    }
    echo '</select>';
}

#-------------------------------------------------------------------------------------------------------

function AddBlanksAll($con)
{
    $link = pg_connect($con);
    if (isset($_GET['id']) and !empty($_GET['id'])) {
        $row = pg_fetch_array(pg_query($link, "SELECT * FROM raspisanie.schedule WHERE id=" . $_GET['id']));
        if ($_GET['week'] == 'все') {
            $subject = getVars($link, $row, $_GET['lec_row'], '')['0'];
            $hall = getVars($link, $row, $_GET['lec_row'], '')['1'];
            $lesson_type = getVars($link, $row, $_GET['lec_row'], '')['2'];
            $count_of_weeks = getVars($link, $row, $_GET['lec_row'], '')['3'];
            $lecturer_1_2 = getVars($link, $row, $_GET['lec_row'], '')['5'];

            $group_1 = getValueByForeignKey($link, $row, 'group_1', 'raspisanie.groups', 'group_number');
            $group_2 = getGroups($link, $row)[0];
            $group_3 = getGroups($link, $row)[1];


            sendSelectEdit(' *Дисциплина:', 'Subject', $link, 'raspisanie.subjects', 'subject', $subject);
            sendSelectEdit(' *Тип занятия:', 'Lesson_Type', $link, 'raspisanie.lesson_type', 'type', $lesson_type);
            sendSelectEdit(' Число недель:', 'Count_of_Weeks', $link, 'raspisanie.count_of_lessons', 'count', $count_of_weeks);
            echo '<br><br>';
            sendSelectGroupEdit(' *Группа 1:','Group_1',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyAll', $group_1);
            sendSelectGroupEdit(' Группа 2:','Group_2',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyAll', $group_2);
            sendSelectGroupEdit(' Группа 3:','Group_3',$link, 'raspisanie.groups', 'group_number', 'isGroupBusyAll', $group_3);
            echo '<br><br>';
            sendSelectEdit(' *Аудитория:', 'Hall', $link, 'raspisanie.halls', 'hall', $hall);
            ?>
            Преподаватель 2:
            <select name="Lecturer_2">
                <?php
                echo '<option selected="selected">'.$lecturer_1_2.'</option>';
                if (!empty($lecturer_1_2)) {
                    echo '<option></option>';
                }
                $result = pg_query($link, "SELECT * FROM raspisanie.lecturers");
                while ($row = pg_fetch_array($result)) {
                    if(isLecBusyAll($link, $lec_id=$row['id'])==false and $row['id'] !=$_GET['lid'])
                    {
                        if ($row['lecturer'] != $lecturer_1_2) {
                            echo '<option>' . $row['lecturer'] . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <br><br>
<?


        }

    }
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
}

endif;
?>