<?php
require '../config/config.php';
$id = $_SESSION['user_id'];

// proteksi login
// if (!isset($_SESSION['user_id'])) {
//     header("Location: auth-login.php");
//     exit;
// }

// Ambil data layanan (Gunakan JOIN jika ingin menampilkan nama kategori, tapi di sini saya sesuaikan dengan struktur tabel layanan saja)
$data = mysqli_query($conn, "SELECT l.*, k.nama_kategori FROM layanan l LEFT JOIN kategori k ON l.kategori_id = k.id ORDER BY l.id DESC");
// Ambil semua data kategori untuk dropdown
$list_kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// TAMBAH LAYANAN
if (isset($_POST['tambah_layanan'])) {
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $kategori_id  = mysqli_real_escape_string($conn, $_POST['kategori_id']);
    $deskripsi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga        = mysqli_real_escape_string($conn, $_POST['harga']);
    $estimasi     = mysqli_real_escape_string($conn, $_POST['estimasi_waktu']);
    $status       = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "
        INSERT INTO layanan (kategori_id, nama_layanan, deskripsi, harga, estimasi_waktu, status)
        VALUES ('$kategori_id', '$nama_layanan', '$deskripsi', '$harga', '$estimasi', '$status')
    ");

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Layanan baru berhasil ditambahkan'
    ];
    header("Location: layanan.php"); // Sesuaikan dengan nama file Anda
    exit;
}

// EDIT LAYANAN
if (isset($_POST['edit_layanan'])) {
    $id           = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $kategori_id  = mysqli_real_escape_string($conn, $_POST['kategori_id']); // Tambahkan ini
    $deskripsi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga        = mysqli_real_escape_string($conn, $_POST['harga']);
    $estimasi     = mysqli_real_escape_string($conn, $_POST['estimasi_waktu']);
    $status       = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "
        UPDATE layanan SET
            nama_layanan='$nama_layanan',
            kategori_id='$kategori_id', -- Tambahkan ini
            deskripsi='$deskripsi',
            harga='$harga',
            estimasi_waktu='$estimasi',
            status='$status'
        WHERE id='$id'
    ");

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Layanan berhasil diperbarui'
    ];
    header("Location: layanan.php");
    exit;
}

// HAPUS LAYANAN
if (isset($_POST['hapus_layanan'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    mysqli_query($conn, "DELETE FROM layanan WHERE id='$id'");

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Layanan berhasil dihapus'
    ];
    header("Location: layanan.php");
    exit;
}

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
                        <?php include '../components/notification.php'; ?>
                        <!-- Hoverable Table rows -->
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Pusat Layanan</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bx bx-plus"></i> Tambah Layanan
                                </button>
                            </div>

                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Layanan</th>
                                            <th>Harga</th>
                                            <th>Estimasi</th>
                                            <th>Status</th>
                                            <th width="150">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($data) > 0):
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($data)): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['nama_layanan']) ?></strong><br>
                                                        <small class="text-muted">Kategori: <?= $row['nama_kategori'] ?></small>
                                                    </td>
                                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                                    <td><?= htmlspecialchars($row['estimasi_waktu']) ?> Hari</td>
                                                    <td>
                                                        <span class="badge bg-label-<?= $row['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                                            <?= ucfirst($row['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail<?= $row['id'] ?>">
                                                            Detail
                                                        </button>

                                                        <button class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#edit<?= $row['id'] ?>">
                                                            Edit
                                                        </button>

                                                        <button class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#hapus<?= $row['id'] ?>">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile;
                                        else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Belum ada layanan</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="modal fade" id="modalTambah" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Layanan</h5>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Layanan</label>
                                                    <input type="text" name="nama_layanan" class="form-control" placeholder="Contoh: Jasa Desain Grafis" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Kategori</label>
                                                    <select name="kategori_id" class="form-select" required>
                                                        <?php
                                                        // Memutar kembali pointer hasil query kategori ke awal
                                                        mysqli_data_seek($list_kategori, 0);
                                                        while ($kat = mysqli_fetch_assoc($list_kategori)):
                                                        ?>
                                                            <option value="<?= $kat['id'] ?>">
                                                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Harga (Rp)</label>
                                                    <input type="number" name="harga" class="form-control" placeholder="0" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Estimasi Waktu (Hari)</label>
                                                    <input type="text" name="estimasi_waktu" class="form-control" placeholder="Misal: 2-3">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan detail layanan..."></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="aktif">Aktif</option>
                                                        <option value="nonaktif">Nonaktif</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="tambah_layanan" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php
                            mysqli_data_seek($data, 0);
                            while ($row = mysqli_fetch_assoc($data)):
                            ?>
                                <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Layanan</h5>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Layanan</label>
                                                        <input type="text" name="nama_layanan" value="<?= htmlspecialchars($row['nama_layanan']) ?>" class="form-control" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kategori</label>
                                                        <select name="kategori_id" class="form-select" required>
                                                            <?php
                                                            // Reset pointer query kategori agar bisa digunakan di setiap modal edit
                                                            mysqli_data_seek($list_kategori, 0);
                                                            while ($kat = mysqli_fetch_assoc($list_kategori)):
                                                            ?>
                                                                <option value="<?= $kat['id'] ?>" <?= ($row['kategori_id'] == $kat['id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                                                </option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Harga (Rp)</label>
                                                        <input type="number" name="harga" value="<?= $row['harga'] ?>" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Estimasi Waktu (Hari)</label>
                                                        <input type="text" name="estimasi_waktu" value="<?= htmlspecialchars($row['estimasi_waktu']) ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="aktif" <?= $row['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                                            <option value="nonaktif" <?= $row['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit_layanan" class="btn btn-warning">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="detail<?= $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Detail Layanan: <?= htmlspecialchars($row['nama_layanan']); ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <h6 class="text-primary mb-2">📋 Informasi Dasar Layanan</h6>
                                                <table class="table table-bordered table-sm mb-4">
                                                    <tr>
                                                        <th width="30%">Nama Layanan</th>
                                                        <td><?= htmlspecialchars($row['nama_layanan']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kategori ID</th>
                                                        <td><span class="badge bg-label-info">ID: <?= $row['kategori_id']; ?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status Layanan</th>
                                                        <td>
                                                            <span class="badge bg-<?= $row['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                                                <?= ucfirst($row['status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <h6 class="text-primary mb-2">💰 Harga & Estimasi</h6>
                                                <table class="table table-bordered table-sm mb-4">
                                                    <tr>
                                                        <th width="30%">Harga Layanan</th>
                                                        <td class="fw-bold text-success">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Estimasi Pengerjaan</th>
                                                        <td><?= htmlspecialchars($row['estimasi_waktu']); ?> Hari</td>
                                                    </tr>
                                                </table>

                                                <h6 class="text-primary mb-2">📝 Deskripsi Lengkap</h6>
                                                <div class="p-3 border rounded bg-light">
                                                    <?= $row['deskripsi'] ? nl2br(htmlspecialchars($row['deskripsi'])) : '<em class="text-muted">Tidak ada deskripsi tersedia.</em>'; ?>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <!-- <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id'] ?>">
                                                    <i class="bx bx-edit me-1"></i> Edit Data
                                                </button> -->
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Tutup
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="hapus<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger">Konfirmasi Hapus</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus layanan:</p>
                                                    <strong><?= htmlspecialchars($row['nama_layanan']) ?></strong>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="hapus_layanan" class="btn btn-danger">Ya, hapus</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
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

</body>

</html>