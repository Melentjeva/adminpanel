<?php
include '../templates/menu.php';
include '../db_connection.php';
include '../function.php';
include 'add_consultation.php';

session_start();

if (!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
    $link = pg_connect($connection);
    ?>
<!doctype html>
<html lang=''>
<head>
    <title>Консультации</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="../templates/tables_style.css">
    <link rel="stylesheet" href="style.css">

    <script src="modal_ajax_script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <!--<meta http-equiv="refresh" content="10">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    -->

</head>
<body>

<div id="filter" onclick="
show_modal('none', 'deleteForm');
show_modal('none', 'addForm');
window.location.reload();"></div>

<div class="modal" id="deleteForm">
    <form method="post">
        <h1 class="headline" id="delH1">Удалить консультацию?</h1>
        <div id="deletionDIV"></div>
    </form>
</div>

<div class="modal" id="addForm">
    <form method="post">
        <h1 class="headline" id="addH1">Добавить новую консультацию<br></h1>
        <div id="addSelectDIV">
            <?php
                sendConsultationsDatalists($link);
            ?>
        </div>
        <div id="addDIV"></div>
    </form>
</div>

<div class="block">
    <div class="middle">
        <h1>Консультации</h1>
    </div>
</div>

<div class="tbl-header">
    <table cellpadding="0" cellspacing="0" border="0">
        <col span="2" class="col2">
        <col span="4" class="colnn">
        <thead>
        <tr><th>Преподаватель</th><th>День</th><th>Аудитория</th><th>№ пары</th><th></th><th></th><th></th></tr>
        </thead>
    </table>
</div>

<div class="tbl-content">
    <table border="1">
        <col span="2" class="col2">
        <col span="4" class="colnn">
        <?php
        $page='КОНСУЛЬТАЦИИ';
        $table='raspisanie.consultations';
        $row1='lecturer';
        $row2='day_1';
        $row3='hall_1';
        $row4='lesson_number_1';

        $result=pg_query($link, "SELECT * FROM raspisanie.consultations");
        while ($consultation_row = pg_fetch_array($result))
        {
            $lecturer=getValueByForeignKey($link, $consultation_row, 'lecturer', 'raspisanie.lecturers', 'lecturer');
            $day_1=getValueByForeignKey($link, $consultation_row, 'day_1', 'raspisanie.days_of_week', 'day');
            $hall_1=getValueByForeignKey($link, $consultation_row, 'hall_1', 'raspisanie.halls', 'hall');
            $lesson_number_1=getValueByForeignKey($link, $consultation_row, 'lesson_number_1', 'raspisanie.time', 'number_of_lesson');

            if(!empty($consultation_row['day_2']) and !empty($consultation_row['hall_2']) and !empty($consultation_row['lesson_number_2']))
            {
                $row5='day_2';
                $row6='hall_2';
                $row7='lesson_number_2';
                $id=$consultation_row['id'];
                echo '<tr><td>' . $lecturer . '</td>' .
                    '<td>' . $day_1 . '</td>' .
                    '<td>' . $hall_1 . '</td>' .
                    '<td>' . $lesson_number_1 . '</td>' .
                    '<td>                            
                        <form method="post"><button type="button" name="delete" id="del" value="'.$id.'">Удалить</button></form>
                    </td>'.
                    '<td><a href="edit/edit_consultation.php?red_id='.$consultation_row['id'].'" target="_blank">Редактировать</a></td></tr>';

                $day_2=getValueByForeignKey($link, $consultation_row, 'day_2', 'raspisanie.days_of_week', 'day');
                $hall_2=getValueByForeignKey($link, $consultation_row, 'hall_2', 'raspisanie.halls', 'hall');
                $lesson_number_2=getValueByForeignKey($link, $consultation_row, 'lesson_number_2', 'raspisanie.time', 'number_of_lesson');
                echo '<tr><td> </td>'.
                    '<td>'.$day_2.'</td>'.
                    '<td>'.$hall_2.'</td>'.
                    '<td>'.$lesson_number_2.'</td>'.
                    '<td> </td>'.
                    '<td> </td></tr>';
            }
            else {
                $id=$consultation_row['id'];
                echo '<tr><td>' . $lecturer . '</td>' .
                        '<td>' . $day_1 . '</td>' .
                        '<td>' . $hall_1 . '</td>' .
                        '<td>' . $lesson_number_1 . '</td>'.
                        '<td>
                            <form method="post"><button type="button" name="delete" id="del" value="'.$id.'">Удалить</button></form>
                        </td>' .
                        '<td><a href="../edit/edit_consultation.php?red_id=' . $consultation_row['id'] . '" target="_blank">Редактировать</a></td></tr>';
                }
        }
        ?>
    </table>
</div>

<div class="block">
    <div class="middle">
        <p>
            <a href="../add/add_consultation.php" target="_blank">Добавить</a>
            <!--<form method="post"><button type="button" name="add" id="add" value="add">Добавить</button></form>-->
        </p>
    </div>
</div>

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
</body>
</html>
<?
    pg_close($link);
endif;
?>