<?php
 include 'sidebar.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}
if (!isset($_SESSION['login_id'])) {
header("Location: login.php");
exit();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);


$dsn = 'mysql:host=localhost;dbname=solomon';
$dbUser = 'root';
$dbPassword = '';
try {
$pdo = new PDO($dsn, $dbUser, $dbPassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
die('Database connection failed: ' . $e->getMessage());
}

$userId = $_SESSION['login_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
die("User details not found.");
}

$profilePic = (!empty($user['profile_pic'])) ? $user['profile_pic'] : 'assets/userimg/defualt.jpg';
?>
<!doctype html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Profile | ExpressLoom</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <!-- Using Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        display: flex;
    }

    .profile-container {
        margin: 100px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        width: 100%;
        min-height: 400px;
        /* Ensure minimum height */
        overflow: auto;
        /* Handle overflow for large content */
    }

    .profile-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-header h2 {
        margin: 0;
        font-size: 28px;
        color: #007bff;
    }

    .profile-pic {
        display: block;
        margin: 0 auto 20px;
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #007bff;
    }

    .profile-details {
        list-style: none;
        padding: 0;
        font-size: 16px;
        color: #333;
    }

    .profile-details li {
        margin-bottom: 10px;
    }

    .profile-details li span {
        font-weight: bold;
    }

    .edit-btn {
        display: block;
        width: 100%;
        text-align: center;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
    }

    .edit-btn:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>

    <div class="profile-container">
        <div class="profile-header">
            <h2><i class="fas fa-user"></i> My Profile</h2>
        </div>
        <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="profile-pic">
        <ul class="profile-details">
            <li><span>First Name:</span> <?php echo htmlspecialchars($user['first_name'] ?? ''); ?></li>
            <li><span>Last Name:</span> <?php echo htmlspecialchars($user['last_name'] ?? ''); ?></li>
            <li><span>Email:</span> <?php echo htmlspecialchars($user['email'] ?? ''); ?></li>
            <?php if (!empty($user['phone'])): ?>
            <li><span>Phone:</span> <?php echo htmlspecialchars($user['phone']); ?></li>
            <?php endif; ?>
            <li><span>Address:</span> <?php echo htmlspecialchars($user['address'] ?? ''); ?></li>
            <li><span>State:</span> <?php echo htmlspecialchars($user['state'] ?? ''); ?></li>
            <li><span>Country:</span> <?php echo htmlspecialchars($user['country'] ?? ''); ?></li>
            <li><span>Zip Code:</span> <?php echo htmlspecialchars($user['zip_code'] ?? ''); ?></li>
            <li><span>Account Type:</span> <?php echo htmlspecialchars($user['account_type'] ?? ''); ?></li>
        </ul>
        <a href="edit-profile.php" class="edit-btn"><i class="fas fa-edit"></i> Edit Profile</a>
    </div>

</body>

</html>