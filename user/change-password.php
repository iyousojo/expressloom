<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

$dsn = 'mysql:host=localhost;dbname=solomon';
$dbUser = 'root';
$dbPassword = '';
try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['login_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } else {
        // Retrieve current password from database
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($currentPassword, $user['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if($updateStmt->execute([$hashedPassword, $userId])) {
                $success = "Password changed successfully.";
            } else {
                $error = "Error updating password.";
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Change Password | ExpressLoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        max-width: 500px;
        width: 100%;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #007bff;
        font-size: 24px;
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

    input {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .submit-btn {
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

    .submit-btn:hover {
        background-color: #0056b3;
    }

    .message {
        text-align: center;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .message.error {
        color: red;
    }

    .message.success {
        color: green;
    }

    .back-link {
        text-align: center;
        margin-top: 15px;
    }

    .back-link a {
        color: #007bff;
        text-decoration: none;
    }

    .back-link a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h2><i class="fas fa-key"></i> Change Password</h2>
        <?php if(!empty($error)): ?>
        <p class="message error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if(!empty($success)): ?>
        <p class="message success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Update Password</button>
        </form>
    </div>
</body>

</html>