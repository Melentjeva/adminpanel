<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
?>
<!doctype html>
<html lang="ru">
<head>
    <title>Добавить консультацию</title>

</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">КОНСУЛЬТАЦИИ</h1>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
//Если переменная передана
function isLecBusyCons($link, $lec_id, $day, $lesson_number){
    $a=0;

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
    WHERE (lecturer_1_1=".$lec_id." or lecturer_1_2=".$lec_id." or lecturer_2_1=".$lec_id." or lecturer_2_2=".$lec_id.")
    AND day=".$day." AND time=".$lesson_number."");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}


if (isset($_POST["Lecturer"]) and isset($_POST["Day_1"]) and isset($_POST["Hall_1"]) and isset($_POST["Lesson_Num_1"]))
{
    $id_lecturer=getFKeyByValue($link, $_POST['Lecturer'], 'raspisanie.lecturers', 'lecturer' );
    $id_day_1=getFKeyByValue($link, $_POST['Day_1'], 'raspisanie.days_of_week', 'day' );
    $id_hall_1=getFKeyByValue($link, $_POST['Hall_1'], 'raspisanie.halls', 'hall' );
    $id_lesson_num_1=getFKeyByValue($link, $_POST['Lesson_Num_1'], 'raspisanie.time', 'number_of_lesson' );

    if (!empty($id_lecturer) and !empty($id_day_1) and !empty($id_hall_1) and !empty($id_lesson_num_1))
    {
        if(isNewField($my_link=$link, $column_name='lecturer', $table='raspisanie.consultations', $user_data=$id_lecturer)==true)
        {
            if (!empty($_POST["Day_2"]) and !empty($_POST["Hall_2"]) and !empty($_POST["Lesson_Num_2"]))
            {
                if (isInTablesConsultation1($conn = $connection) and isInTablesConsultation2($conn = $connection))
                {
                    if(isNewConsultation()){
                        $id_day_2=getFKeyByValue($link, $_POST['Day_2'], 'raspisanie.days_of_week', 'day' );
                        $id_hall_2=getFKeyByValue($link, $_POST['Hall_2'], 'raspisanie.halls', 'hall' );
                        $id_lesson_num_2=getFKeyByValue($link, $_POST['Lesson_Num_2'], 'raspisanie.time', 'number_of_lesson' );

                        if(isLecBusyCons($link, $id_lecturer, $id_day_1, $id_lesson_num_1)==false and isLecBusyCons($link, $id_lecturer, $id_day_2, $id_lesson_num_2)==false)
                        {
                            $sql = pg_query("INSERT INTO raspisanie.consultations (lecturer, day_1, hall_1, lesson_number_1, day_2, hall_2, lesson_number_2) 
                          VALUES (" . $id_lecturer . ", " . $id_day_1 . ", " . $id_hall_1 . ", " . $id_lesson_num_1 . ", " . $id_day_2 . ", " . $id_hall_2 . ", " . $id_lesson_num_2 . ")");
                            if ($sql) { echo "<p>Консультация 1: данные успешно добавлены в таблицу.</p><p>Консультация 2: данные успешно добавлены в таблицу.</p>"; }
                            else { echo "<p>Произошла ошибка.</p>"; }
                        }
                        else { echo "<p>Запись не добавлена! У преподавателя пара в этот день и время.</p>";}
                    }
                    else { echo "<p>Запись не добавлена! Консультация №1 и Консультация №2 не должны совпадать.</p>"; }
                }
                else { echo "<p>Запись не добавлена! Выберите значения из списка или добавьте новое в соответствующем разделе.</p>"; }
            }
            else {
                if (isInTablesConsultation1($conn = $connection))
                {
                    if(isLecBusyCons($link, $id_lecturer, $id_day_1, $id_lesson_num_1)==false)
                    {
                        $sql = pg_query("INSERT INTO raspisanie.consultations (lecturer, day_1, hall_1, lesson_number_1) VALUES (" . $id_lecturer . ", " . $id_day_1 . ", " . $id_hall_1 . ", " . $id_lesson_num_1 . ")");
                        if ($sql) { echo "<p>Консультация 1: данные успешно добавлены в таблицу.</p><p>Консультация 2: не добавлена. Заполните все поля, чтобы добавить.</p>"; }
                        else { echo "<p>Произошла ошибка.</p>"; }
                    }//Если вставка прошла успешно
                    else { echo "<p>Запись не добавлена! У преподавателя пара в этот день и время.</p>";}
                }
                else { echo "<p>Запись не добавлена! Выберите значения из списка или добавьте новое в соответствующем разделе.</p>"; }
            }
        }
        else { echo "<p>Новая запись не добавлена!</p><p>Такой преподаватель уже есть в таблице, нажмите на 'Редактировать' в соответствующей строке, чтобы изменить расписание его консультаций.</p>"; }
    }
    else{ echo "<p>Все поля консультации №1 должны быть заполнены.</p><p>Заполнение полей консультации №2 не обязательно.</p>"; }
}

pg_close($link);
?>

<form action="" method="post">
    <label>
    Преподаватель:
    <input list="choose_lecturer" name="Lecturer" autocomplete="on">
    <datalist id="choose_lecturer">
        <?php
        $link = pg_connect($connection);
        $sql = "SELECT lecturer FROM raspisanie.lecturers";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            echo '<option>' . $row['lecturer'] . '</option>';
        } pg_free_result($result); pg_close($link);
        ?>
    </datalist>
    </label>
    <label>
        <br><br>
        Консультация 1:
    День:
    <input list="choose_day_1" name="Day_1" autocomplete="on">
    <datalist id="choose_day_1">
        <?php
        $link = pg_connect($connection);
        $sql = "SELECT day FROM raspisanie.days_of_week";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            echo '<option>' . $row['day'] . '</option>';
        } pg_free_result($result);  pg_close($link);
        ?>
    </datalist>

    Аудитория:
    <input list="choose_hall_1" name="Hall_1" autocomplete="on">
    <datalist id="choose_hall_1">
        <?php
        $link = pg_connect($connection);
        $sql = "SELECT hall FROM raspisanie.halls";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            echo '<option>'.$row['hall'].'</option>';
        } pg_free_result($result);  pg_close($link);
        ?>
    </datalist>

    № пары:
    <input list="choose_lesson_num_1" name="Lesson_Num_1" autocomplete="on">
    <datalist id="choose_lesson_num_1">
        <?php
        $link = pg_connect($connection);
        $sql = "SELECT number_of_lesson FROM raspisanie.time";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
        echo '<option>'.$row['number_of_lesson'].'</option>';
        } pg_free_result($result);  pg_close($link);
        ?>
    </datalist>
    </label>
    <label>
        <br><br>
        Консультация 2:
        День:
        <input list="choose_day_2" name="Day_2" autocomplete="off">
        <datalist id="choose_day_2">
            <?php
            $link = pg_connect($connection);
            $sql = "SELECT day FROM raspisanie.days_of_week";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                echo '<option>' . $row['day'] . '</option>';
            } pg_free_result($result);  pg_close($link);
            ?>
        </datalist>

        Аудитория:
        <input list="choose_hall_2" name="Hall_2" autocomplete="off">
        <datalist id="choose_hall_2">
            <?php
            $link = pg_connect($connection);
            $sql = "SELECT hall FROM raspisanie.halls";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                echo '<option>'.$row['hall'].'</option>';
            } pg_free_result($result);  pg_close($link);
            ?>
        </datalist>

        № пары:
        <input list="choose_lesson_num_2" name="Lesson_Num_2" autocomplete="off">
        <datalist id="choose_lesson_num_2">
            <?php
            $link = pg_connect($connection);
            $sql = "SELECT number_of_lesson FROM raspisanie.time";
            $result = pg_query($link, $sql);
            while ($row = pg_fetch_array($result)) {
                echo '<option>'.$row['number_of_lesson'].'</option>';
            } pg_free_result($result);  pg_close($link);
            ?>
        </datalist>
    </label>

    <label><input type="submit" value="OK"></label>

</form>
</body>

<? endif;
?>