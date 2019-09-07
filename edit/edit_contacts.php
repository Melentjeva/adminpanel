<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
    ?>
    <head>
    <title>Редактировать контакты</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">Контакты</h1>
    </div>
</div>
</body>
<?php

include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
if (isset($_GET['red_id']) ) {
    $str = (string)$_POST['Adress'];
    $str2 = str_replace("'", "`", $str);
    $adress = str_replace('"', "`", $str2);

    $str_phone = (string)$_POST['Phone'];
    $str2_phone = str_replace("'", "`", $str_phone);
    $phone = str_replace('"', "`", $str2_phone);

    $id = $_GET['red_id'];

    if (!empty($_POST["Adress"] and !empty($_POST["Phone"])))
    { //Проверяем, передана ли переменная на редактирования
        if(isNewField($my_link=$link, $column_name='adress', $table='kafedra_data.contacts', $user_data=$adress)==true) {
            if (isset($_POST["Adress"]) and isset($_POST["Phone"])) {
                $upd_sql = pg_query($link, "UPDATE kafedra_data.contacts SET adress='" . $adress . "', phone='". $phone ."' WHERE id = " . $_GET['red_id']);
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
    $res = pg_query($link,"SELECT id, adress, phone FROM kafedra_data.contacts WHERE id=".$_GET['red_id']); //запрос к БД
    $row = pg_fetch_array($res); //получение самой записи
    ?>

    <table>
        <form action="" method="post">
            <tr>
                <td>Адрес:</td>
                <td><textarea name="Adress" cols="40" rows="5"><?php echo ($row['adress']); ?></textarea></td>
            </tr>
            <tr>
                <td>Телефон:</td>
                <td><textarea name="Phone" cols="40" rows="5"><?php echo ($row['phone']); ?></textarea></td>
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
