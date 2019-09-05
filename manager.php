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
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <a class="navbar-brand" href="#">
                <img src="img/email.svg" width="30" height="30" class="d-inline-block align-top" alt="">
                WebMail
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
             </button>
             <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                 <li class="nav-item">
                    <a class="nav-link active nav-link-custom" href="index.php">Сообщения</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active nav-link-custom" href="manager.php">Диспетчер аккаунтов</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <?php
                        if(isset($_SESSION['name'])){
					        echo '<a class="nav-link disabled nav-link-custom" id="user-log">Вы вошли,как <span class="user-login">'.$_SESSION['name'].'</span></a></li>';
					        echo '<li><a class="nav-link nav-link-logout" href="lib/logout.php">Выйти</a>';
                        }
                        else{  
					        header("Location:welcome.php");
                        }
                    ?>
                </li>
            </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="content">
            <div class="container">
                <div class="row">               
                    <table class="table table-accounts">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Сервер</th>
                                <th scope="col">Порт</th>
                                <th scope="col">Логин</th>                            
                                <th scope="col">Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
						require_once( 'lib/db_connect.php' );
						$query="SELECT id_account,server,port,remoteuser,remotepassword FROM accounts as A LEFT OUTER JOIN users as B ON A.id_user=B.id_user WHERE username = ?"; 
						$statement = $connect->prepare($query);
						$statement->execute(array($_SESSION['name']));
                        $result = $statement->fetchAll();
                        $total_row = $statement->rowCount();
                        if($total_row>0){                         
						    foreach($result as $key => $row ){
                                echo '<tr class="visible-xs" aria-hidden="true">';
                                echo '<td>'.($key+1).'</td>';   
                                echo '<td colspan="4">'.$row['remoteuser'].'</td>';
                                echo '</tr>';
                                echo '<tr>';
                                echo '<td>'.($key+1).'</td>';    
                                echo '<td>'.$row['server'].'</td>';      
                                echo '<td>'.$row['port'].'</td>';   
                                echo '<td>'.$row['remoteuser'].'</td>';   
                                echo '<td><button type="button" class="btn btn-success btn-sm btn-action" id="mailbox-change" data-id='.$row['id_account'].'>Ред.</button>
                                <button type="button" class="btn btn-danger btn-sm btn-action" id="mailbox-delete" data-id='.$row['id_account'].'>Удал.</button></td>';
                                echo '</tr>';  
						    }                                   
                        }
                        else{
                            echo '<tr><td colspan="3" >Список пуст</td></tr>';
                        }
					?>
                        </tbody>
                    </table>
                    </div>         
                    <button type="button" class="btn btn-primary" id="add-mailbox" data-id="0" data-toggle="modal"
                        data-target="#mail-manager">Добавить</button>              
            </div>
        </div>
        </div>
        <!-- Modal add -->
        <div class="modal fade" id="mail-manager" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Добавить почтовый адрес</h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="mail-add" id="mailAdd">
                            <div class="form-group server-block">
                                <input type="hidden" name="user_id" id="user_id" 
                                <?php
                                    require_once( 'lib/db_connect.php' );
                                    $login=$_SESSION['name'];
                                    $query="SELECT id_user,username  FROM users  WHERE username = ?"; 
                                    $statement = $connect->prepare($query);
                                    $statement->execute(array($login));
                                    $result = $statement->fetchAll();
                                    $users_info = array();
                                    foreach($result as $row){
                                        $users_info=$row;    
                                    } 
                                    echo 'value='.$users_info['id_user'].'';
                                ?> >
                                <input type="hidden" name="account_id" id="account_id">
                                <label for="server" class="col-form-label">Сервер</label>
                                <input type="text" class="form-control" name="server" id="server" placeholder="imap.yandex.ru">
                            </div>
                            <div class="form-group port-block">
                                <label for="port" class="col-form-label">Порт</label>
                                <input type="text" class="form-control" name="port" id="port" placeholder="993">
                            </div>
                            <div class="form-group login-block">
                                <label for="login" class="col-form-label" >Логин</label>
                                <input type="text" class="form-control" name="login" id="login" placeholder="user@yandex.ru">
                            </div>
                            <div class="form-group pass-block">
                                <label for="pass" class="col-form-label">Пароль</label>
                                <input type="password" class="form-control" name="pass" id="pass">
                            </div>
                            <div id="response"></div>
                            <button type="submit" class="btn btn-primary" id="addMail" data-id="0">Сохранить</button>
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