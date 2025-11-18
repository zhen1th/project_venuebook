<?php
require "../../config/database.php";
require "../../config/session.php";
require_admin();

$venues = $mysqli->query("SELECT * FROM venues ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Venue - Admin</title>
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

    .navbar-sporty {
        background: var(--dark-blue);
        padding: 15px;
    }

    .navbar-brand {
        color: var(--neon-green) !important;
        font-weight: bold;
    }

    .btn-neon {
        background: var(--neon-green);
        color: var(--dark-blue);
        font-weight: bold;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-sporty">
        <div class="container">
            <a class="navbar-brand">Admin Panel</a>
            <div class="ms-auto">
                <a href="../dashboard.php" class="btn btn-light me-2">Dashboard</a>
                <a href="../../logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="fw-bold mb-3">Kelola Venue</h2>

        <a href="add.php" class="btn btn-neon mb-3">+ Tambah Venue</a>

        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama Venue</th>
                    <th>Gambar</th>
                    <th>Alamat</th>
                    <th>Harga / Jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($v = $venues->fetch_assoc()): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= $v['nama_venue'] ?></td>

                    <td>
                        <?php if ($v['gambar']): ?>
                        <img src="../../assets/images/<?= $v['gambar'] ?>" width="80">
                        <?php else: ?> - <?php endif; ?>
                    </td>

                    <td><?= $v['alamat'] ?></td>
                    <td>Rp <?= number_format($v['harga_per_jam'], 0, ',', '.') ?></td>

                    <td>
                        <?php if ($v['status'] == 'available'): ?>
                        <span class="badge bg-success">Available</span>
                        <?php else: ?>
                        <span class="badge bg-danger">Unavailable</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="edit.php?id=<?= $v['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?id=<?= $v['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin hapus ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</body>

</html>