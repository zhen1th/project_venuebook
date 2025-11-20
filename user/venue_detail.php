<?php
require "../config/database.php";
require "../config/session.php";

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM venues WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$venue = $stmt->get_result()->fetch_assoc();

if (!$venue) {
    die("Venue tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $venue['nama_venue'] ?></title>
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
    </style>

</head>

<body>

    <div class="container py-4">

        <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

        <div class="card shadow p-4">

            <img src="../assets/images/<?= $venue['gambar'] ?>" class="mb-3"
                style="width:100%; max-height:400px; object-fit:cover; border-radius:12px;">

            <h2 class="fw-bold"><?= $venue['nama_venue'] ?></h2>

            <p class="mt-3"><b>Deskripsi:</b> <?= $venue['deskripsi'] ?></p>
            <p><b>Alamat:</b> <?= $venue['alamat'] ?></p>
            <p><b>Fasilitas:</b> <?= $venue['fasilitas'] ?></p>

            <h4 class="fw-bold text-success">
                Rp <?= number_format($venue['harga_per_jam'], 0, ',', '.') ?>/jam
            </h4>

            <a href="booking.php?id=<?= $venue['id'] ?>" class="btn btn-neon btn-lg mt-3">
                Pesan Sekarang
            </a>

        </div>

    </div>

</body>

</html>