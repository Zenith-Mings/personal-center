<?php
require '../config.php';
$pageTitle = '成就荣誉';
$activePage = 'achievements';

$achievements = loadData('achievements');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $achievements[] = [
            'id'    => nextId($achievements),
            'title' => trim($_POST['title'] ?? ''),
            'desc'  => trim($_POST['desc'] ?? ''),
            'date'  => $_POST['date'] ?? date('Y-m-d'),
            'icon'  => trim($_POST['icon'] ?? '🏆')
        ];
        saveData('achievements', $achievements);
        redirect('achievements.php');
    }

    if (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        foreach ($achievements as &$a) {
            if ((int)$a['id'] === $id) {
                $a['title'] = trim($_POST['title'] ?? $a['title']);
                $a['desc']  = trim($_POST['desc'] ?? '');
                $a['date']  = $_POST['date'] ?? $a['date'];
                $a['icon']  = trim($_POST['icon'] ?? '🏆');
                break;
            }
        }
        unset($a);
        saveData('achievements', $achievements);
        redirect('achievements.php');
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $achievements = array_values(array_filter($achievements, function ($a) use ($id) {
            return (int)$a['id'] !== $id;
        }));
        saveData('achievements', $achievements);
        redirect('achievements.php');
    }
}

// 默认展示新增表单；点了"编辑"才进入编辑模式
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
foreach ($achievements as $a) {
    if ((int)$a['id'] === $editId) { $editItem = $a; break; }
}

include '_header.php';
?>
    <div class="card" style="margin-bottom:20px">
        <div class="section-title"><?= $editItem ? '编辑成就' : '新增成就' ?></div>
        <form method="POST" style="display:grid;grid-template-columns:90px 1fr 2fr 150px auto;gap:10px;align-items:end">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
            <?php endif; ?>
            <div class="form-group" style="margin:0">
                <label>图标</label>
                <input type="text" name="icon" value="<?= h($editItem['icon'] ?? '🏆') ?>" placeholder="Emoji">
            </div>
            <div class="form-group" style="margin:0">
                <label>标题</label>
                <input type="text" name="title" required value="<?= h($editItem['title'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>描述</label>
                <input type="text" name="desc" value="<?= h($editItem['desc'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>日期</label>
                <input type="date" name="date" value="<?= h($editItem['date'] ?? date('Y-m-d')) ?>">
            </div>
            <?php if ($editItem): ?>
                <button type="submit" name="update" class="btn btn-primary">保存修改</button>
                <a href="achievements.php" class="btn btn-ghost">取消</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-primary">添加</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="section-title">成就列表（<?= count($achievements) ?>）</div>
        <?php if (empty($achievements)): ?>
            <div class="empty">暂无成就</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>图标</th>
                            <th>标题</th>
                            <th>描述</th>
                            <th>日期</th>
                            <th style="width:160px">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achievements as $a): ?>
                        <tr>
                            <td style="font-size:22px"><?= h($a['icon']) ?></td>
                            <td><?= h($a['title']) ?></td>
                            <td style="color:var(--text-secondary)"><?= h($a['desc']) ?></td>
                            <td><?= h($a['date']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="achievements.php?edit=<?= (int)$a['id'] ?>" class="btn btn-ghost btn-sm">编辑</a>
                                    <form method="POST" data-confirm="确定删除成就「<?= h($a['title']) ?>」？" style="display:inline">
                                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">删除</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php include '_footer.php'; ?>
