<?php
include 'templates/menu.php';
session_start();

if(!isset($_SESSION["session_username"])):
    header("location:login/login.php");
else:
    ?>

<!doctype html>
<html lang=''>
<head>
    <title>Админ-панель</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="templates/tables_style.css">
</head>
<?php
/**
 * Created by PhpStorm.
 * User: Maria_Melentyeva
 * Date: 11.06.2018
 * Time: 00:46
 */

?>
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script  src="templates/tables_script.js"></script>
<?
endif;
?>
