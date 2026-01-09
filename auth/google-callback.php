<?php
session_start();
require '../dashboard/config/config.php';
require '../dashboard/config/google.php';

/**
 * 1. Pastikan ada authorization code
 */
if (!isset($_GET['code'])) {
  header("Location: ../login.php?error=google_failed");
  exit;
}

/**
 * 2. Tukar code → access token
 */
$tokenResponse = file_get_contents(
  'https://oauth2.googleapis.com/token',
  false,
  stream_context_create([
    'http' => [
      'method'  => 'POST',
      'header'  => "Content-Type: application/x-www-form-urlencoded",
      'content' => http_build_query([
        'code'          => $_GET['code'],
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code',
      ])
    ]
  ])
);

$tokenData = json_decode($tokenResponse, true);

if (!isset($tokenData['access_token'])) {
  header("Location: ../login.php?error=google_token");
  exit;
}

$accessToken = $tokenData['access_token'];

/**
 * 3. Ambil data user dari Google
 */
$userInfo = json_decode(
  file_get_contents(
    'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $accessToken
  ),
  true
);

if (!isset($userInfo['email'])) {
  header("Location: ../login.php?error=google_userdata");
  exit;
}

$email     = mysqli_real_escape_string($conn, $userInfo['email']);
$nama      = mysqli_real_escape_string($conn, $userInfo['name']);
$google_id = mysqli_real_escape_string($conn, $userInfo['id']);

/**
 * 4. Cari user berdasarkan email
 */
$q = mysqli_query($conn, "
  SELECT * FROM users 
  WHERE email='$email' 
  LIMIT 1
");

if (mysqli_num_rows($q) > 0) {

  $user = mysqli_fetch_assoc($q);

  // JIKA AKUN PERNAH DIHAPUS (SOFT DELETE)
  if ($user['is_active'] == 0) {
    mysqli_query($conn, "
      UPDATE users SET
        is_active = 1,
        deleted_at = NULL,
        google_id = '$google_id',
        auth_provider = 'google',
        updated_at = NOW()
      WHERE id = '{$user['id']}'
    ");
  }

} else {

  // AUTO REGISTER USER BARU
  mysqli_query($conn, "
    INSERT INTO users (nama, email, google_id, auth_provider, is_active)
    VALUES ('$nama', '$email', '$google_id', 'google', 1)
  ");

  $user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE email='$email'")
  );
}

/**
 * 5. LOGIN (SESSION)
 */
$_SESSION['user_id'] = $user['id'];
$_SESSION['role']    = $user['role'];

/**
 * 6. REMEMBER ME (COOKIE)
 * → supaya login google berikutnya auto masuk
 */
$rememberToken = bin2hex(random_bytes(32));

mysqli_query($conn, "
  UPDATE users
  SET remember_token='$rememberToken'
  WHERE id='{$user['id']}'
");

setcookie(
  'remember_token',
  $rememberToken,
  time() + (60 * 60 * 24 * 30), // 30 hari
  '/',
  '',
  isset($_SERVER['HTTPS']),
  true // httpOnly
);

/**
 * 7. Redirect ke dashboard
 */
header("Location: ../dashboard/html/index.php");
exit;
