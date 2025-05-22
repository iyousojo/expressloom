<?php 
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

// Include the sidebar (assumes sidebar.php is styled separately)
include 'sidebar.php';

$dsn = 'mysql:host=localhost;dbname=solomon';
$dbUser = 'root';
$dbPassword = '';
try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$userId = $_SESSION['login_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit Profile | ExpressLoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    .container {
        margin: 100px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        width: 100%;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #007bff;
        font-size: 28px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    input[type="text"],
    input[type="email"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .col-half {
        flex: 1;
        min-width: 250px;
    }

    button.submit-btn {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }

    button.submit-btn:hover {
        background-color: #0056b3;
    }

    img.profile-pic {
        margin-top: 15px;
        max-width: 100px;
        border-radius: 5px;
    }
    </style>
</head>

<body>

    <div class="container">
        <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
        <form action="update_profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_pic">Profile Picture</label>
                <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? 'assets/img/default-profile.png'); ?>"
                    alt="Profile Picture" class="profile-pic">
                <input type="file" name="profile_pic" id="profile_pic">
            </div>
            <div class="row">
                <div class="form-group col-half">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name"
                        value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group col-half">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name"
                        value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email"
                    value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone"
                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Save</button>
        </form>
    </div>
</body>

</html>