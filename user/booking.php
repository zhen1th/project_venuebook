<?php
require "../config/database.php";
require "../config/session.php";
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php?msg=belum_login");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

if (!isset($_GET['id'])) die("Venue tidak ditemukan.");
$venue_id = intval($_GET['id']);

$q = $mysqli->prepare("SELECT * FROM venues WHERE id = ? LIMIT 1");
$q->bind_param("i", $venue_id);
$q->execute();
$venue = $q->get_result()->fetch_assoc();
if (!$venue) die("Venue tidak ditemukan.");

$selected_date = isset($_GET['date'])
    ? preg_replace('/[^0-9\-]/', '', $_GET['date'])
    : date("Y-m-d");

$today_str = date("Y-m-d");
if ($selected_date < $today_str) {
    header("Location: booking.php?id={$venue_id}&date={$today_str}");
    exit;
}

function hariIndo($d)
{
    return [
        "Mon" => "Senin",
        "Tue" => "Selasa",
        "Wed" => "Rabu",
        "Thu" => "Kamis",
        "Fri" => "Jumat",
        "Sat" => "Sabtu",
        "Sun" => "Minggu"
    ][$d] ?? $d;
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'book'
) {

    $tanggal = $_POST['tanggal'] ?? '';
    $slots = json_decode($_POST['slots'] ?? '[]', true);
    $total_client = floatval($_POST['total'] ?? 0);

    /* --- valid date format --- */
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error = "Tanggal tidak valid.";
    } elseif ($tanggal < date("Y-m-d")) {
        $error = "Tanggal sudah berlalu.";
    } elseif (!is_array($slots) || count($slots) === 0) {
        $error = "Pilih minimal 1 slot.";
    }

    if (!isset($error)) {
        try {
            $mysqli->begin_transaction();

            $check = $mysqli->prepare("
                SELECT id FROM bookings 
                WHERE venue_id = ? AND tanggal_booking = ?
                AND NOT (jam_selesai <= ? OR jam_mulai >= ?)
                AND status != 'cancelled'
                LIMIT 1
            ");

            $price = floatval($venue['harga_per_jam']);
            $calc_total = 0;
            $current_hour = intval(date("H"));
            $is_today = ($tanggal === date("Y-m-d"));

            $slot_data = [];
            $validated_slots = [];

            foreach ($slots as $slot) {
                if (!isset($slot['start'])) throw new Exception("Slot tidak valid.");

                $jam_mulai = $slot['start'];
                if (!preg_match('/^\d{2}:\d{2}$/', $jam_mulai))
                    throw new Exception("Format jam tidak valid.");

                $startHour = intval(substr($jam_mulai, 0, 2));
                $endHour = ($startHour + 1) % 24;
                $jam_selesai = sprintf("%02d:00", $endHour);

                if ($is_today && $startHour < $current_hour)
                    throw new Exception("Slot {$jam_mulai} sudah terlewat.");

                $check->bind_param(
                    "isss",
                    $venue_id,
                    $tanggal,
                    $jam_mulai,
                    $jam_selesai
                );
                $check->execute();
                if ($check->get_result()->num_rows > 0)
                    throw new Exception("Slot {$jam_mulai} sudah dibooking.");

                $calc_total += $price;

                $validated_slots[] = [
                    'start' => $jam_mulai,
                    'end' => $jam_selesai,
                    'price' => $price
                ];
            }

            if (abs($calc_total - $total_client) > 0.01)
                $total_client = $calc_total;

            $mysqli->commit();

            $_SESSION['temp_booking'] = [
                'venue_id' => $venue_id,
                'venue_name' => $venue['nama_venue'],
                'venue_category' => $venue['kategori'],
                'tanggal_booking' => $tanggal,
                'slots' => $validated_slots,
                'total_harga' => $total_client,
                'created_at' => time(),
                'expires_at' => time() + 1800 
            ];

            header("Location: payment.php");
            exit;
        } catch (Exception $ex) {
            $mysqli->rollback();
            $error = $ex->getMessage();
        }
    }
}


$booked_hours = [];
$b = $mysqli->prepare("
    SELECT jam_mulai 
    FROM bookings 
    WHERE venue_id = ? AND tanggal_booking = ? AND status != 'cancelled'
");
$b->bind_param("is", $venue_id, $selected_date);
$b->execute();
$rb = $b->get_result();
while ($row = $rb->fetch_assoc()) {
    $booked_hours[] = $row['jam_mulai'];
}
$booked_hours = array_unique($booked_hours);
sort($booked_hours);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Booking - <?= htmlspecialchars($venue['nama_venue']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-blue: #0A2647;
            --neon-green: #00FF88;
            --bg: #f7f7f9;
            --card-bg: #fff;
            --muted: #e9e9ea;
        }

        body {
            background: var(--bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        .navbar {
            background: var(--dark-blue) !important;
        }

        .navbar .navbar-brand,
        .navbar .text-white {
            color: #fff !important;
        }

        .container {
            max-width: 1100px;
        }

        .daybar {
            display: flex;
            gap: 10px;
            overflow: auto;
            padding: 12px 6px
        }

        .day-item {
            min-width: 110px;
            padding: 10px;
            border-radius: 8px;
            background: var(--card-bg);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border: 1px solid #f0f0f0;
            display: inline-block
        }

        .day-item small {
            display: block;
            color: #7a7a7a
        }

        .day-item.active {
            background: var(--dark-blue);
            color: #fff;
            border-color: rgba(10, 38, 71, 0.9)
        }

        .day-item .date-big {
            font-weight: 700
        }

        .venue-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 8px;
            cursor: zoom-in
        }

        .venue-card .meta {
            color: #6b6b6b
        }

        .slot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px
        }

        .slot {
            background: var(--card-bg);
            padding: 14px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            border: 1px solid #eee;
            min-height: 84px;
            display: flex;
            flex-direction: column;
            justify-content: center
        }

        .slot .time {
            font-weight: 700
        }

        .slot .price {
            margin-top: 6px;
            font-weight: 600;
            color: var(--dark-blue)
        }

        .slot.booked {
            background: #f5f5f6;
            color: #9a9a9a;
            cursor: not-allowed
        }

        .slot.past {
            background: #f5f5f6;
            color: #9a9a9a;
            cursor: not-allowed;
            border-color: #ddd
        }

        .slot.selected {
            outline: 3px solid var(--dark-blue);
            box-shadow: 0 4px 12px rgba(10, 38, 71, 0.2);
            background: #f0f7ff
        }

        @media(max-width:992px) {
            .slot-grid {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        @media(max-width:700px) {
            .slot-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        .controls {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .img-clickable {
            cursor: zoom-in
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg px-4 py-2">
        <a class="navbar-brand fw-bold" href="#">VanueBook</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white">Halo, <strong><?= htmlspecialchars($user['nama']) ?></strong></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-5">
                <div class="card p-3 venue-card mb-3" style="border:0;">
                    <img src="../assets/images/<?= htmlspecialchars($venue['gambar']) ?>" alt="gambar" class="img-clickable" data-bs-toggle="modal" data-bs-target="#imgModal">

                    <div class="mt-3">
                        <h4 class="mb-1"><?= htmlspecialchars($venue['nama_venue']) ?></h4>
                        <div class="meta mb-2"><?= htmlspecialchars($venue['kategori']) ?></div>
                        <p class="small"><?= nl2br(htmlspecialchars($venue['deskripsi'])) ?></p>
                        <h5 class="text-success">Rp <?= number_format($venue['harga_per_jam'], 0, ',', '.') ?>/jam</h5>
                    </div>
                </div>

                <a href="venue_detail.php?id=<?= $venue_id ?>" class="btn btn-secondary mb-3">← Kembali</a>
            </div>

            <div class="col-md-7">
                <div class="card p-3 mb-3" style="border:0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5 class="mb-0">Pilih Tanggal</h5>
                            <small class="text-muted">Klik hari atau gunakan date picker</small>
                        </div>
                        <div class="controls">
                            <form id="jumpForm" method="GET" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="id" value="<?= $venue_id ?>">
                                <input type="date" name="date" value="<?= htmlspecialchars($selected_date) ?>" min="<?= date('Y-m-d') ?>" class="form-control form-control-sm">
                                <button class="btn btn-sm btn-outline-secondary" type="submit">Pilih</button>
                            </form>
                        </div>
                    </div>

                    <div class="daybar" id="daybar">
                        <?php
                        $today_obj = new DateTime();
                        $today_str = $today_obj->format('Y-m-d');

                        for ($i = 0; $i < 7; $i++) {
                            $d = clone $today_obj;
                            $d->modify("+$i day");
                            $label = hariIndo($d->format('D'));
                            $date = $d->format('Y-m-d');
                            $human = $d->format('d M');
                            $active = ($date === $selected_date) ? "active" : "";

                            if ($date >= $today_str) {
                                echo "<a href=\"?id={$venue_id}&date={$date}\" class=\"day-item {$active}\" role='button'>
                                        <small>{$label}</small>
                                        <div class='date-big'>{$human}</div>
                                    </a>";
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="card p-3" style="border:0;">
                    <h5 class="mb-3">Pilih Slot (<?= htmlspecialchars($selected_date) ?>)</h5>

                    <div class="slot-grid" id="slotGrid">
                        <?php
                        $price = floatval($venue['harga_per_jam']);
                        $today = date("Y-m-d");
                        $current_hour = intval(date("H"));
                        $is_today = ($selected_date === $today);

                        for ($hour = 0; $hour < 24; $hour++) {
                            $start = str_pad($hour, 2, "0", STR_PAD_LEFT) . ":00";
                            $endHour = ($hour + 1) % 24;
                            $end = str_pad($endHour, 2, "0", STR_PAD_LEFT) . ":00";

                            $booked = in_array($start, $booked_hours);
                            $past = $is_today && ($hour < $current_hour);

                            $class = "";
                            if ($booked) $class = "booked";
                            elseif ($past) $class = "past";

                            echo "<div class='slot {$class}' data-start='{$start}' data-end='{$end}' data-price='{$price}'>";
                            echo "<div class='time'>{$start} - {$end}</div>";

                            if ($booked) {
                                echo "<div class='text-muted mt-2'><small>Booked</small></div>";
                            } elseif ($past) {
                                echo "<div class='text-muted mt-2'><small>Tidak Tersedia</small></div>";
                            } else {
                                echo "<div class='price mt-2'>Rp " . number_format($price, 0, ',', '.') . "</div>";
                                echo "<div class='mt-2'><button type='button' class='btn btn-sm btn-outline-success select-slot'>Pilih</button></div>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>

                    <div class="mt-4">
                        <form method="POST" id="confirmForm">
                            <input type="hidden" name="action" value="book">
                            <input type="hidden" name="tanggal" id="f_tanggal" value="<?= htmlspecialchars($selected_date) ?>">
                            <input type="hidden" name="slots" id="f_slots" value="">
                            <input type="hidden" name="total" id="f_total" value="">

                            <div id="selectionBox" style="display:none" class="d-flex gap-3 align-items-center bg-white p-3 border rounded shadow-sm">
                                <div>
                                    <strong>Terpilih:</strong>
                                    <div id="selTime"></div>
                                </div>
                                <div>
                                    <strong>Total:</strong>
                                    <div id="selPrice" class="text-success fw-bold"></div>
                                </div>
                                <div class="ms-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-success">checkout</button>
                                    <button type="button" id="cancelSel" class="btn btn-outline-secondary">Batal</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-2" style="background:transparent;border:0;">
                <img src="../assets/images/<?= htmlspecialchars($venue['gambar']) ?>" class="w-100 rounded">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let selectedSlots = [];

        function toRupiah(num) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }

        function updateSelectionBox() {
            const selectionBox = document.getElementById('selectionBox');

            if (selectedSlots.length === 0) {
                selectionBox.style.display = 'none';
                document.getElementById('f_slots').value = "";
                document.getElementById('f_total').value = "";
                return;
            }

            selectedSlots.sort((a, b) => a.start.localeCompare(b.start));

            let total = selectedSlots.reduce((sum, s) => sum + s.price, 0);

            let timesDisplay = selectedSlots.map(s => {
                const hour = parseInt(s.start.split(':')[0]);
                const endHour = (hour + 1) % 24;
                const end = endHour.toString().padStart(2, '0') + ":00";
                return s.start + " - " + end;
            }).join(", ");

            document.getElementById('selTime').innerText = timesDisplay;
            document.getElementById('selPrice').innerText = toRupiah(total);

            document.getElementById('f_slots').value = JSON.stringify(selectedSlots);
            document.getElementById('f_total').value = total.toFixed(2);
            selectionBox.style.display = 'flex';
        }

        document.addEventListener('click', function(e) {
            if (e.target && e.target.matches('.select-slot')) {
                const slot = e.target.closest('.slot');
                if (!slot) return;

                if (slot.classList.contains('booked') || slot.classList.contains('past')) {
                    if (slot.classList.contains('past')) {
                        alert('Waktu ini tidak tersedia. Silakan pilih waktu yang akan datang.');
                    } else {
                        alert('Slot ini sudah dibooking oleh orang lain.');
                    }
                    return;
                }

                const start = slot.getAttribute('data-start');
                const price = parseFloat(slot.getAttribute('data-price'));

                if (slot.classList.contains('selected')) {
                    slot.classList.remove('selected');
                    selectedSlots = selectedSlots.filter(s => s.start !== start);
                } else {
                    slot.classList.add('selected');
                    selectedSlots.push({
                        start: start,
                        price: price
                    });
                }
                updateSelectionBox();
            }
        });

        document.getElementById('cancelSel').addEventListener('click', function() {
            selectedSlots = [];
            document.querySelectorAll('.slot').forEach(s => s.classList.remove('selected'));
            updateSelectionBox();
        });

        document.getElementById('confirmForm').addEventListener('submit', function(e) {
            if (selectedSlots.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 slot terlebih dahulu.');
            } else {
                this.querySelector('button[type="submit"]').setAttribute('disabled', 'disabled');
            }
        });
    </script>
</body>

</html>