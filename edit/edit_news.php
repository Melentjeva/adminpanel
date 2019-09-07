<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?><head>
    <title>Редактировать новость</title>
</head>
<body>
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
            <h1 class="text-center">НОВОСТИ</h1>
        </div>
    </div>
</body>
<?php
include '../db_connection.php';
include 'func.php';

$link = pg_connect($connection);
if (isset($_GET['red_id'])) {
    $str = (string)$_POST['New'];
    $str2 = str_replace("'", "`", $str);
    $new = str_replace('"', "`", $str2);
    $id = $_GET['red_id'];

    if (!empty($_POST["New"])) { //Проверяем, передана ли переменная на редактирования
        if(isNewField($my_link=$link, $column_name='text', $table='kafedra_data.news', $user_data=$new)==true) {
            if (isset($_POST['New']))
            { //Вставляем данные, подставляя их в запрос
                $upd_sql = pg_query($link, "UPDATE kafedra_data.news SET text='" . $new . "' WHERE id = " . $_GET['red_id']);
                if ($upd_sql) {
                    echo "Изменения успешно добавлены!";
                } else {
                    echo "Изменения не добавлены. Ошибка: " . $link->error;
                }
            }
        }
    }
    else{
        echo "<p>Поле не должно быть пустым!</p>";
    }
}

if (isset($_GET['red_id'])) { //Если передана переменная на редактирование
    //Достаем запсись из БД
    $res = pg_query($link,"SELECT id, text FROM kafedra_data.news WHERE id=".$_GET['red_id']); //запрос к БД
    $row = pg_fetch_array($res); //получение самой записи
    ?>
    <table>
        <form action="" method="post">
            <tr>
                <td>Новость:</td>
                <td><textarea name="New" cols="40" rows="5"><?php echo ($row['text']); ?></textarea></td>
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
