<?php
include "dashboard/config/config.php";
$success = $error = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Email sudah terdaftar";
    } else {
        mysqli_query($conn, "INSERT INTO users (nama,email,password,role)
            VALUES ('$nama','$email','$password','user')");
        $success = "Registrasi berhasil, silakan login";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | WebSmith</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#0b0f1a] flex items-center justify-center">
<div class="w-full max-w-md bg-[#111827]/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl">

    <h2 class="text-3xl font-bold text-center mb-6">
        <span class="text-cyan-400">Daftar</span> <span class="text-purple-500">WebSmith</span>
    </h2>

    <?php if ($error): ?>
        <div class="bg-red-500/20 text-red-400 p-3 rounded mb-4 text-center"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-500/20 text-green-400 p-3 rounded mb-4 text-center"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <input type="text" name="nama" placeholder="Nama Lengkap" required class="input">
        <input type="email" name="email" placeholder="Email" required class="input">
        <input type="password" name="password" placeholder="Password" required class="input">

        <button name="register"
            class="w-full py-3 bg-gradient-to-r from-purple-600 to-cyan-500 rounded-lg font-semibold">
            Daftar
        </button>
    </form>

    <p class="text-gray-400 text-sm text-center mt-5">
        Sudah punya akun?
        <a href="login.php" class="text-cyan-400">Login</a>
    </p>
</div>

<style>
.input{
    width:100%;
    padding:12px;
    background:#0b0f1a;
    border:1px solid #374151;
    border-radius:10px;
    color:white;
}
</style>

</body>
</html>
