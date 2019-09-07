<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?>
<!doctype html>
<html lang="ru">
<head>
    <title>Добавить время</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Добавить время</h1>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
//Если переменная передана
if (isset($_POST["Time"]) and isset($_POST["Number"])) {
    $str = (string)$_POST['Time'];
    $str2 = str_replace("'", "`", $str);
    $time = str_replace('"', "`", $str2);

    $str_number = (string)$_POST['Number'];
    $str2_number = str_replace("'", "`", $str_number);
    $number = str_replace('"', "`", $str2_number);

    if (!empty($_POST["Time"]) and !empty($_POST["Number"])) {
        if(isNewField($my_link=$link, $column_name='time', $table='raspisanie.time', $user_data=$time)==true and
            isNewField($my_link=$link, $column_name='number_of_lesson', $table='raspisanie.time', $user_data=$_POST['Number'])==true) {
            //Вставляем данные, подставляя их в запрос

            $sql = pg_query("INSERT INTO raspisanie.time (time, number_of_lesson) VALUES ('" . $time . "', '".$number."')");
            //Если вставка прошла успешно
            if ($sql) { echo "<p>Данные успешно добавлены в таблицу.</p>"; } else { echo "<p>Произошла ошибка.</p>";}
        } else { echo "<p>Такая запись уже есть!</p>"; }
    }
    else{ echo "<p>Поле не должно быть пустым!</p>"; }
}
?>

<table>
    <form action="" method="post">
        <tr>
            <td>Время:</td>
            <td><textarea name="Time" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <td>№ пары:</td>
            <td><textarea name="Number" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <td><input type="submit" value="OK"> </td>
        </tr>
    </form>
</table>
</body>

<? endif;
?>