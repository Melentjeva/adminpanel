<?php
function sendDatalist($name, $id, $id_datalist, $name_input, $link, $table, $column)
{
    echo $name;
    echo '<input id='.$id.' list='.$id_datalist.' name='.$name_input.' autocomplete="on">';
    echo '<datalist id='.$id_datalist.'>';
    $result = pg_query($link, "SELECT ".$column." FROM ".$table."");
    while ($row = pg_fetch_array($result))
    {
        echo '<option>' . $row[$column] . '</option>';
    }
    echo '</datalist>';
}

function sendConsultationsDatalists($link) {
    echo '<div id="comment">* выделенные цветом поля обязательны к заполнению</div>';
    echo '<div class="headline" id="lecturerH1">';
    sendDatalist('Преподаватель ', 'inputLecturer','choose_lecturer', 'Lecturer', $link, 'raspisanie.lecturers', 'lecturer');
    echo '</div><br>';

    echo '<div id="consultation_number">Консультация 1</div><br>';
    echo '<div id="consultation_text">';
    sendDatalist('День   ', 'inputConsultation1','choose_day_1', 'Day_1', $link, 'raspisanie.days_of_week', 'day');
    sendDatalist(' Аудитория   ', 'inputConsultation1','choose_hall_1', 'Hall_1', $link, 'raspisanie.halls', 'hall');
    sendDatalist(' № пары   ', 'inputConsultation1','choose_lesson_num_1', 'Lesson_Num_1', $link, 'raspisanie.time', 'number_of_lesson');
    echo '</div><br>';

    echo '<div id="consultation_number" style="color: #777777">Консультация 2</div><br>';
    echo '<div id="consultation_text">';
    sendDatalist('День   ', 'inputConsultation2','choose_day_1', 'Day_1', $link, 'raspisanie.days_of_week', 'day');
    sendDatalist(' Аудитория   ', 'inputConsultation2','choose_hall_1', 'Hall_1', $link, 'raspisanie.halls', 'hall');
    sendDatalist(' № пары   ', 'inputConsultation2','choose_lesson_num_1', 'Lesson_Num_1', $link, 'raspisanie.time', 'number_of_lesson');
    echo '</div><br>';
    echo '<div id="button_ok" style="padding: 13px;"><form method="post"><button type="button" name="add_OK" id="addOK" value="add">Добавить</button></form></div>';

}
