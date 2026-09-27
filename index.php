<?php
require 'config.php';

$user = currentUser();
// 未登录时也允许浏览，但显示为"访客"
$settings      = loadData('settings');
$skills        = loadData('skills');
$achievements  = loadData('achievements');
$diaries       = loadData('diaries');
$subscriptions = loadData('subscriptions');
$rents         = loadData('rents');

// 按分类整理技能
$skillCategories = [];
foreach ($skills as $s) {
    $skillCategories[$s['category']][] = $s;
}

// 判断模块是否启用
function isModuleEnabled($key, $settings) {
    foreach ($settings as $s) {
        if ($s['key'] === $key) return $s['enabled'];
    }
    return false;
}

// 模块排序
function moduleSort($key, $settings) {
    foreach ($settings as $s) {
        if ($s['key'] === $key) return $s['sort'];
    }
    return 999;
}

$enabledModules = [];
foreach (['skills', 'achievements', 'diary', 'subscriptions', 'rents'] as $k) {
    if (isModuleEnabled($k, $settings)) {
        $enabledModules[$k] = moduleSort($k, $settings);
    }
}
asort($enabledModules);

$recentDiaries = array_slice(array_reverse($diaries), 0, 3);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>个人管理中心</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="profile-info">
                <div class="avatar">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="uploads/<?= h($user['avatar']) ?>" alt="头像">
                    <?php else: ?>
                        👤
                    <?php endif; ?>
                </div>
                <div>
                    <div class="nickname"><?= h($user['nickname'] ?? '访客') ?></div>
                    <div class="signature"><?= h($user['signature'] ?? '网络安全 · 持续学习中') ?></div>
                </div>
            </div>
            <div class="header-actions">
                <button id="themeToggle" class="theme-toggle">🌙 深色</button>
                <?php if (isLoggedIn()): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary btn-sm">管理后台</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">登录</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="card-grid">
            <?php foreach ($enabledModules as $key => $sort): ?>

            <?php if ($key === 'skills'): ?>
            <div class="card" id="skills">
                <div class="card-title">🌳 技能树</div>
                <?php foreach ($skillCategories as $cat => $items): ?>
                <div class="skill-category">
                    <div class="category-name"><?= h($cat) ?>
                        <span class="category-count"><?= count($items) ?></span>
                    </div>
                    <?php foreach ($items as $skill): ?>
                    <div class="skill-item <?= $skill['unlocked'] ? 'unlocked' : 'locked' ?>">
                        <div class="skill-icon">💻</div>
                        <div class="skill-info">
                            <div class="skill-name"><?= h($skill['name']) ?></div>
                            <div class="skill-desc"><?= h($skill['desc']) ?></div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:<?= (int)$skill['progress'] ?>%"></div>
                            </div>
                        </div>
                        <span class="progress-text"><?= (int)$skill['progress'] ?>%</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($key === 'achievements'): ?>
            <div class="card" id="achievements">
                <div class="card-title">🏆 成就荣誉</div>
                <div class="achievement-list">
                    <?php if (empty($achievements)): ?>
                        <div class="empty">暂无成就</div>
                    <?php else: ?>
                        <?php foreach ($achievements as $a): ?>
                        <div class="achievement-item">
                            <div class="achievement-icon"><?= h($a['icon']) ?></div>
                            <div class="achievement-content">
                                <h4><?= h($a['title']) ?></h4>
                                <p><?= h($a['desc']) ?></p>
                                <div class="achievement-date"><?= h($a['date']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($key === 'diary'): ?>
            <div class="card" id="diary">
                <div class="card-title">📝 每日日记</div>
                <?php if (empty($recentDiaries)): ?>
                    <div class="empty">暂无日记</div>
                <?php else: ?>
                    <?php foreach ($recentDiaries as $d): ?>
                    <div class="diary-item">
                        <div class="diary-header">
                            <span class="diary-title"><?= h($d['title']) ?></span>
                            <span class="diary-meta">
                                <?= h($d['mood'] ?? '') ?>
                                <span><?= h($d['created_at']) ?></span>
                            </span>
                        </div>
                        <div class="diary-content"><?= h($d['content']) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($key === 'subscriptions'): ?>
            <div class="card" id="subscriptions">
                <div class="card-title">📅 订阅管理</div>
                <?php if (empty($subscriptions)): ?>
                    <div class="empty">暂无订阅</div>
                <?php else: ?>
                    <?php foreach ($subscriptions as $sub): ?>
                    <div class="record-item">
                        <div>
                            <div class="record-name"><?= h($sub['name']) ?></div>
                            <div class="record-meta">
                                下次扣费：<?= h($sub['next_due_date']) ?>
                            </div>
                        </div>
                        <div class="record-right">
                            <div class="record-amount">¥<?= number_format($sub['price'], 2) ?></div>
                            <span class="status-badge status-active"><?= h($sub['cycle']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($key === 'rents'): ?>
            <div class="card" id="rents">
                <div class="card-title">🏠 收租记录</div>
                <?php if (empty($rents)): ?>
                    <div class="empty">暂无收租记录</div>
                <?php else: ?>
                    <?php foreach ($rents as $rent): ?>
                    <div class="record-item">
                        <div>
                            <div class="record-name"><?= h($rent['tenant_name']) ?></div>
                            <div class="record-meta">
                                <?= h($rent['property']) ?> · <?= h($rent['rent_date']) ?>
                            </div>
                        </div>
                        <div class="record-right">
                            <div class="record-amount">¥<?= number_format($rent['amount'], 2) ?></div>
                            <span class="status-badge <?= $rent['is_paid'] ? 'status-active' : 'status-pending' ?>">
                                <?= $rent['is_paid'] ? '已收' : '待收' ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
