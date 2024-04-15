<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	session_start();
	include('../includes/db_connect.php');
	include('../functions/functions.php');
	
	$arr_msg = array();
	$arr_msg["content"] = '';
	$arr_msg["errors"] = '';
	
	$bot_id = (int) $_POST["bot_id"];
	
	if($bot_id <= 0){
		$arr_msg["errors"] .= 'Ошибка идентификации бота';
		exit(json_encode($arr_msg));
	}
	
	mysql_query("INSERT INTO questions(q_title, q_bot, q_client) VALUES('Не задан', '$bot_id', '{$_SESSION["client_id"]}')");
	$id = mysql_insert_id();
	if($id > 0) $arr_msg["content"] = $id;
	else $arr_msg["errors"] .= 'Ошибка добавления';
	
	exit(json_encode($arr_msg));
}
?>