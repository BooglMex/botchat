$(document).ready(function () {
	var answer = $("body").data("answer");
	
	function get_question(answer)
	{
		$(".loading_dots").removeClass('none');
		
		var bot = $("body").data("bot");
		var ref = $("body").data("ref");
		
		var data = new FormData(); // Создаем объект формы
		data.append('bot', bot);
		data.append('answer', answer);
		data.append('ref', ref);
		
		$.ajax({
			type: "POST",
			url: "handlers/get_question.php",
			data: data,
			dataType: "json",
			cache: false,
			processData: false, // отключаем обработку передаваемых данных, пусть передаются как есть
			contentType: false, // отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
			success: function (data) {
				if(data.content.length){
					var doc_h1 = $(document).height();
					
					setTimeout(function(){
						$(".loading_dots").addClass('none');
						$(".loading_dots").before(data.content);
						
						var doc_h2 = $(document).height();
						var deff_h = doc_h2 - doc_h1;
						if(deff_h > 0){
							var current_h_scroll = $(window).scrollTop();
							$("body,html").animate({ scrollTop: (current_h_scroll + deff_h + 150) }, 600);
						}
					},1000);
				}
				if(data.finish.length) $(".loading_dots").addClass('none');
				if(data.errors.length){
					$(".loading_dots").addClass('none');
					alert(data.errors);
				}
				if(data.console.length) console.log(data.console);
			},
			error: function (data) {
				alert("Ошибка соединения");
			}
		});
	}
	
	get_question(answer);
	
	$(document).on('click', '.span_answer[data-link=0]', function(e) {
		answer = $(this).data("answer");
		var text = $(this).html();
		$(".block_answers").remove();
		$(".loading_dots").before('<div class="row"><div class="bot_block_right col-10 offset-2 justify-content-end"><div class="bot_msg">'+text+'</div></div></div>');
		get_question(answer);
	});
});
