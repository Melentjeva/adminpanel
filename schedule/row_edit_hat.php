<head>
    <title>Редактировать расписание</title>
</head>
<body>
<div class="container">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: bisque">
        <h1 class="text-center">
            Редактировать расписание преподавателя
            <?php
            if (isset($_GET['lec'])){
                echo(' '.$_GET['lec']);
            }
            ?>
        </h1>
        <h1 class="text-center">
            <?php
            if (isset($_GET['day']) and isset($_GET['time']) and isset($_GET['week'])){
                echo("на ".$_GET['day']." ".$_GET['time']." пара (".$_GET['week']." недели)");
            }
            ?>
        </h1>
    </div>
</div>