<?php
$Qadmin = mysql_query("SELECT * FROM clients WHERE cl_id='{$_SESSION['client_id']}'",$link);
if(mysql_num_rows($Qadmin) == 1){
	$Radmin = mysql_fetch_array($Qadmin);
	
	$_SESSION['auth_client'] = 'yes_auth';
	$_SESSION['client_id'] = $Radmin["cl_id"];
}
else{
	unset($_SESSION['auth_client']);
	header("Location: login.php");
	exit();
}
?>

<header class="topbar-nav">
    <nav class="navbar navbar-expand fixed-top">
        <ul class="navbar-nav mr-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link toggle-menu" href="javascript:void();">
                    <i class="icon-menu menu-icon"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav align-items-center right-nav-link">
            
            <li class="nav-item">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                    <span class="user-profile"><img src="assets/images/avatar.jpg" class="img-circle" alt="user avatar"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="dropdown-item user-details">
                        <a href="javaScript:void();">
                            <div class="media">
                                <div class="avatar"><img class="align-self-start mr-3" src="assets/images/avatar.jpg" alt="user avatar"></div>
                                <div class="media-body">
                                    <h6 class="mt-2 user-title"><?php echo $Radmin["cl_fio"]; ?></h6>
                                    <p class="user-subtitle"><?php echo $Radmin["cl_email"]; ?></p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <!--<li class="dropdown-divider"></li>
                    <li class="dropdown-item"><i class="icon-user mr-2"></i> Аккаунт</li>
                    <li class="dropdown-divider"></li>
                    <li class="dropdown-item"><i class="icon-settings mr-2"></i> Настройки</li>
                    <li class="dropdown-divider"></li>-->
                    <li class="dropdown-item"><i class="icon-power mr-2"></i><a href="?logout"> Выход</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>