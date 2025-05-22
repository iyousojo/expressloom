<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
    // Redirect to login.php if the user is not logged in
	if(!isset($_SESSION['login_id'])) {
	    header('location:login.php'); // Updated to login.php
        exit();
    }
    include 'db_connect.php';
    ob_start();
    if(!isset($_SESSION['system'])){
        $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
        foreach($system as $k => $v){
            $_SESSION['system'][$k] = $v;
        }
    }
    ob_end_flush();
	include 'header.php' 
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include 'topbar.php' ?>
        <?php include 'sidebar.php' ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                    </div>
                    <hr class="border-primary">
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php 
                        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
                        if(!file_exists($page.".php")){
                            include '404.html';
                        }else{
                            include $page.'.php';
                        }
                    ?>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>Copyright &copy; 2020 <a href="#"></a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b><?php echo $_SESSION['system']['name'] ?></b>
            </div>
        </footer>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>