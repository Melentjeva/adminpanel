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
    <title>Преподаватели</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="templates/tables_style.css">

</head>
<body>

<div class="block">
    <div class="middle">
        <h1>Преподаватели</h1>
    </div>
</div>

<div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr>
            <th>ФИО</th>
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
        $sql = "SELECT id, lecturer FROM raspisanie.lecturers";
        $result = pg_query($link, $sql);
        $page='ПРЕПОДАВАТЕЛИ';
        $table='raspisanie.lecturers';
        $row_name='lecturer';
        while ($row = pg_fetch_array($result)) {
            echo '<td>'.$row['lecturer'].'</td>'.
                '<td><a href="delete/del.php?page='.$page.'&del_id='.$row['id'].'&table='.$table.'&row='.$row_name.'" target="_blank">Удалить</a></td>'.
                '<td><a href="edit/edit_lecturer.php?red_id='.$row['id'].'" target="_blank">Редактировать</a></td></tr>';
        }
        pg_close($link);
        ?>
    </table>
</div>

<div class="block">
    <div class="middle">
        <p><a href="/add/add_lecturer.php" target="_blank">Добавить</a></p>
    </div>
</div>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="templates/tables_script.js"></script>
</body>
</html>
<?
endif;
?>