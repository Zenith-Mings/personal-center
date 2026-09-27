<?php
/**
 * 后台公共头部
 * 使用前需先 require ../config.php 并设置 $pageTitle、$activePage
 */
if (!isLoggedIn()) redirect('../login.php');
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title><?= h($pageTitle ?? '管理后台') ?> - 个人管理中心</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="profile-info">
                <div class="avatar" style="width:44px;height:44px;font-size:20px">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="../uploads/<?= h($user['avatar']) ?>" alt="头像">
                    <?php else: ?>
                        👤
                    <?php endif; ?>
                </div>
                <div>
                    <div class="nickname" style="font-size:18px"><?= h($pageTitle ?? '管理后台') ?></div>
                    <div class="signature"><?= h($user['nickname']) ?> 的管理后台</div>
                </div>
            </div>
            <div class="header-actions">
                <button id="themeToggle" class="theme-toggle">🌙 深色</button>
                <a href="../index.php" class="btn btn-ghost btn-sm">返回首页</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">退出</a>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="dashboard.php" class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">概览</a>
            <a href="skills.php" class="<?= ($activePage ?? '') === 'skills' ? 'active' : '' ?>">技能树</a>
            <a href="achievements.php" class="<?= ($activePage ?? '') === 'achievements' ? 'active' : '' ?>">成就荣誉</a>
            <a href="diary.php" class="<?= ($activePage ?? '') === 'diary' ? 'active' : '' ?>">日记管理</a>
            <a href="subscriptions.php" class="<?= ($activePage ?? '') === 'subscriptions' ? 'active' : '' ?>">订阅管理</a>
            <a href="rents.php" class="<?= ($activePage ?? '') === 'rents' ? 'active' : '' ?>">收租管理</a>
            <a href="settings.php" class="<?= ($activePage ?? '') === 'settings' ? 'active' : '' ?>">模块设置</a>
            <a href="profile.php" class="<?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>">个人资料</a>
        </nav>
