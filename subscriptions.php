<?php
require '../config.php';
$pageTitle = '订阅管理';
$activePage = 'subscriptions';

$subs = loadData('subscriptions');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $subs[] = [
            'id'           => nextId($subs),
            'name'         => trim($_POST['name'] ?? ''),
            'price'        => round((float)($_POST['price'] ?? 0), 2),
            'cycle'        => trim($_POST['cycle'] ?? '月付'),
            'start_date'   => $_POST['start_date'] ?? date('Y-m-d'),
            'next_due_date'=> $_POST['next_due_date'] ?? date('Y-m-d'),
            'status'       => 1, // 新增默认启用
            'remark'       => trim($_POST['remark'] ?? '')
        ];
        saveData('subscriptions', $subs);
        redirect('subscriptions.php');
    }

    if (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        foreach ($subs as &$s) {
            if ((int)$s['id'] === $id) {
                $s['name']          = trim($_POST['name'] ?? $s['name']);
                $s['price']         = round((float)($_POST['price'] ?? 0), 2);
                $s['cycle']         = trim($_POST['cycle'] ?? $s['cycle']);
                $s['start_date']    = $_POST['start_date'] ?? $s['start_date'];
                $s['next_due_date'] = $_POST['next_due_date'] ?? $s['next_due_date'];
                $s['status']        = isset($_POST['status']) ? 1 : 0;
                $s['remark']        = trim($_POST['remark'] ?? '');
                break;
            }
        }
        unset($s);
        saveData('subscriptions', $subs);
        redirect('subscriptions.php');
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $subs = array_values(array_filter($subs, function ($s) use ($id) {
            return (int)$s['id'] !== $id;
        }));
        saveData('subscriptions', $subs);
        redirect('subscriptions.php');
    }
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
foreach ($subs as $s) {
    if ((int)$s['id'] === $editId) { $editItem = $s; break; }
}

include '_header.php';
?>
    <div class="card" style="margin-bottom:20px">
        <div class="section-title"><?= $editItem ? '编辑订阅' : '新增订阅' ?></div>
        <form method="POST" style="display:grid;grid-template-columns:1.4fr 110px 100px 150px 150px auto;gap:10px;align-items:end">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
            <?php endif; ?>
            <div class="form-group" style="margin:0">
                <label>名称</label>
                <input type="text" name="name" required value="<?= h($editItem['name'] ?? '') ?>" placeholder="如：视频会员">
            </div>
            <div class="form-group" style="margin:0">
                <label>价格(¥)</label>
                <input type="number" name="price" step="0.01" min="0" required value="<?= h($editItem['price'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>周期</label>
                <select name="cycle">
                    <?php
                    $cycles = ['月付', '季付', '年付', '周付'];
                    $cur = $editItem['cycle'] ?? '月付';
                    foreach ($cycles as $c) {
                        $sel = $c === $cur ? 'selected' : '';
                        echo "<option $sel>" . h($c) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>开始日期</label>
                <input type="date" name="start_date" value="<?= h($editItem['start_date'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>下次扣费</label>
                <input type="date" name="next_due_date" value="<?= h($editItem['next_due_date'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px">
                    <?php if ($editItem): ?>
                        <button type="submit" name="update" class="btn btn-primary btn-sm">保存</button>
                        <a href="subscriptions.php" class="btn btn-ghost btn-sm">取消</a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-primary btn-sm">添加</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        <?php if (!$editItem): ?>
        <div style="margin-top:10px">
            <label class="checkbox-switch">
                <input type="checkbox" name="status" checked disabled> 新订阅默认启用（可在列表中停用）
            </label>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="section-title">订阅列表（<?= count($subs) ?> 项，月付合计 ¥<?= number_format(array_sum(array_map(function($s){return $s['price'];}, $subs)), 2) ?>）</div>
        <?php if (empty($subs)): ?>
            <div class="empty">暂无订阅</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>名称</th>
                            <th>价格</th>
                            <th>周期</th>
                            <th>下次扣费</th>
                            <th>状态</th>
                            <th>备注</th>
                            <th style="width:150px">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subs as $s): ?>
                        <tr>
                            <td><?= h($s['name']) ?></td>
                            <td style="color:var(--success);font-weight:600">¥<?= number_format($s['price'], 2) ?></td>
                            <td><?= h($s['cycle']) ?></td>
                            <td><?= h($s['next_due_date']) ?></td>
                            <td>
                                <span class="status-badge <?= $s['status'] ? 'status-active' : 'status-expired' ?>">
                                    <?= $s['status'] ? '启用' : '停用' ?>
                                </span>
                            </td>
                            <td style="color:var(--text-secondary)"><?= h($s['remark']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="subscriptions.php?edit=<?= (int)$s['id'] ?>" class="btn btn-ghost btn-sm">编辑</a>
                                    <form method="POST" data-confirm="确定删除订阅「<?= h($s['name']) ?>」？" style="display:inline">
                                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
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
