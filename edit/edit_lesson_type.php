<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><head>
    <title>Редактировать тип занятий</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Редактировать тип пзанятий</h1>
    </div>
</div>
</body>
<?php

include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
if (isset($_GET['red_id'])) {
    $str = (string)$_POST['Type'];
    $str2 = str_replace("'", "`", $str);
    $type = str_replace('"', "`", $str2);
    $id = $_GET['red_id'];

    if (!empty($_POST["Type"]))
    { //Проверяем, передана ли переменная на редактирования
        if(isNewField($my_link=$link, $column_name='type', $table='raspisanie.lesson_type', $user_data=$type)==true) {
            if (isset($_POST['Type'])) { //Если новое имя предано, то обновляем и имя и цену
                $upd_sql = pg_query($link, "UPDATE raspisanie.lesson_type SET type='" . $type . "' WHERE id = " . $_GET['red_id']);
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
    $res = pg_query($link,"SELECT id, type FROM raspisanie.lesson_type WHERE id=".$_GET['red_id']); //запрос к БД
    $row = pg_fetch_array($res); //получение самой записи
    ?>
    <table>
        <form action="" method="post">
            <tr>
                <td>Тип пары:</td>
                <td><textarea name="Type" cols="40" rows="5"><?php echo ($row['type']); ?></textarea></td>
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
