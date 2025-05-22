<!DOCTYPE html>
<html lang="en">
<?php 
include_once 'sidebar.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Dashboard</title>
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        display: flex;
    }

    .dashboard-container {
        margin: 100px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 100%;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .dashboard-header h2 {
        margin: 0;
        font-size: 28px;
        color: #007bff;
    }

    .dashboard-content {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
    }

    .dashboard-box {
        flex: 1 1 calc(50% - 20px);
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .dashboard-box h3 {
        margin-top: 0;
        font-size: 20px;
        color: #333;
    }

    .dashboard-box p {
        font-size: 16px;
        color: #666;
    }

    .dashboard-box button {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
    }

    .dashboard-box button:hover {
        background-color: #0056b3;
    }

    @media screen and (max-width: 768px) {
        .dashboard-box {
            flex: 1 1 100%;
        }
    }

    #parcel_history_modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        animation: fadeIn 0.3s ease-in-out;
    }

    #parcel_history_modal .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #333;
    }

    #modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .track-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 20px;
        border-radius: 10px;
        overflow-y: auto;
    }

    .track-header h1 {
        font-size: 24px;
        margin-top: 0;
        color: #007bff;
    }

    .input-group-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .input-group {
        display: flex;
        width: 100%;
        max-width: 600px;
    }

    .input-group input[type="text"] {
        flex: 1;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px 0 0 5px;
        outline: none;
    }

    .input-group button {
        padding: 10px 20px;
        font-size: 16px;
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
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2><i class="fas fa-tachometer-alt"></i> Courier Dashboard</h2>
        </div>
        <div class="dashboard-content">
            <div class="dashboard-box">
                <h3>Create Parcel</h3>
                <p>Start a new parcel delivery.</p>
                <button onclick="window.location.href='create-parcel.php'">Create Parcel</button>
            </div>
            <div class="dashboard-box">
                <h3>Track Your Parcels</h3>
                <p>View the status of your parcels.</p>
                <button id="view-parcel-btn">View Parcel</button>
            </div>
        </div>
    </div>
    <div id="modal-overlay"></div>
    <div id="parcel_history_modal">
        <button class="close-btn">&times;</button>
        <div class="track-container">
            <div class="track-header">
                <h1><i class="fas fa-box"></i> Track Your Parcel</h1>
            </div>
            <div class="input-group-container">
                <div class="input-group">
                    <input type="text" id="ref_no_modal" placeholder="Enter Tracking Number">
                    <button id="track-btn-modal"><i class="fas fa-search"></i> Track</button>
                </div>
            </div>
            <div id="parcel_history_modal_content"></div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#view-parcel-btn').click(function() {
            $('#modal-overlay').show();
            $('#parcel_history_modal').show();
        });

        $('.close-btn, #modal-overlay').click(function() {
            $('#parcel_history_modal').hide();
            $('#modal-overlay').hide();
        });

        $('#track-btn-modal').click(trackParcelModal);
        $('#ref_no_modal').keypress(function(e) {
            if (e.which === 13) {
                trackParcelModal();
            }
        });

        function trackParcelModal() {
            let trackingNum = $('#ref_no_modal').val().trim();
            let $results = $('#parcel_history_modal_content');

            if (!trackingNum) {
                $results.html('<div class="alert alert-warning">Please enter a tracking number.</div>');
                return;
            }

            $('#track-btn-modal').prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Tracking...');

            $.ajax({
                url: 'tracking.php?action=get_parcel_history',
                method: 'POST',
                data: {
                    ref_no: trackingNum
                },
                dataType: 'json'
            }).done(function(response) {
                $results.empty();
                if (response.status === 'success') {
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
                $('#track-btn-modal').prop('disabled', false).html(
                    '<i class="fas fa-search"></i> Track');
            });
        }
    });
    </script>
</body>

</html>