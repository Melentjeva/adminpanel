<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?>
<!doctype html>
<html lang="ru">
<head>
    <title>Добавить день</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">ДНИ НЕДЕЛИ</h1>
    </div>
</div>
<div class="block2">
    <div class="middle2">
        <h2>* Заполните поле "винительный падеж" для правильного построения предложения, например: "Среда", "среду"</h2>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
//Если переменная передана
if (isset($_POST["Day"]) and isset($_POST["Vin"])) {
    $str = (string)$_POST['Day'];
    $str2 = str_replace("'", "`", $str);
    $day = str_replace('"', "`", $str2);

    $str_vin = (string)$_POST['Vin'];
    $str2_vin = str_replace("'", "`", $str_vin);
    $vin = str_replace('"', "`", $str2_vin);
    if (!empty($_POST["Day"]) and !empty($_POST["Vin"])) {
        if(isNewField($my_link=$link, $column_name='day', $table='raspisanie.days_of_week', $user_data=$day)==true and
            isNewField($my_link=$link, $column_name='vinitelni_padej', $table='raspisanie.days_of_week', $user_data=$_POST['Vin'])==true) {
            //Вставляем данные, подставляя их в запрос

            $sql = pg_query("INSERT INTO raspisanie.days_of_week (day, vinitelni_padej) VALUES ('" . $day . "', '".$vin."')");
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
            <td>День:</td>
            <td><textarea name="Day" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <td>Винительный падеж:</td>
            <td><textarea name="Vin" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <td><input type="submit" value="OK"> </td>
        </tr>
    </form>
</table>
</body>


<? endif;
?>