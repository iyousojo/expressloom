<!-- Navbar -->
<?php
// Display the user's full name or a default value if not set
$login_firstname = isset($_SESSION['login_firstname']) ? $_SESSION['login_firstname'] : '';
$login_lastname = isset($_SESSION['login_lastname']) ? $_SESSION['login_lastname'] : '';
$login_fullname = trim($login_firstname . ' ' . $login_lastname);
$account_type = isset($_SESSION['account_type']) ? ucfirst($_SESSION['account_type']) : 'N/A';
?>
<nav class="main-header navbar navbar-expand navbar-primary navbar-dark ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <?php if(isset($_SESSION['login_id'])): ?>
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <?php endif; ?>
        <li>
            <a class="nav-link text-white" href="./" role="button"> <large><b><?php echo $_SESSION['system']['name'] ?></b></large></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
                <span>
                    <div class="d-flex badge-pill">
                        <span class="fa fa-user mr-2"></span>
                        <span><b><?php echo htmlspecialchars($login_fullname ?: 'User'); ?></b></span>
                        <span class="fa fa-angle-down ml-2"></span>
                    </div>
                </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
                <a class="dropdown-item" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i> Manage Account</a>
                <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
<script>
    $('#manage_account').click(function(){
        uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
    })
</script>
