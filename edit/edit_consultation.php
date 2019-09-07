<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
    ?>
    <head>
    <title>Редактировать консультации</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Консультации</h1>
    </div>
</div>
</body>
<?php

include '../db_connection.php';
include 'func.php';

function isLecBusyCons($link, $lec_id, $day, $lesson_number){
    $a=0;

    $result = pg_query($link, "SELECT * FROM raspisanie.schedule 
    WHERE (lecturer_1_1=".$lec_id." or lecturer_1_2=".$lec_id." or lecturer_2_1=".$lec_id." or lecturer_2_2=".$lec_id.")
    AND day=".$day." AND time=".$lesson_number."");

    while ($row = pg_fetch_array($result))
    {$a=$a+1; }
    if($a>0){return true;} else {return false;}
}

$link = pg_connect($connection);

if (isset($_GET['red_id']) )
{
    if (!empty($_POST["Lecturer"]) and !empty($_POST["Day_1"]) and !empty($_POST["Hall_1"]) and !empty($_POST["Lesson_Num_1"]))
    { //Проверяем, передана ли переменная на редактирования

        $id_lecturer=getFKeyByValue($link, $_POST['Lecturer'], 'raspisanie.lecturers', 'lecturer' );
        $id_day_1=getFKeyByValue($link, $_POST['Day_1'], 'raspisanie.days_of_week', 'day' );
        $id_hall_1=getFKeyByValue($link, $_POST['Hall_1'], 'raspisanie.halls', 'hall' );
        $id_lesson_num_1=getFKeyByValue($link, $_POST['Lesson_Num_1'], 'raspisanie.time', 'number_of_lesson' );
        $id_day_2=getFKeyByValue($link, $_POST['Day_2'], 'raspisanie.days_of_week', 'day' );
        $id_hall_2=getFKeyByValue($link, $_POST['Hall_2'], 'raspisanie.halls', 'hall' );
        $id_lesson_num_2=getFKeyByValue($link, $_POST['Lesson_Num_2'], 'raspisanie.time', 'number_of_lesson' );

        $id = $_GET['red_id'];

        if(isNewField($my_link=$link, $column_name='lecturer', $table='raspisanie.consultations', $user_data=$id_lecturer)==true)
        {
            if (isset($_POST["Lecturer"]) and isset($_POST["Day_1"]) and isset($_POST["Hall_1"]) and isset($_POST["Lesson_Num_1"]))
            {
                if (!empty($_POST["Hall_2"]) and !empty($_POST["Lesson_Num_2"]) and isset($_POST["Hall_2"]) and isset($_POST["Lesson_Num_2"]))
                {
                    if(isNewConsultation()==true)
                    {
                        if(isLecBusyCons($link, $id_lecturer, $id_day_2, $id_lesson_num_2)==false)
                        {
                            $upd_sql = pg_query($link, "UPDATE raspisanie.consultations SET 
                            day_2=" . $id_day_2 . ", hall_2=" . $id_hall_2 . ", lesson_number_2=" . $id_lesson_num_2 . "
                              WHERE id = " . $_GET['red_id']);
                            if ($upd_sql) {
                                echo "Консультация 2: изменения успешно добавлены!<br>";
                            } else {
                                echo "Изменения не добавлены. Ошибка." . $link->error;
                            }
                        }
                        else { echo "<p>Консультация 2 не изменена! У преподавателя пара в этот день и время.</p>";}
                    }
                    else { echo "<p>Запись не добавлена! Консультация №1 и Консультация №2 не должны совпадать.</p>"; }

                }
                else {
                    //Вставляем данные, подставляя их в запрос
                    if(isLecBusyCons($link, $id_lecturer, $id_day_1, $id_lesson_num_1)==false)
                    {
                        $upd_sql = pg_query($link, "UPDATE raspisanie.consultations SET 
                    lecturer=" . $id_lecturer . ", day_1=". $id_day_1 .", hall_1=". $id_hall_1 .", lesson_number_1=". $id_lesson_num_1 ."
                     WHERE id = " . $_GET['red_id']);
                        if ($upd_sql) {
                            echo "Консультация 1: изменения успешно добавлены!<br>";
                        } else {
                            echo "Изменения не добавлены. Ошибка." . $link->error;
                        }
                        echo "<p>Консультация 2: изменения не добавлены!</p>";
                    }
                    else { echo "<p>Консультация 1 не изменена! У преподавателя пара в этот день и время.</p>";}
                }
            }
        }
        else { echo "<p>Изменения не добавлены!</p><p>Такой преподаватель уже есть в таблице, нажмите на 'Редактировать' в соответствующей строке, чтобы изменить расписание его консультаций.</p>"; }
    }
    else{
        echo "<p>Заполнение поля консультация №1 обязательно.</p>";
    }
}
pg_close($link);


if (isset($_GET['red_id'])) { //Если передана переменная на редактирование
    //Достаем запсись из БД
    $link = pg_connect($connection);
    $res = pg_query($link,"SELECT * FROM raspisanie.consultations WHERE id=".$_GET['red_id']); //запрос к БД
    $consultations_row = pg_fetch_array($res); //получение самой записи

    $lec=getValueByForeignKey($link, $consultations_row, 'lecturer', 'raspisanie.lecturers', 'lecturer');
    $day_1=getValueByForeignKey($link, $consultations_row, 'day_1', 'raspisanie.days_of_week', 'day');
    $hall_1=getValueByForeignKey($link, $consultations_row, 'hall_1', 'raspisanie.halls', 'hall');
    $lesson_number_1=getValueByForeignKey($link, $consultations_row, 'lesson_number_1', 'raspisanie.time', 'number_of_lesson');

    $day_2=getValueByForeignKey($link, $consultations_row, 'day_2', 'raspisanie.days_of_week', 'day');
    $hall_2=getValueByForeignKey($link, $consultations_row, 'hall_2', 'raspisanie.halls', 'hall');
    $lesson_number_2=getValueByForeignKey($link, $consultations_row, 'lesson_number_2', 'raspisanie.time', 'number_of_lesson');

    ?>
    <form action="" method="post">
        <label>
            Преподаватель:
            <select name="Lecturer">
                <?php
                echo '<option selected="selected">' . $lec . '</option>';
                $link = pg_connect($connection);
                $sql = "SELECT lecturer FROM raspisanie.lecturers";
                $result = pg_query($link, $sql);
                while ($row = pg_fetch_array($result)) {
                    if ($row['lecturer'] != $lec) {echo '<option>' . $row['lecturer'] . '</option>';}
                } pg_close($link);
                ?>
            </select>
            <br><br>
        </label>
        <label>
            Консультация 1:

            День:
            <select name="Day_1">
                <?php
                echo '<option selected="selected">' . $day_1 . '</option>';
                $link = pg_connect($connection);
                $sql = "SELECT day FROM raspisanie.days_of_week";
                $result = pg_query($link, $sql);
                while ($row = pg_fetch_array($result)) {
                    if ($row['day'] != $day_1) {echo '<option>' . $row['day'] . '</option>';}
                } pg_close($link);
                ?>
            </select>

            Аудитория:
            <select name="Hall_1">
                <?php
                echo '<option selected="selected">' . $hall_1 . '</option>';
                $link = pg_connect($connection);
                $sql = "SELECT hall FROM raspisanie.halls";
                $result = pg_query($link, $sql);
                while ($row = pg_fetch_array($result)) {
                    if ($row['hall'] != $hall_1) {echo '<option>'.$row['hall'].'</option>';}
                } pg_close($link);
                ?>
            </select>

            № пары:
            <select name="Lesson_Num_1">
                <?php
                echo '<option selected="selected">' . $lesson_number_1 . '</option>';
                $link = pg_connect($connection);
                $sql = "SELECT number_of_lesson FROM raspisanie.time";
                $result = pg_query($link, $sql);
                while ($row = pg_fetch_array($result)) {
                    if ($row['number_of_lesson'] != $lesson_number_1) {echo '<option>'.$row['number_of_lesson'].'</option>';}
                } pg_close($link);
                ?>
            </select>
        </label>

        <label>
        <br><br>
            <?php
            if(!empty($consultations_row['day_2']))
            {
                ?>
                Консультация 2:
                День:
                <select name="Day_2">
                <?php
                echo '<option selected="selected">' . $day_2 . '</option>';
                echo '<option></option>';
                $link = pg_connect($connection);
                $sql = "SELECT day FROM raspisanie.days_of_week";
                $result = pg_query($link, $sql);
                while ($row = pg_fetch_array($result)) {
                    if ($row['day'] != $day_2) {echo '<option>' . $row['day'] . '</option>';}
                } pg_close($link);
                ?>
                </select>

                Аудитория:
                <select name="Hall_2">
                    <?php
                    echo '<option selected="selected">' . $hall_2 . '</option>';
                    echo '<option></option>';
                    $link = pg_connect($connection);
                    $sql = "SELECT hall FROM raspisanie.halls";
                    $result = pg_query($link, $sql);
                    while ($row = pg_fetch_array($result)) {
                        if ($row['hall'] != $hall_2) {echo '<option>'.$row['hall'].'</option>';}
                    } pg_close($link);
                    ?>
                </select>

                № пары:
                <select name="Lesson_Num_2">
                    <?php
                    echo '<option selected="selected">' . $lesson_number_2 . '</option>';
                    echo '<option></option>';
                    $link = pg_connect($connection);
                    $sql = "SELECT number_of_lesson FROM raspisanie.time";
                    $result = pg_query($link, $sql);
                    while ($row = pg_fetch_array($result)) {
                        if ($row['number_of_lesson'] != $lesson_number_2) {echo '<option>'.$row['number_of_lesson'].'</option>';}
                    } pg_close($link);
                    ?>
                </select>
                <?php
            }
            else {
                ?>
                Консультация 2:
                День:
                <select name="Day_2">
                    <?php
                    echo '<option selected="selected"></option>';
                    $link = pg_connect($connection);
                    $sql = "SELECT day FROM raspisanie.days_of_week";
                    $result = pg_query($link, $sql);
                    while ($row = pg_fetch_array($result)) {
                        echo '<option>' . $row['day'] . '</option>';
                    } pg_close($link);
                    ?>
                </select>

                Аудитория:
                <select name="Hall_2">
                    <?php
                    echo '<option selected="selected"></option>';
                    $link = pg_connect($connection);
                    $sql = "SELECT hall FROM raspisanie.halls";
                    $result = pg_query($link, $sql);
                    while ($row = pg_fetch_array($result)) {
                        echo '<option>'.$row['hall'].'</option>';
                    } pg_close($link);
                    ?>
                </select>

                № пары:
                <select name="Lesson_Num_2">
                    <?php
                    echo '<option selected="selected"></option>';
                    $link = pg_connect($connection);
                    $sql = "SELECT number_of_lesson FROM raspisanie.time";
                    $result = pg_query($link, $sql);
                    while ($row = pg_fetch_array($result)) {
                        echo '<option>'.$row['number_of_lesson'].'</option>';
                    } pg_close($link);
                    ?>
                </select>
                <?php
            }
            ?>
        </label>

        <label><br><br><input type="submit" value="OK"></label>

    </form>
    <?php
}
?>

<?
endif;
?>
