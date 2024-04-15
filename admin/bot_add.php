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

$MB = 1024*1024;

if(isset($_POST["form_submit"])){
	$error = array();
	
	// Принятие информации
    $form_name = clear_string($_POST["form_name"]);
    $form_parent = (int) $_POST['form_parent'];
	
	// Проверка полей
	if(!strlen($form_name)) $error[] = "Укажите название бота";
	if(!($form_parent > 0)) $error[] = "Прикрепите бота к админу";
	
	if(count($error)) $msgtext = '<div class="alert-danger">'.implode('<br>',$error).'</div>';
	else{
		mysql_query("INSERT INTO bots(bot_name, bot_admin) VALUES('$form_name', $form_parent)", $link);
		if(mysql_insert_id()){
			$id = mysql_insert_id();
			
			$_SESSION['msgtext'] .= '<div class="alert-success">Успешно добавлено!</div>';
			
			header("Location: bot_edit.php?id=$id");
			exit;
		}
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
    <title>Добавление бота</title>
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
                        <h4 class="page-title">Добавление бота</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Админ-панель</a></li>
                            <li class="breadcrumb-item"><a href="bots.php">Боты</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Добавление бота</li>
                        </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-body">
                                <form id="personal-info" method="POST" enctype="multipart/form-data">
                                   <?php echo $msgtext; ?>
                                    <h4 class="form-header">
                                        <i class="fa fa-file-text-o"></i>
                                        Заполните поля
                                    </h4>
                                    <div class="form-group">
                                        <label class="col-sm-12 col-form-label">Наименование бота</label>
                                        <div class="col-sm-12">
                                            <input type="text" name="form_name" value="<?php echo htmlchars($_POST["form_name"]); ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <select class="form-control" name="form_parent" required>
                                                <option value="0" disabled selected>Выберите админа</option>
                                                <?php
                                                $Qget = mysql_query("SELECT * FROM clients ORDER BY cl_fio");
                                                if(mysql_num_rows($Qget)){
                                                    while($Rget = mysql_fetch_array($Qget)){
                                                        $selected = ($Rget["cl_id"] == $_POST["form_parent"]) ? "selected" : "";
                                                        echo '<option value="'.$Rget["cl_id"].'" '.$selected.'>'.$Rget["cl_fio"].'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-footer">
                                        <button type="submit" name="form_submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Добавить</button>
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