<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
?>
<!doctype html>
<html lang="ru">
<head>
    <title>Добавить контакты</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">КОНТАКТЫ</h1>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
//Если переменная передана
if (isset($_POST["Adress"]) and isset($_POST["Phone"])) {
    $str = (string)$_POST['Adress'];
    $str2 = str_replace("'", "`", $str);
    $adress = str_replace('"', "`", $str2);

    $str_phone = (string)$_POST['Phone'];
    $str2_phone = str_replace("'", "`", $str_phone);
    $phone = str_replace('"', "`", $str2_phone);

    if (!empty($_POST["Adress"]) and !empty($_POST["Phone"])) {
        if(isNewField($my_link=$link, $column_name='adress', $table='kafedra_data.contacts', $user_data=$adress)==true) {
            //Вставляем данные, подставляя их в запрос
            $sql = pg_query("INSERT INTO kafedra_data.contacts (adress, phone) VALUES ('" . $adress . "', '".$phone."')");
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
            <td>Адрес:</td>
            <td><textarea name="Adress" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <td>Телефон:</td>
            <td><textarea name="Phone" cols="40" rows="5"></textarea></td>
        </tr>
        <tr>
            <td><input type="submit" value="OK"> </td>
        </tr>
    </form>
</table>
</body>

<? endif;
?>