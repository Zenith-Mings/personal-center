<?php
require '../config.php';
$pageTitle = '技能树管理';
$activePage = 'skills';

$skills = loadData('skills');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 保存修改
    if (isset($_POST['save'])) {
        $id = (int)$_POST['id'];
        foreach ($skills as &$s) {
            if ((int)$s['id'] === $id) {
                $s['name']     = trim($_POST['name'] ?? $s['name']);
                $s['category'] = trim($_POST['category'] ?? $s['category']);
                $s['desc']     = trim($_POST['desc'] ?? '');
                $s['progress'] = max(0, min(100, (int)($_POST['progress'] ?? 0)));
                $s['unlocked'] = isset($_POST['unlocked']);
                break;
            }
        }
        unset($s);
        saveData('skills', $skills);
        redirect('skills.php');
    }

    // 添加技能
    if (isset($_POST['add'])) {
        $skills[] = [
            'id'       => nextId($skills),
            'category' => trim($_POST['category'] ?? '通用'),
            'name'     => trim($_POST['name'] ?? ''),
            'desc'     => trim($_POST['desc'] ?? ''),
            'progress' => 0,
            'unlocked' => false,
            'sort'     => 99
        ];
        saveData('skills', $skills);
        redirect('skills.php');
    }

    // 删除技能
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $skills = array_values(array_filter($skills, function ($s) use ($id) {
            return (int)$s['id'] !== $id;
        }));
        saveData('skills', $skills);
        redirect('skills.php');
    }
}

// 按分类整理
$categories = [];
foreach ($skills as $s) {
    $categories[$s['category']][] = $s;
}

include '_header.php';
?>
    <div class="card" style="margin-bottom:20px">
        <div class="section-title">添加新技能</div>
        <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 2fr auto;gap:10px;align-items:end">
            <div class="form-group" style="margin:0">
                <label>分类</label>
                <input type="text" name="category" required placeholder="如：编程语言">
            </div>
            <div class="form-group" style="margin:0">
                <label>技能名称</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>描述</label>
                <input type="text" name="desc" placeholder="一句话说明这个技能">
            </div>
            <button type="submit" name="add" class="btn btn-primary">添加</button>
        </form>
    </div>

    <?php if (empty($skills)): ?>
        <div class="card"><div class="empty">还没有技能，先添加一个吧</div></div>
    <?php else: ?>
        <?php foreach ($categories as $cat => $items): ?>
        <div class="card" style="margin-bottom:15px">
            <div class="section-title"><?= h($cat) ?>（<?= count($items) ?>）</div>
            <?php foreach ($items as $skill): ?>
            <form method="POST" class="inline-form">
                <input type="hidden" name="id" value="<?= (int)$skill['id'] ?>">
                <input type="hidden" name="category" value="<?= h($skill['category']) ?>">
                <input type="text" name="name" value="<?= h($skill['name']) ?>">
                <input type="text" name="desc" value="<?= h($skill['desc']) ?>">
                <input type="number" name="progress" value="<?= (int)$skill['progress'] ?>" min="0" max="100" title="学习进度 0-100">
                <label class="chk">
                    <input type="checkbox" name="unlocked" <?= $skill['unlocked'] ? 'checked' : '' ?>>
                    点亮
                </label>
                <div class="row-actions">
                    <button type="submit" name="save" class="btn btn-primary btn-sm">保存</button>
                    <button type="submit" name="delete" class="btn btn-danger btn-sm"
                        form="del-skill-<?= (int)$skill['id'] ?>" data-confirm="确定删除技能「<?= h($skill['name']) ?>」？">删</button>
                </div>
            </form>
            <form id="del-skill-<?= (int)$skill['id'] ?>" method="POST" style="display:none">
                <input type="hidden" name="id" value="<?= (int)$skill['id'] ?>">
                <input type="hidden" name="delete" value="1">
            </form>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php include '_footer.php'; ?>
