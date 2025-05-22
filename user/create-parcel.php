<?php
include 'db_connect.php';

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . ($conn->connect_error ?? 'Unknown error'));
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['login_id'];

// Fetch user email
$user_query = $conn->prepare("SELECT email FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_details = $user_result->fetch_assoc();

// Generate a unique reference number
function generateReferenceNumber($conn) {
    do {
        $reference_number = date('YmdHis') . rand(100, 999);
        $query = $conn->prepare("SELECT COUNT(*) FROM cms_db.parcels WHERE reference_number = ?");
        $query->bind_param("s", $reference_number);
        $query->execute();
        $query->bind_result($count);
        $query->fetch();
        $query->close();
    } while ($count > 0);
    return $reference_number;
}

$reference_number = generateReferenceNumber($conn);

// Fetch branches from the database
$branches_query = $conn->query("SELECT id, branch_code, city FROM cms_db.branches"); // Corrected database name
$branches = [];
while ($row = $branches_query->fetch_assoc()) {
    $branches[] = $row;
}

// Fetch payment methods from the `cms_db` database using $logistics_db
$payment_methods_query = $logistics_db->query("SELECT id, payment_method, description FROM payment_method"); // Corrected column and table name
if (!$payment_methods_query) {
    die("Error fetching payment methods: " . $logistics_db->error);
}
$payment_methods = [];
while ($row = $payment_methods_query->fetch_assoc()) {
    $payment_methods[] = $row;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Create Parcel | ExpressLoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh;
        background-color: #f4f6f9;
    }

    .sidebar {
        width: 300px;
        background-color: #343a40;
        color: #fff;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        border-bottom: 1px solid #495057;
        padding-bottom: 10px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin: 10px 0;
        padding: 10px;
        background-color: #495057;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .sidebar ul li.active {
        background-color: #007bff;
        color: #fff;
    }

    .container {
        flex: 1;
        padding: 40px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        margin: auto;
        max-width: 800px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #007bff;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        color: #555;
    }

    input,
    select {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    input:focus,
    select:focus {
        border-color: #007bff;
        outline: none;
    }

    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }

    .form-navigation {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
    }

    button {
        padding: 12px 25px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        background-color: #007bff;
        color: white;
    }

    button:hover {
        background-color: #0056b3;
    }

    button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .form-navigation .submit {
        background-color: #28a745;
    }

    .form-navigation .submit:hover {
        background-color: #218838;
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.form-step');
        const nextBtn = document.querySelector('.form-navigation .next');
        const prevBtn = document.querySelector('.form-navigation .prev');
        const submitBtn = document.querySelector('.form-navigation .submit');
        const stepIndicator = document.querySelectorAll('#step-indicator li');
        const weightInput = document.getElementById('weight');
        const heightInput = document.getElementById('height');
        const widthInput = document.getElementById('width');
        const lengthInput = document.getElementById('length');
        const priceInput = document.getElementById('price');
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentLinkContainer = document.getElementById('payment-link-container');
        const paymentLink = document.getElementById('payment-link');
        const form = document.getElementById('multi-step-form');
        let paymentCompleted = false;
        let currentStep = 0;

        function validateStep() {
            const currentFields = steps[currentStep].querySelectorAll('input, select');
            for (let field of currentFields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        }

        function updateStep() {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === currentStep);
            });
            stepIndicator.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentStep);
            });

            prevBtn.disabled = currentStep === 0;
            nextBtn.style.display = currentStep < steps.length - 1 ? 'inline-block' : 'none';
            submitBtn.style.display = currentStep === steps.length - 1 ? 'inline-block' : 'none';
        }

        function calculatePrice() {
            const weight = parseFloat(weightInput.value) || 0;
            const height = parseFloat(heightInput.value) || 0;
            const width = parseFloat(widthInput.value) || 0;
            const length = parseFloat(lengthInput.value) || 0;
            const volume = height * width * length || 0;

            // Example pricing logic: $5 per kg + $0.01 per cubic cm
            const price = (weight * 5) + (volume * 0.01);
            priceInput.value = price.toFixed(2);
        }

        paymentMethodSelect.addEventListener('change', () => {
            if (paymentMethodSelect.value === '3' || paymentMethodSelect.value ===
                '') { // Cash on Delivery or unselected
                paymentLinkContainer.style.display = 'none'; // Hide payment link
                paymentCompleted = paymentMethodSelect.value ===
                    '3'; // Allow submission only for Cash on Delivery
            } else {
                paymentLinkContainer.style.display = 'block'; // Show payment link for other methods
                paymentCompleted = false; // Require payment for other methods
            }
        });

        paymentLink.addEventListener('click', (e) => {
            e.preventDefault();
            alert('Redirecting to payment gateway...');
            paymentCompleted = true; // Mark payment as completed
        });

        form.addEventListener('submit', (e) => {
            if (!paymentCompleted && paymentMethodSelect.value !== '3') {
                e.preventDefault();
                alert('Please complete the payment before submitting the form.');
            }
        });

        weightInput.addEventListener('input', calculatePrice);
        heightInput.addEventListener('input', calculatePrice);
        widthInput.addEventListener('input', calculatePrice);
        lengthInput.addEventListener('input', calculatePrice);

        nextBtn.addEventListener('click', () => {
            if (validateStep() && currentStep < steps.length - 1) {
                currentStep++;
                updateStep();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateStep();
            }
        });

        updateStep();
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentLinkContainer = document.getElementById('payment-link-container');

        paymentMethodSelect.addEventListener('change', () => {
            const selectedValue = paymentMethodSelect.value;
            if (selectedValue === '3' || selectedValue === '') { // Cash on Delivery or unselected
                paymentLinkContainer.style.display = 'none'; // Hide payment link
            } else {
                paymentLinkContainer.style.display = 'block'; // Show payment link for other methods
            }
        });
    });
    </script>
</head>

<body>
    <div class="sidebar">
        <h2>ExpressLoom</h2>
        <ul id="step-indicator">
            <li class="active">Step 1: Sender Details</li>
            <li>Step 2: Receiver Details</li>
            <li>Step 3: Parcel Details</li>
            <li>Step 4: Payment</li>
        </ul>
    </div>

    <div class="container">
        <form id="multi-step-form" method="POST" action="save-parcel.php">
            <!-- Hidden fields -->
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="reference_number"
                value="<?php echo htmlspecialchars($reference_number, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="from_branch_id" value="1"> <!-- Default branch ID -->
            <input type="hidden" name="to_branch_id" value="3"> <!-- Default branch ID -->

            <!-- Step 1: Sender Details -->
            <div class="form-step active">
                <h2>Sender Details</h2>
                <div class="form-group">
                    <label for="sender_name">Name</label>
                    <input type="text" id="sender_name" name="sender_name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="sender_contact">Phone</label>
                    <input type="text" id="sender_contact" name="sender_contact" placeholder="Enter your phone number"
                        required>
                </div>
                <div class="form-group">
                    <label for="sender_email">Email</label>
                    <input type="email" id="sender_email" name="sender_email"
                        value="<?php echo htmlspecialchars($user_details['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="sender_address">Address</label>
                    <input type="text" id="sender_address" name="sender_address" placeholder="Enter your address"
                        required>
                </div>
                <div class="form-group">
                    <label for="sender_zip">Zip Code</label>
                    <input type="text" id="sender_zip" name="sender_zip" placeholder="Enter sender's zip code" required>
                </div>
            </div>

            <!-- Step 2: Receiver Details -->
            <div class="form-step">
                <h2>Receiver Details</h2>
                <div class="form-group">
                    <label for="recipient_name">Name</label>
                    <input type="text" id="recipient_name" name="recipient_name" placeholder="Enter recipient's name"
                        required>
                </div>
                <div class="form-group">
                    <label for="recipient_contact">Phone</label>
                    <input type="text" id="recipient_contact" name="recipient_contact"
                        placeholder="Enter recipient's phone number" required>
                </div>
                <div class="form-group">
                    <label for="recipient_address">Address</label>
                    <input type="text" id="recipient_address" name="recipient_address"
                        placeholder="Enter recipient's address" required>
                </div>
                <div class="form-group">
                    <label for="recipient_zip">Zip Code</label>
                    <input type="text" id="recipient_zip" name="recipient_zip" placeholder="Enter recipient's zip code"
                        required>
                </div>
            </div>

            <!-- Step 3: Parcel Details -->
            <div class="form-step">
                <h2>Parcel Details</h2>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="" disabled selected>Select</option>
                        <option value="1">Deliver</option>
                        <option value="2">Pickup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="from_branch_id">From Branch</label>
                    <select id="from_branch_id" name="from_branch_id" required>
                        <option value="" disabled selected>Select From Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>">
                            <?php echo htmlspecialchars($branch['branch_code'] . ' - ' . $branch['city'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" id="delivery-branch-container" style="display: none;">
                    <label for="delivery_branch_id">Delivery Branch</label>
                    <select id="delivery_branch_id" name="delivery_branch_id">
                        <option value="" disabled selected>Select Delivery Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>">
                            <?php echo htmlspecialchars($branch['branch_code'] . ' - ' . $branch['city'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" id="pickup-branch-container" style="display: none;">
                    <label for="pickup_branch_id">Pickup Branch</label>
                    <select id="pickup_branch_id" name="pickup_branch_id">
                        <option value="" disabled selected>Select Pickup Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>">
                            <?php echo htmlspecialchars($branch['branch_code'] . ' - ' . $branch['city'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input type="text" id="weight" name="weight" placeholder="Enter parcel weight" required>
                </div>
                <div class="form-group">
                    <label for="height">Height (cm)</label>
                    <input type="text" id="height" name="height" placeholder="Enter parcel height" required>
                </div>
                <div class="form-group">
                    <label for="width">Width (cm)</label>
                    <input type="text" id="width" name="width" placeholder="Enter parcel width" required>
                </div>
                <div class="form-group">
                    <label for="length">Length (cm)</label>
                    <input type="text" id="length" name="length" placeholder="Enter parcel length" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" placeholder="Calculated price" readonly>
                </div>
            </div>

            <!-- Step 4: Payment -->
            <div class="form-step">
                <h2>Payment</h2>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method">
                        <option value="" disabled selected>Select Payment Method</option>
                        <?php foreach ($payment_methods as $method): ?>
                        <option value="<?php echo $method['id']; ?>">
                            <?php echo htmlspecialchars($method['payment_method'], ENT_QUOTES, 'UTF-8'); ?>
                            <!-- Corrected column name -->
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="payment-link-container" style="margin-top: 20px; display: none;">
                    <a href="#" id="payment-link" class="btn btn-primary">Proceed to Payment</a>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const paymentMethodSelect = document.getElementById('payment_method');
                    const paymentLinkContainer = document.getElementById('payment-link-container');

                    paymentMethodSelect.addEventListener('change', () => {
                        const selectedOption = paymentMethodSelect.options[paymentMethodSelect
                            .selectedIndex];
                        if (selectedOption && selectedOption.getAttribute('data-hide-link') ===
                            'true') {
                            paymentLinkContainer.style.display =
                                'none'; // Hide payment link for Cash on Delivery
                        } else {
                            paymentLinkContainer.style.display =
                                'block'; // Show payment link for other methods
                        }
                    });
                });
                </script>
            </div>

            <!-- Navigation Buttons -->
            <div class="form-navigation">
                <button type="button" class="prev" disabled>Previous</button>
                <button type="button" class="next">Next</button>
                <button type="submit" class="submit" style="display: none;">Submit</button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const deliveryBranchContainer = document.getElementById('delivery-branch-container');
        const pickupBranchContainer = document.getElementById('pickup-branch-container');
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentMethodContainer = document.getElementById('payment-link-container');

        typeSelect.addEventListener('change', () => {
            if (typeSelect.value === '1') { // Deliver
                deliveryBranchContainer.style.display = 'block';
                pickupBranchContainer.style.display = 'none';
                document.getElementById('delivery_branch_id').required = true;
                document.getElementById('pickup_branch_id').required = false;
            } else if (typeSelect.value === '2') { // Pickup
                deliveryBranchContainer.style.display = 'none';
                pickupBranchContainer.style.display = 'block';
                document.getElementById('delivery_branch_id').required = false;
                document.getElementById('pickup_branch_id').required = true;
            } else {
                deliveryBranchContainer.style.display = 'none';
                pickupBranchContainer.style.display = 'none';
                document.getElementById('delivery_branch_id').required = false;
                document.getElementById('pickup_branch_id').required = false;
            }
        });

        paymentMethodSelect.addEventListener('change', () => {
            if (paymentMethodSelect.value) {
                paymentMethodContainer.style.display = 'block';
                paymentMethodSelect.required = true;
            } else {
                paymentMethodContainer.style.display = 'none';
                paymentMethodSelect.required = false;
            }
        });
    });
    </script>
</body>

</html>