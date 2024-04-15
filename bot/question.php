<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

if($_SESSION['auth_client'] != 'yes_auth'){
	header("Location: login.php");
	exit();
}

if(isset($_GET["logout"])){
	unset($_SESSION['auth_client']);
	header("Location: login.php");
	exit();
}

$id = $_GET["id"];
$Q = mysql_query("SELECT * FROM questions WHERE q_id=$id AND q_client='{$_SESSION["client_id"]}'");
if(mysql_num_rows($Q)){
	$R = mysql_fetch_array($Q);
	$bot = $R["q_bot"];
}
else{
	if($_SERVER["HTTP_REFERER"]) header("Location: ".$_SERVER["HTTP_REFERER"]);
	else header("Location: /bot/");
	exit();
}

$arr_parent_answers = array();
$Qget = mysql_query("SELECT * FROM question_answer WHERE qa_question=$id");
if(mysql_num_rows($Qget)){
	while($Rget = mysql_fetch_array($Qget)){
		$arr_parent_answers[] = $Rget["qa_answer"];
	}
}

// Выводить выбор главного вопроса, если у данного бота ещё не установлен главный вопрос
$Qcheck = mysql_query("SELECT * FROM questions WHERE q_bot='$bot' AND q_client='{$_SESSION["client_id"]}' AND q_main=1 AND q_id<>$id");
if(!mysql_num_rows($Qcheck)){
	if($R["q_main"] == 1){
		$checked = "checked";
		$hide_question_answer_selectors = 'none';
	}
	else{
		$checked = "";
		$hide_question_answer_selectors = '';
	}
	
	$set_as_first = '
	<div class="col-12">
		<label><input type="checkbox" id="set_as_first" data-hide="hide_question_answer_selectors" '.$checked.'> Сделать стартовым вопросом</label>
	</div>';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<title>Редактор вопроса</title>
	<link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/sidebar-menu.css" rel="stylesheet" />
	<link rel="stylesheet" href="assets/plugins/summernote/dist/summernote-bs4.css" />
	<link href="assets/css/app-style.css" rel="stylesheet" />
</head>
<body class="bg-theme bg-theme2">

	<!-- start loader -->
	<div id="pageloader-overlay" class="visible incoming">
		<div class="loader-wrapper-outer">
			<div class="loader-wrapper-inner">
				<div class="loader"></div>
			</div>
		</div>
	</div>
	<!-- end loader -->

	<!-- Start wrapper-->
	<div id="wrapper">
		<?php include("includes/sidebar.php"); ?>

		<?php include("includes/header.php"); ?>

		<div class="clearfix"></div>

		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row pt-2 pb-2">
					<div class="col-12">
						<h4 class="page-title">Редактор вопроса</h4>
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="/bot/">Главная</a></li>
							<li class="breadcrumb-item"><a href="questions.php?bot=<?php echo $bot; ?>">Вопросы</a></li>
							<li class="breadcrumb-item active" aria-current="page">...</li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<div class="card">
							<div class="card-body">
								<div class="msg none"></div>
								
								<?php
								echo '
								<form method="POST" enctype="multipart/form-data" action="javascript:void(0)" onsubmit="return false;" >
									<div class="form-group">
										<label class="col-md-12 col-form-label">Заголовок вопроса</label>
										<div class="col-sm-12">
											<input type="text" id="q_title" value="'.htmlchars($R['q_title']).'" class="form-control" required>
										</div>
									</div>
									
									'.$set_as_first.'
									
									<div class="form-group hide_question_answer_selectors '.$hide_question_answer_selectors.'">
										<label class="col-md-12 col-form-label">Выберите вопрос</label>
										<div class="col-md-12">
											<select class="form-control" id="select_bot_questions">
												<option value="0" selected disabled>Выберите вопрос</option>';
                                                    $Qget = mysql_query("SELECT * FROM questions WHERE q_client='{$_SESSION["client_id"]}' AND q_bot='$bot' AND q_id<>$id ORDER BY q_title");
                                                    if(mysql_num_rows($Qget)){
                                                        while($Rget = mysql_fetch_array($Qget)){
															echo '<option value="'.$Rget["q_id"].'">'.$Rget["q_title"].'</option>';
														}
                                                    }
                                                    echo'
											</select>
										</div>
									</div>
									
									<div class="form-group hide_question_answer_selectors '.$hide_question_answer_selectors.'">
										<label class="col-md-12 col-form-label">Выберите связанные ответы</label>
										<div class="col-md-12">
											<select class="form-control" id="select_bot_answers">
												<option value="0" selected disabled>Выберите связанные ответы</option>';
                                                    $Qget = mysql_query("SELECT * FROM answers WHERE a_bot='$bot' AND a_question<>$id AND a_id NOT IN(SELECT qa_answer FROM question_answer WHERE qa_question<>$id) ORDER BY a_text");
                                                    if(mysql_num_rows($Qget)){
                                                        while($Rget = mysql_fetch_array($Qget)){
															if(in_array($Rget["a_id"], $arr_parent_answers)){
																$data_selected = 1;
																$span_selected_answer .= '<span class="span_selected span_selected_answer" data-id="'.$Rget["a_id"].'">'.htmlspecialchars($Rget["a_text"]).' <i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer span_deselect deselected_answer" title="Удалить"></i></span>';
															}
															else $data_selected = 0;
															echo '<option value="'.$Rget["a_id"].'" class="none" data-question="'.$Rget["a_question"].'" data-selected="'.$data_selected.'">'.$Rget["a_text"].'</option>';
														}
                                                    }
                                                    echo'
											</select>
										</div>
									</div>
									
									<div id="div_selected_answers" class="col-12 hide_question_answer_selectors '.$hide_question_answer_selectors.'">'.$span_selected_answer.'</div>
									
									
									<div id="wrapper_elements_block">';
									$Qres = mysql_query("SELECT * FROM bot_sequence WHERE bsq_bot='$bot' AND bsq_question='$id' AND bsq_type IN(1,2,3)");
									if(mysql_num_rows($Qres)){
										while($Rraw = mysql_fetch_array($Qres)){
											$item_id = $Rraw["bsq_id"];
											
											if($Rraw["bsq_type"] == 1){
												$Qget = mysql_query("SELECT * FROM iframes WHERE ifr_bot='$bot' AND ifr_question='$id' AND ifr_id='$item_id'");
												if(mysql_num_rows($Qget)){
													$Rget = mysql_fetch_array($Qget);
													echo '
													<div>
														<hr>
														<div class="form-group">
															<label class="col-12 col-form-label move_cursor">Вставьте код <span class="rfloat"><i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer delete_iframe" title="Удалить"></i></span></label>
															<div class="col-sm-12">
																<textarea class="form-control bot_iframe" rows="4" data-id="'.$Rget["ifr_id"].'" required>'.$Rget["ifr_content"].'</textarea>
															</div>
														</div>
													</div>';
												}
											}
											elseif($Rraw["bsq_type"] == 2){
												$Qget = mysql_query("SELECT * FROM question_content WHERE qc_bot='$bot' AND qc_question='$id' AND qc_id='$item_id'");
												if(mysql_num_rows($Qget)){
													$Rget = mysql_fetch_array($Qget);
													echo '
													<div>
														<hr>
														<div class="form-group">
															<label class="col-12 col-form-label move_cursor">Введите текст <span class="rfloat"><i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer delete_editor" title="Удалить"></i></span></label>
															<div class="col-sm-12">
																<textarea class="summerNoteEditor bot_editor" id="new_editor_numb__'.$Rget["qc_id"].'" data-id="'.$Rget["qc_id"].'" required>'.$Rget["qc_text"].'</textarea>
															</div>
														</div>
													</div>';
												}
											}
											elseif($Rraw["bsq_type"] == 3){
												
											}
										}
									}
									echo '
									</div>
									
									
									<hr>
									
									<p class="text-center mt10"><span class="pointer text-info" data-toggle="modal" data-target="#modal_NewElement"><i class="icon-plus icons"></i> Добавить новый элемент</span></p>
									
									<hr>
									
									<h5 class="text-center">Варианты ответов</h5>
									
									<div id="div_answers" class="col-12">';
									$Qres = mysql_query("SELECT * FROM bot_sequence WHERE bsq_bot='$bot' AND bsq_question='$id' AND bsq_type=4");
									if(mysql_num_rows($Qres)){
										while($Rraw = mysql_fetch_array($Qres)){
											$item_id = $Rraw["bsq_id"];
											
											$Qget = mysql_query("SELECT * FROM answers WHERE a_bot='$bot' AND a_question=$id AND a_id='$item_id'");
											if(mysql_num_rows($Qget)){
												$Rget = mysql_fetch_array($Qget);
												$a_link = ($Rget["a_link"]) ? '<i aria-hidden="true" class="fa fa-external-link text-info" title="Ссылка"></i>' : '';
												echo '
												<span class="span_selected span_answer" data-id="'.$Rget["a_id"].'" data-toggle="modal" data-target="#modal_answer">
													<span rel="'.$Rget["a_link"].'">'.htmlspecialchars($Rget["a_text"]).'</span>
													'.$a_link.'
													<i aria-hidden="true" class="fa fa-fw fa-edit text-success" title="Изменить"></i>
													<i aria-hidden="true" class="fa fa-fw fa-times text-danger pointer span_deselect delete_answer" title="Удалить"></i>
												</span>';
											}
										}
									}
									echo '
									</div>
									
									<p class="text-center mt10"><span id="add_new_answer" class="pointer text-info" data-toggle="modal" data-target="#modal_answer"><i class="icon-plus icons"></i> Добавить новый ответ</span></p>
									
									<div class="form-footer">
										<button type="submit" id="question_save" class="btn btn-success" data-id="'.$id.'"><i class="fa fa-check-square-o"></i> Сохранить</button>
									</div>
								</form>';
								?>
							</div>
						</div>
					</div>
				</div>

				<!--start overlay-->
				<div class="overlay toggle-menu"></div>
				<!--end overlay-->
			</div>
			<!-- End container-fluid-->
		</div>

		<!--Start Back To Top Button-->
		<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
		<!--End Back To Top Button-->

		<?php include("includes/footer.php"); ?>
	</div>
	<!--End wrapper-->
	
	<!--Modal-->
	<div class="modal fade" id="modal_answer">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Текст ответа</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="javascript:void(0)" onsubmit="return false;">
					<div class="form-group">
						<input type="text" class="form-control" id="text_answer" placeholder="Введите текст ответа" required>
					</div>
					<div class="form-group">
						<label><small title="для формирования реферальной ссылки в месте, где должен быть ID, нужно вставить '__REFERAL__'">*?</small></label>
						<input type="text" class="form-control" id="text_answer_link" placeholder="Ссылка ответа">
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary px-5" id="save_answer" data-id="0" data-question="<?php echo $id; ?>"><i class="icon-lock"></i> Сохранить</button>
					</div>
				</form>
			</div>
			</div>
		</div>
    </div>
	
	<!--New Element-->
	<div class="modal fade" id="modal_NewElement">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Выберите тип элемента</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="javascript:void(0)" onsubmit="return false;">
					<div class="form-group">
						<div class="col-md-12 div_radio_inputs">
							<div><label><input type="radio" name="type_NewElement" class="type_NewElement" value="editor" checked> Редактор</label></div>
							<div><label><input type="radio" name="type_NewElement" class="type_NewElement" value="iframe"> IFRAME</label></div>
							<div><label><input type="radio" name="type_NewElement" class="type_NewElement" value="gallery"> Галерея</label></div>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary px-5" id="select_NewElement" data-id="0" data-question="<?php echo $id; ?>"><i class="icon-lock"></i> Выбрать</button>
					</div>
				</form>
			</div>
			</div>
		</div>
    </div>


	<!-- Bootstrap core JavaScript-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="js/jQueryUI/jquery-ui.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.js"></script>
	<script src="assets/js/sidebar-menu.js"></script>
	<script src="assets/plugins/apexcharts/apexcharts.js"></script>
	<script src="assets/js/dashboard-digital-marketing.js"></script>
	<script src="assets/js/app-script.js"></script>
	<script src="assets/plugins/summernote/dist/summernote-bs4.min.js"></script>
	<script src="js/main.js"></script>
</body>
</html>