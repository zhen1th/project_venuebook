<?php
require "../config/database.php";
require "../config/session.php";
require_login();

$q = $mysqli->query("SELECT * FROM venues WHERE status='available' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard - VenueBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root {
        --dark-blue: #0A2647;
        --neon-green: #00FF88;
        --light-gray: #E0E0E0;
        --white: #ffffff;
    }

    body {
        background: var(--light-gray);
    }

    .navbar-sporty {
        background: var(--dark-blue);
        padding: 15px;
    }

    .navbar-brand {
        color: var(--neon-green) !important;
        font-size: 1.6rem;
        font-weight: bold;
    }

    .nav-link {
        color: var(--white) !important;
    }

    .btn-neon {
        background: var(--neon-green);
        color: var(--dark-blue);
        font-weight: bold;
    }

    .card-venue {
        border: 2px solid var(--dark-blue);
        border-radius: 10px;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-sporty">
        <div class="container">
            <a class="navbar-brand">VenueBook</a>

            <div class="ms-auto">
                <span class="text-white me-3">Halo, <?= $_SESSION['user']['nama'] ?></span>
                <a href="../logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <h2 class="fw-bold mb-4">Dashboard User</h2>

        <div class="d-flex justify-content-between mb-3">
            <h4>Daftar Venue</h4>
            <a href="my_bookings.php" class="btn btn-neon">Riwayat Booking</a>
        </div>

        <div class="row">
            <?php while ($v = $q->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card card-venue">

                    <?php if ($v['gambar']): ?>
                    <img src="../assets/images/<?= $v['gambar'] ?>" class="card-img-top"
                        style="height:180px;object-fit:cover">
                    <?php else: ?>
                    <div style="height:180px;background:#ddd"></div>
                    <?php endif; ?>

                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?= $v['nama_venue'] ?></h5>
                        <p><?= $v['deskripsi'] ?></p>
                        <p class="fw-bold text-success">Rp <?= number_format($v['harga_per_jam'], 0, ',', '.') ?>/jam
                        </p>

                        <a href="booking.php?venue_id=<?= $v['id'] ?>" class="btn btn-neon w-100">
                            Pesan Sekarang
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>

</body>

</html>