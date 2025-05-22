<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if user is logged in using the correct session key
if (!isset($_SESSION['login_id'])) {
    header("Location: ../login.php");
    exit();
}

// Set up the PDO connection
$dsn = 'mysql:host=localhost;dbname=solomon';
$dbUser = 'root';
$dbPassword = '';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Retrieve the user's profile picture from the database
$userId = $_SESSION['login_id'];
$query = "SELECT profile_pic FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($user && !empty($user['profile_pic'])) {
    $_SESSION['profile_pic'] = $user['profile_pic'];
} else {
    $_SESSION['profile_pic'] = 'assets/userimg/defualt.jpg';
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>User Dashboard || expressloom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <link rel="stylesheet" href="assets/css/02-all.min.css">
    <link rel="stylesheet" href="assets/css/10-icomoon.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <style>
    * {
        box-sizing: border-box;
    }

    .dashboard-wrapper {
        display: flex;
        min-height: 100vh;
        margin-top: 70px;
        padding-top: 0px;
    }

    .content {
        flex: 1;
        padding: 20px;
        background-color: #fff;
        margin: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .sidebar {
        background: linear-gradient(180deg, #ffffff, #f8f9fa);
        padding: 20px;
        width: 250px;
        overflow: auto;
        height: calc(100vh - 70px);
        border-right: 1px solid #ddd;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        transition: width 0.3s ease;
        position: relative;
        margin-right: 200px;
        margin-top: 0;
        font-size: 1.1em;
    }

    .sidebar .logo img {
        width: 100%;
        height: auto;
    }

    .sidebar .nav-section {
        margin-top: 20px;
    }

    .sidebar .nav-title {
        font-size: 1.2em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .sidebar .nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        transition: background-color 0.3s ease, color 0.3s ease;
        white-space: nowrap;
        font-size: 1.1em;
        text-decoration: none;
        color: #333;
    }

    .sidebar .nav-item i {
        font-size: 1.3em;
    }

    .sidebar .nav-item:hover,
    .sidebar .nav-item.active {
        background-color: #007bff;
        color: #fff;
    }

    .sidebar .referral-card {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        margin-top: 20px;
    }

    .sidebar .referral-card h3 {
        margin: 0 0 10px;
    }

    .sidebar .referral-card p {
        margin: 0 0 15px;
    }

    .sidebar .referral-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .sidebar .referral-btn:hover {
        background-color: #0056b3;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        color: #333;
    }

    header {
        background: linear-gradient(135deg, #007bff, #4c70ba);
        color: #fff;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    header .logo img {
        height: 40px;
        width: auto;
    }

    header h1 {
        margin-left: 15px;
        font-size: 1.5em;
        letter-spacing: 0.5px;
    }

    .header-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-right img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-right i {
        font-size: 1.5em;
        cursor: pointer;
        color: #fff;
        transition: color 0.3s;
    }

    .header-right i:hover {
        color: #dfe3ee;
    }

    .user-menu {
        position: absolute;
        top: 70px;
        right: 20px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        display: none;
        padding: 10px 0;
        width: 150px;
        z-index: 2000;
    }

    .user-menu a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .user-menu a:hover {
        background-color: #f0f2f5;
    }

    .dashboard-wrapper {
        display: flex;
        min-height: 100vh;
        margin-top: 70px;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar ul li {
        margin-bottom: 30px;
    }

    .sidebar ul li a {
        text-decoration: none;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        transition: background-color 0.3s ease, color 0.3s ease;
        white-space: nowrap;
        font-size: 1.1em;
    }

    .sidebar ul li a i {
        font-size: 1.3em;
    }

    .sidebar ul li a:hover,
    .sidebar ul li.active>a {
        background-color: #007bff;
        color: #fff;
    }

    .sidebar.collapsed ul li a span {
        display: none;
    }

    .sidebar.collapsed ul li a {
        justify-content: center;
    }

    .sidebar ul li .dropdown-menu {
        display: none;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar ul li .dropdown-menu li a {
        padding-left: 40px;
        font-size: 0.95em;
        transition: background-color 0.3s;
    }

    .sidebar ul li .dropdown-menu li a:hover {
        background-color: #dfe3ee;
    }

    #toggleSidebar {
        position: fixed;
        top: 20px;
        left: 250px;
        width: 30px;
        height: 30px;
        background-color: #007bff;
        border: none;
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s, left 0.3s;
        outline: none;
    }

    #toggleSidebar:hover {
        background-color: #0056b3;
    }

    .sidebar.collapsed #toggleSidebar {
        left: 80px;
    }

    .content {
        flex: 1;
        padding: 2px;
        background-color: #fff;
        margin: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: margin 0.3s;
    }

    .content h3 {
        margin-top: 0;
    }

    @media screen and (max-width: 1024px) {
        .sidebar {
            width: 200px;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        #toggleSidebar {
            left: 200px;
        }

        .sidebar.collapsed #toggleSidebar {
            left: 70px;
        }

        .content {
            margin: 15px;
            padding: 15px;
        }
    }

    @media screen and (max-width: 1200px) {
        .sidebar {
            position: fixed;
            top: 70px;
            bottom: 0;
            left: 0;
            z-index: 999;
        }

        .dashboard-wrapper {
            margin-left: 200px;
        }

        .sidebar.collapsed~.dashboard-wrapper {
            margin-left: 70px;
        }
    }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <img src="assets/img/resource/expressloom10.png" alt="Logo">
        </div>
        <h1>Expressloom</h1>
        <div class="header-right">
            <i class="fas fa-bell"></i>
            <img src="<?php echo $_SESSION['profile_pic']; ?>" alt="User Profile" id="userProfile">
        </div>
    </header>

    <div class="user-menu" id="userMenu">
        <a href="view-profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="change-password.php"><i class="fas fa-key"></i> Change Password</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="dashboard-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <img src="assets/img/resource/expressloom10.png" alt="Logo" style="width: 100%; height: auto;">
            </div>
            <div class="nav-section">
                <div class="nav-title">Navigation</div>
                <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="view-orders.php"
                    class="nav-item <?php echo ($current_page == 'view-orders.php') ? 'active' : ''; ?>">
                    <i class="fas fa-eye"></i> View Orders
                </a>
                <a href="view-profile.php"
                    class="nav-item <?php echo ($current_page == 'view-profile.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> View Profile
                </a>
                <a href="wallet.php" class="nav-item <?php echo ($current_page == 'wallet.php') ? 'active' : ''; ?>">
                    <i class="fas fa-wallet"></i> Wallet
                </a>
                <a href="complaint.php"
                    class="nav-item <?php echo ($current_page == 'complaint.php') ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Complaint
                </a>
            </div>
            <div class="referral-card">
                <h3>Refer & Earn</h3>
                <p>Invite your friends and earn rewards!</p>
                <a href="#" class="referral-btn">Invite Now</a>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.sidebar ul li > a').on('click', function(e) {
            if ($(this).next('.dropdown-menu').length) {
                e.preventDefault();
                $(this).next('.dropdown-menu').slideToggle(200);
            }
            $('.sidebar ul li').removeClass('active');
            $(this).parent().addClass('active');
        });

        $('#toggleSidebar').on('click', function() {
            $('#sidebar').toggleClass('collapsed');
            $(this).html($('#sidebar').hasClass('collapsed') ?
                '<i class="fas fa-angle-double-right"></i>' :
                '<i class="fas fa-angle-double-left"></i>');
        });

        $('#userProfile').on('click', function() {
            $('#userMenu').toggle();
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#userProfile, #userMenu').length) {
                $('#userMenu').hide();
            }
        });
    });
    </script>
</body>

</html>