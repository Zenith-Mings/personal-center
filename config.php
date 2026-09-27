<?php
/**
 * 个人管理中心 - 核心配置与数据读写
 * 数据使用 JSON 本地文件存储，无需数据库
 */
session_start();
date_default_timezone_set('Asia/Shanghai');

// 数据与上传目录
define('DATA_DIR', __DIR__ . '/data/');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// 确保目录存在
if (!file_exists(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

/**
 * 读取 JSON 数据
 * @param string $filename 文件名（不含 .json）
 * @return array
 */
function loadData($filename) {
    $path = DATA_DIR . $filename . '.json';
    if (!file_exists($path)) {
        initDefaultData($filename);
    }
    $content = file_get_contents($path);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

/**
 * 保存 JSON 数据
 * @param string $filename 文件名（不含 .json）
 * @param array  $data     数据
 */
function saveData($filename, $data) {
    $path = DATA_DIR . $filename . '.json';
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

/**
 * 初始化默认数据
 */
function initDefaultData($type) {
    switch ($type) {
        case 'users':
            $data = [[
                'id' => 1,
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'nickname' => '安全研究员',
                'avatar' => '',
                'signature' => '网络安全 · 持续学习中'
            ]];
            break;

        case 'settings':
            $data = [
                ['key' => 'skills',          'name' => '技能树',   'enabled' => true, 'sort' => 1],
                ['key' => 'achievements',    'name' => '成就荣誉', 'enabled' => true, 'sort' => 2],
                ['key' => 'diary',           'name' => '每日日记', 'enabled' => true, 'sort' => 3],
                ['key' => 'subscriptions',   'name' => '订阅管理', 'enabled' => true, 'sort' => 4],
                ['key' => 'rents',           'name' => '收租记录', 'enabled' => true, 'sort' => 5]
            ];
            break;

        case 'skills':
            $data = [
                // 编程语言
                ['id' => 1,  'category' => '编程语言', 'name' => 'Python',      'progress' => 0, 'unlocked' => false, 'desc' => '自动化脚本、漏洞利用开发',       'sort' => 1],
                ['id' => 2,  'category' => '编程语言', 'name' => 'PHP',          'progress' => 0, 'unlocked' => false, 'desc' => 'Web开发与代码审计',              'sort' => 2],
                ['id' => 3,  'category' => '编程语言', 'name' => 'C/C++',        'progress' => 0, 'unlocked' => false, 'desc' => '底层开发与逆向分析',              'sort' => 3],
                ['id' => 4,  'category' => '编程语言', 'name' => 'Go',           'progress' => 0, 'unlocked' => false, 'desc' => '高性能工具开发',                  'sort' => 4],
                ['id' => 5,  'category' => '编程语言', 'name' => 'JavaScript',   'progress' => 0, 'unlocked' => false, 'desc' => '前端与XSS Payload编写',           'sort' => 5],
                // 网络安全
                ['id' => 6,  'category' => '网络安全', 'name' => 'Web渗透测试', 'progress' => 0, 'unlocked' => false, 'desc' => 'SQL注入、XSS、CSRF等Web漏洞',    'sort' => 1],
                ['id' => 7,  'category' => '网络安全', 'name' => '逆向工程',     'progress' => 0, 'unlocked' => false, 'desc' => '二进制分析、脱壳、反编译',        'sort' => 2],
                ['id' => 8,  'category' => '网络安全', 'name' => '密码学',       'progress' => 0, 'unlocked' => false, 'desc' => '加密算法、口令破解、证书安全',    'sort' => 3],
                ['id' => 9,  'category' => '网络安全', 'name' => '安全运维',     'progress' => 0, 'unlocked' => false, 'desc' => '等保合规、日志审计、入侵检测',    'sort' => 4],
                ['id' => 10, 'category' => '网络安全', 'name' => '移动安全',     'progress' => 0, 'unlocked' => false, 'desc' => 'Android逆向、APP漏洞分析',        'sort' => 5],
                // 安全工具
                ['id' => 11, 'category' => '安全工具', 'name' => 'Burp Suite',  'progress' => 0, 'unlocked' => false, 'desc' => 'Web抓包与渗透测试神器',           'sort' => 1],
                ['id' => 12, 'category' => '安全工具', 'name' => 'Wireshark',   'progress' => 0, 'unlocked' => false, 'desc' => '网络流量分析与抓包',              'sort' => 2],
                ['id' => 13, 'category' => '安全工具', 'name' => 'Nmap',        'progress' => 0, 'unlocked' => false, 'desc' => '端口扫描与资产探测',              'sort' => 3],
                ['id' => 14, 'category' => '安全工具', 'name' => 'Metasploit',  'progress' => 0, 'unlocked' => false, 'desc' => '渗透测试框架与漏洞利用',          'sort' => 4],
                ['id' => 15, 'category' => '安全工具', 'name' => 'Ghidra',      'progress' => 0, 'unlocked' => false, 'desc' => '逆向工程反汇编工具',              'sort' => 5]
            ];
            break;

        case 'achievements':
            $data = [
                ['id' => 1, 'title' => '入门之路',   'desc' => '完成第一个渗透测试靶场', 'date' => '2024-01-15', 'icon' => '🎯'],
                ['id' => 2, 'title' => '代码审计师', 'desc' => '独立发现并提交一个高危漏洞', 'date' => '2024-03-20', 'icon' => '🔍'],
                ['id' => 3, 'title' => 'CTF 选手',   'desc' => '在 CTF 比赛中获得名次', 'date' => '2024-05-01', 'icon' => '🚩']
            ];
            break;

        case 'diaries':
        case 'subscriptions':
        case 'rents':
            $data = [];
            break;

        default:
            $data = [];
    }
    saveData($type, $data);
}

/**
 * 是否已登录
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * 获取当前用户
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    $users = loadData('users');
    foreach ($users as $u) {
        if ($u['id'] == $_SESSION['user_id']) return $u;
    }
    return null;
}

/**
 * 生成下一个 ID
 */
function nextId($arr) {
    if (empty($arr)) return 1;
    $ids = array_column($arr, 'id');
    return max($ids) + 1;
}

/**
 * 安全跳转
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * HTML 转义
 */
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
