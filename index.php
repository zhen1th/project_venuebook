<?php
require "config/database.php";

$carousel = $mysqli->query("
    SELECT gambar FROM venues 
    WHERE status='available' 
    ORDER BY id DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>VanueBook - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root {
        --dark-blue: #0A2647;
        --neon-green: #00FF88;
    }

    .navbar {
        background: var(--dark-blue);
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
    }

    .hero {
        background: var(--dark-blue);
        color: white;
        padding: 70px 0;
        text-align: center;
    }

    .btn-neon {
        background: var(--neon-green);
        font-weight: bold;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg px-4 py-2">
        <a class="navbar-brand fw-bold">VanueBook</a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-light me-2">Login</a>
            <a href="register.php" class="btn btn-success">Register</a>
        </div>
    </nav>

    <div class="hero">
        <h1 class="fw-bold">Cari Venue Olahraga Favoritmu</h1>
        <p>Gunakan fitur pencarian dan filter untuk menemukan venue terbaik.</p>
    </div>

    <div class="container mt-4">

        <form action="user/dashboard.php" method="GET" class="d-flex gap-2 mb-4">

            <div class="col-md-10">
                <input type="text" name="search" placeholder="Cari venue atau lokasi..." class="form-control">
            </div>

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
                        <option value="Sepakbola">Sepakbola</option>
                        <option value="Futsal">Futsal</option>
                        <option value="Mini Soccer">Mini Soccer</option>
                        <option value="Basket">Basket</option>
                        <option value="Voli">Voli</option>
                        <option value="Bulu Tangkis">Bulu Tangkis</option>
                        <option value="Tenis">Tenis</option>
                        <option value="Renang">Renang</option>
                    </select>
                    <button class="btn btn-neon w-100 mb-2" type="submit">Terapkan</button>
                    <a href="dashboard.php" class="btn btn-dark w-100">Reset</a>
                </div>
            </div>

        </form>
    </div>

    <div class="container mt-4">
        <div id="venueCarousel" class="carousel slide shadow" data-bs-ride="carousel">
            <div class="carousel-inner">

                <?php
                $active = "active";
                while ($img = $carousel->fetch_assoc()): ?>
                <div class="carousel-item <?= $active ?>">
                    <img src="assets/images/<?= $img['gambar'] ?>" class="d-block w-100"
                        style="height:420px; object-fit:cover;">
                </div>
                <?php
                    $active = "";
                endwhile;
                ?>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#venueCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#venueCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>