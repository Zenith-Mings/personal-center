<?php
require '../config.php';
$pageTitle = '模块设置';
$activePage = 'settings';

$settings = loadData('settings');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($settings as &$s) {
        $key = $s['key'];
        $s['enabled'] = isset($_POST['enabled_' . $key]);
        $s['name']    = trim($_POST['name_' . $key] ?? $s['name']);
        $s['sort']    = (int)($_POST['sort_' . $key] ?? $s['sort']);
    }
    unset($s);
    usort($settings, function ($a, $b) { return $a['sort'] - $b['sort']; });
    saveData('settings', $settings);
    redirect('settings.php');
}

include '_header.php';
?>
    <div class="card">
        <div class="section-title">首页模块开关</div>
        <p style="color:var(--text-secondary);font-size:13px;margin-bottom:10px">
            勾选启用的模块会在首页展示；「显示名称」可自定义，「排序」决定首页展示顺序（数字小在前）。
        </p>
        <form method="POST">
            <?php foreach ($settings as $s): ?>
            <div class="setting-row">
                <input type="checkbox" name="enabled_<?= h($s['key']) ?>" <?= $s['enabled'] ? 'checked' : '' ?> title="是否在首页展示">
                <input type="text" name="name_<?= h($s['key']) ?>" value="<?= h($s['name']) ?>">
                <input type="number" name="sort_<?= h($s['key']) ?>" value="<?= (int)$s['sort'] ?>" min="0">
                <span style="font-size:13px;color:var(--text-secondary)"><?= h($s['key']) ?></span>
            </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary" style="margin-top:16px">保存设置</button>
        </form>
    </div>

    <div class="card" style="margin-top:20px">
        <div class="section-title">模块说明</div>
        <table class="table">
            <tbody>
                <tr><td>skills</td><td style="color:var(--text-secondary)">技能树：展示编程语言、网络安全、安全工具的学习进度</td></tr>
                <tr><td>achievements</td><td style="color:var(--text-secondary)">成就荣誉：展示获得的成就与荣誉</td></tr>
                <tr><td>diary</td><td style="color:var(--text-secondary)">每日日记：首页展示最近 3 篇日记</td></tr>
                <tr><td>subscriptions</td><td style="color:var(--text-secondary)">订阅管理：展示订阅项与下次扣费时间</td></tr>
                <tr><td>rents</td><td style="color:var(--text-secondary)">收租记录：展示租金收/待收状态</td></tr>
            </tbody>
        </table>
    </div>
<?php include '_footer.php'; ?>
