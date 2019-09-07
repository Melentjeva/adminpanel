<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><head>
    <title>Редактировать время</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Редактировать время</h1>
    </div>
</div>
</body>
<?php

include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
if (isset($_GET['red_id']) ) {
    $str = (string)$_POST['Time'];
    $str2 = str_replace("'", "`", $str);
    $time = str_replace('"', "`", $str2);

    $str_number = (string)$_POST['Number'];
    $str2_number = str_replace("'", "`", $str_number);
    $number = str_replace('"', "`", $str2_number);

    $id = $_GET['red_id'];


    if (!empty($_POST["Time"] and !empty($_POST["Number"])))
    { //Проверяем, передана ли переменная на редактирования
        if(isNewField($my_link=$link, $column_name='time', $table='raspisanie.time', $user_data=$time)==true and
            isNewField($my_link=$link, $column_name='number_of_lesson', $table='raspisanie.time', $user_data=$_POST['Number'])==true) {
            if (isset($_POST["Time"]) and isset($_POST["Number"])) {
                $upd_sql = pg_query($link, "UPDATE raspisanie.time SET time='" . $time . "', number_of_lesson='". $number ."' WHERE id = " . $_GET['red_id']);
                if ($upd_sql) {
                    echo "Изменения успешно добавлены!";
                } else {
                    echo "Изменения не добавлены. Ошибка: " . $link->error;
                }
            }
        } else { echo "<p>Такая запись уже есть!</p>"; }
    }
    else{
        echo "<p>Поле не должно быть пустым!</p>";
    }
}

if (isset($_GET['red_id'])) { //Если передана переменная на редактирование
    //Достаем запсись из БД
    $res = pg_query($link,"SELECT id, time, number_of_lesson FROM raspisanie.time WHERE id=".$_GET['red_id']); //запрос к БД
    $row = pg_fetch_array($res); //получение самой записи
    ?>

    <table>
        <form action="" method="post">
            <tr>
                <td>Время:</td>
                <td><textarea name="Time" cols="40" rows="5"><?php echo ($row['time']); ?></textarea></td>
            </tr>
            <tr>
                <td>№ пары:</td>
                <td><textarea name="Number" cols="40" rows="5"><?php echo ($row['number_of_lesson']); ?></textarea></td>
            </tr>
            <tr>
                <td><input type="submit" value="OK"> </td>
            </tr>
        </form>
    </table>
    <?php
}
pg_close($link);
?>

<?
endif;
?>
