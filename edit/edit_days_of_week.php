<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><head>
    <title>Редактировать день недели</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Редактировать день недели</h1>
    </div>
</div>
<div class="block2">
    <div class="middle2">
        <h2>* Заполните поле "винительный падеж" для правильного построения предложения, например: "Среда", "среду"</h2>
    </div>
</div>
</body>
<?php

include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
if (isset($_GET['red_id']) ) {
    $str = (string)$_POST['Day'];
    $str2 = str_replace("'", "`", $str);
    $day = str_replace('"', "`", $str2);

    $str_vin = (string)$_POST['Vin'];
    $str2_vin = str_replace("'", "`", $str_vin);
    $vin = str_replace('"', "`", $str2_vin);

    $id = $_GET['red_id'];

    if (!empty($_POST["Day"] and !empty($_POST["Vin"])))
    { //Проверяем, передана ли переменная на редактирования
        if(isNewField($my_link=$link, $column_name='day', $table='raspisanie.days_of_week', $user_data=$day)==true and
            isNewField($my_link=$link, $column_name='vinitelni_padej', $table='raspisanie.days_of_week', $user_data=$_POST['Vin'])==true) {
            if (isset($_POST["Day"]) and isset($_POST["Vin"])) {

                $upd_sql = pg_query($link, "UPDATE raspisanie.days_of_week SET day='" . $day . "', vinitelni_padej='". $vin ."' WHERE id = " . $_GET['red_id']);
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
    $res = pg_query($link,"SELECT id, day, vinitelni_padej FROM raspisanie.days_of_week WHERE id=".$_GET['red_id']); //запрос к БД
    $row = pg_fetch_array($res); //получение самой записи
    ?>

    <table>
        <form action="" method="post">
            <tr>
                <td>День:</td>
                <td><textarea name="Day" cols="40" rows="5"><?php echo ($row['day']); ?></textarea></td>
            </tr>
            <tr>
                <td>Винительный падеж:</td>
                <td><textarea name="Vin" cols="40" rows="5"><?php echo ($row['vinitelni_padej']); ?></textarea></td>
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
