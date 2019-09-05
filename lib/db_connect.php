<?php
//подключение к базе данных
$connect = new PDO("mysql:host=localhost;dbname=mail;charset=utf8;", "root", "");
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>