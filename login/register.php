<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title> Регистрация </title>
    <link href="style.css" media="screen" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'rel='stylesheet' type='text/css'>
</head>
<body>
<div class="container mregister">
    <div id="login">
        <h1>Регистрация</h1>
        <form action="register.php" id="registerform" method="post"name="registerform">
            <p><label for="user_pass">Имя пользователя<br>
                    <input class="input" id="username" name="username"size="20" type="text" value=""></label></p>
            <p><label for="user_pass">Пароль<br>
                    <input class="input" id="password" name="password"size="32"   type="password" value=""></label></p>
            <p><label for="user_pass">Секретный код<br>
                    <input class="input" id="secretcode" name="secretcode"size="32"   type="password" value=""></label></p>
            <p class="submit"><input class="button" id="register" name= "register" type="submit" value="Зарегистрироваться"></p>
            <p class="regtext">Уже зарегистрированы? <a href= "login.php">Введите имя пользователя</a>!</p>
        </form>
    </div>
</div>
</body>
</html>

<?php
include("../db_connection.php");
$link = pg_connect($connection);

if(isset($_POST["register"])){

    if(!empty($_POST['username']) && !empty($_POST['password'])) {
        $username=htmlspecialchars($_POST['username']);
        $password=htmlspecialchars($_POST['password']);
        $secretcode=htmlspecialchars($_POST['secretcode']);

        $row = pg_fetch_row(pg_query($link, "SELECT code FROM adminpanel.secretcode"));
        if (!empty($row)){ $code=$row[0]; } else { $code='';}

        $query=pg_query($link,"SELECT * FROM adminpanel.usertbl WHERE username='".$username."', password='".$password."'");
        if(empty($query))
        {
            if(empty($code) or $secretcode==$code)
            {
                $sql="INSERT INTO adminpanel.usertbl (username, password)
	        VALUES('$username', '$password')";
                $result=pg_query($link,$sql);
                if($result){
                    $message = "Аккаунт создан";
                } else {
                    $message = "Ошибка!";
                }
            }
            else {
                $message = "Неверный код"; }
        } else {
            $message = "Такое имя уже существует";
        }
    }
    else {
        $message = "Все поля должны быть заполнены!";
    }
}
?>

<?php if (!empty($message)) {echo "<p>".$message."</p>"; pg_close($connection);} ?>

