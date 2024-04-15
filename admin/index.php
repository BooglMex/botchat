<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

if($_SESSION['auth_admin'] != 'yes_auth')
{
	header("Location: login.php");
	exit();
}

if(isset($_GET["logout"]))
{
    unset($_SESSION['auth_admin']);
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
    <title>Админ-панель</title>
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

                <div class="row mt-3">
                    <div class="col-12 col-lg-6 col-xl-6">
                        <div class="row">
                            <div class="col-12 col-lg-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <p>Website Sessions</p>
                                        <h3>652.9K</h3>
                                        <p class="mb-0">72% <span class="float-right">500k</span></p>
                                    </div>
                                    <div class="progress-wrapper">
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-white" role="progressbar" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <p>Twitter Followers</p>
                                        <h3>8,256K</h3>
                                        <p class="mb-0">2.5% Increase</p>
                                    </div>
                                    <div id="twitter-followers"></div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body text-center px-0">
                                        <h6 class="text-uppercase">Total Visitors</h6>
                                        <div class="chart-container-10 d-flex align-items-center justify-content-center">
                                            <div id="total-visitors"></div>
                                        </div>
                                    </div>
                                    <div class="card-footer border-0">
                                        <div class="row align-items-center text-center">
                                            <div class="col border-right border-light">
                                                <h5 class="mb-0">563</h5>
                                                <small class="extra-small-font">Last Week</small>
                                            </div>
                                            <div class="col">
                                                <h5 class="mb-0">985</h5>
                                                <small class="extra-small-font">Last Month</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </div>


                    <div class="col-12 col-lg-6 col-xl-6">
                        <div class="row">
                            <div class="col-12 col-lg-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <p>Facebook Pageviews</p>
                                        <h3>35.7K</h3>
                                        <div id="facebook-pageviews"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6 col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <p>Bounce Rate</p>
                                        <h3>82.3%</h3>
                                        <div id="bounce-rate"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-12 col-xl-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="sr-only">List Subscribers</p>
                                        <div id="list-subscribers"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </div>

                </div>
                <!--end row-->


                <div class="row">
                    <div class="col-12 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-uppercase mb-3">Goal Completion by channel</h6>
                                <div class="mt-1">
                                    <div id="direct"></div>
                                </div>
                                <div class="mt-1">
                                    <div id="organic-search"></div>
                                </div>
                                <div class="mt-1">
                                    <div id="referral"></div>
                                </div>
                                <div class="mt-1">
                                    <div id="social"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-6">
                        <div class="card-group flex-column flex-md-row">
                            <div class="card border-right border-light mb-0">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Newsletter Open Rate</h6>
                                    <div class="chart-container-10 d-flex align-items-center justify-content-center">
                                        <div id="newsletter-open-rate"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-0">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Click Through Rate</h6>
                                    <div class="chart-container-10 d-flex align-items-center justify-content-center">
                                        <div id="click-through-rate"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
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

</body>

</html>