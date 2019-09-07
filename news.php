<?php
/**
 * Created by PhpStorm.
 * User: Maria_Melentyeva
 * Date: 11.06.2018
 * Time: 00:46
 */

include 'templates/menu.php';
include 'db_connection.php';

session_start();

if (!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
?>
<!doctype html>
<html lang=''>
<head>
    <title>Новости</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="templates/tables_style.css">

</head>
<body>

<div class="block">
    <div class="middle">
        <h1>Новости</h1>
    </div>
</div>

<div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr>
            <th>Новость</th>
        </tr>
        </thead>
    </table>
</div>
<div class="tbl-content">
<table border="1">
    <col class="col1">
    <col span="2" class="coln">
    <?php
    $link = pg_connect($connection);
    $sql = "SELECT id, text FROM kafedra_data.news";
    $result = pg_query($link, $sql);
    $page='НОВОСТИ';
    $table='kafedra_data.news';
    $row_name='text';
    while ($row = pg_fetch_array($result)) {
        echo '<td>'.$row['text'].'</td>'.
            '<td><a href="delete/del.php?page='.$page.'&del_id='.$row['id'].'&table='.$table.'&row='.$row_name.'" target="_blank">Удалить</a></td>'.
            '<td><a href="edit/edit_news.php?red_id='.$row['id'].'" target="_blank">Редактировать</a></td></tr>';
    }
    pg_close($link);
    ?>
</table>
</div>

<div class="block">
    <div class="middle">
        <p><a href="/add/add_news.php" target="_blank">Добавить</a></p>
    </div>
</div>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="templates/tables_script.js"></script>
</body>
</html>
<?
endif;
?>