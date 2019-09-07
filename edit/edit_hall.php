<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><head>
    <title>Редактировать аудиторию</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">АУДИТОРИИ</h1>
    </div>
</div>
</body>
<?php

include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
if (isset($_GET['red_id'])) {
    $str = (string)$_POST['Hall'];
    $str2 = str_replace("'", "`", $str);
    $hall = str_replace('"', "`", $str2);
    $id = $_GET['red_id'];

    if (!empty($_POST["Hall"]))
    { //Проверяем, передана ли переменная на редактирования
        if(isNewField($my_link=$link, $column_name='hall', $table='raspisanie.halls', $user_data=$hall)==true) {
            if (isset($_POST['Hall'])) { //Если новое имя предано, то обновляем и имя и цену
                $upd_sql = pg_query($link, "UPDATE raspisanie.halls SET hall='" . $hall . "' WHERE id = " . $_GET['red_id']);
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
    $res = pg_query($link,"SELECT id, hall FROM raspisanie.halls WHERE id=".$_GET['red_id']); //запрос к БД
    $row = pg_fetch_array($res); //получение самой записи
    ?>
    <table>
        <form action="" method="post">
            <tr>
                <td>Аудитория:</td>
                <td><textarea name="Hall" cols="40" rows="5"><?php echo ($row['hall']); ?></textarea></td>
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
