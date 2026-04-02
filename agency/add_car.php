<?php
    require_once '../includes/db_connection.php';
    require_once '../includes/auth_functions.php';

    if (!isLoggedIn() || !isAgency()) {
        header("Location: ../login.php");
        exit();
    }

    $agency_id = $_SESSION['user_id'];
    $edit_mode = false;
    $car = ['model' => '', 'vehicle_number' => '', 'seating_capacity' => '', 'rent_per_day' => '', 'category' => 'Sedan'];

    // Handle Edit Mode
    if (isset($_GET['edit'])) {
        $edit_mode = true;
        $id = $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND agency_id = ?");
        $stmt->execute([$id, $agency_id]);
        $car = $stmt->fetch();
        if (!$car) { header("Location: dashboard.php"); exit(); }
    }

    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $model = $_POST['model'];
        $v_num = $_POST['vehicle_number'];
        $seats = $_POST['seating_capacity'];
        $rent = $_POST['rent_per_day'];
        $category = $_POST['category'];

        if ($edit_mode) {
            $stmt = $pdo->prepare("UPDATE cars SET model = ?, vehicle_number = ?, seating_capacity = ?, rent_per_day = ?, category = ? WHERE id = ? AND agency_id = ?");
            $stmt->execute([$model, $v_num, $seats, $rent, $category, $id, $agency_id]);
            $success = "Vehicle listing updated successfully!";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO cars (agency_id, model, vehicle_number, seating_capacity, rent_per_day, category) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$agency_id, $model, $v_num, $seats, $rent, $category]);
                $success = "New elite vehicle added to the fleet!";
                $car = ['model' => '', 'vehicle_number' => '', 'seating_capacity' => '', 'rent_per_day' => '', 'category' => 'Sedan'];
            } catch (Exception $e) { $error = "Registration number already exists or invalid data."; }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $edit_mode ? 'Modify Vehicle' : 'Register Vehicle' ?> | Obsidian Automotive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar { background: var(--bg-card); height: 100vh; position: fixed; width: 250px; padding-top: 2rem; border-right: 1px solid var(--border-color); }
        .sidebar a { color: var(--text-secondary); padding: 1rem 2rem; display: block; border-left: 2px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: #fff; border-left-color: #fff; }
        .main-content { margin-left: 250px; padding: 3rem; }
        .card { background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; border-radius: var(--radius-card); max-width: 600px; margin: 0 auto; padding: 2.5rem; }
        .form-control, .form-select { background: var(--bg-dark); border-color: var(--border-color); color: #fff; border-radius: 8px; font-size: 14px; padding: 0.8rem 1rem;}
        .form-control:focus, .form-select:focus { background: var(--bg-dark); border-color: #555; color: #fff; box-shadow: none; }
        .btn-solid-white { width: 100%; margin-top: 1.5rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="text-center mb-5 brand-logo" style="color: #fff;">Obsidian Automotive</h3>
    <a href="dashboard.php">Fleet Overview</a>
    <a href="add_car.php" class="<?= !$edit_mode ? 'active' : '' ?>">Add New Vehicle</a>
    <a href="view_bookings.php">Booking History</a>
    <a href="../index.php">Return to Website</a>
    <a href="../login.php?logout=1" class="text-danger mt-5">Log Out</a>
</div>

<div class="main-content">
    <h1 class="mb-5 text-center font-heading"><?= $edit_mode ? 'Manage Vehicle Asset' : 'Register New Asset' ?></h1>

    <div class="card">
        <?php if(isset($success)): ?><div class="alert alert-success px-3 py-2 text-center" style="background:#002200; color:#55ff55; border:none; font-size:13px;"><?= $success ?></div><?php endif; ?>
        <?php if(isset($error)): ?><div class="alert alert-danger px-3 py-2 text-center" style="background:#220000; color:#ff5555; border:none; font-size:13px;"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-gray small">Vehicle Model (e.g., Porsche 911 GT3)</label>
                <input type="text" name="model" class="form-control" value="<?= htmlspecialchars($car['model']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-gray small">Registration String</label>
                <input type="text" name="vehicle_number" class="form-control" value="<?= htmlspecialchars($car['vehicle_number']) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-gray small">Vehicle Category</label>
                    <select name="category" class="form-select" required>
                        <?php 
                        $cats = ['Sedan', 'SUV', 'Coupe', 'Hatchback', 'MPV', 'Convertible', 'Wagon', 'Pickup Truck', 'Crossover'];
                        foreach($cats as $c): 
                            $sel = ($car['category'] === $c) ? 'selected' : '';
                        ?>
                            <option value="<?= $c ?>" <?= $sel ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-gray small">Passenger Capacity</label>
                    <input type="number" name="seating_capacity" class="form-control" value="<?= $car['seating_capacity'] ?>" required min="1">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label text-gray small">Daily Rental Rate (₹)</label>
                    <input type="number" step="0.01" name="rent_per_day" class="form-control" value="<?= $car['rent_per_day'] ?>" required min="1">
                </div>
            </div>
            <button type="submit" class="btn-pill btn-solid-white fw-bold"><?= $edit_mode ? 'Update Listing' : 'Publish Asset' ?></button>
            <p class="text-center mt-3 mb-0"><a href="dashboard.php" class="text-muted small text-decoration-none">Cancel & Return</a></p>
        </form>
    </div>
</div>

</body>
</html>
