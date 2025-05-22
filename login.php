<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a new MySQLi connection and check for errors.
$conn = new mysqli('localhost', 'root', '', 'solomon');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the login form when submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim input to remove extra whitespace.
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Prepare a secure SQL statement to fetch the user record.
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    // Bind the result columns to variables.
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    // If a record is found and the password is correct, log the user in.
    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['login_id'] = $id;
        header("Location: user/index.php");
        exit();
    } else {
        $error = '<div class="alert alert-danger" role="alert">Invalid email or password. Please try again.</div>';
    }
    
    $stmt->close();
}

$conn->close();
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login || Expressloom</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <!-- CSS files -->
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
    .reset-password-link,
    .register-link {
        display: block;
        margin-bottom: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .reset-password-link:hover,
    .register-link:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    .password-toggle {
        position: relative;
    }

    .password-toggle .toggle-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }
    </style>
</head>

<body class="body-gray-bg">
    <div class="page-wrapper">
        <!-- Main Header (if any) -->
        <header class="main-header main-header-one">
            <!-- Header content goes here -->
        </header>
        <!-- Login Form Section -->
        <section class="login-form-section" style="padding: 50px 0;">
            <div class="container">
                <div class="sec-title text-center">
                    <div class="sub-title"></div>
                </div>
                <div class="login-form"
                    style="border: 2px solid #ddd; border-radius: 15px; padding: 20px; max-width: 800px; margin: 0 auto;">
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="Enter Your Email" required
                                style="width: 100%; padding: 10px; border-radius: 5px;">
                        </div>
                        <div class="form-group password-toggle">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" placeholder="Enter Your Password"
                                required style="width: 100%; padding: 10px; border-radius: 5px;">
                            <span class="toggle-icon" onclick="togglePasswordVisibility()">
                                <i class="icon-eye"></i>
                            </span>
                        </div>
                        <div class="form-group">
                            <a href="reset_password.html" class="reset-password-link">Forgot Password?</a>
                            <a href="signup.php" class="register-link">Don't have an account? Sign up</a>
                            <button type="submit" class="thm-btn"
                                style="width: 100%; padding: 10px; border-radius: 5px;">
                                <span class="txt">Login</span>
                                <i class="icon-next"></i>
                            </button>
                            <?php if (isset($error)): ?>
                            <?php echo $error; ?>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- Footer (if any) -->
        <footer class="footer-one">
            <!-- Footer content goes here -->
        </footer>
    </div>
    <!-- JS files -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/01-ajax-form.js"></script>
    <script src="assets/js/02-bootstrap.min.js"></script>
    <script src="assets/js/03-jquery.appear.js"></script>
    <script src="assets/js/04-swiper.min.js"></script>
    <script src="assets/js/05-jquery.odometer.min.js"></script>
    <script src="assets/js/06-jquery.magnific-popup.min.js"></script>
    <script src="assets/js/07-jquery.nice-select.min.js"></script>
    <script src="assets/js/08-slick.min.js"></script>
    <script src="assets/js/09-wow.min.js"></script>
    <script src="assets/js/10-jquery.circleType.js"></script>
    <script src="assets/js/11-jquery.lettering.min.js"></script>
    <script src="assets/js/12-TweenMax.min.js"></script>
    <script src="assets/vendor/jarallax/jarallax.min.js"></script>
    <script src="assets/vendor/marquee/marquee.min.js"></script>
    <script src="assets/vendor/odometer/odometer.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-icon i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('icon-eye');
            toggleIcon.classList.add('icon-eye-off');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('icon-eye-off');
            toggleIcon.classList.add('icon-eye');
        }
    }
    </script>
</body>

</html>