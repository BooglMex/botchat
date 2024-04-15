$(document).ready(function () {
	// CKEDITOR
	var editorClass = $(".editorClass");
	if(editorClass.length){
		editorClass.each(function(){
			var textareaid = $(this).attr("id");
			if(textareaid.length) CKEDITOR.replace(""+textareaid+"");
		});
	}
	
	// SUMMERNOTE
	var summerNoteEditor = $(".summerNoteEditor");
	if(summerNoteEditor.length){
		summerNoteEditor.each(function(){
			var textareaid = $(this).attr("id");
			if(textareaid.length){
				$(this).summernote({
					height: 120,
					tabsize: 2
				});
			}
		});
	}
	
	// DRAG&DROP&SORT
	if($("#wrapper_elements_block").length){
		$("#wrapper_elements_block").sortable({
			revert:true, // Возвращение на место, если незавершённый акт
			containment:"#wrapper_elements_block", // Движение только внутри указанного элемента
			handle: ".move_cursor", // Участок (элемент), на котором можно хватать для перетаскивания
			//cancel: ".move_cursor" // Участок (элемент), на котором нельзя хватать для перетаскивания
		});
	}
	if($("#div_answers").length){
		$("#div_answers").sortable({
			revert:true, // Возвращение на место, если незавершённый акт
			containment:"#div_answers", // Движение только внутри указанного элемента
			cancel: ".fa", // Участок (элемент), на котором нельзя хватать для перетаскивания
			scroll:false
			//axis: "y" // Двигать только вертикально
		});
	}
	
	// ADD QUESTION
	$(".add_question").on('click', function(e) {
		var bot_id = $(this).attr('data-bot');
		
		var data = new FormData(); // Создаем объект формы
		
		data.append('bot_id', bot_id);
		
		$.ajax({
			type: "POST",
			url: "handlers/add_question.php",
			data: data,
			dataType: "json",
			cache: false,
			processData: false, // отключаем обработку передаваемых данных, пусть передаются как есть
			contentType: false, // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
			success: function (data) {
				if(data.content) location.href = 'question.php?id='+data.content;
				if(data.errors.length > 0) alert(data.errors);
			},
			error: function (data) {
				alert("Ошибка соединения");
			}
		});
	});
	
	// DELETE QUESTION
	$('.delete_question').on('click', function(e) {
		var question_id = $(this).attr('data-item');
		$("#questionDeleteModal .btn-primary").attr('data-item', question_id);
	});
	$("#questionDeleteModal .btn-primary").on('click', function(e) {
		var question_id = $(this).attr('data-item');
		
		var data = new FormData(); // Создаем объект формы
		
		data.append('question_id', question_id);
		
		$.ajax({
			type: "POST",
			url: "handlers/delete_question.php",
			data: data,
			dataType: "json",
			cache: false,
			processData: false, // отключаем обработку передаваемых данных, пусть передаются как есть
			contentType: false, // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
			success: function (data) {
				if(data.content){
					$('.delete_question[data-item='+question_id+']').parents('.col-12').remove();
					$("#questionDeleteModal").modal('hide');
				}
				if(data.errors.length > 0) alert(data.errors);
			},
			error: function (data) {
				alert("Ошибка соединения");
			}
		});
	});
	
	
	$('#select_bot_questions').on('change', function() {
		var question = $("option:selected", this).val();
		$("#select_bot_answers option").addClass("none");
		$("#select_bot_answers option[value=0]").removeClass("none");
		$("#select_bot_answers option[data-question="+question+"][data-selected=0]").removeClass("none");
	});
	
	$('#select_bot_answers').on('change', function() {
		var answer = $(this).val();
		var answer_text = $("#select_bot_answers option[value="+answer+"]").text();
		$("#div_selected_answers").append('<span class="span_selected span_selected_answer" data-id="'+answer+'">'+answer_text+' <i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer span_deselect deselected_answer" title="Удалить"></i></span>');
		$("#select_bot_answers option[value="+answer+"]").attr("data-selected", "1").addClass("none");
		$("option:first", this).prop("selected", true);
	});
	
	$('#set_as_first').on('change', function() {
		var clsName = $(this).attr("data-hide");
		if($(this).prop('checked') === true) $("."+clsName).addClass('none');
		else $("."+clsName).removeClass('none');
	});
	
	$(document).on('click', '.deselected_answer', function() {
		var deselected_answer = $(this);
		var parent_span = $(this).parents('.span_selected_answer');
		var id = parent_span.attr("data-id");
		var option = $("#select_bot_answers option[value="+id+"]");
		var question = option.attr("data-question");
		option.attr("data-selected", "0");
		parent_span.fadeOut(300, function(){ parent_span.remove(); });
		if($('#select_bot_questions').val() === question) option.removeClass("none");
	});
	
	// ANSWERS
	$(document).on('click', '#add_new_answer', function() {
		$("#text_answer").val('');
		$("#text_answer_link").val('');
		$("#save_answer").attr('data-id', '0');
	});
	$(document).on('click', '.span_answer', function() {
		var id = $(this).attr('data-id');
		$("#text_answer").val( $("span", this).text() );
		$("#text_answer_link").val( $("span", this).attr("rel") );
		$("#save_answer").attr('data-id', id);
	});
	$(document).on('click', '#save_answer', function() {
		var id = $(this).attr('data-id');
		var question = $(this).attr('data-question');
		var text_answer = $("#text_answer").val();
		var text_answer_link = $("#text_answer_link").val();
		
		var data = new FormData(); // Создаем объект формы
		
		data.append('id', id);
		data.append('question', question);
		data.append('text_answer', text_answer);
		data.append('text_answer_link', text_answer_link);
		
		$.ajax({
			type: "POST",
			url: "handlers/save_answer.php",
			data: data,
			dataType: "json",
			cache: false,
			processData: false, // отключаем обработку передаваемых данных, пусть передаются как есть
			contentType: false, // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
			success: function (data) {
				if(data.content && data.id){
					if($(".span_answer[data-id="+data.id+"]").length) $(".span_answer[data-id="+data.id+"]").replaceWith(data.content);
					else $("#div_answers").append(data.content);
					$("#modal_answer").modal('hide');
				}
				if(data.errors) alert(data.errors);
			},
			error: function (data) {
				alert("Ошибка соединения");
			}
		});
	});
	$(document).on('click', '.delete_answer', function(e) {
		e.stopPropagation();
		var parent_span = $(this).parent();
		parent_span.fadeOut(300, function(){ parent_span.remove(); });
	});
	
	var new_editor_numb = 0;
	$(document).on('click', '#select_NewElement', function(e) {
		var type = $(".type_NewElement:checked").val();
		new_editor_numb++;
		
		if(type == 'editor'){
			var new_element = '<div><hr><div class="form-group"><label class="col-12 col-form-label move_cursor">Введите текст <span class="rfloat"><i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer delete_editor" title="Удалить"></i></span></label><div class="col-sm-12"><textarea class="summerNoteEditor bot_editor" id="new_editor_numb'+new_editor_numb+'" data-id="0" required></textarea></div></div></div>';
			$("#wrapper_elements_block").append(new_element);
			$('#new_editor_numb'+new_editor_numb).summernote({
				height: 120,
				tabsize: 2
			});
		}
		else if(type == 'iframe'){
			var new_element = '<div><hr><div class="form-group"><label class="col-12 col-form-label move_cursor">Вставьте код <span class="rfloat"><i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer delete_iframe" title="Удалить"></i></span></label><div class="col-sm-12"><textarea class="form-control bot_iframe" rows="4" data-id="0" required></textarea></div></div></div>';
			$("#wrapper_elements_block").append(new_element);
		}
		else if(type == 'gallery'){
			
		}
		else return 0;
		
		$("#modal_NewElement").modal('hide');
	});
	
	$(document).on('click', '.delete_iframe, .delete_editor', function(e) {
		e.stopPropagation();
		var parent_to_remove = $(this).parents('.form-group').parent();
		parent_to_remove.fadeOut(300, function(){ parent_to_remove.remove(); });
	});
	
	// SAVE
	$(document).on('click', '#question_save', function() {
		var arr_parent_answers = [];
		var arr_answers = [];
		var arr_editor_ids = [];
		var arr_iframe_ids = [];
		var arr_elements_sequence = [];
		
		var id = $(this).attr("data-id");
		var q_title = $("#q_title").val();
		
		var set_as_first = 0;
		if($("#set_as_first").length){
			if($("#set_as_first").prop("checked") === true) var set_as_first = 1;
		}
		
		// parent_answers
		$(".span_selected_answer").each(function(){
			arr_parent_answers.push($(this).attr("data-id"));
		});
		var parent_answers = JSON.stringify(arr_parent_answers);
		
		// answers
		$(".span_answer").each(function(){
			arr_answers.push($(this).attr("data-id"));
		});
		var answers = JSON.stringify(arr_answers);
		
		// editors
		$(".bot_editor").each(function(){
			arr_editor_ids.push($(this).attr("data-id"));
		});
		var editor_ids = JSON.stringify(arr_editor_ids);
		
		// iframes
		$(".bot_iframe").each(function(){
			arr_iframe_ids.push($(this).attr("data-id"));
		});
		var iframe_ids = JSON.stringify(arr_iframe_ids);
		
		// elements sequence
		$(".bot_editor, .bot_iframe, .span_answer").each(function(){
			if($(this).hasClass("bot_editor")) var arr_dop = [$(this).attr("data-id"), 'editor', $(this).val()];
			else if($(this).hasClass("bot_iframe")) var arr_dop = [$(this).attr("data-id"), 'iframe', $(this).val()];
			else if($(this).hasClass("span_answer")) var arr_dop = [$(this).attr("data-id"), 'answer', ''];
			else{
				alert('Ошибка идентификации типа элемента.');
				return 0;
			}
			arr_elements_sequence.push(arr_dop);
		});
		var elements_sequence = JSON.stringify(arr_elements_sequence);
		
		// Создаем объект формы
		var data = new FormData();
		data.append('id', id);
		data.append('q_title', q_title);
		data.append('set_as_first', set_as_first);
		data.append('parent_answers', parent_answers);
		data.append('answers', answers);
		data.append('editor_ids', editor_ids);
		data.append('iframe_ids', iframe_ids);
		data.append('elements_sequence', elements_sequence);
		
		$.ajax({
			type: "POST",
			url: "handlers/question_save.php",
			data: data,
			dataType: "json",
			cache: false,
			processData: false, // отключаем обработку передаваемых данных, пусть передаются как есть
			contentType: false, // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
			success: function (data) {
				if(data.content) location.reload();
				if(data.errors.length) alert(data.errors);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(XMLHttpRequest.responseText);
				console.log(XMLHttpRequest.statusText);
				console.log(textStatus);
				console.log(errorThrown);
				alert("Ошибка соединения");
			}
		});
	});
});