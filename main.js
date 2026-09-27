// 个人管理中心 - 前端交互
(function () {
    'use strict';

    // ===== 主题切换 =====
    var themeToggle = document.getElementById('themeToggle');
    var savedTheme = localStorage.getItem('pc_theme') || 'light';

    // 初始化主题
    document.documentElement.setAttribute('data-theme', savedTheme);
    if (themeToggle) themeToggle.textContent = savedTheme === 'dark' ? '☀️ 浅色' : '🌙 深色';

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('pc_theme', next);
            themeToggle.textContent = next === 'dark' ? '☀️ 浅色' : '🌙 深色';
        });
    }

    // ===== 平滑滚动到区块 =====
    var anchors = document.querySelectorAll('a[href^="#"]');
    Array.prototype.forEach.call(anchors, function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId.length > 1) {
                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // ===== 表单删除二次确认 =====
    var delForms = document.querySelectorAll('form[data-confirm]');
    Array.prototype.forEach.call(delForms, function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
})();
