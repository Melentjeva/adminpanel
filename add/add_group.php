<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?>
<!doctype html>
<html lang="ru">
<head>
    <title>Добавить группу</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Группы</h1>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
//Если переменная New передана
if (isset($_POST["Group"])) {
    $str = (string)$_POST['Group'];
    $str2 = str_replace("'", "`", $str);
    $group = str_replace('"', "`", $str2);
    if (!empty($_POST["Group"])) {
        if(isNewField($my_link=$link, $column_name='group_number', $table='raspisanie.groups', $user_data=$group)==true) {
            //Вставляем данные, подставляя их в запрос
            $sql = pg_query("INSERT INTO raspisanie.groups (group_number) VALUES ('" . $group . "')");
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
            <td>Номер группы:</td>
            <td><textarea name="Group" cols="40" rows="5"></textarea></td>
            <td><input type="submit" value="OK"> </td>
        </tr>
    </form>
</table>
</body>



<? endif;
?>