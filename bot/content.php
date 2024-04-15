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

$id = (int) $_GET["id"];
$Q = mysql_query("SELECT * FROM bots WHERE bot_id=$id AND bot_admin='{$_SESSION["client_id"]}'");
if(mysql_num_rows($Q)) $R = mysql_fetch_array($Q);
else{
    header("Location: login.php");
	exit();
}

if(isset($_POST["form_submit"])){
    $error = array();
	
	if(is_uploaded_file($_FILES['form_bot_avatar']['tmp_name'])) {
        $imgPrefiks = "";
        $uploaddir = '../images/bots/avatars/'; // Папка для загрузки
        $queryAddImg1 = "UPDATE bots SET bot_avatar='";
        $queryAddImg2 = "' WHERE bot_id=$id AND bot_admin='{$_SESSION["client_id"]}'";
        $resImg = MyFuncUploadImage($_FILES['form_bot_avatar'], (10 * $MB), $uploaddir, $queryAddImg1, $queryAddImg2, $imgPrefiks, 100, false, 90);
        if(!($resImg === true || $resImg=1)) $msgtext .= '<div class="alert-success">'.$resImg.'</div>';
        else @unlink('../images/bots/avatars/'.$R['bot_avatar']);
        unset($_POST["form_bot_avatar"]); // Очищаем картинку
    }
	
	if(is_uploaded_file($_FILES['form_bot_favicon']['tmp_name'])) {
        $imgPrefiks = "";
        $uploaddir = '../images/bots/favicons/'; // Папка для загрузки
        $queryAddImg1 = "UPDATE bots SET bot_favicon='";
        $queryAddImg2 = "' WHERE bot_id=$id AND bot_admin='{$_SESSION["client_id"]}'";
        $resImg = MyFuncUploadImage($_FILES['form_bot_favicon'], (10 * $MB), $uploaddir, $queryAddImg1, $queryAddImg2, $imgPrefiks, 100, false, 90);
        if(!($resImg === true || $resImg=1)) $msgtext .= '<div class="alert-success">'.$resImg.'</div>';
        else @unlink('../images/bots/favicons/'.$R['bot_favicon']);
        unset($_POST["form_bot_favicon"]); // Очищаем картинку
    }
	
	if(is_uploaded_file($_FILES['form_bot_bg_chat']['tmp_name'])) {
        $imgPrefiks = "";
        $uploaddir = '../images/bots/bg/'; // Папка для загрузки
        $queryAddImg1 = "UPDATE bots SET bot_bg_chat='";
        $queryAddImg2 = "' WHERE bot_id=$id AND bot_admin='{$_SESSION["client_id"]}'";
        $resImg = MyFuncUploadImage($_FILES['form_bot_bg_chat'], (10 * $MB), $uploaddir, $queryAddImg1, $queryAddImg2, $imgPrefiks, 1920, false, 90);
        if(!($resImg === true || $resImg=1)) $msgtext .= '<div class="alert-success">'.$resImg.'</div>';
        else @unlink('../images/bots/bg/'.$R['bot_bg_chat']);
        unset($_POST["form_bot_bg_chat"]); // Очищаем картинку
    }
	
	if(is_uploaded_file($_FILES['form_bot_bg_index']['tmp_name'])) {
        $imgPrefiks = "";
        $uploaddir = '../images/bots/banners/'; // Папка для загрузки
        $queryAddImg1 = "UPDATE bots SET bot_bg_index='";
        $queryAddImg2 = "' WHERE bot_id=$id AND bot_admin='{$_SESSION["client_id"]}'";
        $resImg = MyFuncUploadImage($_FILES['form_bot_bg_index'], (10 * $MB), $uploaddir, $queryAddImg1, $queryAddImg2, $imgPrefiks, 1920, false, 90);
        if(!($resImg === true || $resImg=1)) $msgtext .= '<div class="alert-success">'.$resImg.'</div>';
        else @unlink('../images/bots/banners/'.$R['bot_bg_index']);
        unset($_POST["form_bot_bg_index"]); // Очищаем картинку
    }
	
	// Обновляем данные после сохранения
	$Q = mysql_query("SELECT * FROM bots WHERE bot_id=$id AND bot_admin='{$_SESSION["client_id"]}'");
	if(mysql_num_rows($Q)) $R = mysql_fetch_array($Q);
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
	<title>Контент бота</title>
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
						<h4 class="page-title">Контент бота</h4>
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="/bot/">Боты</a></li>
							<li class="breadcrumb-item"><a href="questions.php?bot=<?php echo $id; ?>">Бот</a></li>
							<li class="breadcrumb-item active" aria-current="page">...</li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<div class="card">
							<div class="card-body">
								<?php
								echo '
								<form id="personal-info" method="POST" enctype="multipart/form-data">
									'.$msgtext.'
									
									<div class="form-group">
										<h6 class="text-center">Аватар бота</h6>';
									if($R["bot_avatar"] && file_exists("../images/bots/avatars/".$R["bot_avatar"])){
										echo '
										<div class="col-md-2 offset-md-5">
											<div class="mb20">
												<img src="../images/bots/avatars/'.$R["bot_avatar"].'" class="card-img-top">
											</div>
										</div>';
									}
									echo '
										<div class="col-sm-12">
											<input type="file" name="form_bot_avatar" class="form-control">
										</div>
									</div>
									
									<hr>
									
									<div class="form-group">
										<h6 class="text-center">FAVICON</h6>';
									if($R["bot_favicon"] && file_exists("../images/bots/favicons/".$R["bot_favicon"])){
										echo '
										<div class="col-md-2 offset-md-5">
											<div class="mb20">
												<img src="../images/bots/favicons/'.$R["bot_favicon"].'" class="card-img-top">
											</div>
										</div>';
									}
									echo '
										<div class="col-sm-12">
											<input type="file" name="form_bot_favicon" class="form-control">
										</div>
									</div>
									
									<hr>
									
									<div class="form-group">
										<h6 class="text-center">Фон диалогового окна (чата)</h6>';
									if($R["bot_bg_chat"] && file_exists("../images/bots/bg/".$R["bot_bg_chat"])){
										echo '
										<div class="col-md-6 offset-md-3">
											<div class="card">
												<img src="../images/bots/bg/'.$R["bot_bg_chat"].'" class="card-img-top">
											</div>
										</div>';
									}
									echo '
										<div class="col-sm-12">
											<input type="file" name="form_bot_bg_chat" class="form-control">
										</div>
									</div>
									
									<hr>
									
									<div class="form-group">
										<h6 class="text-center">Фон главной страницы</h6>';
									if($R["bot_bg_index"] && file_exists("../images/bots/banners/".$R["bot_bg_index"])){
										echo '
										<div class="col-md-6 offset-md-3">
											<div class="card">
												<img src="../images/bots/banners/'.$R["bot_bg_index"].'" class="card-img-top">
											</div>
										</div>';
									}
									echo '
										<div class="col-sm-12">
											<input type="file" name="form_bot_bg_index" class="form-control">
										</div>
									</div>
									
                                    <div class="form-footer text-center">
										<button type="submit" name="form_submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Сохранить</button>
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


	<!-- Bootstrap core JavaScript-->
	<script src="assets/js/jquery.min.js"></script>
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