<?php
    require_once '../includes/db_connection.php';
    require_once '../includes/auth_functions.php';

    if (!isLoggedIn() || !isAgency()) {
        header("Location: ../login.php");
        exit();
    }

    $agency_id = $_SESSION['user_id'];
    
    // Fetch agency's cars
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE agency_id = ? ORDER BY created_at DESC");
    $stmt->execute([$agency_id]);
    $my_cars = $stmt->fetchAll();

    // Fetch total bookings for this agency's cars
    $stmt_bookings = $pdo->prepare("SELECT COUNT(*) FROM bookings b JOIN cars c ON b.car_id = c.id WHERE c.agency_id = ?");
    $stmt_bookings->execute([$agency_id]);
    $total_bookings = $stmt_bookings->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agency Dashboard | Obsidian Automotive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar { background: var(--bg-card); height: 100vh; position: fixed; width: 250px; padding-top: 2rem; border-right: 1px solid var(--border-color); }
        .sidebar a { color: var(--text-secondary); padding: 1rem 2rem; display: block; border-left: 2px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: #fff; border-left-color: #fff; }
        .main-content { margin-left: 250px; padding: 3rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-card); text-align: center; padding: 2.5rem; margin-bottom: 2rem; }
        .stat-val { font-size: 3.5rem; font-weight: 700; font-family: var(--font-brand); color: #fff; line-height: 1; margin-bottom: 0.5rem; }
        .stat-label { text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.15em; color: var(--text-muted); }
        .table { color: var(--text-secondary); border-color: var(--border-color); }
        .table th { background-color: rgba(255,255,255,0.02) !important; color: #fff !important; font-weight: 500; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        .table td { background-color: var(--bg-card) !important; color: var(--text-secondary) !important; padding: 1rem; border-color: var(--border-color); vertical-align: middle; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="text-center mb-5 brand-logo" style="color: #fff;">Obsidian Automotive</h3>
    <a href="dashboard.php" class="active">Fleet Overview</a>
    <a href="add_car.php">Add New Vehicle</a>
    <a href="view_bookings.php">Booking History</a>
    <a href="../index.php">Return to Website</a>
    <a href="../login.php?logout=1" class="text-danger mt-5">Log Out</a>
</div>

<div class="main-content">
    <h1 class="mb-5 font-heading">Partner Portal <span class="text-muted fs-4 fw-normal">/ <?= htmlspecialchars($_SESSION['user_name']) ?></span></h1>
    
    <div class="row g-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-val"><?= count($my_cars) ?></div>
                <div class="stat-label">Active Vehicles in Fleet</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-val"><?= $total_bookings ?></div>
                <div class="stat-label">Lifetime Bookings</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
        <h3 class="font-heading m-0">Fleet Roster</h3>
        <a href="add_car.php" class="btn-pill btn-solid-white py-2 px-4 shadow-sm" style="font-size: 13px;">+ Register Vehicle</a>
    </div>

    <div class="table-responsive rounded-3 overflow-hidden" style="border: 1px solid var(--border-color);">
        <table class="table table-borderless m-0">
            <thead>
                <tr>
                    <th>Model Designation</th>
                    <th>Registration</th>
                    <th>Category</th>
                    <th>Config</th>
                    <th>Daily Rate</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($my_cars)): ?>
                    <tr><td colspan="5" class="text-center py-5">Your Obsidian fleet is currently empty.</td></tr>
                <?php endif; ?>
                <?php foreach($my_cars as $car): ?>
                <tr>
                    <td class="text-white fw-bold"><?= htmlspecialchars($car['model']) ?></td>
                    <td><span class="badge border border-secondary text-gray bg-transparent fw-normal"><?= htmlspecialchars($car['vehicle_number']) ?></span></td>
                    <td><?= htmlspecialchars($car['category']) ?></td>
                    <td><?= $car['seating_capacity'] ?> Passenger</td>
                    <td>₹<?= number_format($car['rent_per_day'], 2) ?></td>
                    <td class="text-end">
                        <a href="add_car.php?edit=<?= $car['id'] ?>" class="btn-pill btn-ghost py-1 px-3" style="font-size:12px;">Modify</a>
                        <a href="view_bookings.php?car_id=<?= $car['id'] ?>" class="btn-pill btn-solid-white py-1 px-3 ms-2" style="font-size:12px;">Logs</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
