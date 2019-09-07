<?php
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
    <title>Парность недели</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="templates/tables_style.css">

</head>
<body>

<div class="block">
    <div class="middle">
        <h1>Парность недели</h1>
    </div>
</div>

<div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr>
            <th>Тип недель</th>
        </tr>
        </thead>
    </table>
</div>
<div class="tbl-content">
    <table border="1">
        <col class="col1">
        <!--<col span="2" class="coln">-->
        <?php
        $link = pg_connect($connection);
        $sql = "SELECT id, type FROM raspisanie.date_type";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result)) {
            echo '<td>'.$row['type'].'</td></tr>';
                //'<td><a href="?del_id='.$row['id'].'">Удалить</a></td>'.
                //'<td><a href="edit/edit_parity_type.php?red_id='.$row['id'].'" target="_blank">Редактировать</a></td></tr>';
        }
        pg_close($link);
        ?>
    </table>
</div>

<!--
<div class="block">
    <div class="middle">
        <p><a href="/add/add_parity_type.php" target="_blank">Добавить</a></p>
    </div>
</div>
-->

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="templates/tables_script.js"></script>
</body>
</html>
<?
endif;
?>