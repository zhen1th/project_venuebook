<?php
require "../config/database.php";
require "../config/session.php";

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

function potongDeskripsi($text, $limit = 150)
{
    if (strlen($text) <= $limit) return $text;
    return substr($text, 0, $limit) . "...";
}

$search     = isset($_GET['search']) ? trim($_GET['search']) : "";
$kategori   = isset($_GET['kategori']) ? trim($_GET['kategori']) : "";
$min_price  = isset($_GET['min_price']) ? trim($_GET['min_price']) : "";
$max_price  = isset($_GET['max_price']) ? trim($_GET['max_price']) : "";
$sort       = isset($_GET['sort']) ? trim($_GET['sort']) : "";


$query = "SELECT * FROM venues WHERE status = 'available'";


if ($kategori !== "") {
    $kategori_safe = $mysqli->real_escape_string($kategori);
    $query .= " AND kategori = '$kategori_safe'";
}


if ($search !== "") {
    $search_safe = strtolower($mysqli->real_escape_string($search));
    $query .= " AND (
        LOWER(nama_venue) LIKE '%$search_safe%' OR
        LOWER(alamat) LIKE '%$search_safe%' OR
        LOWER(kategori) LIKE '%$search_safe%' OR
        LOWER(deskripsi) LIKE '%$search_safe%'
    )";
}


if ($min_price !== "" && is_numeric($min_price)) {
    $query .= " AND harga_per_jam >= $min_price";
}

if ($max_price !== "" && is_numeric($max_price)) {
    $query .= " AND harga_per_jam <= $max_price";
}


if ($sort == "low") {
    $query .= " ORDER BY harga_per_jam ASC";
} elseif ($sort == "high") {
    $query .= " ORDER BY harga_per_jam DESC";
} else {
    $query .= " ORDER BY id ASC";
}


$venues = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root {
        --dark-blue: #0A2647;
        --neon-green: #00FF88;
        --light-gray: #E0E0E0;
        --white: #FFFFFF;
        --dark-gray: #1B1B1B;
    }

    body {
        background: #e9ecef;
    }

    .navbar {
        background: var(--dark-blue);
    }

    .navbar-brand,
    .nav-link,
    .nav-item {
        color: var(--white) !important;
    }

    .btn-neon {
        background: var(--neon-green);
        color: #000;
        font-weight: bold;
        border-radius: 10px;
    }

    .card-venue {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .card-desc {
        flex-grow: 1;
        overflow: hidden;
    }

    .card-venue:hover {
        transform: scale(1.02);
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
    }

    .card-venue img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 5px;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg px-4 py-2">
        <a class="navbar-brand fw-bold" href="#">VanueBook</a>

        <div class="ms-auto d-flex align-items-center gap-3">

            <?php if ($user): ?>
            <span class="text-white">
                Halo, <strong><?= $user['nama'] ?></strong>
            </span>

            <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>

            <?php else: ?>
            <div class="ms-auto">
                <a href="../login.php" class="btn btn-light me-2">Login</a>
                <a href="../register.php" class="btn btn-success">Register</a>
            </div>
            <?php endif; ?>

        </div>
    </nav>

    <div class="container mt-4">

        <h3 class="fw-bold mb-3">Daftar Venue</h3>

        <form method="GET" class="d-flex gap-2 mb-4">
            <input type="text" name="search" class="form-control" placeholder="Cari venue atau lokasi..."
                value="<?= htmlspecialchars($search) ?>">

            <button class="btn btn-success" type="submit">
                Search
            </button>

            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Filter
                </button>
                <div class="dropdown-menu p-3" style="width:260px;">
                    <label class="fw-bold">Kategori</label>
                    <select name="kategori" class="form-control mb-3">
                        <option value="">Semua Kategori</option>
                        <option value="Sepakbola"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Sepakbola" ? "selected" : "" ?>>
                            Sepakbola</option>
                        <option value="Futsal"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Futsal" ? "selected" : "" ?>>Futsal
                        </option>
                        <option value="Mini Soccer"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Mini Soccer" ? "selected" : "" ?>>Mini
                            Soccer</option>
                        <option value="Basket"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Basket" ? "selected" : "" ?>>Basket
                        </option>
                        <option value="Voli"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Voli" ? "selected" : "" ?>>Voli
                        </option>
                        <option value="Bulu Tangkis"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Bulu Tangkis" ? "selected" : "" ?>>
                            Bulu Tangkis</option>
                        <option value="Tenis"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Tenis" ? "selected" : "" ?>>Tenis
                        </option>
                        <option value="Renang"
                            <?= isset($_GET['kategori']) && $_GET['kategori'] == "Renang" ? "selected" : "" ?>>Renang
                        </option>
                    </select>
                    <label class="mb-1 fw-bold">Harga Minimum</label>
                    <input type="number" name="min_price" class="form-control mb-2" value="<?= $min_price ?>">

                    <label class="mb-1 fw-bold">Harga Maksimum</label>
                    <input type="number" name="max_price" class="form-control mb-2" value="<?= $max_price ?>">

                    <label class="fw-bold">Urutkan</label>
                    <select name="sort" class="form-control mb-3">
                        <option value="">Default</option>
                        <option value="low" <?= $sort == "low" ? "selected" : "" ?>>Harga Terendah</option>
                        <option value="high" <?= $sort == "high" ? "selected" : "" ?>>Harga Tertinggi</option>
                    </select>

                    <button class="btn btn-neon w-100 mb-2" type="submit">Terapkan</button>
                    <a href="dashboard.php" class="btn btn-dark w-100">Reset</a>
                </div>
            </div>
        </form>

        <div class="row">
            <?php if ($venues->num_rows == 0): ?>
            <p class="text-center text-muted mt-4">Tidak ada venue ditemukan.</p>
            <?php endif; ?>

            <?php while ($v = $venues->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <a href="venue_detail.php?id=<?= $v['id'] ?>" style="text-decoration:none; color:inherit;">
                    <div class="card card-venue shadow">
                        <img src="../assets/images/<?= $v['gambar'] ?>" alt="Gambar Venue">
                        <div class="card-body d-flex flex-column">

                            <h4 class="fw-bold"><?= $v['nama_venue'] ?></h4>

                            <p><?= potongDeskripsi($v['deskripsi'], 150) ?></p>

                            <h5 class="fw-bold text-success mt-auto">
                                Rp <?= number_format($v['harga_per_jam'], 0, ',', '.') ?>/jam
                            </h5>

                            <button class="btn btn-neon mt-3">Pesan Sekarang</button>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>