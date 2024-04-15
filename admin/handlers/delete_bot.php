<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	session_start();
	include('../includes/db_connect.php');
	include('../functions/functions.php');
	
	$arr_msg = array();
	$arr_msg["content"] = '';
	$arr_msg["errors"] = '';
	
	$bot_id = (int) $_POST["bot_id"];
	
	if($bot_id <= 0)	{
		$arr_msg["errors"] .= 'Ошибка удаления';
		exit(json_encode($arr_msg));
	}
	
	$Q = mysql_query("SELECT * FROM bots WHERE bot_id=$bot_id");
	if(mysql_num_rows($Q)) $R = mysql_fetch_array($Q);
	else{
		$arr_msg["errors"] .= 'Ошибка удаления';
		exit(json_encode($arr_msg));
	}
	
	mysql_query("DELETE FROM bots WHERE bot_id='$bot_id'");
	mysql_query("DELETE FROM answers WHERE a_bot='$bot_id'");
	mysql_query("DELETE FROM questions WHERE q_bot='$bot_id'");
	mysql_query("DELETE FROM question_answer WHERE qa_question IN(SELECT q_id FROM questions WHERE q_bot='$bot_id')");
	mysql_query("DELETE FROM iframes WHERE ifr_bot='$bot_id'");
	mysql_query("DELETE FROM question_content WHERE qc_bot='$bot_id'");
	// Удаление галереи
	$Q3 = mysql_query("SELECT * FROM gallery WHERE glr_bot=$bot_id");
	if(mysql_num_rows($Q3)){
		while($R3 = mysql_fetch_array($Q3)){
			if(file_exists("../../images/bot/gallery/".$R3["glr_image"]) && $R3["glr_image"]) @unlink("../../images/bot/gallery/".$R3["glr_image"]);
		}
	}
	mysql_query("DELETE FROM gallery WHERE glr_bot='$bot_id'");
	
	@unlink('../images/bots/avatars/'.$R['bot_avatar']);
	@unlink('../images/bots/favicons/'.$R['bot_favicon']);
	@unlink('../images/bots/bg/'.$R['bot_bg_chat']);
	@unlink('../images/bots/banners/'.$R['bot_bg_index']);
	
	$arr_msg["content"] = $bot_id;
	
	exit(json_encode($arr_msg));
}
?>