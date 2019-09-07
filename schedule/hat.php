<?php
include '../templates/menu.php';
include '../db_connection.php';
?>
<!doctype html>
<html lang=''>
<head>
    <title><?php
        if (isset($_GET['lec'])) {
            if (!empty($_GET['lec'])){
                echo ($_GET['lec']);
            }} ?>
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="../templates/tables_style.css">

</head>
<body>
<div class="block">
    <div class="middle">
        <h1>
            <?php
            if (isset($_GET['lec'])) {
                if (!empty($_GET['lec'])){
                    echo ($_GET['lec']);
                }} ?>
        </h1>
    </div>
</div>
<div class="block2">
    <div class="middle2">
        <h2>расписание преподавателя на неделю</h2>
    </div>
</div>
</body>
</html>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="../templates/tables_script.js"></script>
