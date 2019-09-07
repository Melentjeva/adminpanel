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
    <title>Контакты</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="templates/tables_style.css">

</head>
<body>

<div class="block">
    <div class="middle">
        <h1>Контакты</h1>
    </div>
</div>

<div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
        <col span="2" class="col2">
        <col span="2" class="colnn">
        <thead>
        <tr>
            <th>Адрес</th>
            <th>Телефон</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
    </table>
</div>
<div class="tbl-content">
    <table border="1">
        <col span="2" class="col2">
        <col span="2" class="colnn">
        <?php
        $link = pg_connect($connection);
        $sql = "SELECT id, adress, phone FROM kafedra_data.contacts";
        $result = pg_query($link, $sql);
        $page='КОНТАКТЫ';
        $table='kafedra_data.contacts';
        $row1='adress';
        $row2='phone';
        while ($row = pg_fetch_array($result)) {
            echo '<td>'.$row['adress'].'</td>'.
                '<td>'.$row['phone'].'</td>'.
                '<td><a href="delete/del_contacts.php?page='.$page.'&del_id='.$row['id'].'&table='.$table.'&row1='.$row1.'&row2='.$row2.'" target="_blank">Удалить</a></td>'.
                '<td><a href="edit/edit_contacts.php?red_id='.$row['id'].'" target="_blank">Редактировать</a></td></tr>';
        }
        pg_close($link);
        ?>
    </table>
</div>

<div class="block">
    <div class="middle">
        <p><a href="/add/add_contacts.php" target="_blank">Добавить</a></p>
    </div>
</div>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="templates/tables_script.js"></script>
</body>
</html>
<?
endif;
?>