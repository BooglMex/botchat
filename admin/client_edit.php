<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

if($_SESSION['auth_admin'] != 'yes_auth'){
	header("Location: login.php");
	exit();
}

if(isset($_GET["logout"])){
    unset($_SESSION['auth_admin']);
	header("Location: login.php");
	exit();
}

$id = $_GET["id"];
$Q = mysql_query("SELECT * FROM clients WHERE cl_id=$id");
if(mysql_num_rows($Q) == 1) $R = mysql_fetch_array($Q);
else{
    header("Location: clients.php");
	exit();
}

$MB = 1024*1024;

if(isset($_POST["form_submit"])){
	$error = array();
	
	// Принятие информации
    $form_name = clear_string($_POST["form_name"]);
    $form_email = clear_string($_POST["form_email"]);
    $form_pass = clear_string($_POST["form_pass"]);
    $form_pass_repeat = clear_string($_POST["form_pass_repeat"]);
	
	// Проверка полей
	if(!strlen($form_name)) $error[] = "Укажите название бота";
	if(filter_var($form_email, FILTER_VALIDATE_EMAIL) === false) $error[] = "Укажите E-mail в правильном формате";
	else{
		$Qcheck = mysql_query("SELECT cl_email FROM clients WHERE cl_email='$form_email' AND cl_id<>$id");
		if(mysql_num_rows($Qcheck)) $error[] = "Данный E-mail уже существует. Пожалуйста укажите другую почту.";
	}
	if(strlen($form_pass)){
		if($form_pass != $form_pass_repeat) $error[] = "Укажите пароль подтверждения правильно";
		else{
			$form_pass = strrev(md5($form_pass)); // Шифровка пароля
			$Qadd = ", cl_pass='$form_pass' ";
		}
	}
	
	if(count($error)) $msgtext = '<div class="alert-danger">'.implode('<br>',$error).'</div>';
	else{
		mysql_query("UPDATE clients SET cl_fio='$form_name', cl_admin=$form_parent $Qadd WHERE cl_id=$id", $link);
		$msgtext .= '<div class="alert-success">Успешно обновлено!</div>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Изменение клиента</title>
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
                    <div class="col-md-8 offset-md-2">
                        <h4 class="page-title">Изменение клиента</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Админ-панель</a></li>
                            <li class="breadcrumb-item"><a href="clients.php">Клиенты</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Изменение клиента</li>
                        </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-body">
                                <form id="personal-info" method="POST" enctype="multipart/form-data">
									<?php
									if(isset($_SESSION['msgtext'])){
										$msgtext .= $_SESSION['msgtext'];
										unset($_SESSION['msgtext']);
									}
									
									echo $msgtext;
									?>
                                    <h4 class="form-header">
                                        <i class="fa fa-file-text-o"></i>
                                        Заполните поля
                                    </h4>
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label">ФИО</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="form_name" value="<?php echo htmlchars($R["cl_fio"]); ?>" class="form-control" required>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label class="col-sm-12 col-form-label">E-mail</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="form_email" value="<?php echo htmlchars($R["cl_email"]); ?>" class="form-control" required>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label class="col-sm-12 col-form-label">Пароль</label>
                                        <div class="col-sm-12">
                                            <input type="password" name="form_pass" value="" class="form-control">
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label class="col-sm-12 col-form-label">Повторите пароль</label>
                                        <div class="col-sm-12">
                                            <input type="password" name="form_pass_repeat" value="" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-footer">
                                        <button type="submit" name="form_submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Сохранить</button>
                                    </div>
                                </form>
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