<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?>
<!doctype html>
<html lang="ru">
<head>
    <title>Добавить преподавателя</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Преподаватели</h1>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
//Если переменная New передана
if (isset($_POST["FIO"])) {
    $str = (string)$_POST['FIO'];
    $str2 = str_replace("'", "`", $str);
    $lecturer = str_replace('"', "`", $str2);
    if (!empty($_POST["FIO"])) {
        if(isNewField($my_link=$link, $column_name='lecturer', $table='raspisanie.lecturers', $user_data=$lecturer)==true) {
            //Вставляем данные, подставляя их в запрос
            $sql = pg_query("INSERT INTO raspisanie.lecturers (lecturer) VALUES ('" . $lecturer . "')");
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
            <td>ФИО</td>
            <td><textarea name="FIO" cols="40" rows="5"></textarea></td>
            <td><input type="submit" value="OK"> </td>
        </tr>
    </form>
</table>
</body>



<? endif;
?>