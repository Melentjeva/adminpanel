<?php

function getValueByForeignKey($link, $row, $column, $table, $table_column){
    $id=$row[$column];
    $result=pg_query($link, "SELECT ".$table_column." FROM ".$table." WHERE id=".$id);
    $column_row = pg_fetch_array($result);
    return $column_row[$table_column];
}

function getFKeyByValue($link, $value, $table, $table_column){
    $str2 = str_replace("'", "`", (string)$value);
    $new_value=replace_quotes($str2);
    $row = pg_fetch_row(pg_query($link,"SELECT id FROM ".$table." WHERE ".$table_column."='".$new_value."'"));
    $id=$row[0];
    return $id;
}

?>