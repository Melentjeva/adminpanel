<?php

session_start();

if(!isset($_SESSION["session_username"])):
    header("location:../login/login.php");
else:
?><?php
include '../db_connection.php';
include 'send.php';
include 'hat.php';
include_once '../function.php';

$link = pg_connect($connection);

#Все недели
echo '<div class="block"><div class="middle"><h1>Все недели</h1></div></div>';
sendDay($link, $parity='все');
#Парные недели
echo '<div class="block" style="background: linear-gradient(to right, #D98E46, #A1A6C5)"><div class="middle"><h1>парные недели</h1></div></div>';
sendDay($link, $parity='пар');
#Непарные недели
echo '<div class="block" style="background: linear-gradient(to right, #CC9286, #B586CF)"><div class="middle"><h1>непарные недели</h1></div></div>';
sendDay($link, $parity='н/п');

pg_close($link);
?>
<?
endif;
?>
