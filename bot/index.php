<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

$file = basename($_SERVER['PHP_SELF']);

if($_SESSION['auth_client'] != 'yes_auth'){
	header("Location: login.php");
	exit();
}

if(isset($_GET["logout"])){
	unset($_SESSION['auth_client']);
	header("Location: login.php");
	exit();
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
    <title>Боты</title>
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
                        <h4 class="page-title">Боты</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/bot/">Главная</a></li>
                        </ol>
                    </div>
                </div>

                <?php
                $num = 40; // Количество выводимых результатов на одной странице
                $temp = mysql_fetch_array( mysql_query("SELECT COUNT(bot_id) FROM bots WHERE bot_admin='{$_SESSION["client_id"]}'",$link) );
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

                $sort = "ORDER BY bot_name";

                $query_categories = mysql_query("SELECT * FROM bots WHERE bot_admin='{$_SESSION["client_id"]}' $sort LIMIT $start, $num",$link);
                if(mysql_num_rows($query_categories) > 0) {
                    echo '<div class="row">';
					
                    while($row_prs = mysql_fetch_array($query_categories)){
						echo '
                        <div class="col-12 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-center">'.$row_prs["bot_name"].'</h4>
                                    <hr>
                                    <div class="text-center">
										<a href="questions.php?bot='.$row_prs["bot_id"].'" class="btn btn-light waves-effect waves-light m-1"><i class="fa fa-edit text-success"></i> <span> Редактировать</span></a>
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
    
	
	<!-- Modal -->
	<div class="modal fade" id="botsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="botsModalLabel">Подтвердите действие</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<div class="modal-body">
				После удаления бота, восстановление данных не возможно!
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
				<button type="button" class="btn btn-primary">ОК</button>
			</div>
		</div>
		</div>
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