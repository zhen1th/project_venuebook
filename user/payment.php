<?php
require "../config/database.php";
require "../config/session.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

if (!isset($_SESSION['temp_booking'])) {
    header("Location: venues.php?msg=no_booking_data");
    exit;
}

$temp_booking = $_SESSION['temp_booking'];
$venue_id = $temp_booking['venue_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode = $_POST['metode_pembayaran'] ?? '';
    
    try {
        $mysqli->begin_transaction();

        $insert_booking = $mysqli->prepare("
            INSERT INTO bookings (user_id, venue_id, tanggal_booking, jam_mulai, jam_selesai, total_harga, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'confirmed', NOW())
        ");

        $booking_ids = [];
        foreach ($temp_booking['slots'] as $slot) {
            $insert_booking->bind_param(
                "iisssd",
                $user_id,
                $venue_id,
                $temp_booking['tanggal_booking'],
                $slot['start'],
                $slot['end'],
                $slot['price']
            );
            
            if (!$insert_booking->execute()) {
                throw new Exception("Gagal menyimpan booking.");
            }
            
            $booking_ids[] = $insert_booking->insert_id;
        }

        $insert_payment = $mysqli->prepare("
            INSERT INTO payments (booking_id, metode_pembayaran, status, created_at) 
            VALUES (?, ?, 'success', NOW())
        ");
        
        foreach ($booking_ids as $booking_id) {
            $insert_payment->bind_param("is", $booking_id, $metode);
            $insert_payment->execute();
        }

        $mysqli->commit();

        unset($_SESSION['temp_booking']);
        
        $success = true;
        $first_booking_id = $booking_ids[0]; 

    } catch (Exception $e) {
        $mysqli->rollback();
        $error = "Pembayaran gagal: " . $e->getMessage();
    }
}

$query = $mysqli->prepare("SELECT * FROM venues WHERE id = ?");
$query->bind_param("i", $venue_id);
$query->execute();
$venue = $query->get_result()->fetch_assoc();

if (!$venue) die("Venue tidak ditemukan.");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Pembayaran</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-blue: #0A2647;
            --bg: #f7f7f9;
        }

        body {
            background: var(--bg);
            font-family: system-ui, -apple-system, sans-serif;
        }

        .navbar {
            background: var(--dark-blue) !important;
        }

        .navbar-brand,
        .navbar .text-white {
            color: #fff !important;
        }

        .container {
            max-width: 600px;
            margin-top: 2rem;
        }

        .card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .venue-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail:last-child {
            border-bottom: none;
            font-weight: 700;
        }

        .payment-option {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .payment-option:hover {
            border-color: var(--dark-blue);
        }

        .payment-option input {
            margin-right: 0.5rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #d1e7dd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .success-icon svg {
            width: 48px;
            height: 48px;
            color: #0f5132;
        }
        
        .expiry-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg px-4 py-2">
        <a class="navbar-brand fw-bold" href="dashboard.php">VenueBook</a>
        <div class="ms-auto">
            <span class="text-white">Halo, <strong><?= htmlspecialchars($user['nama']) ?></strong></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mb-5">
        <?php if (isset($success)): ?>
            <div class="card p-5 text-center">
                <div class="success-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-success mb-2">Pembayaran Berhasil!</h2>
                <p class="text-muted mb-4">Booking Anda telah dikonfirmasi</p>

                <div class="text-start">
                    <div class="detail">
                        <span>Venue</span>
                        <span><?= htmlspecialchars($venue['nama_venue']) ?></span>
                    </div>
                    <div class="detail">
                        <span>Tanggal</span>
                        <span><?= date('d M Y', strtotime($temp_booking['tanggal_booking'])) ?></span>
                    </div>
                    <div class="detail">
                        <span>Jumlah Slot</span>
                        <span><?= count($temp_booking['slots']) ?> slot</span>
                    </div>
                    <div class="detail">
                        <span>Waktu</span>
                        <span>
                            <?php 
                            $times = [];
                            foreach ($temp_booking['slots'] as $slot) {
                                $times[] = $slot['start'] . ' - ' . $slot['end'];
                            }
                            echo implode(', ', $times);
                            ?>
                        </span>
                    </div>
                    <div class="detail">
                        <span>Total</span>
                        <span class="text-success">Rp <?= number_format($temp_booking['total_harga'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <a href="dashboard.php" class="btn btn-primary w-100 mt-4">Kembali ke Dashboard</a>
            </div>
        <?php else: ?>
            <div class="card p-4">
                <h4 class="mb-3">Pembayaran</h4>

                <div class="expiry-warning">
                    <small>
                        <strong>Perhatian:</strong> Selesaikan pembayaran dalam 30 menit. 
                        Booking akan kadaluarsa otomatis setelah waktu habis.
                    </small>
                </div>

                <img src="../assets/images/<?= htmlspecialchars($venue['gambar']) ?>" class="venue-img mb-3">

                <h5><?= htmlspecialchars($venue['nama_venue']) ?></h5>

                <div class="my-3">
                    <div class="detail">
                        <span>Tanggal</span>
                        <span><?= date('d M Y', strtotime($temp_booking['tanggal_booking'])) ?></span>
                    </div>
                    <div class="detail">
                        <span>Jumlah Slot</span>
                        <span><?= count($temp_booking['slots']) ?> slot</span>
                    </div>
                    <div class="detail">
                        <span>Waktu</span>
                        <span>
                            <?php 
                            $times = [];
                            foreach ($temp_booking['slots'] as $slot) {
                                $times[] = $slot['start'] . ' - ' . $slot['end'];
                            }
                            echo implode(', ', $times);
                            ?>
                        </span>
                    </div>
                    <div class="detail">
                        <span>Total</span>
                        <strong class="text-success">Rp <?= number_format($temp_booking['total_harga'], 0, ',', '.') ?></strong>
                    </div>
                </div>

                <form method="POST">
                    <h6 class="mb-3">Pilih Metode Pembayaran:</h6>

                    <label class="payment-option">
                        <input type="radio" name="metode_pembayaran" value="transfer" required checked>
                        <strong>Transfer Bank</strong>
                        <div class="small text-muted">BCA, BNI, Mandiri</div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="metode_pembayaran" value="e-wallet" required>
                        <strong>E-Wallet</strong>
                        <div class="small text-muted">GoPay, OVO, Dana</div>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="metode_pembayaran" value="cash" required>
                        <strong>Cash</strong>
                        <div class="small text-muted">Bayar di tempat</div>
                    </label>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success w-100 mt-3">Bayar Sekarang</button>
                    <a href="booking.php?id=<?= $venue_id ?>" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    setTimeout(function() {
        if (!document.querySelector('.alert-success')) {
            if (confirm('Waktu pembayaran hampir habis. Lanjutkan pembayaran?')) {
            } else {
                window.location.href = 'booking.php?id=<?= $venue_id ?>';
            }
        }
    }, 600000); 
    </script>
</body>
</html>