<?php
require "../config/database.php";
require "../config/session.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['update']) && isset($_GET['booking_id']) && isset($_GET['new_status'])) {

    $booking_id = intval($_GET['booking_id']);
    $new_status = $_GET['new_status'];

    $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed)) {
        die("Status tidak valid.");
    }

    $up = $mysqli->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $up->bind_param("si", $new_status, $booking_id);
    $up->execute();

    if ($new_status === 'completed') {
        $pay = $mysqli->prepare("UPDATE payments SET status='success' WHERE booking_id=?");
        $pay->bind_param("i", $booking_id);
        $pay->execute();
    }

    header("Location: booking.php");
    exit;
}

$status_filter = "";
if (isset($_GET['status']) && in_array($_GET['status'], ['confirmed', 'completed', 'cancelled'])) {
    $status_filter = "WHERE b.status = '" . $_GET['status'] . "'";
}

$query = "
    SELECT 
        b.*,
        v.nama_venue,
        v.kategori,
        u.nama as user_nama,
        p.status as payment_status,
        p.metode_pembayaran
    FROM bookings b
    JOIN venues v ON b.venue_id = v.id
    JOIN users u ON b.user_id = u.id
    LEFT JOIN payments p ON b.id = p.booking_id
    $status_filter
    ORDER BY b.created_at DESC
";
$bookings = $mysqli->query($query);

$stats = [
    'confirmed' => $mysqli->query("SELECT COUNT(*) as c FROM bookings WHERE status='confirmed'")->fetch_assoc()['c'],
    'completed' => $mysqli->query("SELECT COUNT(*) as c FROM bookings WHERE status='completed'")->fetch_assoc()['c'],
    'cancelled' => $mysqli->query("SELECT COUNT(*) as c FROM bookings WHERE status='cancelled'")->fetch_assoc()['c'],
    'total' => $mysqli->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c']
];

$revenue = $mysqli->query("SELECT COALESCE(SUM(total_harga),0) as t FROM bookings WHERE status='completed'")
    ->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Booking - Admin VenueBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark-blue: #0A2647;
            --neon-green: #00FF88;
            --light-gray: #E0E0E0;
            --white: #ffffff;
        }

        body {
            background: var(--light-gray);
            font-family: 'Segoe UI', sans-serif
        }

        .navbar-sporty {
            background: var(--dark-blue);
            padding: 15px
        }

        .navbar-brand {
            color: var(--neon-green) !important;
            font-size: 1.6rem;
            font-weight: bold
        }

        .nav-link {
            color: var(--white) !important
        }

        .card-stat {
            border-left: 6px solid var(--dark-blue);
            background: var(--white);
            border-radius: 10px;
            padding: 20px;
            transition: .2s
        }

        .card-stat:hover {
            transform: translateY(-2px)
        }

        .btn-neon {
            background: var(--neon-green);
            color: var(--dark-blue);
            font-weight: bold
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1)
        }

        .table th {
            background: var(--dark-blue);
            color: white
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: .85rem;
            font-weight: 600
        }

        .status-confirmed {
            background: #d1edff;
            color: #0a58ca
        }

        .status-completed {
            background: #d1f2eb;
            color: #0d6e4e
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24
        }

        .payment-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: .75rem;
            font-weight: 600
        }

        .payment-success {
            background: #d1f2eb;
            color: #0d6e4e
        }

        .payment-pending {
            background: #fff3cd;
            color: #856404
        }

        .payment-failed {
            background: #f8d7da;
            color: #721c24
        }

        .btn-complete {
            background: #198754;
            color: white
        }

        .btn-cancel {
            background: #dc3545;
            color: white
        }

        .active-filter {
            background-color: var(--dark-blue) !important;
            color: white !important
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-sporty">
        <div class="container">
            <a class="navbar-brand">VenueBook Admin</a>
            <div class="ms-auto">
                <span class="text-white me-3"><i class="fas fa-user-shield"></i> Admin: <?= $_SESSION['user']['nama'] ?></span>
                <a href="../logout.php" class="btn btn-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-check me-2"></i>Kelola Booking</h2>
            <a href="dashboard.php" class="btn btn-neon"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

       <div class="row mb-4">
    <div class="col-md-2">
        <div class="card-stat text-center">
            <div class="stat-number"><?= $stats['total'] ?></div>
            <div>Total Booking</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card-stat text-center">
            <div class="stat-number text-primary"><?= $stats['confirmed'] ?></div>
            <div>Confirmed</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card-stat text-center">
            <div class="stat-number text-success"><?= $stats['completed'] ?></div>
            <div>Completed</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card-stat text-center">
            <div class="stat-number text-danger"><?= $stats['cancelled'] ?></div>
            <div>Cancelled</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-stat text-center">
            <div class="stat-number text-info">
                Rp <?= number_format($revenue, 0, ',', '.') ?>
            </div>
            <div>Total Pendapatan</div>
        </div>
    </div>
</div>


        <?php $current_status = $_GET['status'] ?? ''; ?>
        <div class="filter-buttons mb-3">
            <a href="booking.php" class="btn btn-sm <?= empty($current_status) ? 'active-filter' : 'btn-outline-dark' ?>">Semua</a>
            <a href="booking.php?status=confirmed" class="btn btn-sm <?= $current_status == 'confirmed' ? 'active-filter' : 'btn-outline-primary' ?>">Confirmed</a>
            <a href="booking.php?status=completed" class="btn btn-sm <?= $current_status == 'completed' ? 'active-filter' : 'btn-outline-success' ?>">Completed</a>
            <a href="booking.php?status=cancelled" class="btn btn-sm <?= $current_status == 'cancelled' ? 'active-filter' : 'btn-outline-danger' ?>">Cancelled</a>
        </div>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Venue</th>
                        <th>User</th>
                        <th>Tanggal & Waktu</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($bookings->num_rows > 0): ?>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><b><?= $booking['nama_venue'] ?></b><br><small><?= $booking['kategori'] ?></small></td>
                                <td><?= $booking['user_nama'] ?></td>
                                <td>
                                    <b><?= date('d M Y', strtotime($booking['tanggal_booking'])) ?></b><br>
                                    <small><?= $booking['jam_mulai'] ?> - <?= $booking['jam_selesai'] ?></small>
                                </td>
                                <td class="text-success">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="status-badge status-<?= $booking['status'] ?>"><?= strtoupper($booking['status']) ?></span></td>
                                <td>
                                    <?php if ($booking['payment_status']): ?>
                                        <span class="payment-badge payment-<?= $booking['payment_status'] ?>"><?= strtoupper($booking['payment_status']) ?></span><br>
                                        <small><?= $booking['metode_pembayaran'] ?></small>
                                    <?php else: ?> <span class="text-muted">-</span> <?php endif; ?>
                                </td>
                                <td><?= date('d M Y H:i', strtotime($booking['created_at'])) ?></td>

                                <td>
                                    <div class="btn-group">
                                        <?php if ($booking['status'] == 'pending'): ?>
                                            <a href="booking.php?update=1&booking_id=<?= $booking['id'] ?>&new_status=confirmed"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="booking.php?update=1&booking_id=<?= $booking['id'] ?>&new_status=cancelled"
                                                class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </a>

                                        <?php elseif ($booking['status'] == 'confirmed'): ?>
                                            <a href="booking.php?update=1&booking_id=<?= $booking['id'] ?>&new_status=completed"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-flag-checkered"></i>
                                            </a>
                                            <a href="booking.php?update=1&booking_id=<?= $booking['id'] ?>&new_status=cancelled"
                                                class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </a>

                                        <?php elseif ($booking['status'] == 'completed'): ?>
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Selesai</span>

                                        <?php else: ?>
                                            <span class="text-muted">-</span>

                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted"></i><br>
                                <h4 class="text-muted">Tidak ada booking</h4>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>