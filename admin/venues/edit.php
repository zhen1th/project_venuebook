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

if (!$venue) die("Venue tidak ditemukan!");

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = $_POST['nama_venue'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $alamat = $_POST['alamat'];
    $harga = $_POST['harga_per_jam'];
    $fasilitas = $_POST['fasilitas'];
    $status = $_POST['status'];

    if (!$nama || !$alamat || !$harga) {
        $err = "Nama, alamat, dan harga wajib diisi!";
    } else {

        if (!empty($_FILES['gambar']['name'])) {
            $gambarName = time() . "_" . $_FILES['gambar']['name'];
            move_uploaded_file($_FILES['gambar']['tmp_name'], "../../assets/images/" . $gambarName);

            $stmt = $mysqli->prepare("UPDATE venues SET nama_venue=?, kategori=?, deskripsi=?, alamat=?, harga_per_jam=?, fasilitas=?, gambar=?, status=?WHERE id=?");
            $stmt->bind_param("ssssdsssi", $nama, $kategori, $deskripsi, $alamat, $harga, $fasilitas, $gambarName, $status, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE venues SET nama_venue=?, kategori=?, deskripsi=?, alamat=?, harga_per_jam=?, fasilitas=?, status=? WHERE id=?");
            $stmt->bind_param("ssssdssi", $nama, $kategori, $deskripsi, $alamat, $harga, $fasilitas, $status, $id);
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
            <input type="text" name="nama_venue" class="form-control mb-3" value="<?= $venue['nama_venue'] ?>" required>

            <div class="mb-3">
                <label class="form-label fw-bold">Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Sepakbola" <?= $venue['kategori'] == "Sepakbola" ? "selected" : "" ?>>Sepakbola
                    </option>
                    <option value="Futsal" <?= $venue['kategori'] == "Futsal" ? "selected" : "" ?>>Futsal</option>
                    <option value="Mini Soccer" <?= $venue['kategori'] == "Mini Soccer" ? "selected" : "" ?>>Mini Soccer
                    </option>
                    <option value="Basket" <?= $venue['kategori'] == "Basket" ? "selected" : "" ?>>Basket</option>
                    <option value="Voli" <?= $venue['kategori'] == "Voli" ? "selected" : "" ?>>Voli</option>
                    <option value="Bulu Tangkis" <?= $venue['kategori'] == "Bulu Tangkis" ? "selected" : "" ?>>Bulu
                        Tangkis</option>
                    <option value="Tenis" <?= $venue['kategori'] == "Tenis" ? "selected" : "" ?>>Tenis</option>
                    <option value="Renang" <?= $venue['kategori'] == "Renang" ? "selected" : "" ?>>Renang</option>
                    <option value="Gym" <?= $venue['kategori'] == "Gym" ? "selected" : "" ?>>Gym</option>
                    <option value="Bela Diri" <?= $venue['kategori'] == "Bela Diri" ? "selected" : "" ?>>Bela Diri
                    </option>
                    <option value="Lainnya" <?= $venue['kategori'] == "Lainnya" ? "selected" : "" ?>>Lainnya</option>
                </select>
            </div>

            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control mb-3"><?= $venue['deskripsi'] ?></textarea>

            <label>Alamat</label>
            <textarea name="alamat" class="form-control mb-3"><?= $venue['alamat'] ?></textarea>

            <label>Harga per Jam</label>
            <input type="number" name="harga_per_jam" class="form-control" value="<?= $venue['harga_per_jam'] ?>"
                required>

            <label>Fasilitas</label>
            <textarea name="fasilitas" class="form-control mb-3"><?= $venue['fasilitas'] ?></textarea>

            <label>Status</label>
            <select name="status" class="form-control mb-3">
                <option value="available" <?= $venue['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                <option value="unavailable" <?= $venue['status'] == 'unavailable' ? 'selected' : '' ?>>Unavailable
                </option>
            </select>

            <label>Gambar Baru (opsional)</label>
            <input type="file" name="gambar" class="form-control mb-3">

            <?php if ($venue['gambar']): ?>
            <img src="../../assets/images/<?= $venue['gambar'] ?>" width="120" class="my-2">
            <?php endif; ?>

            <br>

            <button class="btn btn-neon">Update</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>

        </form>

    </div>

</body>

</html>