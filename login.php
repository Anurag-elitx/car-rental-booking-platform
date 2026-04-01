<?php
    require_once 'includes/db_connection.php';
    require_once 'includes/auth_functions.php';

    if (isset($_GET['logout'])) {
        logoutUser();
    }

    redirectIfLoggedIn();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user['id'], $user['role'], $user['name']);
            if (isAgency()) header("Location: agency/dashboard.php");
            else header("Location: index.php");
            exit();
        } else { $error = "Invalid credentials."; }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Log In | Obsidian Automotive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .auth-card { background: var(--bg-card); padding: 3rem; border-radius: var(--radius-card); width: 100%; max-width: 420px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); border: 1px solid var(--border-color); }
        .form-control { background: var(--bg-dark); border-color: var(--border-color); color: #fff; padding: 0.8rem 1rem; border-radius: 8px; font-size: 14px; }
        .form-control:focus { background: var(--bg-dark); color: #fff; border-color: #555; box-shadow: none; }
        .btn-solid-white { width: 100%; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2 class="text-center mb-1 brand-logo">Obsidian Automotive</h2>
        <p class="text-center text-muted small mb-4">Elite Automotive Experiences</p>
        
        <?php if(isset($error)): ?><div class="alert alert-danger px-3 py-2 text-center" style="background:#220000; color:#ff5555; border:none; font-size:13px;"><?= $error ?></div><?php endif; ?>
        <?php if(isset($_GET['msg'])): ?><div class="alert alert-success px-3 py-2 text-center" style="background:#002200; color:#55ff55; border:none; font-size:13px;"><?= $_GET['msg'] ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-gray small">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-gray small">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-pill btn-solid-white fw-bold">Sign In</button>
            <p class="text-center mt-4 small text-muted mb-1">New Customer? <a href="register.php" class="text-white text-decoration-underline">Sign up now</a></p>
            <p class="text-center small text-muted">New Agency? <a href="register_agency.php" class="text-white text-decoration-underline">Register your fleet</a></p>
            <p class="text-center mt-3 mb-0"><a href="index.php" class="text-muted small text-decoration-none">← Back to Home</a></p>
        </form>
    </div>
</body>
</html>
