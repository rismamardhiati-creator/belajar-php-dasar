<?php
session_start();
require_once "config/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$userObj = new User($db);

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userData = $userObj->login($username, $password);
    if ($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['nama'] = $userData['nama'];
        $_SESSION['role'] = $userData['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Rizky Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #fffdf5; color: #43302b; }
        .btn-custom { background-color: #e6b800; color: #ffffff; font-weight: bold; }
        .btn-custom:hover { background-color: #cc9900; }
    </style>
</head>
<body class="h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96 border border-amber-200">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-amber-950"><i class="fa-solid fa-shirt mr-2 text-amber-500"></i>Rizky Laundry</h2>
            <p class="text-xs text-amber-800 mt-1">Silakan masuk untuk melanjutkan</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 p-2.5 rounded mb-4 text-sm text-center"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1 text-amber-900">Username</label>
                <input type="text" name="username" class="w-full border border-amber-200 p-2.5 rounded-lg focus:border-amber-400 focus:ring-1 focus:ring-amber-400 text-sm" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-1 text-amber-900">Password</label>
                <input type="password" name="password" class="w-full border border-amber-200 p-2.5 rounded-lg focus:border-amber-400 focus:ring-1 focus:ring-amber-400 text-sm" required>
            </div>
            <button type="submit" name="login" class="w-full btn-custom py-2.5 rounded-lg shadow transition duration-200 text-sm">Login</button>
        </form>
    </div>
</body>
</html>