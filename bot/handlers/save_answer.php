<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	session_start();
	include('../includes/db_connect.php');
	include('../functions/functions.php');
	
	$arr_msg = array();
	$arr_msg["content"] = '';
	$arr_msg["errors"] = '';
	$arr_msg["id"] = '';
	
	function exit_from_script($type, $msg)
	{
		global $arr_msg;
		$arr_msg[$type] = $msg;
		exit(json_encode($arr_msg));
	}
	
	$id = (int) $_POST["id"];
	$question = (int) $_POST["question"];
	$text_answer = clear_string($_POST["text_answer"], 1);
	$text_answer_link = clear_string($_POST["text_answer_link"], 1);
	
	if(!strlen($text_answer)) exit_from_script('errors', 'Укажите текст ответа!');
	
	$Q = mysql_query("SELECT * FROM questions WHERE q_id=$question AND q_client='{$_SESSION["client_id"]}'");
	if(mysql_num_rows($Q)) $R = mysql_fetch_array($Q);
	else exit_from_script('errors', 'Ошибка идентификации вопроса!');
	
	$text_answer_link = ($text_answer_link) ? "'$text_answer_link'" : "NULL";
	
	$bot = (int) $R["q_bot"];
	
	$a_link = ($text_answer_link != "NULL") ? '<i aria-hidden="true" class="fa fa-external-link text-info" title="Ссылка"></i>' : '';
	
	if($id == 0){
		mysql_query("INSERT INTO answers(a_bot, a_question, a_text, a_link) VALUES('$bot', '$question', '$text_answer', $text_answer_link)");
		$answer = mysql_insert_id();
		if($answer){
			$arr_msg["id"] = $answer;
			$arr_msg["content"] = '
			<span class="span_selected span_answer" data-id="'.$answer.'" data-toggle="modal" data-target="#modal_answer">
				<span rel="'.$_POST["text_answer_link"].'">'.htmlspecialchars($_POST["text_answer"]).'</span>
				'.$a_link.'
				<i aria-hidden="true" class="fa fa-fw fa-edit text-success" title="Изменить"></i>
				<i aria-hidden="true" class="fa fa-fw fa-times text-danger span_deselect delete_answer" title="Удалить"></i>
			</span>';
		}
		else exit_from_script('errors', 'Ошибка запроса!');
	}
	else{
		if(mysql_query("UPDATE answers SET a_text='$text_answer', a_link=$text_answer_link WHERE a_id='$id' AND a_bot='$bot' AND a_question='$question'")){
			$arr_msg["id"] = $id;
			$arr_msg["content"] = '
			<span class="span_selected span_answer" data-id="'.$id.'" data-toggle="modal" data-target="#modal_answer">
				<span rel="'.$_POST["text_answer_link"].'">'.htmlspecialchars($_POST["text_answer"]).'</span>
				'.$a_link.'
				<i aria-hidden="true" class="fa fa-fw fa-edit text-success" title="Изменить"></i>
				<i aria-hidden="true" class="fa fa-fw fa-times text-danger span_deselect delete_answer" title="Удалить"></i>
			</span>';
		}
		else exit_from_script('errors', 'Ошибка запроса!');
	}
	
	exit(json_encode($arr_msg));
}
?>