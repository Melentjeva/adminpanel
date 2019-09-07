<?php
/**
 * Created by PhpStorm.
 * User: Maria_Melentyeva
 * Date: 12.06.2018
 * Time: 04:07
 */

include '../db_connection.php';


function isLecturer_1_1orLecturer_1_2($link, $id, $get_lid){
    $row = pg_fetch_row(pg_query($link,"SELECT lecturer_1_1 FROM raspisanie.schedule WHERE id=".$id));
    if($get_lid==$row[0]){
        return 'lecturer_1_1';
    }
    else { return 'lecturer_1_2'; }
}

function isLec2Busy($link, $day_id, $time_id, $lec2_id){
    $a=0;
    $result = pg_query($link, "SELECT * FROM raspisanie.schedule WHERE lecturer_1_1=".$lec2_id." or lecturer_1_2=".$lec2_id);
    while ($row = pg_fetch_array($result))
    {
        echo $row['day'].' '.$day_id.'   '.$row['time'].' '.$time_id.'<br>';
        if($row['day']==$day_id and $row['time']==$time_id)
        { $a=$a+1; }
    }
    if($a>0){return true;} else {return false;}
}
/*
function isLec2BusyLikeLecturer_1_2($link, $day_id, $time_id, $lec2){
    $row = pg_fetch_array(pg_query($link, "SELECT * FROM raspisanie.schedule WHERE lecturer_1_2=" . $lec2));
    if($row['day']==$day_id and $row['time']==$time_id)
    {
        return true;
    } else {return false;}
}
*/
function isNewField($my_link, $column_name, $table, $user_data)
{
    // проверяем, не совпадает ли новая запись с уже существующей
    $count=0;
    $res = pg_query($my_link,"SELECT * FROM $table"); //запрос к БД
    while($row = pg_fetch_array($res))
    {
        if ($user_data == $row[$column_name] and $row['id']!=$_GET['red_id'])
        {
            $count = 1;
        }
    }
    pg_free_result($res);
    if($count==0){ return true; } else { return false; }
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

function SendScheduleBlanks($con){
    $subject = '';
    $lesson_type = '';
    $count_of_weeks = '';
    $group_1 = '';
    $group_2 = '';
    $group_3 = '';
    $hall = '';
    $lecturer_1_2 = '';
    $lecturer_1_1 = '';

    if (isset($_GET['id']) and !empty($_GET['id'])) {
        $link = pg_connect($con);
        $res = pg_query($link, "SELECT * FROM raspisanie.schedule WHERE id=" . $_GET['id']); //запрос к БД
        $row = pg_fetch_array($res); //получение самой записи

        if ($_GET['week'] == 'все')
        {
            $lec=isLecturer_1_1orLecturer_1_2($link, $_GET['id'], $_GET['lid']);
            echo 'lec_id='.$lec;

            $subject=getValueByForeignKey($link,$row,'subject_1', 'raspisanie.subjects','subject');
            $group_1=getValueByForeignKey($link,$row,'group_1', 'raspisanie.groups','group_number');
            $hall=getValueByForeignKey($link,$row,'hall_1', 'raspisanie.halls','hall');
            $lesson_type=getValueByForeignKey($link,$row,'lesson_type_1', 'raspisanie.lesson_type','type');
            if(!empty($row['count_of_weeks_1'])) {
                $count_of_weeks = getValueByForeignKey($link, $row, 'count_of_weeks_1', 'raspisanie.count_of_lessons', 'count');
            } else {$count_of_weeks ='';}
            if (!empty($row['group_3'])) {
                $group_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
            } else {$group_3='';}
            if (!empty($row['group_2'])) {
                $group_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
            } else {$group_2='';}

            $lecturer_1_1=getValueByForeignKey($link,$row,'lecturer_1_1', 'raspisanie.lecturers','lecturer');
            if(!empty($row['lecturer_1_2'])){
                $lecturer_1_2=getValueByForeignKey($link,$row,'lecturer_1_2', 'raspisanie.lecturers','lecturer');
            } else {$lecturer_1_2='';}

        }
    }
    else {
        $lec='lecturer_1_1';
    }
    ?>
    *Дисциплина:
    <select name="Subject">
        <?php
        echo '<option selected="selected">' . $subject . '</option>';
        if (!empty($subject)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT subject FROM raspisanie.subjects";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if($row['subject'] != $subject){
                echo '<option>' . $row['subject'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    *Тип занятия:
    <select name="Lesson_Type">
        <?php
        echo '<option selected="selected">' . $lesson_type . '</option>';
        if (!empty($lesson_type)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT type FROM raspisanie.lesson_type";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['type'] != $lesson_type) {
                echo '<option>' . $row['type'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Число недель:
    <select name="Count_of_Weeks">
        <?php
        echo '<option selected="selected">' . $count_of_weeks . '</option>';
        if (!empty($count_of_weeks)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT count FROM raspisanie.count_of_lessons";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['count'] != $count_of_weeks) {
                echo '<option>' . $row['count'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    <br><br>
    *Группа 1:
    <select name="Group_1">
        <?php
        echo '<option selected="selected">' . $group_1 . '</option>';
        if (!empty($group_1)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_1) {
                echo '<option>' . $row['group_number'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Группа 2:
    <select name="Group_2">
        <?php
        echo '<option selected="selected">' . $group_2 . '</option>';
        if (!empty($group_2)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_2 ) {
                echo '<option>' . $row['group_number'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Группа 3:
    <select name="Group_3">
        <?php
        echo '<option selected="selected">' . $group_3 . '</option>';
        if (!empty($group_3)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_3) {
                echo '<option>' . $row['group_number'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    <br><br>
    *Аудитория:
    <select name="Hall">
        <?php
        echo '<option selected="selected">' . $hall . '</option>';
        if (!empty($hall)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT hall FROM raspisanie.halls";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['hall'] != $hall) {
                echo '<option>' . $row['hall'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Преподаватель 2:
    <select name="Lecturer_2">
        <?php
        if ($lec == 'lecturer_1_1') {
            echo '<option selected="selected">' . $lecturer_1_2 . '</option>';
            if (!empty($lecturer_1_2)) {
                echo '<option></option>';
            }
            $link = pg_connect($con);
            $sql = "SELECT * FROM raspisanie.lecturers";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                if ($row['lecturer'] != $lecturer_1_2 and $row['lecturer'] !=$_GET['lec'])
                {
                    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
                    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');
                    if(isLec2Busy($link, $day_id, $time_id, $lec2_id=$row['id'])==false){
                        echo '<option>' . $row['lecturer'] . '</option>';
                    }
                }
            }
            pg_close($link);
        }
        elseif ($lec == 'lecturer_1_2') {
            echo '<option selected="selected">' . $lecturer_1_1 . '</option>';
            if (!empty($lecturer_1_1)) {
                echo '<option></option>';
            }
            $link = pg_connect($con);
            $sql = "SELECT * FROM raspisanie.lecturers";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                if ($row['lecturer'] != $lecturer_1_1 and $row['lecturer'] !=$_GET['lec'])
                {
                    $day_id=getFKeyByValue($link, $_GET['day'], 'raspisanie.days_of_week', 'day');
                    $time_id=getFKeyByValue($link, $_GET['time'], 'raspisanie.time', 'number_of_lesson');
                    if(isLec2Busy($link, $day_id, $time_id, $lec2_id=$row['id'])==false)
                    {
                        echo '<option>' . $row['lecturer'] . '</option>';
                    }
                }
            }
            pg_close($link);
        }
        ?>
    </select>
    <br><br>
    <?php
}


function SendScheduleBlanks_Pare($con){
    $subject = '';
    $lesson_type = '';
    $count_of_weeks = '';
    $group_1 = '';
    $group_2 = '';
    $group_3 = '';
    $hall = '';
    $lecturer_2 = '';
    $lecturer_1 = '';
    if (isset($_GET['id']) and !empty($_GET['id'])) {
        $link = pg_connect($con);
        $res = pg_query($link, "SELECT * FROM raspisanie.schedule WHERE id=" . $_GET['id']); //запрос к БД
        $row = pg_fetch_array($res); //получение самой записи
        if ($_GET['week'] == 'пар') {
            if ($_GET['lid'] == '1' or $_GET['lid'] == '2') {
                $subject=getValueByForeignKey($link,$row,'subject_1', 'raspisanie.subjects','subject');
                $group_1=getValueByForeignKey($link,$row,'group_1', 'raspisanie.groups','group_number');
                $hall=getValueByForeignKey($link,$row,'hall_1', 'raspisanie.halls','hall');
                $lesson_type=getValueByForeignKey($link,$row,'lesson_type_1', 'raspisanie.lesson_type','type');
                if(!empty($row['count_of_weeks_1'])) {
                    $count_of_weeks = getValueByForeignKey($link, $row, 'count_of_weeks_1', 'raspisanie.count_of_lessons', 'count');
                }
                if (!empty($row['group_3'])) {
                    $group_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
                }
                if (!empty($row['group_2'])) {
                    $group_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
                }

                $lecturer_1=getValueByForeignKey($link,$row,'lecturer_1_1', 'raspisanie.lecturers','lecturer');
                if(!empty($row['lecturer_1_2'])){
                    $lecturer_2=getValueByForeignKey($link,$row,'lecturer_1_2', 'raspisanie.lecturers','lecturer');
                }

            } else {
                $subject=getValueByForeignKey($link,$row,'subject_2', 'raspisanie.subjects','subject');
                $group_1=getValueByForeignKey($link,$row,'group_1', 'raspisanie.groups','group_number');
                $hall=getValueByForeignKey($link,$row,'hall_1', 'raspisanie.halls','hall');
                $lesson_type=getValueByForeignKey($link,$row,'lesson_type_2', 'raspisanie.lesson_type','type');
                if(!empty($row['count_of_weeks_1'])) {
                    $count_of_weeks = getValueByForeignKey($link, $row, 'count_of_weeks_2', 'raspisanie.count_of_lessons', 'count');
                }
                if (!empty($row['group_3'])) {
                    $group_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
                }
                if (!empty($row['group_2'])) {
                    $group_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
                }

                $lecturer_1=getValueByForeignKey($link,$row,'lecturer_2_1', 'raspisanie.lecturers','lecturer');
                if(!empty($row['lecturer_1_2'])){
                    $lecturer_2=getValueByForeignKey($link,$row,'lecturer_2_2', 'raspisanie.lecturers','lecturer');
                }

            }
        }
    }
    ?>
    *Дисциплина:
    <select name="Subject_P">
        <?php
        echo '<option selected="selected">' . $subject . '</option>';
        if (!empty($subject)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT subject FROM raspisanie.subjects";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if($row['subject'] != $subject){
                echo '<option>' . $row['subject'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    *Тип занятия:
    <select name="Lesson_Type_P">
        <?php
        echo '<option selected="selected">' . $lesson_type . '</option>';
        if (!empty($lesson_type)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT type FROM raspisanie.lesson_type";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['type'] != $lesson_type) {
                echo '<option>' . $row['type'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Число недель:
    <select name="Count_of_Weeks_P">
        <?php
        echo '<option selected="selected">' . $count_of_weeks . '</option>';
        if (!empty($count_of_weeks)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT count FROM raspisanie.count_of_lessons";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['count'] != $count_of_weeks) {
                echo '<option>' . $row['count'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    <br><br>
    *Группа 1:
    <select name="Group_1_P">
        <?php
        echo '<option selected="selected">' . $group_1 . '</option>';
        if (!empty($group_1)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_1) {
                echo '<option>' . $row['group_number'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Группа 2:
    <select name="Group_2_P">
        <?php
        echo '<option selected="selected">' . $group_2 . '</option>';
        if (!empty($group_2)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_2) {
                echo '<option>' . $row['group_number'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Группа 3:
    <select name="Group_3_P">
        <?php
        echo '<option selected="selected">' . $group_3 . '</option>';
        if (!empty($group_3)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_3) {
                echo '<option>' . $row['group_number'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    <br><br>
    *Аудитория:
    <select name="Hall_P">
        <?php
        echo '<option selected="selected">' . $hall . '</option>';
        if (!empty($hall)) {
            echo '<option></option>';
        }
        $link = pg_connect($con);
        $sql = "SELECT hall FROM raspisanie.halls";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['hall'] != $hall) {
                echo '<option>' . $row['hall'] . '</option>';
            }
        }
        pg_close($link);
        ?>
    </select>
    Преподаватель 2:
    <select name="Lecturer_2_P">
        <?php
        if ($_GET['lid'] == 1 or $_GET['lid'] == 3) {
            echo '<option selected="selected">' . $lecturer_2 . '</option>';
            if (!empty($lecturer_2)) {
                echo '<option></option>';
            }
            $link = pg_connect($con);
            $sql = "SELECT lecturer FROM raspisanie.lecturers";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                if ($row['lecturer'] != $lecturer_2 and $row['lecturer'] !=$_GET['lec']) {
                    echo '<option>' . $row['lecturer'] . '</option>';
                }
            }
            pg_close($link);
        } else {
            echo '<option selected="selected">' . $lecturer_1 . '</option>';
            if (!empty($lecturer_1)) {
                echo '<option></option>';
            }
            $link = pg_connect($con);
            $sql = "SELECT lecturer FROM raspisanie.lecturers";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                if ($row['lecturer'] != $lecturer_1 and $row['lecturer'] !=$_GET['lec']) {
                    echo '<option>' . $row['lecturer'] . '</option>';
                }
            }
            pg_close($link);
        }
        ?>
    </select>
    <br><br>
    <?php
}


function SendScheduleBlanks_NotPare($con){
    $subject = '';
    $lesson_type = '';
    $count_of_weeks = '';
    $group_1 = '';
    $group_2 = '';
    $group_3 = '';
    $hall = '';
    $lecturer_2 = '';
    $lecturer_1 = '';
    if (isset($_GET['id']) and !empty($_GET['id'])) {
        $link = pg_connect($con);
        $res = pg_query($link, "SELECT * FROM raspisanie.schedule WHERE id=" . $_GET['id']); //запрос к БД
        $row = pg_fetch_array($res); //получение самой записи
        if ($_GET['week'] == 'н/п')
        {
            if ($_GET['lid'] == '1' or $_GET['lid'] == '2')
            {
                $subject=getValueByForeignKey($link,$row,'subject_1', 'raspisanie.subjects','subject');
                $group_1=getValueByForeignKey($link,$row,'group_1', 'raspisanie.groups','group_number');
                $hall=getValueByForeignKey($link,$row,'hall_1', 'raspisanie.halls','hall');
                $lesson_type=getValueByForeignKey($link,$row,'lesson_type_1', 'raspisanie.lesson_type','type');
                if(!empty($row['count_of_weeks_1'])) {
                    $count_of_weeks = getValueByForeignKey($link, $row, 'count_of_weeks_1', 'raspisanie.count_of_lessons', 'count');
                }
                if (!empty($row['group_3'])) {
                    $group_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
                }
                if (!empty($row['group_2'])) {
                    $group_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
                }

                $lecturer_1=getValueByForeignKey($link,$row,'lecturer_1_1', 'raspisanie.lecturers','lecturer');
                if(!empty($row['lecturer_1_2'])){
                    $lecturer_2=getValueByForeignKey($link,$row,'lecturer_1_2', 'raspisanie.lecturers','lecturer');
                }
            }
            else {
                $subject=getValueByForeignKey($link,$row,'subject_2', 'raspisanie.subjects','subject');
                $group_1=getValueByForeignKey($link,$row,'group_1', 'raspisanie.groups','group_number');
                $hall=getValueByForeignKey($link,$row,'hall_1', 'raspisanie.halls','hall');
                $lesson_type=getValueByForeignKey($link,$row,'lesson_type_2', 'raspisanie.lesson_type','type');
                if(!empty($row['count_of_weeks_1'])) {
                    $count_of_weeks = getValueByForeignKey($link, $row, 'count_of_weeks_2', 'raspisanie.count_of_lessons', 'count');
                }
                if (!empty($row['group_3'])) {
                    $group_3=getValueByForeignKey($link,$row,'group_3', 'raspisanie.groups','group_number');
                }
                if (!empty($row['group_2'])) {
                    $group_2=getValueByForeignKey($link,$row,'group_2', 'raspisanie.groups','group_number');
                }

                $lecturer_1=getValueByForeignKey($link,$row,'lecturer_2_1', 'raspisanie.lecturers','lecturer');
                if(!empty($row['lecturer_1_2'])){
                    $lecturer_2=getValueByForeignKey($link,$row,'lecturer_2_2', 'raspisanie.lecturers','lecturer');
                }
            }
        }
    }
    ?>
    *Дисциплина:
    <select name="Subject_N">
        <?php
        echo '<option selected="selected">'.$subject.'</option>';
        if (!empty($subject)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT subject FROM raspisanie.subjects";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if($row['subject'] != $subject)
            {
                echo '<option>' . $row['subject'] . '</option>';

            }
        } pg_close($link);
        ?>
    </select>
    *Тип занятия:
    <select name="Lesson_Type_N">
        <?php
        echo '<option selected="selected">'.$lesson_type.'</option>';
        if (!empty($lesson_type)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT type FROM raspisanie.lesson_type";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['type'] != $lesson_type) { echo '<option>' . $row['type'] . '</option>';}
        } pg_close($link);
        ?>
    </select>
    Число недель:
    <select name="Count_of_Weeks_N">
        <?php
        echo '<option selected="selected">'.$count_of_weeks.'</option>';
        if (!empty($count_of_weeks)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT count FROM raspisanie.count_of_lessons";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['count'] != $count_of_weeks) { echo '<option>' . $row['count'] . '</option>';}
        } pg_close($link);
        ?>
    </select>
    <br><br>
    *Группа 1:
    <select name="Group_1_N">
        <?php
        echo '<option selected="selected">'.$group_1.'</option>';
        if (!empty($group_1)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_1) { echo '<option>' . $row['group_number'] . '</option>';}
        } pg_close($link);
        ?>
    </select>
    Группа 2:
    <select name="Group_2_N">
        <?php
        echo '<option selected="selected">'.$group_2.'</option>';
        if (!empty($group_2)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_2) { echo '<option>' . $row['group_number'] . '</option>';}
        } pg_close($link);
        ?>
    </select>
    Группа 3:
    <select name="Group_3_N">
        <?php
        echo '<option selected="selected">'.$group_3.'</option>';
        if (!empty($group_3)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT group_number FROM raspisanie.groups";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['group_number'] != $group_3) {echo '<option>' . $row['group_number'] . '</option>';}
        } pg_close($link);
        ?>
    </select>
    <br><br>
    *Аудитория:
    <select name="Hall_N">
        <?php
        echo '<option selected="selected">'.$hall.'</option>';
        if (!empty($hall)){ echo '<option></option>'; }
        $link = pg_connect($con);
        $sql = "SELECT hall FROM raspisanie.halls";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            if ($row['hall'] != $hall) { echo '<option>' . $row['hall'] . '</option>';}
        } pg_close($link);
        ?>
    </select>
    Преподаватель 2:
    <select name="Lecturer_2_N">
        <?php
        if($_GET['lid']==1 or $_GET['lid']==3)
        {
            echo '<option selected="selected">'.$lecturer_2.'</option>';
            if (!empty($lecturer_2)){ echo '<option></option>'; }
            $link = pg_connect($con);
            $sql = "SELECT lecturer FROM raspisanie.lecturers";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                if ($row['lecturer'] != $lecturer_1 and $row['lecturer'] !=$_GET['lec'])
                { echo '<option>' . $row['lecturer'] . '</option>';}
            } pg_close($link);
        }
        else{
            echo '<option selected="selected">'.$lecturer_1.'</option>';
            if (!empty($lecturer_1)){ echo '<option></option>'; }
            $link = pg_connect($con);
            $sql = "SELECT lecturer FROM raspisanie.lecturers";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                if ($row['lecturer'] != $lecturer_2 and $row['lecturer'] !=$_GET['lec'])
                { echo '<option>' . $row['lecturer'] . '</option>';}
            } pg_close($link);
        }
        ?>
    </select>
    <br><br>
    <?php
}


function getValueByForeignKey($link, $row, $column, $table, $table_column){
    $id=$row[$column];
    $result=pg_query($link, "SELECT ".$table_column." FROM ".$table." WHERE id=".$id);
    $column_row = pg_fetch_array($result);
    return $column_row[$table_column];
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