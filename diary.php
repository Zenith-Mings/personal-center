<?php
require '../config.php';
$pageTitle = '日记管理';
$activePage = 'diary';

$diaries = loadData('diaries');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $diaries[] = [
            'id'         => nextId($diaries),
            'title'      => trim($_POST['title'] ?? ''),
            'content'    => trim($_POST['content'] ?? ''),
            'mood'       => trim($_POST['mood'] ?? '😊'),
            'created_at' => $_POST['date'] ?? date('Y-m-d'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        // 按日期倒序
        usort($diaries, function ($a, $b) { return strcmp($b['created_at'], $a['created_at']); });
        saveData('diaries', $diaries);
        redirect('diary.php');
    }

    if (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        foreach ($diaries as &$d) {
            if ((int)$d['id'] === $id) {
                $d['title']      = trim($_POST['title'] ?? $d['title']);
                $d['content']    = trim($_POST['content'] ?? '');
                $d['mood']       = trim($_POST['mood'] ?? $d['mood']);
                $d['created_at'] = $_POST['date'] ?? $d['created_at'];
                $d['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($d);
        usort($diaries, function ($a, $b) { return strcmp($b['created_at'], $a['created_at']); });
        saveData('diaries', $diaries);
        redirect('diary.php');
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $diaries = array_values(array_filter($diaries, function ($d) use ($id) {
            return (int)$d['id'] !== $id;
        }));
        saveData('diaries', $diaries);
        redirect('diary.php');
    }
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
foreach ($diaries as $d) {
    if ((int)$d['id'] === $editId) { $editItem = $d; break; }
}

include '_header.php';
?>
    <div class="card" style="margin-bottom:20px">
        <div class="section-title"><?= $editItem ? '编辑日记' : '写日记' ?></div>
        <form method="POST" style="max-width:820px">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
            <?php endif; ?>
            <div class="toolbar">
                <div class="form-group" style="flex:1;min-width:220px">
                    <label>标题</label>
                    <input type="text" name="title" required value="<?= h($editItem['title'] ?? '') ?>" placeholder="今天的标题">
                </div>
                <div class="form-group" style="width:120px">
                    <label>心情</label>
                    <select name="mood">
                        <?php
                        $moods = ['😊 开心', '😐 平静', '😔 低落', '🤯 头大', '💪 奋斗', '🎉 兴奋'];
                        $cur = $editItem['mood'] ?? '😊 开心';
                        foreach ($moods as $m) {
                            $sel = $m === $cur || strpos($cur, $m) !== false ? 'selected' : '';
                            echo "<option value=\"" . h($m) . "\" $sel>" . h($m) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" style="width:150px">
                    <label>日期</label>
                    <input type="date" name="date" value="<?= h($editItem['created_at'] ?? date('Y-m-d')) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>内容</label>
                <textarea name="content" required placeholder="记录今天的学习与思考……" style="min-height:180px"><?= h($editItem['content'] ?? '') ?></textarea>
            </div>
            <?php if ($editItem): ?>
                <button type="submit" name="update" class="btn btn-primary">保存修改</button>
                <a href="diary.php" class="btn btn-ghost">取消</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-primary">保存日记</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="section-title">全部日记（<?= count($diaries) ?> 篇）</div>
        <?php if (empty($diaries)): ?>
            <div class="empty">还没有日记，写下第一篇吧</div>
        <?php else: ?>
            <?php foreach ($diaries as $d): ?>
            <div class="diary-item">
                <div class="diary-header">
                    <span class="diary-title"><?= h($d['mood']) ?> <?= h($d['title']) ?></span>
                    <span class="diary-meta"><?= h($d['created_at']) ?></span>
                </div>
                <div class="diary-content" style="-webkit-line-clamp:2"><?= nl2br(h($d['content'])) ?></div>
                <div style="margin-top:8px">
                    <a href="diary.php?edit=<?= (int)$d['id'] ?>" class="btn btn-ghost btn-sm">编辑</a>
                    <form method="POST" data-confirm="确定删除日记「<?= h($d['title']) ?>」？" style="display:inline">
                        <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">删除</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php include '_footer.php'; ?>
