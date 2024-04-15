<?php
session_start();
include("settings.php");
include("includes/db_connect.php");
include("functions/functions.php");

$id = (int) $_GET["id"];
if(isset($_GET["ref"]) && strlen($_GET["ref"])){
	$ref = clear_probels($_GET["ref"]);
	$ref_link = "_".$ref;
}

$Qbot = mysql_query("SELECT * FROM bots WHERE bot_id='$id'");
if(mysql_num_rows($Qbot)) $Rbot = mysql_fetch_array($Qbot);
else{
	http_response_code(404);
	header("Location: 404.php");
	exit();
}

if($Rbot["bot_bg_index"] && file_exists("images/bots/banners/".$Rbot["bot_bg_index"])) $bg = 'background-image: url(images/bots/banners/'.$Rbot["bot_bg_index"].');';

if($Rbot["bot_favicon"] && file_exists("images/bots/favicons/".$Rbot["bot_favicon"])) $bot_favicon = '<link rel="icon" href="images/bots/favicons/'.$Rbot["bot_favicon"].'" type="image/x-icon">';
else $bot_favicon = '<link rel="icon" href="favicon.ico" type="image/x-icon">';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
	<META name="ROBOTS" content="noindex,nofollow">
    <title><?php echo $Rbot["bot_name"]; ?></title>
	<?php echo $bot_favicon; ?>
    <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/sidebar-menu.css" rel="stylesheet" />
    <link href="assets/css/app-style.css" rel="stylesheet" />
</head>
<body class="bg_index" data-bot="<?php echo $id; ?>" data-answer="0" style="<?php echo $bg; ?>">
	<div class="container">
		<div class="row">
			<div id="index_start_button">
				<p class="text-center">
					<a href="chat<?php echo $id.$ref_link; ?>" class="btn btn-lg btn-info">Начать</a>
				</p>
			</div>
		</div>
    </div>
	
	<script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/simplebar/js/simplebar.js"></script>
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/app-script.js"></script>
	<script src="js/main.js?v=<?php echo VERSION; ?>"></script>
</body>
</html>