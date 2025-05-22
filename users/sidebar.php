<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
        <a href="./" class="brand-link">
            <h3 class="text-center p-0 m-0"><b>USER</b></h3>
        </a>
    </div>
    <div class="sidebar pb-4 mb-4">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="home.php" class="nav-link nav-home">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link nav-profile">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="parcels.php" class="nav-link nav-parcels">
                        <i class="nav-icon fas fa-box"></i>
                        <p>My Parcels</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link nav-logout">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<script>
$(document).ready(function() {
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var activeLink = $('.nav-link.nav-' + page);
    if (activeLink.length > 0) {
        activeLink.addClass('active');
    }
});
</script>