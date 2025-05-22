<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set up PDO connection
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

// Retrieve and sanitize form inputs
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');

$errors = [];
if(empty($first_name)) { $errors[] = "First name is required."; }
if(empty($last_name)) { $errors[] = "Last name is required."; }
if(empty($email)) { $errors[] = "Email is required."; }

if (!empty($errors)) {
    echo implode("<br>", $errors);
    exit;
}

// Handle profile picture upload if provided
$profile_pic = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'assets/userimg/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = basename($_FILES['profile_pic']['name']);
    $targetFile = $uploadDir . $filename;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($fileType, $allowed)) {
        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $profile_pic = $targetFile;
        } else {
            echo "Failed to upload profile picture.";
            exit;
        }
    } else {
        echo "Invalid file type for profile picture. Allowed: " . implode(', ', $allowed);
        exit;
    }
}

// Update the user record
if ($profile_pic) {
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, profile_pic = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$first_name, $last_name, $email, $phone, $profile_pic, $userId]);
} else {
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$first_name, $last_name, $email, $phone, $userId]);
}

// Update session variable for profile picture if changed
if ($profile_pic) {
    $_SESSION['profile_pic'] = $profile_pic;
}

// Redirect back to profile view or display a success message
header("Location: view-profile.php");
exit;
?>