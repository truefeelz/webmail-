<?php
if(isset($_POST['user_login']) && (isset($_POST['user_pass']))){//проверяем наличие нужной нам переменной

    if($_POST['user_login']=='' || $_POST['user_pass']==''){//проверяем на пустые поля
        $output='<span class="danger">Поля не могут быть пустыми!</span>';
    }
    else{
        include('db_connect.php');
        $query="SELECT * FROM users WHERE username = ? ";//запрос на выборку с указанным логином
        $statement=$connect->prepare($query);
        $login=$_POST['user_login'];
        $date = date('Y-m-d H:i:s');
        $statement->execute(array($login));
        $total_row=$statement->rowCount();
        $output='';
    
        if($total_row>0){//если такой логин уже есть

            $output='<span class="danger">Данный логин уже существует</span>';
        
        }
        else{
            session_start();
            $query = "INSERT INTO users (id_user,username,password,date) VALUES (NULL,?,?,?)";//запрос на добавление записи
            $statement = $connect->prepare($query);
            $password=password_hash($_POST['user_pass'],PASSWORD_DEFAULT);//шифрования пароля
		    $statement->execute(array($login,$password,$date));
            $_SESSION['name']=$login;
        
        }
    
    
    }
    echo $output;
}
else{
    $output='<span class="danger">Произошла ошибка</span>';
    echo $output;
}
?>
