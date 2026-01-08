<?php
require '../config/config.php';
require '../config/midtrans.php';
$snapToken = null;
$pay_id = null;

// proteksi login
// if (!isset($_SESSION['user_id'])) {
//     header("Location: auth-login.php");
//     exit;
// }

// PROSES TAMBAH PESANAN
if (isset($_POST['tambah_pesanan'])) {

    $user_id       = $_SESSION['user_id'];
    $layanan_id    = $_POST['layanan_id'];
    $deadline      = $_POST['deadline'];
    $total_harga   = $_POST['total_harga'];
    $catatan_user  = $_POST['catatan_user'];
    $judul_project = $_POST['judul_project'];
    $status        = 'menunggu_pembayaran';

    $expired_at = date('Y-m-d H:i:s', strtotime('+20 minutes'));

    mysqli_query($conn, "
        INSERT INTO pesanan 
        (user_id, layanan_id, deadline, catatan_user, total_harga, status, expired_at)
        VALUES 
        ('$user_id', '$layanan_id', '$deadline', '$catatan_user', '$total_harga', '$status', '$expired_at')
    ");

    $pesanan_id = mysqli_insert_id($conn);

    $snap_order_id = 'ORDER-' . $pesanan_id . '-' . time();

    $params = [
        'transaction_details' => [
            'order_id' => $snap_order_id,
            'gross_amount' => (int)$total_harga
        ]
    ];

    mysqli_query($conn, "
    UPDATE pesanan 
    SET snap_order_id='$snap_order_id',
        snap_token='$snapToken'
    WHERE id=$pesanan_id
");

    // upload file
    $file_name = null;
    if (!empty($_FILES['file_user']['name'])) {
        $ext = pathinfo($_FILES['file_user']['name'], PATHINFO_EXTENSION);
        $file_name = 'tugas_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['file_user']['tmp_name'], "uploads/tugas/" . $file_name);
    }

    mysqli_query($conn, "
        INSERT INTO pesanan_detail (pesanan_id, deskripsi, file_user)
        VALUES ('$pesanan_id', '$judul_project', '$file_name')
    ");

    header("Location: jasa-tugas.php?pay=$pesanan_id");
    exit;
}

// PROSES PEMBAYARAN MIDTRANS

if (isset($_GET['pay'])) {
    $id = (int)$_GET['pay'];

    $q = mysqli_query($conn, "
        SELECT * FROM pesanan 
        WHERE id=$id
        AND status='menunggu_pembayaran'
        AND expired_at > NOW()
    ");
    $p = mysqli_fetch_assoc($q);

    if ($p) {

        // ✅ JIKA TOKEN SUDAH ADA → PAKAI ULANG
        if (!empty($p['snap_token'])) {
            $snapToken = $p['snap_token'];
        }
        // ✅ JIKA BELUM → BUAT SEKALI
        else {
            $params = [
                'transaction_details' => [
                    'order_id' => $p['snap_order_id'],
                    'gross_amount' => (int)$p['total_harga']
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            mysqli_query($conn, "
                UPDATE pesanan 
                SET snap_token='$snapToken'
                WHERE id=$id
            ");
        }

        $expired_at = $p['expired_at']; // ✅ FIX BUG JS
    }
}

// PROSES CEK PEMBAYARAN SELESAI
if (isset($_GET['done'])) {
    $id = (int)$_GET['done'];

    mysqli_query($conn, "
        UPDATE pesanan 
        SET status='menunggu_konfirmasi'
        WHERE id=$id
    ");
}

mysqli_query($conn, "
UPDATE pesanan
SET status='dibatalkan'
WHERE status='menunggu_pembayaran'
AND expired_at < NOW();
");

mysqli_query($conn, "
UPDATE pembayaran
SET status='ditolak'
WHERE status='pending'
AND expired_at < NOW();
");
?>

<!doctype html>

<html
    lang="en"
    class="layout-menu-fixed layout-compact"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Demo: Tables - Basic Tables | Sneat - Bootstrap Dashboard FREE</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include '../components/sidebar.php'; ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include '../components/navbar.php'; ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Hoverable Table rows -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Jasa Pengerjaan Tugas</h5>

                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bx bx-plus"></i> Tambah Pesanan
                                </button>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Client</th>
                                            <th>Deadline</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $data = mysqli_query($conn, " SELECT p.*, d.deskripsi, d.file_user, l.nama_layanan, u.nama FROM pesanan p JOIN pesanan_detail d ON p.id = d.pesanan_id JOIN users u ON p.user_id = u.id JOIN layanan l ON p.layanan_id = l.id ORDER BY p.id DESC ");
                                        if (mysqli_num_rows($data) > 0) {
                                            while ($row = mysqli_fetch_assoc($data)) {
                                        ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                                    <td><?= date('d M Y H:i', strtotime($row['deadline'])) ?></td>
                                                    <td>
                                                        <span class="badge bg-label-warning">
                                                            <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['status'] == 'menunggu_pembayaran' && strtotime($row['expired_at']) > time()): ?>
                                                            <a href="jasa-tugas.php?pay=<?= $row['id'] ?>"
                                                                class="btn btn-sm btn-warning">
                                                                Lanjutkan Pembayaran
                                                            </a>
                                                        <?php endif; ?>

                                                        <button class="btn btn-sm btn-info"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#detail<?= $row['id']; ?>">
                                                            Detail
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <!-- Detail Modal -->
                                <?php
                                mysqli_data_seek($data, 0);
                                while ($row = mysqli_fetch_assoc($data)) {
                                ?>
                                    <div class="modal fade" id="detail<?= $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Detail Pemesanan #<?= $row['id']; ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">

                                                    <!-- INFORMASI PESANAN -->
                                                    <h6 class="text-primary mb-2">📦 Informasi Pesanan</h6>
                                                    <table class="table table-bordered table-sm mb-4">
                                                        <tr>
                                                            <th width="30%">Judul Project</th>
                                                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Layanan</th>
                                                            <td><?= htmlspecialchars($row['nama_layanan'] ?? '-'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status</th>
                                                            <td>
                                                                <span class="badge bg-warning">
                                                                    <?= ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- INFORMASI KLIEN -->
                                                    <h6 class="text-primary mb-2">👤 Informasi Klien</h6>
                                                    <table class="table table-bordered table-sm mb-4">
                                                        <tr>
                                                            <th width="30%">Nama Klien</th>
                                                            <td><?= htmlspecialchars($row['nama']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>User ID</th>
                                                            <td><?= $row['user_id']; ?></td>
                                                        </tr>
                                                    </table>

                                                    <!-- INFORMASI WAKTU -->
                                                    <h6 class="text-primary mb-2">⏱ Informasi Waktu</h6>
                                                    <table class="table table-bordered table-sm mb-4">
                                                        <tr>
                                                            <th width="30%">Tanggal Pesan</th>
                                                            <td><?= date('d M Y H:i', strtotime($row['tanggal_pesan'])); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Deadline</th>
                                                            <td><?= date('d M Y H:i', strtotime($row['deadline'])); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Batas Pembayaran</th>
                                                            <td>
                                                                <?= $row['expired_at']
                                                                    ? date('d M Y H:i', strtotime($row['expired_at']))
                                                                    : '-' ?>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- INFORMASI PEMBAYARAN -->
                                                    <h6 class="text-primary mb-2">💳 Informasi Pembayaran</h6>
                                                    <table class="table table-bordered table-sm mb-4">
                                                        <tr>
                                                            <th width="30%">Total Harga</th>
                                                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Order ID Midtrans</th>
                                                            <td><?= $row['snap_order_id'] ?? '-'; ?></td>
                                                        </tr>
                                                    </table>

                                                    <!-- CATATAN & FILE -->
                                                    <h6 class="text-primary mb-2">📝 Catatan & File</h6>
                                                    <table class="table table-bordered table-sm">
                                                        <tr>
                                                            <th width="30%">Catatan User</th>
                                                            <td><?= nl2br(htmlspecialchars($row['catatan_user'] ?: '-')); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>File Tugas</th>
                                                            <td>
                                                                <?php if (!empty($row['file_user'])): ?>
                                                                    <a href="uploads/tugas/<?= $row['file_user']; ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="bx bx-show me-1"></i> Lihat File
                                                                    </a>
                                                                <?php else: ?>
                                                                    -
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- / Detail Modal -->

                                <!-- Modal Tambah Pesanan -->
                                <div class="modal fade" id="modalTambah" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" enctype="multipart/form-data">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tambah Pemesanan Jasa Tugas</h5>
                                                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row g-3">

                                                        <div class="col-md-6">
                                                            <label class="form-label">Judul Tugas</label>
                                                            <input type="text" name="judul_project" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Layanan</label>
                                                            <select name="layanan_id" class="form-select" required>
                                                                <option value="">-- Pilih --</option>
                                                                <?php
                                                                $layanan = mysqli_query($conn, "SELECT * FROM layanan WHERE status='aktif'");
                                                                while ($l = mysqli_fetch_assoc($layanan)) {
                                                                    echo "<option value='{$l['id']}'>{$l['nama_layanan']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Deadline</label>
                                                            <input type="datetime-local" name="deadline" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Harga (Rp)</label>
                                                            <input type="number" name="total_harga" class="form-control" required>
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label">Catatan</label>
                                                            <textarea name="catatan_user" class="form-control"></textarea>
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label">File Tugas</label>
                                                            <input type="file" name="file_user" class="form-control">
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>
                                                    <button type="submit" name="tambah_pesanan" class="btn btn-primary">
                                                        Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- / Modal Tambah Pesanan -->

                            </div>
                        </div>
                        <!-- / Hoverable Table rows -->
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include '../components/footer.php'; ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- timer qris -->
    <?php if (!empty($expired_at)): ?>
        <script>
            const endTime = new Date("<?= $expired_at ?>").getTime();

            const timer = setInterval(() => {
                const now = new Date().getTime();
                const diff = endTime - now;

                if (diff <= 0) {
                    document.getElementById("countdown").innerHTML = "Waktu habis";
                    clearInterval(timer);
                    return;
                }

                const m = Math.floor(diff / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML =
                    `${m}:${s < 10 ? '0' + s : s}`;
            }, 1000);
        </script>
    <?php endif; ?>
    <!-- / timer qris -->

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- midtrans validation -->
    <?php if ($snapToken): ?>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="SB-Mid-client-XXXX"></script>

        <script>
            window.onload = function() {
                snap.pay('<?= $snapToken ?>', {
                    onSuccess: function() {
                        window.location.href = 'jasa-tugas.php?done=<?= (int)$_GET['pay'] ?>';
                    },
                    onPending: function() {
                        alert('Pembayaran ditunda');
                    },
                    onError: function() {
                        alert('Pembayaran gagal');
                    }
                });
            };
        </script>
    <?php endif; ?>
    <!-- / midtrans validation -->

</body>

</html>