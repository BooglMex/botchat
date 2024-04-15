<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

if($_SESSION['auth_client'] != 'yes_auth')
{
	header("Location: login.php");
	exit();
}

if(isset($_GET["logout"]))
{
	unset($_SESSION['auth_client']);
	header("Location: login.php");
	exit();
}

$id = $_GET["id"];
/* $Q = mysql_query("SELECT * FROM services WHERE serv_id=$id");
if(mysql_num_rows($Q)) $R=mysql_fetch_array($Q);
else{
    header("Location: services.php");
	exit();
} */

$MB = 1024*1024;

if(isset($_POST["form_submit"]))
{
    $error = array();

   // Принятие информации
    $form_name = clear_string($_POST["form_name"]);
    $form_descr = $_POST['form_descr'];
    $form_descr_mini = $_POST['form_descr_mini'];
    $form_parent = (int) $_POST['form_parent'];
    
    // Проверка полей
    if(!strlen($form_name)) $error[] = "Укажите название услуги";
    if(!$form_descr) $form_descr = "NULL";
    else $form_descr = "'$form_descr'";
    if(!$form_descr_mini) $form_descr_mini = "NULL";
    else $form_descr_mini = "'$form_descr_mini'";
    if($form_parent <= 0) $error[] = "Укажите категорию";

    if(count($error)) $msgtext = '<div class="alert-danger">'.implode('<br>',$error).'</div>';
    else
    {
        mysql_query("UPDATE services SET serv_name='$form_name', serv_descr=$form_descr, serv_descr_mini=$form_descr_mini, serv_parent=$form_parent WHERE serv_id=$id", $link);

        $_SESSION['msgtext'] .= '<div class="alert-success">Услуга успешно изменена!</div>';

        if(is_uploaded_file($_FILES['form_image']['tmp_name']))
        {
            $imgPrefiks = "";
            $uploaddir = 'images/services/'; // Папка для загрузки
            $queryAddImg1 = "UPDATE services SET serv_image='";
            $queryAddImg2 = "' WHERE serv_id=$id";
            $resImg = MyFuncUploadImage($_FILES['form_image'], (10 * $MB), $uploaddir, $queryAddImg1, $queryAddImg2, $imgPrefiks, 1600, false, 86);
            if(!($resImg === true || $resImg=1)) $_SESSION['msgtext'] .= '<div class="alert-success">'.$resImg.'</div>';
            else @unlink('images/services/'.$R['serv_image']);
            unset($_POST["form_image"]); // Очищаем картинку
        }
    }
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
	<title>Изменение услуги</title>
	<link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/sidebar-menu.css" rel="stylesheet" />
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
						<h4 class="page-title">Изменение услуги</h4>
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="/bot/">Главная</a></li>
							<li class="breadcrumb-item"><a href="services.php">Услуги</a></li>
							<li class="breadcrumb-item active" aria-current="page">Изменение услуги</li>
						</ol>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<div class="card">
							<div class="card-body">
								<?php
								if(count($error)) $msgtext = '<div class="alert-danger">'.implode('<br>',$error).'</div>';
								else
								{
									$q_pr = mysql_query("SELECT * FROM services WHERE serv_id=$id");
									if(mysql_num_rows($q_pr) == 1)
									{
										$r_pr = mysql_fetch_array($q_pr);

										if(isset($_SESSION['msgtext']))
										{
											$msgtext .= $_SESSION['msgtext'];
											unset($_SESSION['msgtext']);
										}

										echo '
										<form id="personal-info" method="POST" enctype="multipart/form-data">
											'.$msgtext.'
											<div class="form-group">
												<label class="col-md-12 col-form-label">Наименование услуги</label>
												<div class="col-sm-12">
													<input type="text" name="form_name" value="'.htmlchars($r_pr['serv_name']).'" class="form-control" required>
												</div>
											</div>
                                            
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <select class="form-control" name="form_parent" required>
                                                        <option value="0">Выберите категорию</option>';
                                                        $Q = mysql_query("SELECT * FROM categories ORDER BY cat_name");
                                                        if(mysql_num_rows($Q)){
                                                            $R=mysql_fetch_array($Q);
                                                            do{
                                                                if($r_pr["serv_parent"]==$R["cat_id"]) $selected="selected";
                                                                else $selected="";
                                                                echo '<option value="'.$R["cat_id"].'" '.$selected.'>'.$R["cat_name"].'</option>';
                                                            }
                                                            while($R=mysql_fetch_array($Q));
                                                        }
                                                        echo'
                                                    </select>
                                                </div>
                                            </div>';
											
											if(strlen($r_pr["serv_image"]) > 0 && file_exists("images/services/".$r_pr["serv_image"]) && $r_pr["serv_image"] != "no_image.png")
											{
												$img_path = 'images/services/'.$r_pr["serv_image"];
												$max_width = 160;
												$max_height = 160;
												list($width, $height) = getimagesize($img_path);
												$ratioh = $max_height/$height;
												$ratiow = $max_width/$width;
												$ratio = min($ratioh, $ratiow);
												// Новые размеры
												$width = intval($ratio*$width);
												$height = intval($ratio*$height);

												echo '
												<div class="form-group">
													<div class="col-md-6 offset-md-3">
														<div class="card">
															<img src="'.$img_path.'" class="card-img-top">
														</div>
													</div>
													<div class="col-sm-12">
														<input type="file" name="form_image" class="form-control">
													</div>
												</div>';
											}
											
											echo '
                                            <div class="form-group">
                                                <label class="col-sm-2 col-form-label">Мини-описание</label>
                                                <div class="col-sm-12">
                                                    <textarea class="form-control" rows="4" name="form_descr_mini" required>'.htmlchars($r_pr['serv_descr_mini']).'</textarea>
                                                </div>
                                            </div>
											
											<div class="form-group">
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-header text-uppercase">Полное описание</div>
                                                        <div class="card-body">
															<textarea id="editor" class="editorClass" name="form_descr" cols="100" rows="20">'.$r_pr["serv_descr"].'</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
											<div class="form-footer">
												<button type="submit" name="form_submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Сохранить</button>
											</div>
										</form>';
									}
									else echo '<div class="msg_error margins">Категория не найдена или защищена от редактирования!</div>';
								}
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
	<script src="assets/plugins/ckEditor/ckeditor.js" type="text/javascript"></script>
	<script src="js/main.js" type="text/javascript"></script>
</body>
</html>