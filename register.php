<?php
require "config/database.php";
require "config/session.php";

$err = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // validasi
    if (!$nama || !$username || !$password) {
        $err = "Semua field wajib diisi.";
    } elseif ($password !== $password2) {
        $err = "Password tidak cocok!";
    } else {

        // cek username unik
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $err = "Username sudah digunakan.";
        } else {

            // insert user (tanpa hash, sesuai permintaan)
            $insert = $mysqli->prepare("
                INSERT INTO users (nama, username, password, role) 
                VALUES (?, ?, ?, 'user')
            ");
            $insert->bind_param("sss", $nama, $username, $password);
            $insert->execute();

            header("Location: login.php?success=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register - VenueBook</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root {
        --dark-blue: #0A2647;
        --neon-green: #00FF88;
        --light-gray: #E0E0E0;
        --white: #FFFFFF;
    }

    body {
        background: var(--light-gray);
    }

    .title {
        color: var(--dark-blue);
        font-weight: bold;
    }

    .btn-neon {
        background: var(--neon-green);
        color: var(--dark-blue);
        font-weight: bold;
    }

    .card {
        border-radius: 12px;
    }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">

            <div class="col-md-5">

                <div class="card p-4">

                    <h3 class="title text-center mb-3">Register</h3>

                    <!-- Error -->
                    <?php if ($err): ?>
                    <div class="alert alert-danger"><?= $err ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control mb-3" required
                            value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">

                        <label>Username</label>
                        <input type="text" name="username" class="form-control mb-3" required
                            value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">

                        <label>Password</label>
                        <input type="password" name="password" class="form-control mb-3" required>

                        <label>Ulangi Password</label>
                        <input type="password" name="password2" class="form-control mb-3" required>

                        <button class="btn btn-neon w-100">Daftar</button>

                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php">Sudah punya akun? Login</a>
                    </div>

                </div>

            </div>

        </div>
    </div>

</body>

</html>