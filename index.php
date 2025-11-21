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

    <div class="container mt-5 mb-5">

        <h2 class="fw-bold text-center" style="color:#0A2647;">Kenapa Memilih VanueBook?</h2>

        <div class="row mt-4 text-center">

            <div class="col-md-4 mb-4">
                <div class="p-4 shadow-sm rounded" style="background:#fff;">
                    <img src="assets/icons/booking.png" width="70" class="mb-3">
                    <h5 class="fw-bold">Mudah & Cepat</h5>
                    <p class="text-muted">Cari dan pesan venue olahraga dengan beberapa klik saja, tanpa ribet.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="p-4 shadow-sm rounded" style="background:#fff;">
                    <img src="assets/icons/price.png" width="70" class="mb-3">
                    <h5 class="fw-bold">Harga Transparan</h5>
                    <p class="text-muted">Semua harga ditampilkan secara jelas tanpa tambahan tersembunyi.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="p-4 shadow-sm rounded" style="background:#fff;">
                    <img src="assets/icons/sport.png" width="70" class="mb-3">
                    <h5 class="fw-bold">Banyak Pilihan</h5>
                    <p class="text-muted">Futsal, mini soccer, badminton, basket, dan banyak venue lainnya.</p>
                </div>
            </div>

        </div>
    </div>

    <footer style="background:#0A2647; color:white; padding:60px 0; margin-top:80px;">
        <div class="container">

            <div class="row">

                <div class="col-md-4 mb-4">
                    <h3 class="fw-bold">VanueBook</h3>
                    <p class="mt-3" style="line-height:1.7;">
                        Jl. Contoh Raya No. 123<br>
                        Jakarta Pusat 10340<br>
                        Indonesia
                    </p>

                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Perusahaan</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Tentang</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Ecosystem</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Sparring</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Main Bareng</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Direktori Tim</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Direktori Lapangan</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Kontak</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Email Support</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="fw-bold mb-3">Unduh Aplikasi</h5>
                    <div class="d-flex gap-3 fs-3">
                        <i class="bi bi-google-play text-white-50"></i>
                        <i class="bi bi-apple text-white-50"></i>
                    </div>
                </div>

            </div>

            <div class="text-center mt-4 text-white-50" style="font-size:14px;">
                © <?= date("Y") ?> VanueBook — All Rights Reserved.
            </div>

        </div>
    </footer>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>