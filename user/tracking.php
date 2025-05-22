<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['action']) && $_GET['action'] == 'get_parcel_history') {
    session_start();
    if (!isset($_SESSION['login_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You must be logged in to track parcels.'
        ]);
        exit;
    }

    $user_id = $_SESSION['login_id'];
    $ref_no = $_POST['ref_no'] ?? '';

    header('Content-Type: application/json');
    
    try {
        if(empty($ref_no)) {
            throw new Exception('Tracking number is required');
        }

        // Use the correct database and table names
        $stmt = $conn->prepare("SELECT * FROM cms_db.parcels WHERE reference_number = ? AND user_id = ?");
        $stmt->bind_param("si", $ref_no, $user_id);
        $stmt->execute();
        $parcel = $stmt->get_result()->fetch_assoc();

        if(!$parcel) {
            throw new Exception('No tracking information found for this parcel.');
        }

        // Get tracking history
        $stmt = $conn->prepare("SELECT * FROM cms_db.parcel_tracks WHERE parcel_id = ? ORDER BY date_created ASC");
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
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Track Your Parcel | ExpressLoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon2.png">
    <!-- Removed Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: Arial, sans-serif;
    }

    /* Sidebar is assumed to be styled in sidebar.php */

    .track-container {
        max-width: 800px;
        margin: 70px auto 50px;
        padding: 20px;
        border-radius: 10px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        ma
        /* Added to control overflow */
    }

    .track-header {
        text-align: center;

    }

    .track-header h1 {
        font-size: 24px;
        margin-top: 50px;
    }

    .input-group-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .input-group {
        margin-bottom: 20px;
        display: flex;
    }

    .input-group input[type="text"] {
        flex: 1;
        padding: 20px;
        /* Increased padding */
        font-size: 24px;
        /* Increased font size */
        border: 1px solid #ccc;
        border-radius: 5px 0 0 5px;
        outline: none;
    }

    .input-group button {
        padding: 20px 30px;
        /* Increased padding */
        font-size: 24px;
        /* Increased font size */
        border: none;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
        border-radius: 0 5px 5px 0;
    }

    .input-group button:disabled {
        background-color: #999;
        cursor: not-allowed;
    }

    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline::before {
        content: "";
        position: absolute;
        width: 4px;
        background: #007bff;
        top: 0;
        bottom: 0;
        left: 20px;
        margin-left: -2px;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        position: relative;
    }

    .timeline-icon {
        background: #007bff;
        color: #fff;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .timeline-content {
        background: #fff;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        flex: 1;
    }

    .alert {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid transparent;
        border-radius: 5px;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border-color: #ffeeba;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="track-container">
        <div class="track-header">
            <h1><i class="fas fa-box"></i> Track Your Parcel</h1>
        </div>
        <div class="input-group-container">
            <div class="input-group">
                <input type="text" id="ref_no" placeholder="Enter Tracking Number">
                <button id="track-btn"><i class="fas fa-search"></i> Track</button>
            </div>
        </div>
        <div id="parcel_history"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#track-btn').click(trackParcel);
        $('#ref_no').keypress(function(e) {
            if (e.which === 13) {
                trackParcel();
            }
        });

        function trackParcel() {
            let trackingNum = $('#ref_no').val().trim();
            let $results = $('#parcel_history');

            if (!trackingNum) {
                $results.html('<div class="alert alert-warning">Please enter a tracking number.</div>');
                return;
            }

            $('#track-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Tracking...');

            $.ajax({
                url: '?action=get_parcel_history',
                method: 'POST',
                data: {
                    ref_no: trackingNum
                },
                dataType: 'json'
            }).done(function(response) {
                $results.empty();
                if (response.status === 'success') {
                    // Filter out duplicate "Item Accepted"
                    let filteredHistory = [];
                    response.history.forEach(function(item) {
                        if (item.status === "Item Accepted") {
                            if (!filteredHistory.some(i => i.status === "Item Accepted")) {
                                filteredHistory.push(item);
                            }
                        } else {
                            filteredHistory.push(item);
                        }
                    });
                    let timeline = '<div class="timeline">';
                    filteredHistory.forEach(item => {
                        timeline += `
              <div class="timeline-item">
                <div class="timeline-icon"><i class="fas fa-truck"></i></div>
                <div class="timeline-content">
                  <strong>${item.status}</strong>
                  <p>${new Date(item.date_created).toLocaleString()}</p>
                </div>
              </div>`;
                    });
                    timeline += '</div>';
                    $results.html(timeline);
                } else {
                    $results.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }).fail(function() {
                $results.html('<div class="alert alert-danger">Error fetching tracking data.</div>');
            }).always(function() {
                $('#track-btn').prop('disabled', false).html('<i class="fas fa-search"></i> Track');
            });
        }
    });
    </script>
</body>

</html>