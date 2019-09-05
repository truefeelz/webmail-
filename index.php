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
	<!-- dataTables -->
	<link rel="stylesheet" type="text/css" media="screen"  href="vendor/dataTables/jquery.dataTables.min.css">
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
					<a class="nav-link  nav-link-custom" href="index.php">Сообщения</a>
				</li>
				<li class="nav-item">
					<a class="nav-link  nav-link-custom" href="manager.php">Диспетчер аккаунтов</a>
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
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="mailbox-info">
						<div class="row">
							<div class="col-lg-4 col-md-12">
								<label for="mailbox">Почта</label>
								<select id="mailbox" class="form-control">
									<?php
										require_once( 'lib/db_connect.php' );
										$query="SELECT remoteuser,username,id_account FROM accounts as A LEFT OUTER JOIN users as B ON A.id_user=B.id_user WHERE username = ?"; 
										$statement = $connect->prepare($query);
										$statement->execute(array($_SESSION['name']));
										$result = $statement->fetchAll();
										foreach($result as $row){
												echo '<option value="'.$row['id_account'].'">'.$row['remoteuser'].'</option>';         
										}        
										echo '</select>';
									?>
							</div>
							<div class="col-lg-8 col-md-12  pt-4">
								<h3 class="text-center pt-1"  >Почтовый ящик
									<a id="mail-link" href="#"></a>
								</h3>
								<h5 class="text-center" id="count-message"></h5>

							</div>
							<div class="col-12">
								<div class="new-message">
									<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#replyModal" id="new-mail">Новое сообщение</button>	
								</div>		
							</div>
								
						</div>
					</div>
		
					<hr>
				
					<table id="myTable" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>№</th>
								<th>Тема</th>
								<th>Автор</th>
								<th>Email</th>
								<th>Дата</th>
							</tr>
						</thead>
						<tbody id="inbox">			
						</tbody>
					</table>


				</div>
			</div>
		</div>
		<!-- Modal message -->
		<div id="addModal" class="modal fade" role="dialog">
			<div class="modal-dialog" id="modal-message">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="message-title"></h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="message">
					</div>
					<div class="modal-footer" id="reply">
						<button type="button" class="btn btn-primary reply-button" data-id="-1" data-toggle="modal"
							data-target="#replyModal">Ответить</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Modal reply -->
		<div class="modal fade" id="replyModal" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Новое сообщение</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form class="send-mail" id="mail-send" enctype="multipart/form-data">
							<div class="form-group to-name-block">
								<label for="to-name" class="col-form-label">Кому</label>
								<input type="text" class="form-control" name="to-name" id="to-name">
							</div>
							<div class="form-group recipient-name-block">
								<label for="recipient-name" class="col-form-label">Тема:</label>
								<input type="text" class="form-control" name="recipient-name" id="recipient-name">
							</div>
							<div class="form-group message-text-block">
								<label for="message-text" class="col-form-label">Сообщение:</label>
								<textarea class="form-control" name="message-text" id="message-text" rows="5"></textarea>
							</div>
							<div class="custom-file">
								<input type="file" class="custom-file-input" name="attachments[]" id="attachments" multiple>
								<label class="custom-file-label" for="attachments">Добавить вложения</label>
							</div>
							<div id="response"></div>
							<button type="submit" class="btn btn-primary" id="send">Отправить</button>
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
	<!-- dataTables -->
	<script src="vendor/dataTables/jquery.dataTables.min.js"></script>
	<!-- loading-overlay -->
	<script src="vendor/loadingOverlay/loadingoverlay.min.js"></script>
	<!-- JavaScript-->
	<script src="js/main.js"></script>
</body>

</html>