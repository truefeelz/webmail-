<?php 
require_once('send/class.phpmailer.php');//подключаем класс для отправки писем через SMTP
require_once('db_connect.php');//подключаемся к базе данных
require_once('crypt.php');//подключаем файл с функциями шифрования
if(isset($_POST['recipient-name']) && isset($_POST['to-name']) && isset($_POST['message-text']) && isset($_POST['id_mail'])){
    if($_POST['recipient-name']=='' || $_POST['to-name']=='' || $_POST['message-text']=='' || $_POST['id_mail']==''){       
         echo '<span class="danger">Поля не могут быть пустыми</span>';       
    }
    else{
        $id_account=$_POST['id_mail'];//адрес отправителя
        $query="SELECT id_account,server,port,remoteuser,remotepassword FROM accounts WHERE id_account = ?"; 
        $statement = $connect->prepare($query);
        $statement->execute(array($id_account));
        $result = $statement->fetchAll();
        $accounts_info = array();
        foreach($result as $row){
	        $accounts_info=$row;    
        }  
        $server='smtp.'.substr($accounts_info['server'], 5);//получаем smtp сервер
        $decrypt_pass=decrypt($accounts_info['remotepassword']);//расшифровка пароля
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $subject=$_POST['recipient-name'];//тема сообщения
        $to=$_POST['to-name'];//адресс получателя
        $message = $_POST['message-text'];//текст сообщения
        if(strpos($to,"(")!==false){
            $str=strpos($to,"(");
            $to=substr($to,0,$str-1);
        }
        $mail->isSMTP();     
        $mail->Host = $server;  				
        $mail->SMTPAuth = true;                               // включение smtp аутенфикации
        $mail->Username = $accounts_info['remoteuser']; //  логин от почты с которой будут отправляться письма
        $mail->Password = $decrypt_pass; //пароль от почты с которой будут отправляться письма
        $mail->SMTPSecure = 'ssl';     //включение ssl протокола
        $mail->Port = 465; //порт
        $mail->setFrom($accounts_info['remoteuser']); // от кого будет уходить письмо?
        $mail->addAddress($to);     // Кому будет уходить письмо 
        $mail->isHTML(true);                               
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = '';
        for($ct=0;$ct<count($_FILES['attachments']['tmp_name']);$ct++)//загрузка файлов если есть
        {
            $mail->AddAttachment($_FILES['attachments']['tmp_name'][$ct],$_FILES['attachments']['name'][$ct]); 
        } 
        if(!$mail->send()) {
            echo '<span class="danger">Ошибка,попробуйте еще раз</span>';
        } 
        else {
            echo '<span class="success">Сообщение отправлено!</span>';
        }
    }
}
else{
    echo '<span class="danger">Произошла ошибка</span>';
}
?>
