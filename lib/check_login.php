<?php

if(isset($_POST['auth_login']) && isset($_POST['auth_pass'])){

require_once('db_connect.php');//подключаем модуль с настройками БД

session_start();//старт сессии
$query="SELECT * FROM users WHERE username= ? ";//sql запрос на проверку логина

$statement=$connect->prepare($query);
$login=$_POST['auth_login'];
$statement->execute(array($login));

$total_row=$statement->rowCount();

$output='';

if($total_row>0){//если найдено больще чем 0
    $result=$statement->fetchAll();

    foreach ($result as $row) {
        if(password_verify($_POST['auth_pass'],$row['password'])){//проверяем пароль

            $_SESSION['name']=$row['username'];
        }
        else{
            $output='<span class="danger">Неверный пароль</span>';

        }
       
    }
}
else{
    $output='<span class="danger">Неверный логин</span>';
}

echo $output;

}


