<?php
require "../../config/database.php";
require "../../config/session.php";
require_admin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$venue = $mysqli->query("SELECT * FROM venues WHERE id=$id")->fetch_assoc();

if (!$venue) {
    die("Venue tidak ditemukan.");
}

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    if (!$nama || !$harga) {
        $err = "Nama dan harga wajib diisi!";
    } else {

        // cek jika ada upload gambar baru
        if (!empty($_FILES['gambar']['name'])) {
            $gambarName = time() . "_" . $_FILES['gambar']['name'];
            move_uploaded_file($_FILES['gambar']['tmp_name'], "../../assets/images/" . $gambarName);

            $stmt = $mysqli->prepare("
                UPDATE venues SET nama=?, harga=?, deskripsi=?, gambar=? WHERE id=?
            ");
            $stmt->bind_param("sissi", $nama, $harga, $deskripsi, $gambarName, $id);
        } else {
            $stmt = $mysqli->prepare("
                UPDATE venues SET nama=?, harga=?, deskripsi=? WHERE id=?
            ");
            $stmt->bind_param("sisi", $nama, $harga, $deskripsi, $id);
        }

        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Venue</title>
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
        <h2 class="fw-bold mb-3">Edit Venue</h2>

        <?php if ($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Nama Venue</label>
            <input type="text" name="nama" value="<?= $venue['nama'] ?>" class="form-control mb-3" required>

            <label>Harga per Jam</label>
            <input type="number" name="harga" value="<?= $venue['harga'] ?>" class="form-control mb-3" required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control mb-3"><?= $venue['deskripsi'] ?></textarea>

            <label>Gambar Baru (opsional)</label>
            <input type="file" name="gambar" class="form-control mb-3">

            <?php if ($venue['gambar']): ?>
            <p>Gambar saat ini:</p>
            <img src="../../assets/images/<?= $venue['gambar'] ?>" width="120">
            <?php endif; ?>

            <br><br>

            <button class="btn btn-neon">Update</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>

        </form>

    </div>

</body>

</html>