<?php
require '../config.php';
$pageTitle = '收租管理';
$activePage = 'rents';

$rents = loadData('rents');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $rents[] = [
            'id'          => nextId($rents),
            'tenant_name' => trim($_POST['tenant_name'] ?? ''),
            'property'    => trim($_POST['property'] ?? ''),
            'amount'      => round((float)($_POST['amount'] ?? 0), 2),
            'rent_date'   => $_POST['rent_date'] ?? date('Y-m-d'),
            'is_paid'     => isset($_POST['is_paid']) ? 1 : 0,
            'remark'      => trim($_POST['remark'] ?? '')
        ];
        saveData('rents', $rents);
        redirect('rents.php');
    }

    if (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        foreach ($rents as &$r) {
            if ((int)$r['id'] === $id) {
                $r['tenant_name'] = trim($_POST['tenant_name'] ?? $r['tenant_name']);
                $r['property']    = trim($_POST['property'] ?? $r['property']);
                $r['amount']      = round((float)($_POST['amount'] ?? 0), 2);
                $r['rent_date']   = $_POST['rent_date'] ?? $r['rent_date'];
                $r['is_paid']     = isset($_POST['is_paid']) ? 1 : 0;
                $r['remark']      = trim($_POST['remark'] ?? '');
                break;
            }
        }
        unset($r);
        saveData('rents', $rents);
        redirect('rents.php');
    }

    // 快速标记收/未收（列表上的切换）
    if (isset($_POST['toggle'])) {
        $id = (int)$_POST['id'];
        foreach ($rents as &$r) {
            if ((int)$r['id'] === $id) { $r['is_paid'] = $r['is_paid'] ? 0 : 1; break; }
        }
        unset($r);
        saveData('rents', $rents);
        redirect('rents.php');
    }

    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $rents = array_values(array_filter($rents, function ($r) use ($id) {
            return (int)$r['id'] !== $id;
        }));
        saveData('rents', $rents);
        redirect('rents.php');
    }
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editItem = null;
foreach ($rents as $r) {
    if ((int)$r['id'] === $editId) { $editItem = $r; break; }
}

// 统计
$total = 0; $paid = 0; $unpaid = 0;
foreach ($rents as $r) {
    $total += $r['amount'];
    if ($r['is_paid']) $paid += $r['amount']; else $unpaid += $r['amount'];
}

include '_header.php';
?>
    <div class="card" style="margin-bottom:20px">
        <div class="section-title"><?= $editItem ? '编辑收租记录' : '新增收租记录' ?></div>
        <form method="POST" style="display:grid;grid-template-columns:1fr 1.6fr 110px 150px auto;gap:10px;align-items:end">
            <?php if ($editItem): ?>
                <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
            <?php endif; ?>
            <div class="form-group" style="margin:0">
                <label>租户</label>
                <input type="text" name="tenant_name" required value="<?= h($editItem['tenant_name'] ?? '') ?>" placeholder="租户姓名">
            </div>
            <div class="form-group" style="margin:0">
                <label>房产</label>
                <input type="text" name="property" value="<?= h($editItem['property'] ?? '') ?>" placeholder="如：XX小区3栋501">
            </div>
            <div class="form-group" style="margin:0">
                <label>租金(¥)</label>
                <input type="number" name="amount" step="0.01" min="0" required value="<?= h($editItem['amount'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>收租日期</label>
                <input type="date" name="rent_date" value="<?= h($editItem['rent_date'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px;align-items:center">
                    <label class="chk">
                        <input type="checkbox" name="is_paid" <?= ($editItem && $editItem['is_paid']) ? 'checked' : '' ?>>
                        已收
                    </label>
                    <?php if ($editItem): ?>
                        <button type="submit" name="update" class="btn btn-primary btn-sm">保存</button>
                        <a href="rents.php" class="btn btn-ghost btn-sm">取消</a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-primary btn-sm">添加</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        <div style="margin-top:12px;font-size:13px;color:var(--text-secondary)">
            合计 ¥<?= number_format($total, 2) ?> ｜ 已收 ¥<strong style="color:var(--success)"><?= number_format($paid, 2) ?></strong>
            ｜ 待收 ¥<strong style="color:var(--warning)"><?= number_format($unpaid, 2) ?></strong>
        </div>
    </div>

    <div class="card">
        <div class="section-title">收租记录（<?= count($rents) ?> 条）</div>
        <?php if (empty($rents)): ?>
            <div class="empty">暂无收租记录</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>租户</th>
                            <th>房产</th>
                            <th>金额</th>
                            <th>日期</th>
                            <th>状态</th>
                            <th>备注</th>
                            <th style="width:180px">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rents as $r): ?>
                        <tr>
                            <td><?= h($r['tenant_name']) ?></td>
                            <td style="color:var(--text-secondary)"><?= h($r['property']) ?></td>
                            <td style="font-weight:600">¥<?= number_format($r['amount'], 2) ?></td>
                            <td><?= h($r['rent_date']) ?></td>
                            <td>
                                <span class="status-badge <?= $r['is_paid'] ? 'status-active' : 'status-pending' ?>">
                                    <?= $r['is_paid'] ? '已收' : '待收' ?>
                                </span>
                            </td>
                            <td style="color:var(--text-secondary)"><?= h($r['remark']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                        <button type="submit" name="toggle" class="btn btn-ghost btn-sm">
                                            <?= $r['is_paid'] ? '标记待收' : '标记已收' ?>
                                        </button>
                                    </form>
                                    <a href="rents.php?edit=<?= (int)$r['id'] ?>" class="btn btn-ghost btn-sm">编辑</a>
                                    <form method="POST" data-confirm="确定删除该收租记录？" style="display:inline">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
