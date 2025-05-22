<?php include 'admin/db_connect.php' ?>
<?php


if(isset($_GET['action']) && $_GET['action'] == 'get_parcel_history') {
    $ref_no = $_POST['ref_no'] ?? '';

    header('Content-Type: application/json');
    
    try {
        if(empty($ref_no)) {
            throw new Exception('Tracking number is required');
        }

        // Get parcel details
        $stmt = $conn->prepare("SELECT * FROM parcels WHERE reference_number = ?");
        $stmt->bind_param("s", $ref_no);
        $stmt->execute();
        $parcel = $stmt->get_result()->fetch_assoc();

        if(!$parcel) {
            throw new Exception('No tracking information found');
        }

        // Get tracking history
        $stmt = $conn->prepare("SELECT * FROM parcel_tracks WHERE parcel_id = ? ORDER BY date_created ASC");
        $stmt->bind_param("i", $parcel['id']);
        $stmt->execute();
        $tracks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $history = array_map(function($item) {
            return [
                'status' => get_status_text($item['status']),
                'date_created' => $item['date_created']
            ];
        }, $tracks);

        array_unshift($history, [
            'status' => 'Item Accepted',
            'date_created' => $parcel['date_created']
        ]);

        echo json_encode([
            'status' => 'success',
            'sender' => [
                'name' => $parcel['sender_name'],
                'address' => $parcel['sender_address'],
                'contact' => $parcel['sender_contact']
            ],
            'receiver' => [
                'name' => $parcel['recipient_name'],
                'address' => $parcel['recipient_address'],
                'contact' => $parcel['recipient_contact']
            ],
            'history' => $history
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

function get_status_text($status) {
    $statuses = [
        1 => "Collected",
        2 => "Shipped",
        3 => "In-Transit",
        4 => "Arrived At Destination",
        5 => "Out for Delivery",
        6 => "Ready to pick up",
        7 => "Delivered",
        8 => "picked-up",
        9=> "unseccesfull Delivery Attempt"
    ];
    return $statuses[$status] ?? "Item Accepted by Courier";
}
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Track ||Expressloom</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <!-- Place favicon.ico in the root directory -->

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
    <!-- Add these in the head section -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
    .custom-track-link {
        font-weight: bold;
        color: #ff0000;
        /* Change color as needed */
        background-color: #f0f0f0;
        /* Change background color as needed */
        padding: 10px 15px;
        border-radius: 5px;
    }

    .navigation a:hover {
        color: #856f71 !important;
    }

    .nnn {
        margin-top: 25px;
        margin-left: 90px;
    }

    .menu {
        color: #000;
    }

    .menu-item-has-children {
        color: #000;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        border-left: 3px solid #007bff;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -9px;
        top: 20px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #007bff;
        border: 3px solid #fff;
    }

    .input-group .btn {
        background-color: #f68b21;
        border-color: #f68b21;
    }

    .input-group .btn:hover {
        background-color: #e67d1a;
        border-color: #e67d1a;
    }
    </style>
</head>

<body class="body-gray-bg">

    <!-- preloader -->
    <div id="preloader">
        <div id="loading-center">
            <div class="loader">
                <div class="loader-outter"></div>
                <div class="loader-inner"></div>
            </div>
        </div>
    </div>
    <!-- preloader-end -->


    <!-- Start Extra Info -->
    <div class="extra-info">
        <div class="close-icon menu-close">
            <button>
                <i class="icon-close"></i>
            </button>
        </div>
        <div class="logo-side">
            <a href="index.php"><img src="assets/img/resource/expressloom10.png" alt="9#" style="width: 50px;"></a>
        </div>
        <div class="side-info">
            <div class="content-box">
                <h3>Welcome to our Best<br> Transportation Company</h3>
                <div class="text">
                    <p>
                        where your satifacton is our piority
                    </p>
                </div>
            </div>
            <!--Start Sidebar Contact Info -->
            <div class="sidebar-contact-info">
                <h3>Conatct Us</h3>
                <ul>
                    <li>
                        <div class="icon">
                            <span class="icon-open-mail"></span>
                        </div>
                        <div class="text">
                            <p><a href="mailto:info@example.com">info@Expessloom.com</a></p>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <span class="icon-phone-call-1"></span>
                        </div>
                        <div class="text">
                            <p><a href="tel:+8801682648101">+1800 456 7890</a></p>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <span class="fa-regular fa-clock"></span>
                        </div>
                        <div class="text">
                            <p>Working Hour : 7.00am to 4.00pm</p>
                        </div>
                    </li>
                </ul>
            </div>
            <!--End Sidebar Contact Info -->
            <div class="side-content-newsletter-box">
                <h3>Newsletter Subscription</h3>
                <form action="https://itcroctheme.com/Expessloom/Expessloom-html/index.php" method="post">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Enter Email Address" required="">
                        <button class="thm-btn" type="submit">
                            <span class="txt">
                                <i class="icon-paper-plane"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            <!--Start Side Social Links -->
            <div class="side-social-links">
                <ul class="clearfix">
                    <li>
                        <a href="#">
                            <i class="icon-facebook-app-symbol"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="icon-twitter"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="icon-linked-in-logo-of-two-letters"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="icon-pinterest"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!--End Side Social Links -->
        </div>
    </div>
    <div class="offcanvas-overly"></div>
    <!-- End Extra Info -->


    <div class="page-wrapper">

        <!--Start Main Header One -->
        <header class="main-header main-header-one">
            <div id="sticky-header" class="menu-area">
                <div class="container">
                    <div class="main-header-one__inner">

                        <!--Start Main Header one Inner Left -->
                        <div class="main-header-one__inner-left">
                            <div class="logo-box-one">
                                <a href="index.php">
                                    <img src="assets/img/footer/expresslomlogo-removebg-preview.png" alt="Logo"
                                        style="width: 150px; height: auto;">
                            </div>
                        </div>
                        <!--End Main Header one Inner Left -->

                        <!--Start Main Header one Inner Right -->
                        <div class=" main-header-one__inner-right">

                            <!--Start Main Header one Inner Right Top-->
                            <div class="main-header-one__inner-right__top">

                                <div class="header-contact-info-one">
                                    <ul>
                                        <li>
                                            <div class="icon">
                                                <span class="icon-open-mail"></span>
                                            </div>
                                            <p><a href="mailto:info@example.com">info@example.com</a></p>
                                        </li>
                                        <li>
                                            <div class="icon">
                                                <span class="icon-location-pin"></span>
                                            </div>
                                            <p>6391 Elgin St. Celina, 10299</p>
                                        </li>
                                        <li>
                                            <div class="icon">
                                                <span class="icon-phone-call-1"></span>
                                            </div>
                                            <p><a href="tel:(629)555-0129">(629) 555-0129</a></p>
                                        </li>
                                    </ul>
                                </div>

                                <div class="header-social-link-one">
                                    <ul class="clearfix">
                                        <li>
                                            <a href="#">
                                                <i class="icon-facebook-app-symbol"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-instagram"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-linked-in-logo-of-two-letters"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="track.php" class="btn"
                                                style="background-color:#f68b21; color: white; padding: 10px 20px; border-radius: 5px;">
                                                track
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                            <!--End Main Header one Inner Right Top-->

                            <!--Start Main Header one Inner Right Bottom-->
                            <div class="main-header-one__inner-right__bottom">

                                <div class="menu-area__inner">
                                    <div class="mobile-nav-toggler">
                                        <i class="fas fa-bars"></i>
                                    </div>
                                    <div class="menu-wrap" style="margin-right: 20px;">
                                        <nav class="menu-nav">
                                            <div class="navbar-wrap main-menu">
                                                <ul class="navigation">
                                                    <li class="menu"><a href="index.php">Home</a></li>
                                                    <li class="menu-item-has-children"><a>About Us</a>
                                                        <ul class="sub-menu">
                                                            <li><a href="aboutcompany.php">About Company</a>
                                                            </li>
                                                            <li><a href="testimonials.php">Testimonials</a></li>
                                                            <li><a href="faq.php">Faq’s</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="menu"><a href="Services.php">Services</a>
                                                    </li>
                                                    <li><a href="updatenews.php">Update news</a></li>
                                                    <li><a href="contact.php">Contact us</a></li>
                                                    <li>
                                                        <a href="login.php" class="btn nnn"
                                                            style="background-color:#f68b21; color: white; padding: 10px 20px; border-radius: 5px;">
                                                            login
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </nav>
                                    </div>
                                </div>

                                <div class="main-menu-right-box-one">
                                    <div class="search-box-one">
                                        <a href="#" class="main-menu__search search-toggler">
                                            <span class="icon-search-interface-symbol"></span>
                                        </a>
                                    </div>
                                    <div class="side-content-button-one">
                                        <a class="menu-tigger" href="#">
                                            <span class="line"></span>
                                            <span class="line two"></span>
                                        </a>
                                    </div>
                                </div>


                            </div>
                            <!--End Main Header one Inner Right Bottom-->
                        </div>
                        <!--End Main Header one Inner Right -->


                    </div>
                </div>
            </div>
            <!--Start Page Header-->
            <section class="page-header">
                <div class="page-header__img float-bob-y"><img src="assets/img/resource/page-header-img.png" alt="">
                </div>
                <div class="container">
                    <div class="page-header__inner">
                        <h2>Track</h2>
                        <ul class="thm-breadcrumb">
                            <li><a href="index.php">Home</a></li>
                            <li><span class="icon-left"></span></li>
                            <li>Track</li>
                        </ul>
                    </div>
                </div>
            </section>
            <!--End Page Header-->


            <!--Start Mobile Menu  -->
            <div class="mobile-menu">
                <nav class="menu-box">
                    <div class="close-btn"><i class="fas fa-times"></i></div>
                    <div class="nav-logo">
                        <a href="index.php"><img src="assets/img/resource/mobile-menu-logo.png" alt="Logo"></a>
                    </div>
                    <div class="menu-outer">
                        <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
                    </div>
                    <div class="social-links">
                        <ul class="clearfix list-wrap">
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="menu-backdrop"></div>
            <!-- End Mobile Menu -->
        </header>
        <!--End Main Header One -->

        <!-- Start Track Form Section -->
        <section class="track-form-section" style="padding: 50px 0;">
            <!-- Track Form - Keep original styling -->
            <div class="track-form"
                style="background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
                <div class="input-group">
                    <input type="search" id="ref_no" class="form-control form-control-lg"
                        placeholder="Type the tracking number here">
                    <button type="button" id="track-btn" class="btn btn-primary btn-lg">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Results Container -->
            <div class="row mt-4">
                <div class="col-md-8 offset-md-2">
                    <div id="parcel_history"></div>
                </div>
            </div>
        </section>

        <!-- Timeline Item Template -->
        <div id="clone_timeline-item" class="d-none">
            <div class="timeline-item mb-3"
                style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 4px solid #007bff;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="status-text" style="font-weight: bold; color: #007bff;"></span>
                    <span class="time text-muted">
                        <i class="fas fa-clock"></i>
                        <span class="dtime"></span>
                    </span>
                </div>
            </div>
        </div>
        <!--End Track Form Section-->
        <!--Start Footer One-->
        <footer class="footer-one">
            <!-- Start Footer Main -->
            <div class="footer-main footer-main--white-bg">
                <div class="container">

                    <div class="footer-main__inner">
                        <div class="row">

                            <!--Start Single Footer Widget-->
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="single-footer-widget single-footer-widget--two">
                                    <div class="title">
                                        <h3>Get Free Link</h3>
                                    </div>
                                    <div class="footer-widget__links-box">
                                        <ul>
                                            <li>
                                                <a href="#"><span class="icon-chevron"></span>Free Industry
                                                    Insights or
                                                    Newsletter Subscription</a>
                                            </li>
                                            <li>
                                                <a href="#"><span class="icon-chevron"></span>Free Logistics
                                                    Consultation </a>
                                            </li>
                                            <li>
                                                <a href="#"><span class="icon-chevron"></span>Free eBook:
                                                    <br>Streamline
                                                    Your Supply Chain</a>
                                            </li>
                                            <li>
                                                <a href="#"><span class="icon-chevron"></span>Free Delivery Time
                                                    Estimator</a>
                                            </li>
                                            <li>
                                                <a href="#"><span class="icon-chevron"></span>Free Logistics
                                                    Audits</a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                            <!--End Single Footer Widget-->

                            <!--Start Single Footer Widget-->
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="single-footer-widget single-footer-widget--two ml-52">
                                    <div class="title">
                                        <h3>Contact</h3>
                                    </div>
                                    <div class="single-footer-widget__contact-info">
                                        <div class="text">
                                            <p>Reach out now, and let's make logistics easy for you!.</p>
                                        </div>
                                        <ul class="clearfix">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-envelope"></span>
                                                </div>
                                                <p><a href="mailto:info@example.com">info@example.com</a></p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-location-pin"></span>
                                                </div>
                                                <p>6391 Elgin St. Celina, 10299</p>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-phone-call-1"></span>
                                                </div>
                                                <p><a href="tel:(629)555-0129">(629) 555-0129</a></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--End Single Footer Widget-->

                            <!--Start Single Footer Widget-->
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="single-footer-widget single-footer-widget--two ml-7">
                                    <div class="title">
                                        <h3>Recent Blog</h3>
                                    </div>
                                    <div class="single-footer-widget__recent-blog-list">
                                        <ul class="clearfix">
                                            <li>
                                                <div class="img-box">
                                                    <img src="assets/img/footer/footer-one__recent-blog__img1.jpg"
                                                        alt="#">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                                <div class="title-box">
                                                    <div class="date-box">
                                                        <div class="icon">
                                                            <span class="icon-calendar"></span>
                                                        </div>
                                                        <div class="text">
                                                            <p>january 11, 2023</p>
                                                        </div>
                                                    </div>
                                                    <h5><a href="#">5 Tips for Reducing Shipping Costs in Your
                                                            Business</a></h5>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="img-box">
                                                    <img src="assets/img/footer/footer-one__recent-blog__img2.jpg"
                                                        alt="#">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                                <div class="title-box">
                                                    <div class="date-box">
                                                        <div class="icon">
                                                            <span class="icon-calendar"></span>
                                                        </div>
                                                        <div class="text">
                                                            <p>january 11, 2023</p>
                                                        </div>
                                                    </div>
                                                    <h5><a href="#">The Role of AI and Automation in Supply
                                                            Chain
                                                            Optimization</a></h5>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--End Single Footer Widget-->

                            <!--Start Single Footer Widget-->
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="single-footer-widget single-footer-widget--two pl40">
                                    <div class="title">
                                        <h3>Photo Gallery</h3>
                                    </div>
                                    <div class="single-footer-widget__photo-gallery-list">
                                        <ul class="clearfix">
                                            <li>
                                                <div class="footer-widget__photo-gallery-img">
                                                    <img src="assets/img/footer/footer-widget__photo-gallery-img-1.jpg"
                                                        alt="">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="footer-widget__photo-gallery-img">
                                                    <img src="assets/img/footer/footer-widget__photo-gallery-img-2.jpg"
                                                        alt="">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="footer-widget__photo-gallery-img">
                                                    <img src="assets/img/footer/footer-widget__photo-gallery-img-3.jpg"
                                                        alt="">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="footer-widget__photo-gallery-img">
                                                    <img src="assets/img/footer/footer-widget__photo-gallery-img-4.jpg"
                                                        alt="">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="footer-widget__photo-gallery-img">
                                                    <img src="assets/img/footer/footer-widget__photo-gallery-img-5.jpg"
                                                        alt="">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="footer-widget__photo-gallery-img">
                                                    <img src="assets/img/footer/footer-widget__photo-gallery-img-6.jpg"
                                                        alt="">
                                                    <a href="#"><i class="fa-solid fa-link"></i></a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!--End Single Footer Widget-->

                        </div>
                    </div>
                </div>
            </div>
            <!-- End Footer Main -->

            <!--Start Footer Middle-->
            <div class="footer-middle footer-middle--two">
                <div class="container">
                    <div class="footer-middle__inner">
                        <div class="footer-logo-box">
                            <img src="assets/img/footer/expressloom12.png" alt="#" style="width: 200px;">
                        </div>
                        <div class="phone-number-box phone-number-box--style2">
                            <div class="icon">
                                <span class="icon-phone-call-1"></span>
                            </div>
                            <div class="text">
                                <p>Need help?</p>
                                <p><a href="tel:(808)555-0111">(808) 555-0111</a></p>
                            </div>
                        </div>
                        <div class="socel-link-box socel-link-box--two">
                            <div class="text">
                                <p>Follow us:</p>
                            </div>
                            <ul class="clearfix">
                                <li>
                                    <a href="#">
                                        <i class="icon-facebook-app-symbol"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="icon-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="icon-linked-in-logo-of-two-letters"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="icon-pinterest"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Footer Middle-->

            <!--Start Footer Bottom -->
            <div class="footer-bottom footer-bottom--two">
                <div class="container">
                    <div class="footer-bottom__inner">
                        <div class="copyright-text copyright-text--two">
                            <p>©<a href="index.php">Expessloom |2025</a>, All Rights Reserved.</p>
                        </div>

                        <div class="copyright-menu copyright-menu--two">
                            <ul>
                                <li>
                                    <p><a href="#">Terms&amp; Condition</a></p>
                                </li>
                                <li>
                                    <p><a href="#">Privacy Policy</a></p>
                                </li>
                                <li>
                                    <p><a href="#">Contact Us</a></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Footer Bottom -->
        </footer>
        <!--Start Footer One-->
    </div>


    <!--Start Search Popup -->
    <div class="search-popup">
        <div class="search-popup__overlay search-toggler">
            <div class="search-close-btn">
                <i class="icon-plus"></i>
            </div>
        </div>
        <div class="search-popup__content">
            <form action="#">
                <label for="search" class="sr-only">search here</label>
                <input type="search" id="search" placeholder="Search Here..." />
                <button type="submit" aria-label="search submit" class="btn-one">
                    <i class="icon-search-interface-symbol"></i>
                </button>
            </form>
        </div>
    </div>
    <!--End Search Popup -->




    <!-- Scroll-top -->
    <button class="scroll-top scroll-to-target" data-target="html">
        <i class="icon-arrowhead-up"></i>
    </button>
    <!-- Scroll-top-end-->


    <!-- JS here -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        $('#track-btn').click(track_now);
        $('#ref_no').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                track_now();
            }
        });

        function track_now() {
            const trackingNum = $('#ref_no').val().trim();
            const $results = $('#parcel_history');

            if (!trackingNum) {
                $results.html(
                    '<div class="alert alert-warning text-center">Please enter a tracking number</div>');
                return;
            }

            const $btn = $('#track-btn');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                    url: '?action=get_parcel_history',
                    method: 'POST',
                    data: {
                        ref_no: trackingNum
                    },
                    dataType: 'json'
                })
                .done(function(response) {
                    $results.empty();
                    if (response.status === 'success') {
                        // Add sender/receiver info
                        $results.append(`
                    <div class="sender-receiver-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fas fa-user"></i> Sender Information</h4>
                                <p><strong>Name:</strong> ${response.sender.name}</p>
                                <p><strong>Address:</strong> ${response.sender.address}</p>
                                <p><strong>Contact:</strong> ${response.sender.contact}</p>
                            </div>
                            <div class="col-md-6">
                                <h4><i class="fas fa-user"></i> Receiver Information</h4>
                                <p><strong>Name:</strong> ${response.receiver.name}</p>
                                <p><strong>Address:</strong> ${response.receiver.address}</p>
                                <p><strong>Contact:</strong> ${response.receiver.contact}</p>
                            </div>
                        </div>
                    </div>
                    <h4 class="mb-3 text-center">Tracking History</h4>
                    <div class="timeline">
                `);

                        // Add timeline items
                        const $timeline = $('.timeline');
                        response.history.forEach(item => {
                            const tl = $('#clone_timeline-item .timeline-item').clone();
                            tl.find('.status-text').text(item.status);
                            tl.find('.dtime').text(new Date(item.date_created).toLocaleString());
                            $timeline.append(tl);
                        });
                        $results.append('</div>');
                    } else {
                        $results.html(
                            `<div class="alert alert-warning text-center">${response.message}</div>`);
                    }
                })
                .fail(function() {
                    $results.html(
                        '<div class="alert alert-danger text-center">Error connecting to server</div>');
                })
                .always(function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-search"></i>');
                });
        }
    });
    </script>
</body>


</html>