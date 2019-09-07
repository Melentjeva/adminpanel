<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?>
    <!doctype html>
<html lang="ru">
<head>
    <title>Добавить новость</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">О КАФЕДРЕ</h1>
    </div>
</div>
<?php
include '../db_connection.php';
include 'func.php';

//Если переменная New передана
$link = pg_connect($connection);
if (isset($_POST["New"])) {
    $str=(string)$_POST['New'];
    $str2 =  str_replace("'", "`", $str);
    $news = str_replace('"', "`", $str2);

    if (!empty($_POST["New"])) {
        if(isNewField($my_link=$link, $column_name='text', $table='kafedra_data.news', $user_data=$news)==true)
        {
            //Вставляем данные, подставляя их в запрос
            $sql = pg_query("INSERT INTO kafedra_data.news (text) VALUES ('" . $news . "')");
            //Если вставка прошла успешно
            if ($sql) {
                echo "<p>Данные успешно добавлены в таблицу.</p>";
            } else {
                echo "<p>Произошла ошибка.</p>";
            }
        }
        else { echo "<p>Такая запись уже есть!</p>"; }
    }
    else{
        echo "<p>Поле не должно быть пустым!</p>";
    }
}
?>

<table>
    <form action="" method="post">
        <tr>
            <td>Новость:</td>
            <td><textarea name="New" cols="40" rows="5"></textarea></td>
            <td><input type="submit" value="OK"> </td>
        </tr>
    </form>
</table>
</body>

<?php
pg_close($link);
endif;

/**
 * Created by PhpStorm.
 * User: Maria_Melentyeva
 * Date: 11.06.2018
 * Time: 13:05
 */
?>