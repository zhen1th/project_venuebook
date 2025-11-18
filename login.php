<?php
require "config/database.php";
require "config/session.php";

$msg = "";
if (isset($_GET['success'])) {
    $msg = "Registrasi berhasil! Silakan login.";
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, nama, username, password, role FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $password === $user['password']) {

        $_SESSION['user'] = $user;

        if ($user['role'] === "admin") {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $err = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login - VenueBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root {
        --dark-blue: #0A2647;
        --neon-green: #00FF88;
        --light-gray: #E0E0E0;
    }

    body {
        background: var(--light-gray);
    }

    .btn-neon {
        background: var(--neon-green);
        color: var(--dark-blue);
        font-weight: bold;
    }

    .title {
        color: var(--dark-blue);
        font-weight: bold;
    }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">

            <div class="col-md-5">
                <div class="card p-4">

                    <h3 class="title text-center">Login</h3>

                    <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>

                    <?php if ($err): ?>
                    <div class="alert alert-danger"><?= $err ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <label>Username</label>
                        <input type="text" name="username" class="form-control mb-3" required>

                        <label>Password</label>
                        <input type="password" name="password" class="form-control mb-3" required>

                        <button class="btn btn-neon w-100">Login</button>

                    </form>

                    <div class="text-center mt-3">
                        <a href="register.php">Belum punya akun? Register</a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</body>

</html>