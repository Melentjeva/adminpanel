<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    ?>
    <head>
    <title>Удаление записи</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <?php
        if (isset($_GET['page'])){
            echo "<h1 class='text-center'>".$_GET['page']."</h1>";
        }
        ?>
    </div>
</div>
</body>

<?php
include '../db_connection.php';
$link = pg_connect($connection);

if($_POST['action_delete']=='Удалить')
{
    if (isset($_GET['del_id']) and isset($_GET['table'])) { //проверяем, есть ли переменная
        if (!empty($_GET["del_id"]) and !empty($_GET["table"])) {
            $table = $_GET['table'];
            $sql = pg_query($link, "DELETE FROM " . $table . " WHERE id = " . $_GET['del_id']); //удаляем строку из таблицы
            if ($sql) {
                echo "Запись успешно удалена!";
            } else {
                echo "Запись не добавлена. Ошибка." . $link->error;
            }
        }
    }
}

if (isset($_GET['del_id']) and isset($_GET['table'])and isset($_GET['row1']) and isset($_GET['row2'])) { //проверяем, есть ли переменная
    if (!empty($_GET["del_id"]) and !empty($_GET["table"]) and !empty($_GET["row1"]) and !empty($_GET["row2"])) {
        $table = $_GET['table'];
        $name1= $_GET['row1'];
        $name2= $_GET['row2'];
        $sql = "SELECT * FROM ".$table;
        $result = pg_query($link, $sql);
        while($row = pg_fetch_array($result)) {
            if ($row['id']==$_GET['del_id']) {
                ?>
                <table>
                    <form action method="post">
                        <tr>
                            <td>Запись: </td>
                        </tr>
                        <tr>
                            <td>Адрес: <?php echo($row[$name1]); ?> Телефон: <?php echo($row[$name2]); ?></td>
                        </tr>
                        <tr>
                            <td>Уверены, что хотите удалить запись?</td>
                        </tr>
                        <tr>
                            <td><input type="submit" name='action_delete' value="Удалить"></td>
                        </tr>
                    </form>
                </table>
                <?php
            }
        }
        pg_close($link);
    }
}
?>
<?php
endif;
?>
