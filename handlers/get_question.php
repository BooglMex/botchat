<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
	session_start();
	include('../includes/db_connect.php');
	include('../functions/functions.php');
	
	$arr_msg = array();
	$arr_msg["content"] = '';
	$arr_msg["errors"] = '';
	$arr_msg["finish"] = '';
	$arr_msg["console"] = '';
	
	function exit_json_encode(){
		global $arr_msg;
		exit(json_encode($arr_msg));
	}
	
	$bot = (int) $_POST["bot"];
	$answer = (int) $_POST["answer"];
	$ref = clear_probels($_POST["ref"]);
	
	$Qbot = mysql_query("SELECT * FROM bots WHERE bot_id='$bot'");
	if(mysql_num_rows($Qbot) == 1){
		$Rbot = mysql_fetch_array($Qbot);
		if($Rbot["bot_avatar"] && file_exists("../images/bots/avatars/".$Rbot["bot_avatar"])) $bot_avatar = $Rbot["bot_avatar"];
		else $bot_avatar = 'avatar.jpg';
	}
	else exit_json_encode($arr_msg["errors"] = 'Ошибка идентификации бота!');
	
	if($answer == 0){
		$Qget = mysql_query("SELECT * FROM questions WHERE q_bot='$bot' AND q_main=1");
		if(mysql_num_rows($Qget) == 1){
			$Rget = mysql_fetch_array($Qget);
			$question = $Rget["q_id"];
		}
		else exit_json_encode($arr_msg["finish"] = '1');
	}
	elseif($answer > 0){
		$Qget = mysql_query("SELECT * FROM question_answer WHERE qa_answer='$answer'");
		if(mysql_num_rows($Qget) == 1){
			$Rget = mysql_fetch_array($Qget);
			$question = $Rget["qa_question"];
		}
		else exit_json_encode($arr_msg["finish"] = '1');
	}
	else exit_json_encode($arr_msg["finish"] = '1');
	
	$Qres = mysql_query("SELECT *, ( IF(bsq_type=4, 10, 0) ) AS relevant FROM bot_sequence WHERE bsq_bot='$bot' AND bsq_question='$question' ORDER BY relevant DESC");
	if(mysql_num_rows($Qres)){
		if(!preg_match("/^([_@.0-9a-zA-Z]+)$/u", $ref)) $ref = '';
		
		while($Rres = mysql_fetch_array($Qres)){
			$item_id = $Rres["bsq_id"];
			
			if($Rres["bsq_type"] == 1){ // IFRAME
				$Qget = mysql_query("SELECT * FROM iframes WHERE ifr_bot='$bot' AND ifr_question='$question' AND ifr_id='$item_id'");
				if(mysql_num_rows($Qget)){
					$Rget = mysql_fetch_array($Qget);
					$arr_msg["content"] .= '
					<div class="row">
						<div class="bot_block_left col-10">
							<img src="images/bots/avatars/'.$bot_avatar.'" class="bot_avatar">
							
							<div class="bot_msg fill_width"><div class="wrapp_iframe_video">'.$Rget["ifr_content"].'</div></div>
						</div>
					</div>';
				}
			}
			elseif($Rres["bsq_type"] == 2){ // EDITOR
				$Qget = mysql_query("SELECT * FROM question_content WHERE qc_bot='$bot' AND qc_question='$question' AND qc_id='$item_id'");
				if(mysql_num_rows($Qget)){
					$Rget = mysql_fetch_array($Qget);
					$arr_msg["content"] .= '
					<div class="row">
						<div class="bot_block_left col-10">
							<img src="images/bots/avatars/'.$bot_avatar.'" class="bot_avatar">
							
							<div class="bot_msg">'.$Rget["qc_text"].'</div>
						</div>
					</div>';
				}
			}
			elseif($Rres["bsq_type"] == 3){ // GALLERY
				
			}
			elseif($Rres["bsq_type"] == 4){ // ANSWERS
				$Qget = mysql_query("SELECT * FROM answers WHERE a_bot='$bot' AND a_question='$question' AND a_id='$item_id'");
				if(mysql_num_rows($Qget)){
					$Rget = mysql_fetch_array($Qget);
					if($Rget["a_link"]) $answers_gourp .= '<a href="'.str_ireplace('__REFERAL__', $ref, $Rget["a_link"]).'" class="span_answer btn btn-lg btn-info" data-answer="'.$Rget["a_id"].'" data-link="1" target="_BLANK">'.$Rget["a_text"].'</a>';
					else $answers_gourp .= '<span class="span_answer btn btn-lg btn-info" data-answer="'.$Rget["a_id"].'" data-link="0">'.$Rget["a_text"].'</span>';
				}
			}
		}
		
		if($answers_gourp){
			$arr_msg["content"] .= '
			<div class="row block_answers">
				<div class="bot_block_center col-12">
					<div class="bot_msg">
						<div class="answers_gourp">'.$answers_gourp.'</div>
					</div>
				</div>
			</div>';
		}
	}
	
	exit(json_encode($arr_msg));
}
?>