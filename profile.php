<?php
require '../config.php';
$pageTitle = '个人资料';
$activePage = 'profile';

if (!isLoggedIn()) redirect('../login.php');

$users = loadData('users');
$user = currentUser();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ---- 更新基本资料 ----
    if (isset($_POST['save_profile'])) {
        $nickname = trim($_POST['nickname'] ?? '');
        $signature = trim($_POST['signature'] ?? '');
        if ($nickname === '') {
            $err = '昵称不能为空';
        } else {
            foreach ($users as &$u) {
                if ((int)$u['id'] === (int)$_SESSION['user_id']) {
                    $u['nickname'] = $nickname;
                    $u['signature'] = $signature;
                    break;
                }
            }
            unset($u);
            saveData('users', $users);
            $msg = '资料已更新';
        }
    }

    // ---- 修改密码 ----
    if (isset($_POST['change_pwd'])) {
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $current = null;
        foreach ($users as $u) {
            if ((int)$u['id'] === (int)$_SESSION['user_id']) { $current = $u; break; }
        }
        if (!password_verify($old, $current['password'])) {
            $err = '原密码不正确';
        } elseif (strlen($new) < 6) {
            $err = '新密码至少 6 位';
        } elseif ($new !== $confirm) {
            $err = '两次输入的新密码不一致';
        } else {
            foreach ($users as &$u) {
                if ((int)$u['id'] === (int)$_SESSION['user_id']) {
                    $u['password'] = password_hash($new, PASSWORD_DEFAULT);
                    break;
                }
            }
            unset($u);
            saveData('users', $users);
            $msg = '密码已修改，下次请用新密码登录';
        }
    }

    // ---- 上传头像 ----
    if (isset($_POST['upload_avatar']) && isset($_FILES['avatar'])) {
        $file = $_FILES['avatar'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allow = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            $mime = mime_content_type($file['tmp_name']);
            if (isset($allow[$mime]) && $file['size'] <= 2 * 1024 * 1024) {
                $ext = $allow[$mime];
                $fname = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $fname)) {
                    // 删除旧头像
                    if (!empty($user['avatar']) && file_exists(UPLOAD_DIR . $user['avatar'])) {
                        @unlink(UPLOAD_DIR . $user['avatar']);
                    }
                    foreach ($users as &$u) {
                        if ((int)$u['id'] === (int)$_SESSION['user_id']) { $u['avatar'] = $fname; break; }
                    }
                    unset($u);
                    saveData('users', $users);
                    $msg = '头像已更新';
                } else {
                    $err = '头像上传失败，请检查 uploads 目录权限';
                }
            } else {
                $err = '仅支持 JPG/PNG/GIF/WebP 且小于 2MB 的图片';
            }
        } else {
            $err = '请选择要上传的图片';
        }
    }

    if ($msg)  { /* 让下方显示成功 */ }
    // 重新加载用户数据
    $users = loadData('users');
    $user = currentUser();
}

include '_header.php';
?>
    <?php if ($msg): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-error"><?= h($err) ?></div><?php endif; ?>

    <div class="card" style="margin-bottom:20px">
        <div class="section-title">头像</div>
        <form method="POST" enctype="multipart/form-data">
            <div class="avatar-upload">
                <div class="avatar-preview">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="../uploads/<?= h($user['avatar']) ?>" alt="头像">
                    <?php else: ?>
                        👤
                    <?php endif; ?>
                </div>
                <div>
                    <input type="file" name="avatar" accept="image/*">
                    <p style="font-size:12px;color:var(--text-secondary);margin-top:6px">支持 JPG/PNG/GIF/WebP，2MB 以内</p>
                </div>
            </div>
            <button type="submit" name="upload_avatar" class="btn btn-primary btn-sm">上传头像</button>
        </form>
    </div>

    <div class="card" style="margin-bottom:20px">
        <div class="section-title">基本信息</div>
        <form method="POST" style="max-width:520px">
            <div class="form-group">
                <label>昵称</label>
                <input type="text" name="nickname" required value="<?= h($user['nickname']) ?>">
            </div>
            <div class="form-group">
                <label>签名</label>
                <input type="text" name="signature" value="<?= h($user['signature']) ?>" placeholder="一句个人介绍">
            </div>
            <div class="form-group">
                <label>用户名（登录账号，不可修改）</label>
                <input type="text" value="<?= h($user['username']) ?>" disabled>
            </div>
            <button type="submit" name="save_profile" class="btn btn-primary btn-sm">保存资料</button>
        </form>
    </div>

    <div class="card">
        <div class="section-title">修改密码</div>
        <form method="POST" style="max-width:520px">
            <div class="form-group">
                <label>原密码</label>
                <input type="password" name="old_password" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>新密码</label>
                <input type="password" name="new_password" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label>确认新密码</label>
                <input type="password" name="confirm_password" required minlength="6" autocomplete="new-password">
            </div>
            <button type="submit" name="change_pwd" class="btn btn-primary btn-sm">修改密码</button>
        </form>
    </div>
<?php include '_footer.php'; ?>
