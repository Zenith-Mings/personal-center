<?php
require 'config.php';

if (isLoggedIn()) {
    redirect('admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $users = loadData('users');
    foreach ($users as $u) {
        if ($u['username'] === $username && password_verify($password, $u['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $u['id'];
            redirect('admin/dashboard.php');
        }
    }
    $error = '用户名或密码错误';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>登录 - 个人管理中心</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <div class="login-brand">
                <div class="logo">🛡️</div>
                <h1>个人管理中心</h1>
                <p>网络安全 · 持续学习</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" required value="admin">
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">登 录</button>
            </form>
            <p class="login-tip">默认账号 admin / admin123，可在后台个人资料中修改</p>
        </div>
    </div>
</body>
</html>
