<?php
require_once('db_connect.php'); //подключаемся к базе данных
require_once('crypt.php');
if (isset($_POST['action'])) {
    //вывод данных почты в окно
    if ($_POST['action']=='fetch-single'){
        if (isset($_POST['id']) && $_POST['id'] != '') {//проверям на пустые полня
            try {
                $user_id = $_POST['id'];
                $query = "SELECT server,port,remoteuser,remotepassword FROM accounts WHERE id_account = ?";
                $statement = $connect->prepare($query);
                $statement->execute(array($user_id));
                $result = $statement->fetchAll();
                foreach($result as $row) {
                    $output['server'] = $row['server'];
                    $output['port'] = $row['port'];
                    $output['login'] = $row['remoteuser'];
                    $output['pass'] = decrypt($row['remotepassword']);
                 }
                 echo json_encode($output);
             } catch (PDOException $e) {
                 echo "Error: ".$e->getMessage();
             }

        }
        else{
            $output = '<span class="danger">Произошла ошибка</span>';
             echo $output;
        }
    }
     //удаление почтового адреса
    if ($_POST['action']=='delete'){
        if(isset($_POST['id']) && $_POST['id'] != '' ){

            $user_id=$_POST['id'];
            $query = "DELETE FROM accounts WHERE id_account = ?";
            $statement = $connect->prepare($query);
            $statement->execute(array($user_id));
        }
        else{
            $output='<span class="danger">Произошла ошибка</span>';
             echo $output;
        }
    }
      //сохранение почтового адреса
    if ($_POST['action']=='update'){
        if(isset($_POST['server']) && isset($_POST['port']) && isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['account_id'])){
            if($_POST['server']=='' || $_POST['port']=='' || $_POST['login']== '' || $_POST['pass']== '' || $_POST['account_id']== ''){//проверяем на пустые поля
                  $output='<span class="danger">Поля не могут быть пустыми!</span>';
                  echo $output;
         
            }
            else{
                 $account_id=$_POST['account_id'];
                 $server=$_POST['server'];
                 $port=$_POST['port'];
                 $login=$_POST['login'];
                 $pass=encrypt($_POST['pass']);//получаем расшифровку пароля
                 $query = "UPDATE accounts SET server=?,port=?,remoteuser=?,remotepassword=?  WHERE id_account = ?";//запрос на добавление записи
                 $statement = $connect->prepare($query);
                 $statement->execute(array($server,$port,$login,$pass,$account_id));
            }
         
         }
         else{
             $output='<span class="danger">Произошла ошибка</span>';
             echo $output;
         }

    }
    //добавление почтового адреса
    if ($_POST['action']=='add'){
        if(isset($_POST['server']) && isset($_POST['port']) && isset($_POST['login']) && isset($_POST['pass']) && isset($_POST['user_id'])){
            if($_POST['server']=='' || $_POST['port']=='' || $_POST['login']== '' || $_POST['pass']== '' || $_POST['user_id']== ''){//проверяем на пустые поля
                  $output='<span class="danger">Поля не могут быть пустыми!</span>';
                  echo $output;
         
            }
            else{
                 $user_id=$_POST['user_id'];
                 $server=$_POST['server'];
                 $port=$_POST['port'];
                 $login=$_POST['login'];
                 $pass=encrypt($_POST['pass']);//получаем расшифровку пароля
                 $query = "INSERT INTO accounts (id_account,id_user,server,port,remoteuser,remotepassword) VALUES (NULL,?,?,?,?,?)";//запрос на добавление записи
                 $statement = $connect->prepare($query);
                 $statement->execute(array($user_id,$server,$port,$login,$pass));
           }
         
         }
         else{
             $output='<span class="danger">Произошла ошибка</span>';
             echo $output;
         }
    }
} 
else{
    $output = '<span class="danger">Произошла ошибка</span>';
    echo $output;
} 
?>