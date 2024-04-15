<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	session_start();
	include('../includes/db_connect.php');
	include('../functions/functions.php');
	
	$arr_Query = array();
	
	$arr_msg = array();
	$arr_msg["content"] = '';
	$arr_msg["errors"] = '';
	
	$id = (int) $_POST["id"];
	$q_title = clear_string($_POST["q_title"]);
	$set_as_first = (int) $_POST["set_as_first"];
	$parent_answers = json_decode($_POST["parent_answers"]);
	$answers = json_decode($_POST["answers"]);
	$editor_ids = json_decode($_POST["editor_ids"]);
	$iframe_ids = json_decode($_POST["iframe_ids"]);
	$elements_sequence = json_decode($_POST["elements_sequence"]);
	
	if(!strlen($q_title)) exit( json_encode( array("content" => "", "errors" => "Укажите заголовок вопроса!") ) );
	
	$Q = mysql_query("SELECT * FROM questions WHERE q_id=$id AND q_client='{$_SESSION["client_id"]}'");
	if(mysql_num_rows($Q)) $R = mysql_fetch_array($Q);
	else exit( json_encode( array("content" => "", "errors" => "Ошибка!") ) );
	
	$bot = $R["q_bot"];
	
	$Q = mysql_query("SELECT * FROM questions WHERE q_bot=$bot AND q_client='{$_SESSION["client_id"]}' AND q_main=1");
	if(!mysql_num_rows($Q) && !$set_as_first) $set_as_first = 1;
	
	if($set_as_first){
		$Q = mysql_query("SELECT * FROM questions WHERE q_bot=$bot AND q_client='{$_SESSION["client_id"]}' AND q_main=1 AND q_id<>$id");
		if(mysql_num_rows($Q)) exit( json_encode( array("content" => "", "errors" => "Ошибка! Стартовый вопрос уже задан.") ) );
	}
	
	// Удалить эдиторы, которые не пришли в скрипт
	mysql_query("DELETE FROM question_content WHERE qc_id NOT IN(".implode(',', $editor_ids).") AND qc_question=$id AND qc_bot=$bot");
	
	// Удалить айфреймы, которые не пришли в скрипт
	mysql_query("DELETE FROM iframes WHERE ifr_id NOT IN(".implode(',', $iframe_ids).") AND ifr_question=$id AND ifr_bot=$bot");
	
	// Удалить ответы, которые не пришли в скрипт
	mysql_query("DELETE FROM answers WHERE a_id NOT IN(".implode(',', $answers).") AND a_question=$id AND a_bot=$bot");
	
	// delete old order
	mysql_query("DELETE FROM bot_sequence WHERE bsq_bot='$bot' AND bsq_question='$id'");
	
	// new elements order
	foreach($elements_sequence as $value){
		$item_id = $value[0];
		$item_type = $value[1];
		$item_content = clear_string($value[2]);
		
		if($item_type == 'iframe'){
			if(!strlen($item_content)) continue;
			
			if($item_id == 0){
				mysql_query("INSERT INTO iframes(ifr_question, ifr_bot, ifr_content) VALUES($id, $bot, '$item_content')");
				$item_id = mysql_insert_id();
			}
			else mysql_query("UPDATE iframes SET ifr_content='$item_content' WHERE ifr_question=$id AND ifr_bot=$bot AND ifr_id='$item_id'");
			
			mysql_query("INSERT INTO bot_sequence(bsq_question, bsq_bot, bsq_type, bsq_id) VALUES($id, $bot, 1, '$item_id')");
		}
		elseif($item_type == 'editor'){
			if(!strlen($item_content)) continue;
			
			if($item_id == 0){
				mysql_query("INSERT INTO question_content(qc_question, qc_bot, qc_text) VALUES($id, $bot, '$item_content')");
				$item_id = mysql_insert_id();
			}
			else mysql_query("UPDATE question_content SET qc_text='$item_content' WHERE qc_question=$id AND qc_bot=$bot AND qc_id='$item_id'");
			
			mysql_query("INSERT INTO bot_sequence(bsq_question, bsq_bot, bsq_type, bsq_id) VALUES($id, $bot, 2, '$item_id')");
		}
		elseif($item_type == 'gallery'){
			
		}
		elseif($item_type == 'answer'){
			mysql_query("INSERT INTO bot_sequence(bsq_question, bsq_bot, bsq_type, bsq_id) VALUES($id, $bot, 4, '$item_id')");
		}
	}
	
	mysql_query("UPDATE questions SET q_title='$q_title', q_main='$set_as_first' WHERE q_id=$id AND q_client='{$_SESSION["client_id"]}'");
	mysql_query("DELETE FROM question_answer WHERE qa_question=$id");
	if(!$set_as_first) mysql_query("INSERT INTO question_answer(qa_answer, qa_question) SELECT a_id, $id FROM answers WHERE a_id IN(".implode(',', $parent_answers).") AND a_bot=$bot AND a_question<>$id");
	
	$arr_msg["content"] .= '<div class="alert-success text-center p10">Изменения успешно сохранены</div>';
	
	exit(json_encode($arr_msg));
}
?>