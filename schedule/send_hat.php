<?php

function sendHat($parity, $day){
    if($parity=='пар')
    {
        echo '<div style="background: linear-gradient(to right, #D98E46, #A1A6C5)">';
        sendScheduleHat($day);
        echo '</div>';
    }
    elseif($parity=='все')
    {
        sendScheduleHat($day);
        echo '</div>';
    }
    elseif($parity=='н/п'){
        echo '<div style="background: linear-gradient(to right, #CC9286, #B586CF)">';
        sendScheduleHat($day);
        echo '</div>';
    }
}
function sendScheduleHat($day){
    echo '<div class="tbl-header">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <thead>
                        <tr>
                            <th> '.$day.' </th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="tbl-header">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <col span="1" class="col_time">
                        <col span="1" class="col_subject">
                        <col span="1" class="col_group">
                        <col span="5" class="col_nnn">
                        <thead>
                        <tr>
                            <th>№ пары</th><th>предмет</th><th>группа</th><th>ауд.</th><th>тип</th><th>нед.</th><th></th>
                        </tr>
                        </thead>
                    </table>
                </div>';
}