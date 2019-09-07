<!doctype html>
<html lang=''>
<head>
    <title></title>
    <script src="modal_ajax_script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
</head>
<?php
include '../db_connection.php';
include '../function.php';

$link = pg_connect($connection);
//echo 'Айди ' . $_POST['value'] . '<br>';

if($_POST['delete']=='ok') {
    $sql = pg_query($link, "DELETE FROM raspisanie.consultations WHERE id = " . $_POST['value']); //удаляем строку из таблицы
    if ($sql) {
        echo "<div id='sql_result' style='color: #25c481'>Запись удалена!</div>";
        ?> <script> setTimeout("window.location.reload()", 5000);</script> <?
    } else {
        echo "<div id='sql_result' style='color: #BF3030'>Запись не удалена. Ошибка.</div>";
    }
}

if(isset($_POST['value']))
{
    $result = pg_query($link, "SELECT * FROM raspisanie.consultations WHERE id=".$_POST['value']);
    while($row = pg_fetch_array($result))
    {
        $lecturer=getValueByForeignKey($link, $row, 'lecturer', 'raspisanie.lecturers', 'lecturer');
        $day_1=getValueByForeignKey($link, $row, 'day_1', 'raspisanie.days_of_week', 'day');
        $hall_1=getValueByForeignKey($link, $row, 'hall_1', 'raspisanie.halls', 'hall');
        $lesson_number_1=getValueByForeignKey($link, $row, 'lesson_number_1', 'raspisanie.time', 'number_of_lesson');
        echo '<div id="lecturer">'.$lecturer.'</div>';
        if(!empty($row['day_2']))
        {
            $day_2=getValueByForeignKey($link, $row, 'day_2', 'raspisanie.days_of_week', 'day');
            $hall_2=getValueByForeignKey($link, $row, 'hall_2', 'raspisanie.halls', 'hall');
            $lesson_number_2=getValueByForeignKey($link, $row, 'lesson_number_2', 'raspisanie.time', 'number_of_lesson');
            echo '<div id="consultation_number"><br>Консультация 1<br><br></div>';
            echo '<div id="consultation_text">'.$day_1.', Ауд.'.$hall_1.', '.$lesson_number_1.' пара</div>';
            echo '<div id="consultation_number"><br>Консультация 2<br><br></div>';
            echo '<div id="consultation_text">'.$day_2.', Ауд.'.$hall_2.', '.$lesson_number_2.' пара</div>';
        }
        else{
            echo '<div id="consultation_number_one"><br>Консультация<br><br></div>';
            echo '<div id="consultation_text_one">'.$day_1.', Ауд.'.$hall_1.', '.$lesson_number_1.' пара</div>';
        }
        $id=$row['id'];
        echo '<div id="button_ok"><form method="post"><button type="button" name="delete_OK" id="del_OK" value="'.$id.'">ОК</button></form></div>';
    }
}
pg_close($link);

?>
