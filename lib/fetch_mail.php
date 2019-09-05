<?php
require_once('class.imap.php');//подключаем класс imap
function connection($accounts_info){//функция для подключения к почтовому серверу и запроса сообщения
	$accounts_info=$accounts_info;
	$decrypt_pass=decrypt($accounts_info['remotepassword']);//получаем расшифровку пароля
	$email = new Imap();
	$connect = $email->connect(//создаем новое подключение
		'{'.$accounts_info['server'].':'.$accounts_info['port'].'/imap/ssl}INBOX', //host
		$accounts_info['remoteuser'], //username
		$decrypt_pass); //password

		if($connect){
			if(isset($_POST['inbox']) && isset($_POST['id_account']) ){
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *');
				// inbox array
				$id_account=$_POST['id_account'];
				$inbox = $email->getMessages('html');//получаем сообщения
				echo json_encode($inbox, JSON_PRETTY_PRINT);
			}else if(!empty($_POST['uid']) && !empty($_POST['part']) && !empty($_POST['file']) && !empty($_POST['encoding'])){
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *');
				// attachments
				$inbox = $email->getFiles($_POST);
				echo json_encode($inbox, JSON_PRETTY_PRINT);

			}
			else if(isset($_POST['action']))	{
				
				$email->deleteMessage($_POST['id']);

			}
		}
}
function emails($id){//функция для получения данных из бд почтового адреса
	require_once('db_connect.php');//подключаемся к базе данных
	require_once('crypt.php');
	$id=$id;
	$query="SELECT id_account,server,port,remoteuser,remotepassword FROM accounts WHERE id_account = ? "; 
	$statement = $connect->prepare($query);
	$statement->execute(array($id));
	$result = $statement->fetchAll();
	$accounts_info = array();
	foreach($result as $row){
		$accounts_info=$row;    
	}    
	
	connection($accounts_info);
}

$id=$_POST['id_account'];
emails($id);
?>