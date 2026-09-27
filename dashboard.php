<?php
require '../config.php';
$pageTitle = '概览';
$activePage = 'dashboard';

// 汇总统计
$skillCount      = count(loadData('skills'));
$unlockedCount   = count(array_filter(loadData('skills'), function ($s) { return $s['unlocked']; }));
$achievementCount= count(loadData('achievements'));
$diaryCount      = count(loadData('diaries'));
$subCount        = count(loadData('subscriptions'));
$rentCount       = count(loadData('rents'));

// 收租统计
$rents    = loadData('rents');
$totalRent = 0; $unpaidRent = 0;
foreach ($rents as $r) {
    $totalRent += $r['amount'];
    if (!$r['is_paid']) $unpaidRent += $r['amount'];
}

// 订阅统计
$subs   = loadData('subscriptions');
$subCost = 0;
foreach ($subs as $s) $subCost += $s['price'];

// 技能整体进度
$avgProgress = $skillCount > 0 ? round(array_sum(array_column(loadData('skills'), 'progress')) / $skillCount) : 0;

include '_header.php';
?>
    <div class="card">
        <div class="section-title">欢迎回来，<?= h($user['nickname']) ?></div>
        <p style="color:var(--text-secondary);font-size:14px">
            这里是你的个人管理中心总览。使用上方导航管理各项内容，在「模块设置」中可开关首页显示的模块。
        </p>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value"><?= $skillCount ?></div>
                <div class="stat-label">技能总数（已点亮 <?= $unlockedCount ?>）</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?= $avgProgress ?>%</div>
                <div class="stat-label">技能平均进度</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?= $achievementCount ?></div>
                <div class="stat-label">成就荣誉</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?= $diaryCount ?></div>
                <div class="stat-label">日记篇数</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?= $subCount ?></div>
                <div class="stat-label">订阅项数</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?= $rentCount ?></div>
                <div class="stat-label">收租记录</div>
            </div>
        </div>

        <div class="summary-line">
            <span style="color:var(--text-secondary)">💰 待收租金合计：</span>
            <strong style="color:var(--warning)">¥<?= number_format($unpaidRent, 2) ?></strong>
            <span style="color:var(--text-secondary)">｜</span>
            <span style="color:var(--text-secondary)">已收租金合计：</span>
            <strong style="color:var(--success)">¥<?= number_format($totalRent - $unpaidRent, 2) ?></strong>
            <span style="color:var(--text-secondary)">｜</span>
            <span style="color:var(--text-secondary)">订阅月付合计：</span>
            <strong style="color:var(--primary)">¥<?= number_format($subCost, 2) ?></strong>
        </div>
    </div>

    <div class="card" style="margin-top:20px">
        <div class="section-title">💡 使用提示</div>
        <ul style="color:var(--text-secondary);font-size:14px;padding-left:18px;line-height:2">
            <li>所有数据保存在 <code>data/</code> 目录的 JSON 文件中，无需数据库。</li>
            <li>在「技能树」中点亮技能并设置学习进度，首页会自动展示进度条。</li>
            <li>在「模块设置」中可独立开关每个功能在首页的显示。</li>
            <li>在「个人资料」中可修改昵称、签名并上传头像。</li>
        </ul>
    </div>
<?php include '_footer.php'; ?>
