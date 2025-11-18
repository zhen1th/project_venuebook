<?php
require "../config/database.php";
require "../config/session.php";
require_admin();

// hitung statistik
$totalVenue = $mysqli->query("SELECT COUNT(*) AS c FROM venues")->fetch_assoc()['c'];
$totalUser  = $mysqli->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch_assoc()['c'];
$totalBook  = $mysqli->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard - VenueBook</title>
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

    .card-stat {
        border-left: 6px solid var(--dark-blue);
        background: var(--white);
        border-radius: 10px;
        padding: 20px;
    }

    .btn-neon {
        background: var(--neon-green);
        color: var(--dark-blue);
        font-weight: bold;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-sporty">
        <div class="container">
            <a class="navbar-brand">VenueBook Admin</a>

            <div class="ms-auto">
                <span class="text-white me-3">Admin: <?= $_SESSION['user']['nama'] ?></span>
                <a href="../logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <h2 class="fw-bold mb-4">Dashboard Admin</h2>

        <div class="row">

            <div class="col-md-4">
                <div class="card-stat shadow-sm">
                    <h4>Total Venue</h4>
                    <h2><?= $totalVenue ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-stat shadow-sm">
                    <h4>Total User</h4>
                    <h2><?= $totalUser ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-stat shadow-sm">
                    <h4>Total Booking</h4>
                    <h2><?= $totalBook ?></h2>
                </div>
            </div>

        </div>

        <div class="mt-4">
            <a href="venues/index.php" class="btn btn-neon me-3">Kelola Venue</a>
            <a href="bookings.php" class="btn btn-neon">Kelola Booking</a>
        </div>

    </div>

</body>

</html>