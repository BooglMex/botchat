<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	session_start();
	include('../includes/db_connect.php');
	include('../functions/functions.php');
	
	$arr_msg = array();
	$arr_msg["content"] = '';
	$arr_msg["errors"] = '';
	
	$question_id = (int) $_POST["question_id"];
	
	if($question_id <= 0){
		$arr_msg["errors"] .= 'Ошибка удаления';
		exit(json_encode($arr_msg));
	}
	
	$Q = mysql_query("SELECT * FROM questions WHERE q_client='{$_SESSION["client_id"]}' AND q_id='$question_id'");
	if(mysql_num_rows($Q) == 0){
		$arr_msg["errors"] .= 'Ошибка идентификации вопроса';
		exit(json_encode($arr_msg));
	}
	
	mysql_query("DELETE FROM answers WHERE a_question='$question_id'");
	mysql_query("DELETE FROM questions WHERE q_id='$question_id'");
	mysql_query("DELETE FROM question_answer WHERE qa_question='$question_id'");
	mysql_query("DELETE FROM iframes WHERE ifr_question='$question_id'");
	// Удаление галереи
	$Q3 = mysql_query("SELECT * FROM gallery WHERE glr_question=$question_id");
	if(mysql_num_rows($Q3)){
		while($R3 = mysql_fetch_array($Q3)){
			if(file_exists("../../images/bot/gallery/".$R3["glr_image"]) && $R3["glr_image"]) @unlink("../../images/bot/gallery/".$R3["glr_image"]);
		}
	}
	mysql_query("DELETE FROM gallery WHERE glr_question='$question_id'");
	
	$arr_msg["content"] = $question_id;
	
	exit(json_encode($arr_msg));
}
?>