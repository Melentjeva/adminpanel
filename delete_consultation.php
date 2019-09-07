<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    include 'db_connection.php';
    include 'function.php';
    $link = pg_connect($connection);

    $sql_consultations = "SELECT * FROM raspisanie.consultations";
    $result = pg_query($link, $sql_consultations);
    while($consultation_row = pg_fetch_array($result))
    {
            if($_POST['delete']==(string)$consultation_row['id']) {
                echo $consultation_row['id'];
            }
    }


    if($_POST['delete']=='Удалить') {
        $sql = pg_query($link, "DELETE FROM raspisanie.consultations WHERE id = " . $_POST['delete']); //удаляем строку из таблицы
        if ($sql) {
            echo "Запись успешно удалена!";
        } else {
            echo "Запись не удалена. Ошибка.";
        }
    }


        if (isset($_GET['del_id']) and isset($_GET['table'])) { //проверяем, есть ли переменная
            if (!empty($_GET["del_id"]) and !empty($_GET["table"])) {
                $table = $_GET['table'];
                $sql = pg_query($link, "DELETE FROM " . $table . " WHERE id = " . $_GET['del_id']); //удаляем строку из таблицы
                if ($sql) {
                    echo "Запись успешно удалена!";
                } else {
                    echo "Запись не добавлена. Ошибка." . $link->error;
                }
            }

    }

    if (isset($_GET['del_id']) and isset($_GET['table'])and isset($_GET['row1']) and isset($_GET['row2'])) { //проверяем, есть ли переменная
        if (!empty($_GET["del_id"]) and !empty($_GET["table"]) and !empty($_GET["row1"]) and !empty($_GET["row2"])) {
            $table = $_GET['table'];
            $name1= $_GET['row1'];
            $name2= $_GET['row2'];
            $name3= $_GET['row3'];
            $name4= $_GET['row4'];

            $sql = "SELECT * FROM ".$table;
            $result = pg_query($link, $sql);
            while($consultation_row = pg_fetch_array($result)) {
                if ($consultation_row['id']==$_GET['del_id']) {

                    $lecturer=getValueByForeignKey($link, $consultation_row, 'lecturer', 'raspisanie.lecturers', 'lecturer');
                    $day_1=getValueByForeignKey($link, $consultation_row, 'day_1', 'raspisanie.days_of_week', 'day');
                    $hall_1=getValueByForeignKey($link, $consultation_row, 'hall_1', 'raspisanie.halls', 'hall');
                    $lesson_number_1=getValueByForeignKey($link, $consultation_row, 'lesson_number_1', 'raspisanie.time', 'number_of_lesson');

                    ?>
                    <table>
                        <form action method="post">
                            <tr>
                                <td>Консультация 1: </td>
                            </tr>
                            <tr>
                                <td>Преподаватель: <?php echo($lecturer); ?>
                                    День: <?php echo($day_1); ?>
                                    Ауд.: <?php echo($hall_1); ?>
                                    № пары: <?php echo($lesson_number_1); ?>
                                </td>
                            </tr>
                            <?php
                            if (isset($_GET['row5']) and !empty($_GET["row5"])){
                                $day_2=getValueByForeignKey($link, $consultation_row, 'day_2', 'raspisanie.days_of_week', 'day');
                                $hall_2=getValueByForeignKey($link, $consultation_row, 'hall_2', 'raspisanie.halls', 'hall');
                                $lesson_number_2=getValueByForeignKey($link, $consultation_row, 'lesson_number_2', 'raspisanie.time', 'number_of_lesson');

                                echo('<tr><td>Консультация 2: </td></tr>');
                                echo ('<tr><td>День 2: '.$day_2.' Ауд.: '.$hall_2.' № пары: '.$lesson_number_2.'</td></tr>');
                            }

                            ?>
                            <tr>
                                <td>Уверены, что хотите удалить запись?</td>
                            </tr>
                            <tr>
                                <td><input type="submit" name='action_delete' value="Удалить" ></td>
                            </tr>
                        </form>
                    </table>
                    <?php
                }
            }
            pg_close($link);
        }
    }
    ?>

<?php
endif;
?>
