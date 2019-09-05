<!DOCTYPE html>
<html>
<?php
session_start();
?>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>WebMail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" media="screen" href="vendor/bs4/bootstrap.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/main.css" />
    <!-- Normalize CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/normalize.css" />
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="#">
                <img src="img/email.svg" width="30" height="30" class="d-inline-block align-top" alt="">
                WebMail
            </a>
            <div class="navbar-header pull-right">
                <a class="btn btn-primary" href="auth.php" role="button">Войти</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-8 ">
                        <h2>Добро пожаловать в почтовый клиент.</h2>
                        <p>Для продолжения,пожалуйста зарегистрируйтесь.</p>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <form class="register">
                            <div class="form-group  user_login-block">
                                <label for="user_login">Логин</label>
                                <input type="login" class="form-control" id="user_login" name="user_login"
                                    aria-describedby="emailHelp" placeholder="Введите логин">
                            </div>
                            <div class="form-group  user_pass-block">
                                <label for="user_pass">Пароль</label>
                                <input type="password" class="form-control" id="user_pass" name="user_pass"
                                    placeholder="Пароль">

                            </div>
                            <div id="response"></div>
                            <button type="submit" class="btn btn-success" id="next">Зарегистрироваться</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
    </footer>
    <!-- jQuery -->
    <script src="vendor/jQuery/jquery-3.4.1.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendor/bs4/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>