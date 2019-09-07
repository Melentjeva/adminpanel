<?php
include 'templates/menu.php';
include 'db_connection.php';

session_start();

if (!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
?>

<html lang=''>
<head>
    <title>Расписание</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="templates/tables_style.css">

</head>
<body link="white" vlink="white" alink="white">
<div class="block">
    <div class="middle">
        <h1>Расписание</h1>
    </div>
</div>
<div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
        <thead>
        <tr>
            <th id="column_scheedule_main">Преподаватели</th>
        </tr>
        </thead>
    </table>
</div>

<div class="tbl-content">
    <table border="1">
        <?php
        $link = pg_connect($connection);

        $sql = "SELECT id, lecturer FROM raspisanie.lecturers";
        $result = pg_query($link, $sql);
        while ($row = pg_fetch_array($result))
        {
            echo '<td id="column_scheedule_main"><a href="schedule/lecturer.php?lec='.$row['lecturer'].'&lec_id='.$row['id'].'" target="_blank">'.$row['lecturer'].'</a></td></tr>';
        }
        pg_close($link);
        ?>
    </table>
</div>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="templates/tables_script.js"></script>
</body>
</html>
<?
endif;
?>