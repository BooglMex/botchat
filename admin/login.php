<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

if(isset($_POST["submit_enter"]))
{
	$login = clear_string($_POST["input_login"]);
	$pass  = clear_string($_POST["input_pass"]);
	
	if($login && $pass)
	{
		$pass_code = md5($pass); // Шифровка пароля
		$pass_code = strrev($pass_code); // Меняем порядок символов (переворачиваем)
		
		$Qadmin = mysql_query("SELECT * FROM admins WHERE a_login='$login' AND a_pass='$pass_code'",$link);
		if(mysql_num_rows($Qadmin) == 1)
		{
			$Radmin = mysql_fetch_array($Qadmin);
			
			$_SESSION['auth_admin'] = 'yes_auth';
			$_SESSION['a_id'] = $Radmin["a_id"];
			
			header("Location: /admin/");
			exit();
		}
		else $msgerror .= "<p>Неверный Логин и(или) Пароль.</p>";
	}
	else $msgerror .= "<p>Заполните все поля!</p>";
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
    <title>Вход</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="assets/css/app-style.css" rel="stylesheet" />

</head>

<body class="bg-theme bg-theme2">

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

        <div class="loader-wrapper">
            <div class="lds-ring">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="card card-authentication1 mx-auto my-5">
           <?php if($msgerror) echo '<div class="alert-danger text-center">'.$msgerror.'</div>'; ?>
            <div class="card-body">
                <div class="card-content p-2">
                    <div class="text-center">
                        <img src="assets/images/favicon.ico" alt="logo icon">
                    </div>
                    <div class="card-title text-uppercase text-center py-3">Вход</div>
                    <form method="POST">
                        <div class="form-group">
                            <label for="exampleInputUsername" class="sr-only">Логин</label>
                            <div class="position-relative has-icon-right">
                                <input type="text" name="input_login" id="exampleInputUsername" class="form-control input-shadow" placeholder="Логин" value="<?php echo $_POST["input_login"] ?>" required>
                                <div class="form-control-position">
                                    <i class="icon-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword" class="sr-only">Пароль</label>
                            <div class="position-relative has-icon-right">
                                <input type="password" name="input_pass" id="exampleInputPassword" class="form-control input-shadow" placeholder="Пароль" value="<?php echo $_POST["input_pass"] ?>" required>
                                <div class="form-control-position">
                                    <i class="icon-lock"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="submit_enter" class="btn btn-light btn-block">Вход</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <!--Start Back To Top Button-->
        <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>


        <!-- Bootstrap core JavaScript-->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/plugins/simplebar/js/simplebar.js"></script>
        <script src="assets/js/sidebar-menu.js"></script>
        <script src="assets/plugins/apexcharts/apexcharts.js"></script>
        <script src="assets/js/dashboard-digital-marketing.js"></script>
        <script src="assets/js/app-script.js"></script>

</body>

</html>
