/**
 * 移动端侧边栏功能
 */
class MobileSidebar {
    constructor() {
        this.init();
    }

    init() {
        // 等待DOM加载完成
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeSidebar();
                this.loadSidebarContent();
            });
        } else {
            this.initializeSidebar();
            this.loadSidebarContent();
        }

        // 适配InstantClick无刷新跳转
        if (window.InstantClick) {
            InstantClick.on('change', () => {
                this.initializeSidebar();
                this.loadSidebarContent();
            });
        }
    }

    // 加载侧边栏内容
    loadSidebarContent() {
        if (!window.puock_metas || !puock_metas.mobile_sidebar_enable) return;

        const sidebarBody = document.querySelector('#mobile-sidebar .mobile-sidebar-body');
        if (!sidebarBody) return;

        // 显示加载状态
        sidebarBody.innerHTML = '<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>';

        // 异步加载侧边栏内容
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html; charset=utf-8'
            }
        })
        .then(response => response.text())
        .then(html => {
            // 解析响应HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newSidebarBody = doc.querySelector('#mobile-sidebar .mobile-sidebar-body');

            if (newSidebarBody) {
                // 更新侧边栏内容
                sidebarBody.innerHTML = newSidebarBody.innerHTML;
                // 重新初始化Puock功能
                if (window.Puock) {
                    window.Puock.pageChangeInit();
                }
            } else {
                sidebarBody.innerHTML = '<div class="text-center py-4 text-danger">加载失败</div>';
            }
        })
        .catch(error => {
            console.error('侧边栏内容加载失败:', error);
            sidebarBody.innerHTML = '<div class="text-center py-4 text-danger">加载失败</div>';
        });
    }

    initializeSidebar() {
        if (!window.puock_metas || !puock_metas.mobile_sidebar_enable) return;

        const toggleBtn = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('mobile-sidebar');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const closeBtn = document.getElementById('mobile-sidebar-close');

        if (!toggleBtn || !sidebar || !overlay || !closeBtn) return;

        // 从sessionStorage获取侧边栏状态
        const sidebarState = sessionStorage.getItem('mobileSidebarState') === 'true';
        if (sidebarState) {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            toggleBtn.classList.add('active');
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-bars-progress');
                icon.classList.add('fa-circle-xmark');
            }
        }

        // 切换按钮点击事件
        toggleBtn.addEventListener('click', () => this.toggleSidebar(toggleBtn, sidebar, overlay));

        // 关闭按钮点击事件
        closeBtn.addEventListener('click', () => this.closeSidebar(toggleBtn, sidebar, overlay));

        // 遮罩层点击事件
        overlay.addEventListener('click', () => this.closeSidebar(toggleBtn, sidebar, overlay));
    }

    toggleSidebar(toggleBtn, sidebar, overlay) {
        const isActive = sidebar.classList.contains('active');
        if (isActive) {
            this.closeSidebar(toggleBtn, sidebar, overlay);
        } else {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            toggleBtn.classList.add('active');
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-bars-progress');
                icon.classList.add('fa-circle-xmark');
            }
            sessionStorage.setItem('mobileSidebarState', 'true');
        }
    }

    closeSidebar(toggleBtn, sidebar, overlay) {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        toggleBtn.classList.remove('active');
        const icon = toggleBtn.querySelector('i');
        if (icon) {
            icon.classList.remove('fa-circle-xmark');
            icon.classList.add('fa-bars-progress');
        }
        sessionStorage.setItem('mobileSidebarState', 'false');
    }
}

// 初始化移动端侧边栏
window.addEventListener('load', () => {
    new MobileSidebar();
});