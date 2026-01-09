<?php
include '../config/config.php';

// if (!isset($_SESSION['user_id'])) {
//   header("Location: ../auth/login.php");
//   exit;
// }

$id = $_SESSION['user_id'];

// Ambil data user
$q = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($q);

function normalisasiNoHP($nomor)
{
  // Hapus semua karakter selain angka
  $nomor = preg_replace('/[^0-9]/', '', $nomor);

  // Jika diawali 0 → ganti jadi 62
  if (substr($nomor, 0, 1) === '0') {
    $nomor = '62' . substr($nomor, 1);
  }

  // Jika diawali 620 → perbaiki (error umum)
  if (substr($nomor, 0, 3) === '620') {
    $nomor = '62' . substr($nomor, 3);
  }

  return $nomor;
}

// Simpan perubahan
if (isset($_POST['save_profile'])) {

  $nama   = $_POST['nama'];
  $email  = $_POST['email'];
  $no_hp = normalisasiNoHP($_POST['no_hp']);
  $alamat = $_POST['alamat'];
  $jk     = $_POST['jenis_kelamin'];
  $bio    = $_POST['bio'];

  $foto = $user['foto_profil'];

  if (!empty($_FILES['foto']['name'])) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nama_foto = 'user_' . $id . '.' . $ext;
    move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/avatars/" . $nama_foto);
    $foto = $nama_foto;
  }

  mysqli_query($conn, "
    UPDATE users SET
      nama='$nama',
      email='$email',
      no_hp='$no_hp',
      alamat='$alamat',
      jenis_kelamin='$jk',
      bio='$bio',
      foto_profil='$foto',
      updated_at=NOW()
    WHERE id='$id'
  ");

  if (!preg_match('/^62[0-9]{9,13}$/', $no_hp)) {
    die("Nomor HP tidak valid");
  }

  $_SESSION['notif'] = [
    'type' => 'success',
    'message' => 'Profil berhasil diperbarui'
  ];

  header("Location: account-setting.php");
  exit;
}

// HAPUS AKUN (SOFT DELETE)
if (isset($_POST['delete_account'])) {

  $id = $_SESSION['user_id'];

  // Soft delete
  mysqli_query($conn, "
    UPDATE users
    SET is_active = 0,
        deleted_at = NOW()
    WHERE id = '$id'
  ");

  // Hancurkan session
  session_destroy();

  // Redirect ke logout
  header("Location: ../../logout.php");
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

  <title>Demo: Account settings - Account | Sneat - Bootstrap Dashboard FREE</title>

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
  <link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">

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
            <div class="row">
              <div class="col-md-12">
                <div class="nav-align-top">
                  <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                    <li class="nav-item">
                      <a class="nav-link active" href="javascript:void(0);"><i class="icon-base bx bx-user icon-sm me-1_5"></i> Account</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="setting-notification.php"><i class="icon-base bx bx-bell icon-sm me-1_5"></i> Notifications</a>
                    </li>
                  </ul>
                </div>
                <div class="card mb-6">
                  <!-- Account -->

                  <form id="formAccountSettings" method="POST" enctype="multipart/form-data">

                    <!-- FOTO PROFIL (TIDAK BERUBAH TAMPILAN) -->
                    <div class="card-body">
                      <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">

                        <img
                          src="../assets/img/avatars/<?= $user['foto_profil'] ?: 'default.png' ?>"
                          data-original="../assets/img/avatars/<?= $user['foto_profil'] ?: 'default.png' ?>"
                          alt="user-avatar"
                          class="d-block w-px-100 h-px-100 rounded border"
                          id="previewAvatar" />

                        <div class="button-wrapper">
                          <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="icon-base bx bx-upload d-block d-sm-none"></i>

                            <!-- INPUT FILE (SEKARANG DI DALAM FORM, TAPI TETAP HIDDEN) -->
                            <input
                              type="file"
                              name="foto"
                              id="upload"
                              class="account-file-input"
                              hidden
                              accept="image/png, image/jpeg" />
                          </label>

                          <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                            <i class="icon-base bx bx-reset d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>

                          <div class="text-muted">
                            *Hanya JPG, PNG, GIF. Maks 2MB
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- FORM DATA -->
                    <div class="card-body pt-4">

                      <style>
                        .required::after {
                          content: " *";
                          color: red;
                        }
                      </style>

                      <div class="row g-6">

                        <div class="col-md-6">
                          <label class="form-label required">Nama Lengkap</label>
                          <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="<?= $user['nama'] ?>"
                            required>
                        </div>

                        <div class="col-md-6">
                          <label class="form-label required">E-mail</label>
                          <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= $user['email'] ?>"
                            required>
                        </div>

                        <div class="col-md-6">
                          <label class="form-label required">Nomor HP</label>
                          <input
                            type="text"
                            name="no_hp"
                            class="form-control"
                            value="<?= $user['no_hp'] ?>"
                            placeholder="(+62) 812xxxxxxx"
                            required>
                        </div>

                        <div class="col-md-6">
                          <label class="form-label">Alamat</label>
                          <input
                            type="text"
                            name="alamat"
                            class="form-control"
                            value="<?= $user['alamat'] ?>"
                            placeholder="Alamat">
                        </div>

                        <div class="col-md-6">
                          <label class="form-label required">Apakah kamu?</label>
                          <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" <?= $user['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= $user['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                          </select>
                        </div>

                        <div class="col-md-6">
                          <label class="form-label">Bio</label>
                          <textarea
                            rows="6"
                            name="bio"
                            class="form-control"
                            placeholder="Ceritakan dirimu di sini"><?= $user['bio'] ?></textarea>
                        </div>

                      </div>

                      <div class="mt-6">
                        <button type="submit" name="save_profile" class="btn btn-primary me-3">
                          Save changes
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                          Cancel
                        </button>
                      </div>

                    </div>

                  </form>
                  <!-- /Account -->
                </div>
                <!-- HAPUS AKUN -->
                <div class="card">
                  <h5 class="card-header">Hapus Akun</h5>
                  <div class="card-body">
                    <div class="mb-6 col-12 mb-0">
                      <div class="alert alert-warning">
                        <h5 class="alert-heading mb-1">Apa kamu yakin ingin menghapus akun?</h5>
                        <p class="mb-0">Akun tidak dapat dipulihkan begitu sudah terhapus, mohon untuk hati-hati.</p>
                      </div>
                    </div>
                    <form id="formAccountDeactivation" method="POST">
                      <div class="form-check my-3 ms-2">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          id="confirmDelete">
                        <label class="form-check-label" for="confirmDelete">
                          Saya yakin ingin menghapus akun
                        </label>
                      </div>

                      <button
                        type="button"
                        class="btn btn-danger"
                        id="btnDeleteAccount"
                        disabled>
                        Hapus Akun
                      </button>
                    </form>
                    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title text-danger">Konfirmasi Hapus Akun</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>

                          <div class="modal-body">
                            <p>Apakah kamu benar-benar yakin ingin menghapus akun ini?</p>
                            <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                          </div>

                          <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                            <button
                              type="submit"
                              name="delete_account"
                              form="formAccountDeactivation"
                              class="btn btn-danger">
                              Ya, saya yakin
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
  <!-- Konfirmasi Hapus Akun -->
  <script>
    const checkbox = document.getElementById('confirmDelete');
    const deleteBtn = document.getElementById('btnDeleteAccount');
    const modal = new bootstrap.Modal(
      document.getElementById('deleteConfirmModal')
    );

    checkbox.addEventListener('change', () => {
      deleteBtn.disabled = !checkbox.checked;
    });

    deleteBtn.addEventListener('click', () => {
      modal.show();
    });
  </script>
  <!-- / Konfirmasi Hapus Akun -->

  <!-- Preview & Crop Avatar -->
  <script>
    const uploadInput = document.getElementById('upload');
    const previewAvatar = document.getElementById('previewAvatar');
    const resetBtn = document.querySelector('.account-image-reset');

    function resetPreview() {
      previewAvatar.src = previewAvatar.dataset.original;
      uploadInput.value = '';
    }

    resetBtn.addEventListener('click', resetPreview);

    uploadInput.addEventListener('change', function() {
      const file = this.files[0];
      if (!file) return;

      // Validasi size 2MB
      if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto maksimal 2MB');
        resetPreview();
        return;
      }

      // Validasi tipe
      if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        alert('Format harus JPG, PNG, atau GIF');
        resetPreview();
        return;
      }

      const img = new Image();
      const reader = new FileReader();

      reader.onload = e => img.src = e.target.result;
      reader.readAsDataURL(file);

      img.onload = () => {
        const size = Math.min(img.width, img.height); // square crop
        const sx = (img.width - size) / 2;
        const sy = (img.height - size) / 2;

        const canvas = document.createElement('canvas');
        const OUTPUT_SIZE = 400;
        canvas.width = OUTPUT_SIZE;
        canvas.height = OUTPUT_SIZE;

        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingQuality = 'high';

        ctx.drawImage(
          img,
          sx, sy, size, size,
          0, 0, OUTPUT_SIZE, OUTPUT_SIZE
        );

        // Preview
        previewAvatar.src = canvas.toDataURL('image/jpeg');

        // Convert canvas → File (untuk submit)
        canvas.toBlob(blob => {
          if (!blob) {
            alert('Gagal memproses gambar');
            resetPreview();
            return;
          }

          const croppedFile = new File(
            [blob],
            'profile.jpg', {
              type: 'image/jpeg'
            }
          );

          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(croppedFile);
          uploadInput.files = dataTransfer.files;

        }, 'image/jpeg', 0.9);
      };
    });
  </script>
  <!-- / Preview & Crop Avatar -->

  <script src="../assets/js/main.js"></script>
  <script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>

  <!-- Page JS -->
  <script src="../assets/js/pages-account-settings-account.js"></script>

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>