<?php
    require_once '../includes/db_connection.php';
    require_once '../includes/auth_functions.php';

    if (!isLoggedIn() || !isAgency()) {
        header("Location: ../login.php");
        exit();
    }

    $agency_id = $_SESSION['user_id'];
    $car_id = $_GET['car_id'] ?? null;

    // Base query
    $sql = "SELECT b.*, u.name as customer_name, u.email as customer_email, c.model as car_model 
            FROM bookings b 
            JOIN users u ON b.customer_id = u.id 
            JOIN cars c ON b.car_id = c.id 
            WHERE c.agency_id = ?";

    $params = [$agency_id];

    if ($car_id) {
        $sql .= " AND b.car_id = ?";
        $params[] = $car_id;
    }

    $sql .= " ORDER BY b.booked_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Logs | Obsidian Automotive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar { background: var(--bg-card); height: 100vh; position: fixed; width: 250px; padding-top: 2rem; border-right: 1px solid var(--border-color); }
        .sidebar a { color: var(--text-secondary); padding: 1rem 2rem; display: block; border-left: 2px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: #fff; border-left-color: #fff; }
        .main-content { margin-left: 250px; padding: 3rem; }
        .table { color: var(--text-secondary); border-color: var(--border-color); }
        .table th { background-color: rgba(255,255,255,0.02) !important; color: #fff !important; font-weight: 500; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        .table td { background-color: var(--bg-card) !important; color: var(--text-secondary) !important; padding: 1rem; border-color: var(--border-color); vertical-align: middle; }
        .badge-outline { border: 1px solid var(--border-color); display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="text-center mb-5 brand-logo" style="color: #fff;">Obsidian Automotive</h3>
    <a href="dashboard.php">Fleet Overview</a>
    <a href="add_car.php">Add New Vehicle</a>
    <a href="view_bookings.php" class="active">Booking History</a>
    <a href="../index.php">Return to Website</a>
    <a href="../login.php?logout=1" class="text-danger mt-5">Log Out</a>
</div>

<div class="main-content">
    <h1 class="mb-4 font-heading">Booking History</h1>
    
    <?php if($car_id): ?>
        <p class="mb-5 text-gray mt-2">Currently filtering logs for Asset ID <span class="badge bg-dark fw-normal text-white px-2">#<?= htmlspecialchars($car_id) ?></span> <a href="view_bookings.php" class="text-white text-decoration-underline ms-3 font-body small">Clear View</a></p>
    <?php endif; ?>

    <div class="table-responsive rounded-3 overflow-hidden" style="border: 1px solid var(--border-color);">
        <table class="table table-borderless m-0">
            <thead>
                <tr>
                    <th>Client Profile</th>
                    <th>Asset</th>
                    <th>Term Start</th>
                    <th>Duration</th>
                    <th>Total Remittance</th>
                    <th class="text-end">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($bookings)): ?>
                    <tr><td colspan="6" class="text-center py-5">No client bookings have been initiated for this period.</td></tr>
                <?php endif; ?>
                <?php foreach($bookings as $booking): ?>
                <tr>
                    <td>
                        <div class="text-white fw-bold"><?= htmlspecialchars($booking['customer_name']) ?></div>
                        <div class="small opacity-50"><?= htmlspecialchars($booking['customer_email']) ?></div>
                    </td>
                    <td><span class="badge-outline text-white"><?= htmlspecialchars($booking['car_model']) ?></span></td>
                    <td class="text-white"><?= date('M j, Y', strtotime($booking['start_date'])) ?></td>
                    <td><?= $booking['duration_days'] ?> Days</td>
                    <td class="text-white fw-bold">₹<?= number_format($booking['total_price'], 2) ?></td>
                    <td class="small opacity-50 text-end"><?= date('M j, H:i', strtotime($booking['booked_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
