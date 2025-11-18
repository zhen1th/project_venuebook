<?php
require "config/database.php";
require "config/session.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>VenueBook - Home</title>
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

    .hero {
        background: var(--dark-blue);
        padding: 60px;
        color: white;
        border-radius: 10px;
    }

    .btn-neon {
        background: var(--neon-green);
        color: var(--dark-blue);
        font-weight: bold;
    }
    </style>
</head>

<body>

    <div class="container py-5">

        <div class="hero text-center mb-5">
            <h1>Selamat Datang di VenueBook</h1>
            <p>Sistem Pemesanan Venue Olahraga</p>

            <?php if (!is_logged_in()): ?>
            <a href="login.php" class="btn btn-neon btn-lg">Mulai Sekarang</a>
            <?php endif; ?>
        </div>

        <h3 class="fw-bold mb-3">Venue Tersedia</h3>

        <div class="row">
            <p>Belum ada venue ditambahkan admin.</p>
        </div>

    </div>

</body>

</html>