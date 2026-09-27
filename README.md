# 个人管理中心

一个极简风格的个人管理中心网站，纯 PHP + HTML + CSS + JS，**数据用 JSON 本地文件存储，无需数据库**。

## 功能

- 🌳 **技能树**：内置网络安全方向技能（编程语言 / 网络安全 / 安全工具），后台可点亮技能、设置学习进度，前端展示进度条
- 🏆 **成就荣誉**：展示获得的成就
- 📝 **每日日记**：后台写日记，前端展示最近 3 篇
- 📅 **订阅管理**：管理订阅项与下次扣费时间
- 🏠 **收租记录**：记录租金收/待收状态
- ⚙️ **模块开关**：后台可独立控制每个功能是否在首页展示，并调整排序和名称
- 🎨 **深色/浅色模式**：一键切换，自动记忆偏好
- 👤 **个人资料**：设置昵称、签名，上传头像
- 📱 **响应式**：支持电脑端和移动端

## 目录结构

```
personal-center/
├── config.php           # 配置与数据读写
├── index.php            # 前端首页
├── login.php            # 登录
├── logout.php           # 退出
├── css/style.css        # 全局样式（深浅双模式）
├── js/main.js           # 前端交互
├── admin/               # 后台管理
│   ├── dashboard.php    # 概览
│   ├── skills.php       # 技能树管理
│   ├── achievements.php # 成就管理
│   ├── diary.php        # 日记管理
│   ├── subscriptions.php# 订阅管理
│   ├── rents.php        # 收租管理
│   ├── settings.php     # 模块开关
│   └── profile.php      # 个人资料
├── data/                # JSON 数据（自动生成）
└── uploads/             # 头像上传
```

## 部署

1. 将整个 `personal-center` 文件夹放到 PHP 环境网站目录（如 XAMPP 的 `htdocs`、宝塔、Nginx+PHP-FPM 等）
2. 确保 `data/` 和 `uploads/` 目录有写入权限
3. 浏览器访问 `http://localhost/personal-center/index.php`

## 默认账号

- 用户名：`admin`
- 密码：`admin123`

登录后可在「个人资料」中修改密码。数据保存在 `data/` 目录下的 JSON 文件中，首次访问会自动初始化。

## 技术栈

- PHP（无框架）
- 原生 HTML / CSS / JavaScript
- JSON 文件存储
