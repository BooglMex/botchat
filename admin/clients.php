<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

$file = basename($_SERVER['PHP_SELF']);

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

$MB = 1024*1024;

/* if($_GET["action"]=="delete")
{
   $Q = mysql_query("SELECT * FROM categories WHERE cl_id=$id");
   if(mysql_num_rows($Q)){
       $R=mysql_fetch_array($Q);
       
       $Q2 = mysql_query("SELECT * FROM services WHERE serv_parent=$id");
       if(mysql_num_rows($Q2)){
           $R2=mysql_fetch_array($Q2);
           do{
               mysql_query("DELETE FROM services WHERE serv_id=".$R2["serv_id"]);
               @unlink('images/services/'.$R2['serv_image']);
           }
           while($R2=mysql_fetch_array($Q2));
       }
       
       mysql_query("DELETE FROM categories WHERE cl_id=$id");
       @unlink('images/categories/'.$R['cl_image']);
   }
    
    header("Location: ".$_SERVER["HTTP_REFERER"]);
	exit();
} */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Клиенты</title>
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
                    <div class="col-sm-9">
                        <h4 class="page-title">Клиенты</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Админ-панель</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Клиенты</li>
                        </ol>
                    </div>

                    <div class="col-sm-3">
                        <div class="btn-group float-sm-right">
                            <a href="client_add.php" class="btn btn-light waves-effect waves-light"><i class="fa fa-plus-square"></i> <span> Добавить</span></a>
                        </div>
                    </div>
                </div>

                <?php
                $num = 40; // Количество выводимых результатов на одной странице
                $temp = mysql_fetch_array( mysql_query("SELECT COUNT(cl_id) FROM clients $filtr",$link) );
                $pr_kolvo = $temp[0];
                if($pr_kolvo > 0){
                    $total = intval( (($pr_kolvo - 1) / $num) + 1 );
                    // Определяем начало сообщений для текущей страницы
                    $page = strip_tags($_GET['page']);              
                    $page = mysql_real_escape_string($page); // Функция экранирует специальные символы для защиты запроса
                    $page = intval($page);
                    // Если значение $page меньше единицы или отрицательно переходим на первую страницу, а если слишком большое, то переходим на последнюю
                    if(empty($page) or $page < 0) $page = 1;
                    if($page > $total) $page = $total;
                    // Вычисляем начиная с какого номера следует выводить информацию
                    $start = $page * $num - $num;
                }
                else $start = 0;

                $sort = "ORDER BY cl_fio";

                $query_categories = mysql_query("SELECT * FROM clients $filtr $sort LIMIT $start, $num",$link);
                if(mysql_num_rows($query_categories) > 0){
                    echo '<div class="row">';
					
                    while($row_prs = mysql_fetch_array($query_categories)){
                        echo '
                        <div class="col-12 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-center">'.$row_prs["cl_fio"].'</h4>
                                    <hr>
                                    <div class="text-center">
										<a href="client_edit.php?id='.$row_prs["cl_id"].'" class="btn btn-light waves-effect waves-light m-1"><i class="fa fa-edit text-success"></i> <span> Изменить</span></a>
										<a href="'.$file.'?id='.$row_prs["cl_id"].'&action=delete" class="btn btn-light waves-effect waves-light m-1"><i class="fa fa fa-trash-o text-danger"></i> <span> Удалить</span></a>
									</div>
                                </div>
                            </div>
                        </div>';
                    }
					
                    echo '</div>';
                }
                else echo '<p class="paddings center">Нет результатов</p>';
                ?>
            </div>
        </div>

        <!--start overlay-->
        <div class="overlay toggle-menu"></div>
        <!--end overlay-->
		
		 <!--Start Back To Top Button-->
		<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
		<!--End Back To Top Button-->

		<?php include("includes/footer.php"); ?>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/plugins/apexcharts/apexcharts.js"></script>
    <script src="assets/js/dashboard-digital-marketing.js"></script>
    <script src="assets/js/app-script.js"></script>
	<script src="js/main.js"></script>
</body>
</html>