$(document).ready(function () {
	// CKEDITOR
	var editorClass = $(".editorClass");
	if(editorClass.length){
		editorClass.each(function(){
			var textareaid = $(this).attr("id");
			if(textareaid.length) CKEDITOR.replace(""+textareaid+"");
		});
	}
	
	$('.delete_bot').on('click', function(e) {
		var bot_id = $(this).attr('data-item');
		$("#botsModal .btn-primary").attr('data-item', bot_id);
	});
	
	$("#botsModal .btn-primary").on('click', function(e) {
		var bot_id = $(this).attr('data-item');
		
		var data = new FormData(); // Создаем объект формы
		
		data.append('bot_id', bot_id);
		
		$.ajax({
			type: "POST",
			url: "handlers/delete_bot.php",
			data: data,
			dataType: "json",
			cache: false,
			processData: false, // отключаем обработку передаваемых данных, пусть передаются как есть
			contentType: false, // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
			success: function (data) {
				if(data.content){
					$('.delete_bot[data-item='+bot_id+']').parents('.col-12').remove();
					$("#botsModal").modal('hide');
				}
				if(data.errors.length > 0) alert(data.errors);
			},
			error: function (data) {
				alert("Ошибка соединения");
			}
		});
	});
});
