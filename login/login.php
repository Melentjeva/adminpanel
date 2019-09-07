<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title> Вход </title>
    <link href="style.css" media="screen" rel="stylesheet">
    <link href= 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="container mlogin">
    <div id="login">
        <h1>Вход</h1>
        <form action="" id="loginform" method="post"name="loginform">
            <p><label for="user_login">Логин<br>
                    <input class="input" id="username" name="username"size="20"
                           type="text" value=""></label></p>
            <p><label for="user_pass">Пароль<br>
                    <input class="input" id="password" name="password"size="20"
                           type="password" value=""></label></p>
            <p class="submit"><input class="button" name="login"type= "submit" value="Log In"></p>
            <p class="regtext">Еще не зарегистрированы?<a href= "register.php">Регистрация</a>!</p>
        </form>
    </div>
</div>
</body>
</html>

<?php
session_start();
?>

<?php include("../db_connection.php");
$link = pg_connect($connection);

if(isset($_SESSION["session_username"])){
    // вывод "Session is set"; // в целях проверки
    header("Location: ../index.php");
}

if(isset($_POST["login"])){

    if(!empty($_POST['username']) && !empty($_POST['password'])) {
        $username=htmlspecialchars($_POST['username']);
        $password=htmlspecialchars($_POST['password']);
        $a=0;
        $query =pg_query($link,"SELECT * FROM adminpanel.usertbl WHERE username='".$username."' AND password='".$password."'");
        $rowscount=pg_query($link, "SELECT COUNT(username) FROM adminpanel.usertbl");
        if(!empty($query))
        {
            while($row=pg_fetch_array($query))
            {
                $dbusername=$row['username'];
                $dbpassword=$row['password'];
            }
            if($username == $dbusername && $password == $dbpassword)
            {
                // старое место расположения
                //  session_start();
                $_SESSION['session_username']=$username;
                /* Перенаправление браузера */
                header("Location: ../index.php");
            }
            elseif($username != $dbusername)
            {
                $a=$a+1;
                if($a==$rowscount){
                    $message = "Этот логин не зарегестрирован";
                }
            }
        } else {
            //  $message = "Invalid username or password!";

            $message = "Неверный пароль";
        }
    } else {
        $message = "Все поля должны быть заполнены!";
    }
}
pg_close($link);
?>
