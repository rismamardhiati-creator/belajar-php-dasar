<?php
require_once "Laundry.php";
session_start();
if (isset($_SESSION['user_logged'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUser = trim($_POST['username'] ?? '');
    $inputPass = trim($_POST['password'] ?? '');

    if (!empty($inputUser) && !empty($inputPass)) {
        $userAkun = new \Data\User\User($inputUser, $inputPass, $inputUser, "Kasir Laundry");
        $_SESSION['user_logged'] = $userAkun->getNamaLengkap();
        $_SESSION['user_role']   = $userAkun->getRole();

        header("Location: index.php");
        exit;
    } else {
        $error = "Username dan Password tidak boleh kosong!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Laundry</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { 
            background: #fffdf5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
        }
        .login-box { 
            background: white; 
            padding: 40px 35px; 
            border-radius: 16px; 
            width: 360px; 
            box-shadow: 0 10px 25px rgba(234, 179, 8, 0.12); 
            border: 1.5px solid #fef08a;
        }
        h2 { 
            text-align: center; 
            margin-bottom: 24px; 
            color: #854d0e; 
            font-size: 24px;
            font-weight: 800;
        }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            font-size: 14px; 
            color: #713f12;
        }
        input { 
            width: 100%; 
            padding: 12px 16px; 
            border: 1.5px solid #fde047; 
            border-radius: 8px; 
            outline: none;
            background: #fffbeb;
            font-size: 14px;
            transition: all 0.2s;
        }
        input:focus {
            border-color: #ca8a04;
            background: #ffffff;
        }
        button { 
            width: 100%; 
            padding: 14px; 
            background: #eab308; 
            border: none; 
            color: white; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: bold; 
            cursor: pointer; 
            margin-top: 10px;
            transition: background 0.2s, transform 0.1s;
        }
        button:hover { background: #ca8a04; }
        button:active { transform: scale(0.99); }
        .error { 
            color: #dc2626; 
            background: #fee2e2;
            border: 1px solid #fca5a5;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px; 
            text-align: center; 
            margin-bottom: 20px; 
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Laundry</h2>
    
    <?php if ($error): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan Username..." required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Password..." required>
        </div>
        <button type="submit">Masuk Ke Sistem</button>
    </form>
</div>

</body>
</html>