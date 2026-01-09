<?php
require '../config/config.php';
$id = $_SESSION['user_id'];

// proteksi login
// if (!isset($_SESSION['user_id'])) {
//     header("Location: auth-login.php");
//     exit;
// }

$data = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id DESC");

// TAMBAH
if (isset($_POST['tambah_kategori'])) {
    mysqli_query($conn, "
    INSERT INTO kategori (nama_kategori, deskripsi)
    VALUES ('$_POST[nama_kategori]', '$_POST[deskripsi]')
  ");

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Kategori berhasil ditambahkan'
    ];
    header("Location: kategori-jasa.php");
    exit;
}

// EDIT
if (isset($_POST['edit_kategori'])) {
    mysqli_query($conn, "
    UPDATE kategori SET
      nama_kategori='$_POST[nama_kategori]',
      deskripsi='$_POST[deskripsi]'
    WHERE id='$_POST[id]'
  ");

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Kategori berhasil diperbarui'
    ];
    header("Location: kategori-jasa.php");
    exit;
}

// HAPUS
if (isset($_POST['hapus_kategori'])) {
    mysqli_query($conn, "DELETE FROM kategori WHERE id='$_POST[id]'");

    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Kategori berhasil dihapus'
    ];
    header("Location: kategori-jasa.php");
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
                                <h5 class="mb-0">Kategori Jasa</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bx bx-plus"></i> Tambah Kategori
                                </button>
                            </div>

                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kategori</th>
                                            <th>Deskripsi</th>
                                            <th width="150">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($data) > 0):
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($data)): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                                    <td><?= htmlspecialchars($row['deskripsi'] ?: '-') ?></td>
                                                    <td>
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
                                                <td colspan="4" class="text-center text-muted">Belum ada kategori</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Modal Tambah Kategori -->
                            <div class="modal fade" id="modalTambah" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Kategori</h5>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Kategori</label>
                                                    <input type="text" name="nama_kategori" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control"></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="tambah_kategori" class="btn btn-primary">
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- / Modal Tambah Kategori -->

                            <?php
                            mysqli_data_seek($data, 0);
                            while ($row = mysqli_fetch_assoc($data)):
                            ?>

                                <!-- EDIT -->
                                <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Kategori</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Kategori</label>
                                                        <input type="text" name="nama_kategori"
                                                            value="<?= htmlspecialchars($row['nama_kategori']) ?>"
                                                            class="form-control" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi"
                                                            class="form-control"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button class="btn btn-warning" name="edit_kategori">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- HAPUS -->
                                <div class="modal fade" id="hapus<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger">Konfirmasi Hapus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>Yakin ingin menghapus kategori:</p>
                                                    <strong><?= htmlspecialchars($row['nama_kategori']) ?></strong>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button class="btn btn-danger" name="hapus_kategori">Ya, hapus</button>
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