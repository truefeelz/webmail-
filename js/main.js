$(function() {
//////////////валидация/////////////////////////
function validate(formData){
	var formData=formData;
	var check=false;
	var labelText;
	var hide;
	for (var key in formData) {
		hide=$('#'+formData[key].name+'').is(":hidden"); 

		if(!hide && $('#'+formData[key].name+'').attr('type') != "file"){
			if(formData[key].value == '' ){
				labelText = $('label[for='+  formData[key].name  +']').text().toLowerCase();;
				$('#'+formData[key].name+'').removeClass("is-valid");	
				$('#'+formData[key].name+'').addClass("is-invalid");
				$('.'+formData[key].name+'-block .valid-feedback').remove();
				$('.'+formData[key].name+'-block .invalid-feedback').remove();
				$('.'+formData[key].name+'-block').append("<div class='invalid-feedback'>Пожалуйста укажите "+labelText+".</div>");
				check = false;			
			}
			else{
				$('#'+formData[key].name+'').removeClass("is-invalid");	
				$('#'+formData[key].name+'').addClass("is-valid");
				$('.'+formData[key].name+'-block  .invalid-feedback').remove();
				$('.'+formData[key].name+'-block .valid-feedback').remove();
				$('.'+formData[key].name+'-block').append("<div class='valid-feedback'>'Отлично'!</div>");
				check = true;
			}
		}
		else{
			continue;
		}	
	}
	return check;
}	
//////////////менеджер аккаунтов////////////////
function manager(action,id){
	var id=id;
	var action=action;
	if (action=='delete'){
		$.ajax({
			url:'lib/mailbox_manager.php',
			type:'post',
			data:{id:id,
						action:action},
			success:function(printdata){
				if(printdata!=''){
					$('#response').html(printdata);
				}
				else{
					location.reload();
				}
			}
		});
	}
	if (action=='fetch-single'){
		$.ajax({
			url:'lib/mailbox_manager.php',
			type:'post',
			dataType:"json",
			data:{id:id,
						action:action},
			success:function(printdata){	
				$('#mail-manager').modal('show');
					jQuery.each(printdata, function(i, val) {
						$("#" + i).val(val);
						 });	
			}
		});
	}
	if(action=='add' || action=='update'){
			var formData = new FormData($('.mail-add')[0]);
			formData.append('action',action);
		$.ajax({
			url:'lib/mailbox_manager.php',
			type:'post',
			data:formData,
			processData: false,
			contentType: false,
			cache: false,
			success:function(printdata){
				if(printdata!=''){
					$('#response').html(printdata);
				}
				else{
					location.reload();
				}
			}
		});
	}
}
$('body').on('click', '#mailbox-delete', function (){//клик по кнопке удалить
		var action="delete";
		var id = $(this).attr('data-id'); 
		manager(action,id);
});
	$('body').on('click', '#mailbox-change', function (){//клик по кнопке изменить
		$('#addMail').attr('data-id', 1);//изменение
		var action="fetch-single";
		var id = $(this).attr('data-id'); 
		$('#account_id').val(id); 
		manager(action,id);
});
	$('body').on('click', '#add-mailbox', function (){//клик по кнопке добавить
		$('#addMail').attr('data-id', 2);//добавление
});
	$(".mail-add").submit(function(event) {//килк по кнопке сохранить в модально окне
		event.preventDefault();
		var formData = $(this).serializeArray();
		var id = $('#addMail').attr('data-id'); 
		var valid=validate(formData);
		console.log(valid);
		if(valid){		
			if(id==1){
				var action='update';			
			}
			if(id==2){
			  var action='add';
			}
			manager(action);
		}	
	});

////////////////////////////////welcome//////////////////////////////////////////////////////
	$(".register").submit(function(event) {//клик по регистрации
		event.preventDefault();
		var formData = $(this).serializeArray(); 
		var valid=validate(formData);
		console.log(valid);
		if(valid){
				$.ajax({
					url:'lib/register.php',
					type:'post',
					data:$(this).serialize(),
					success:function(printdata){
						if(printdata!=''){
							$('#response').html(printdata);
						}
						else{
							window.location='index.php';
						}
					}
				});
			}
	});
	$(".login").submit(function(event) {//клик по кнопке войти
		event.preventDefault();
		var formData = $(this).serializeArray(); 
		var valid=validate(formData);
		console.log(valid);	
			if(valid){
				$.ajax({
					url:'lib/check_login.php',
					type:'post',
					data:$(this).serialize(),
					success:function(printdata){
						if(printdata!=''){
							$('#response').html(printdata);
						}
						else{
							window.location='index.php';
						}
					}
				});
			}
	});
	/////////////////////////////////////////////////////////////////////////////////////////////////////index
  var mail_id=$('#mailbox').val();
	var mail_text=$('#mailbox option:selected').text();

  load_mail(mail_id);
	load_link(mail_text);

	$('#replyModal').on('shown.bs.modal', function () {
      $("#addModal").modal('hide');
	});
	
	$('body').on('click', '.reply-button', function () {//клик по кнопку ответить
		var id = $(this).attr('data-id'); 
		$('#recipient-name').val(json[id].subject);
		$('#to-name').val(json[id].from.address+' ('+json[id].from.name+')');
		if($(this).data('id')>=0){
			$(this).attr('data-id', -1);
		}
	});

	$('body').on('click', '.view', function () {//клие по теме письма открывает сообщение
		var id = $(this).data('id'); 
		$('.reply-button').attr('data-id', id);
		console.log(json);
		var subject=json[id].subject;
		var message = json[id].message;
		var attachments = json[id].attachments;
		var attachment = '';
		if(attachments.length > 0){
			attachment += "<hr>Вложения:";
			$.each(attachments, function(i, a) {
				var file = json[id].uid + ',' + a.part + ',' + a.file + ',' + a.encoding;
				attachment += '<br><a href="#" class="file" data-file="' + file + '">' + a.file + '</a>';
			});
		}
		$('#message-title').html(subject);
		$('#message').html(message + attachment); 
    });	
		$('body').on('click', '#delete-message', function () {	
			var id_account=$('#mailbox').val();
			var id = $(this).data('id');
			var action="delete-message";
			var mail_text=$('#mailbox option:selected').text();
			console.log(id_account,id,action);
			$.ajax({
				url:'lib/fetch_mail.php',
				type:'post',
				data:{action:action,
							id:id,
							id_account:id_account},	
				success:function(){
					console.log('кек');
					load_mail(id_account);
					load_link(mail_text);			
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}					
			});
		});

	$('body').on('click', '.file', function () {//клик по файлу в письме скачивает файл
		$.LoadingOverlay("show");
		var file = $(this).data('file').split(",");
		$.ajax({
			type: "POST",
			url: "lib/fetch_mail.php",
			data: {
				uid: file[0],
				part: file[1],
				file: file[2],
				encoding: file[3]
			},
			dataType: 'json'
		}).done(function(d) {
			if(d.status === "success"){
				$.LoadingOverlay("hide");
				window.open(d.path, '_blank');
			}else{
				alert(d.message);
			}
		});
	});
    
    $('#mailbox').on('change', function() {//выбор почтового ящика       
		var id_account=this.value;
		var text_account=$('#mailbox option:selected').text();
		load_mail(id_account);
		load_link(text_account);
		}); 

	$(".send-mail").submit(function(event) {	//функция отправки сообщения
		event.preventDefault();

		var formData = $(this).serializeArray(); 
		var valid=validate(formData);
		console.log(id_mail);
		console.log(valid);
		if(valid){
			var id_mail = $("#mailbox option:selected").val();
			var formdata = new FormData($('.send-mail')[0]);
			formdata.append('id_mail',id_mail);
		 $.ajax({
		   url:'lib/send_mail.php',
		   type:'post',
		   data: formdata,
		   processData: false,
		   contentType: false,
		   cache: false,
		   success:function(printdata){
			 $('form').trigger("reset");
			 $('#response').fadeIn().html(printdata);
			 $("#mail-send").prop('disabled',false);
			 setTimeout(function(){
			   $('#response').fadeOut("slow");
			 },5000);
		   }
		 });
		}			  
	});

	function load_link(name){
		$('#mail-link').text(name);
	}

  function load_mail(id){//загрузка списка сообщений
        var id_account=id;
				if(id_account==null){
					$('#new-mail').hide();
					$('.mailbox-info').hide();
					$('#myTable').html('<h4 class="text-center">Пока что ваш список почтовых адресов пуст, добавьте их в <a href="manager.php">диспетчере аккаунтов.</a></h4>')
				}
				else{
      $.LoadingOverlay("show");    
	    $.ajax({     
		type: "POST",
		url: "lib/fetch_mail.php",
		data: {
            inbox: "",
            id_account:id_account
		},
				dataType: 'json',
				success:function(d){
						var tbody = "";
						json = d.data;
						console.log(d.data);
						$.each(json, function(i, a) {
								tbody += '<tr><td>' + (i + 1) + '</td>';
							  tbody += '<td><a href="#" data-id="' + i + '" class="view" data-toggle="modal" data-target="#addModal">' + a.subject.substring(0, 20) +'...</a></td>';
							  tbody += '<td>' + (a.from.name === "" ? "[empty]" : a.from.name) + '</td>';
								tbody += '<td><a href="mailto:' + a.from.address + '?subject=Re:' + a.subject + '">' + a.from.address + '</a></td>';
								tbody += '<td>' + a.date + '<a class="reply-button" data-id="' + i + '" data-toggle="modal" data-target="#replyModal" href="#" ><img src="img/reply.png" title="Ответить" class="d-inline-block ml-1 ico-delete"></a><a id="delete-message" href="#" data-id="' + a.uid + 
								'"><img src="img/delete.png" title="Удалить" class="d-inline-block ml-2 ico-delete"></a></td></tr>';
						});
						$('#inbox').html(tbody);
						$('#count-message').html('Новых писем: '+d.data.length +'');
						$('#myTable').DataTable();
						$.LoadingOverlay("hide");	
						$('#new-mail').show();						
				},
				error: function() {
					$.LoadingOverlay("hide");
					$('#new-mail').hide();
					var tbody = '<tr><td class="text-center" colspan="5">Не удалось подключиться к электронному ящику.Некорректные данные для подключения.</td></tr>';
					$('#inbox').html(tbody);	
				}
	}); 
		}
	}
});