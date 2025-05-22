<?php
error_reporting(E_ALL);
include_once "link.php";

session_start();
ob_start();
$conn = new mysqli('localhost', 'root', '', 'solomon') or die("Could not connect to mysql" . mysqli_error($conn));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];
    $account_type = $_POST['account_type'];

    // Server-side validation for email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    // Server-side validation for password
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long and include one uppercase letter, one lowercase letter, and one number.";
    }
    // Check if passwords match
    elseif ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        // Notice that profile_pic is omitted from the INSERT query.
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, address, state, country, zip_code, account_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $first_name, $middle_name, $last_name, $email, $passwordHash, $address, $state, $country, $zip_code, $account_type);
        if ($stmt->execute()) {
            $success = "User registered successfully.";
            // Redirect to login page
            header('Location: login.php');
            exit;
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Sign Up || Expessloom</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/01-bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/02-all.min.css">
    <link rel="stylesheet" href="assets/css/03-jquery.magnific-popup.css">
    <link rel="stylesheet" href="assets/css/04-nice-select.css">
    <link rel="stylesheet" href="assets/css/05-odometer.css">
    <link rel="stylesheet" href="assets/css/06-swiper.min.css">
    <link rel="stylesheet" href="assets/css/07-animate.min.css">
    <link rel="stylesheet" href="assets/css/08-custom-animate.css">
    <link rel="stylesheet" href="assets/css/09-slick.css">
    <link rel="stylesheet" href="assets/css/10-icomoon.css">
    <link rel="stylesheet" href="assets/vendor/custom-animate/custom-animate.css">
    <link rel="stylesheet" href="assets/vendor/jarallax/jarallax.css">
    <link rel="stylesheet" href="assets/vendor/odometer/odometer.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <style>
    .form-group {
        margin-bottom: 15px;
    }

    .form-group-half {
        width: 48%;
        display: inline-block;
    }

    .form-group-full {
        width: 100%;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
    }
    </style>
</head>

<body class="body-gray-bg">
    <div class="page-wrapper">
        <!--Start Main Header One -->
        <header class="main-header main-header-one">
            <!-- Existing header content -->
        </header>
        <!--End Main Header One -->
        <!--Start Sign Up Form Section-->
            <section class="signup-form-section" style="padding: 50px 0;">
            <div class="container">
                <div class="sec-title text-center">
                    <div class="sub-title">
                        <h4>Sign Up</h4>
                    </div>
                    <h2>Create your account<br>to get started</h2>
                </div>
                <div class="signup-form"
                    style="border: 2px solid #ddd; border-radius: 15px; padding: 20px; max-width: 800px; margin: 0 auto;">
                    <form id="signup-form" action="signup.php" method="post">
                        <div class="form-group form-group-half">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first-name" name="first_name" placeholder="Enter Your First Name"
                                required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="middle_name">Middle Name:</label>
                            <input type="text" id="middle-name" name="middle_name" placeholder="Enter Your Middle Name">
                        </div>
                        <div class="form-group form-group-full">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last-name" name="last_name" placeholder="Enter Your Last Name"
                                required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="Enter Your Email" required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" placeholder="Enter Your Password"
                                required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" id="confirm-password" name="confirm_password"
                                placeholder="Confirm Your Password" required>
                        </div>
                        <div class="form-group form-group-full">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" placeholder="Enter Your Address" required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="state">State:</label>
                            <input type="text" id="state" name="state" placeholder="Enter Your State" required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="country">Country:</label>
                            <input type="text" id="country" name="country" placeholder="Enter Your Country" required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="zip_code">Zip Code:</label>
                            <input type="text" id="zip-code" name="zip_code" placeholder="Enter Your Zip Code" required>
                        </div>
                        <div class="form-group form-group-half">
                            <label for="account_type">Type of Account:</label>
                            <select id="account-type" name="account_type" required>
                                <option value="">Select Account Type</option>
                                <option value="personal">Personal</option>
                                <option value="business">Business</option>
                            </select>
                        </div>
                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <div class="form-group form-group-full">
                            <button type="submit" class="thm-btn"
                                style="width: 100%; padding: 10px; border-radius: 5px;">
                                <span class="txt">Sign Up</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!--End Sign Up Form Section-->
    </div>
</body>

</html>