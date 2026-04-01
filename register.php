<?php
    require_once 'includes/db_connection.php';
    require_once 'includes/auth_functions.php';

    redirectIfLoggedIn();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'customer'; // Fixed role for this page
        $contact = $_POST['contact'];

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, contact_number) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $password, $role, $contact]);
            header("Location: login.php?msg=" . urlencode("Registration successful! Welcome to Obsidian Automotive."));
            exit();
        } catch (Exception $e) { $error = "Email already exists or invalid data provided."; }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Sign Up | Obsidian Automotive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; padding: 2rem 0; }
        .auth-card { background: var(--bg-card); padding: 3rem; border-radius: var(--radius-card); width: 100%; max-width: 450px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); border: 1px solid var(--border-color); }
        .form-control, .form-select { background: var(--bg-dark); border-color: var(--border-color); color: #fff; padding: 0.8rem 1rem; border-radius: 8px; font-size: 14px; }
        .form-control:focus, .form-select:focus { background: var(--bg-dark); color: #fff; border-color: #555; box-shadow: none; }
        .btn-solid-white { width: 100%; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2 class="text-center mb-1 brand-logo">Obsidian Automotive</h2>
        <p class="text-center text-muted small mb-4">Create your elite profile</p>
        
        <?php if(isset($error)): ?><div class="alert alert-danger px-3 py-2 text-center" style="background:#220000; color:#ff5555; border:none; font-size:13px;"><?= $error ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-gray small">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-gray small">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-gray small">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-gray small">Contact Number (Optional)</label>
                <input type="text" name="contact" class="form-control" placeholder="+1 (555) 000-0000">
            </div>
            <button type="submit" class="btn-pill btn-solid-white fw-bold">Register as Customer</button>
            <p class="text-center mt-4 small text-muted">A Car Rental Agency? <a href="register_agency.php" class="text-white text-decoration-underline">Register your fleet here</a></p>
            <p class="text-center mt-2 small text-muted">Already a member? <a href="login.php" class="text-white text-decoration-underline">Log in</a></p>
        </form>
    </div>
</body>
</html>
