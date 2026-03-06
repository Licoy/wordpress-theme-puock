<?php if (!file_exists(dirname(__FILE__) . '/template-script-dev.php')): ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.css?ver=<?php echo PUOCK_CUR_VER_STR ?>">
<?php endif; ?>
<style id="pk-options-style"></style>
<style>
    .pk-reset-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    .pk-reset-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .pk-reset-modal {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transform: scale(0.9);
        transition: transform 0.2s ease;
    }
    .pk-reset-modal-overlay.active .pk-reset-modal {
        transform: scale(1);
    }
    .pk-reset-modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pk-reset-modal-title .dashicons {
        color: #f0a020;
    }
    .pk-reset-modal-content {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .pk-reset-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .pk-reset-modal-btn {
        padding: 8px 20px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .pk-reset-modal-btn-cancel {
        background: #f5f5f5;
        color: #666;
    }
    .pk-reset-modal-btn-cancel:hover {
        background: #e8e8e8;
    }
    .pk-reset-modal-btn-confirm {
        background: #d03050;
        color: #fff;
    }
    .pk-reset-modal-btn-confirm:hover {
        background: #bf2040;
    }
    .pk-reset-modal-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .pk-setting-search-wrap {
        margin-bottom: 12px;
        position: sticky;
        top: 0;
        z-index: 3;
        padding-bottom: 6px;
        background: inherit;
    }
    .pk-setting-search {
        width: 100%;
        height: 38px;
        border: 1px solid #d9d9d9;
        border-radius: 10px;
        padding: 0 14px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 8px 20px rgba(12, 28, 52, 0.06);
        transition: all 0.2s ease;
    }
    .pk-setting-search:focus {
        outline: none;
        border-color: #ae4af7;
        box-shadow: 0 0 0 3px rgba(174, 74, 247, 0.12);
    }
    .pk-setting-search-tip {
        margin-top: 6px;
        font-size: 12px;
        color: #7a7a7a;
        line-height: 1.5;
    }
    @media screen and (max-width: 1200px) {
        .pk-setting-search-wrap {
            margin-bottom: 10px;
        }
    }
</style>
<div id="app">
    <div style="padding: 40px;font-size: 30px">loading...</div>
</div>

<!-- 重置确认弹窗 -->
<div id="pk-reset-modal-overlay" class="pk-reset-modal-overlay">
    <div class="pk-reset-modal">
        <div class="pk-reset-modal-title">
            <span class="dashicons dashicons-warning"></span>
            <?php _e('确认重置设置', PUOCK); ?>
        </div>
        <div class="pk-reset-modal-content">
            <?php _e('此操作将把所有主题设置恢复为默认值，您当前的设置将会丢失。', PUOCK); ?>
            <br><br>
            <strong><?php _e('此操作不可撤销，确定要继续吗？', PUOCK); ?></strong>
        </div>
        <div class="pk-reset-modal-actions">
            <button type="button" class="pk-reset-modal-btn pk-reset-modal-btn-cancel" id="pk-reset-cancel">
                <?php _e('取消', PUOCK); ?>
            </button>
            <button type="button" class="pk-reset-modal-btn pk-reset-modal-btn-confirm" id="pk-reset-confirm">
                <?php _e('确认重置', PUOCK); ?>
            </button>
        </div>
    </div>
</div>

<script>
    jQuery(function () {
        var wpAdminBarEl = jQuery("#wpadminbar")
        var wpFooterEl = jQuery("#wpfooter")
        var pkOptionsStyleEl = jQuery("#pk-options-style")

        function loadWpContentHeight() {
            var h = window.innerHeight - wpAdminBarEl.height() - wpFooterEl.height() - 50
            pkOptionsStyleEl.html("#wpbody-content{height:" + h + "px;padding:0;}#pk-options-box{height:" + (window.innerHeight - wpAdminBarEl.height()) + "px}")
        }

        window.addEventListener("resize", loadWpContentHeight)

        loadWpContentHeight()
    })

    window.puockSettingId = "puock-theme-options-global";
    window.puockSettingMetaInfo = {
        version: "V<?php echo PUOCK_CUR_VER_STR ?>",
        colors: {
            primaryColor: '#ae4af7',
            primaryColorHover: '#903eca',
            primaryColorPressed: '#8912e6',
            primaryColorSuppl: '#a537fb',
        },
        language:"<?php echo get_user_locale() ?>",
        description:"<?php _e('简单/方便/高颜值', PUOCK) ?>",
        tag: {text: "<?php _e('主题', PUOCK) ?>", color: 'rgb(155,39,238)'},
        github: "https://github.com/Licoy/wordpress-theme-puock",
        qq: "https://licoy.cn/go/puock-update.php?r=qq_qun",
        license: "GPL V3",
        donate: "https://licoy.cn/puock-theme-sponsor.html",
        update_url: '<?php echo admin_url('admin-ajax.php') ?>?action=update_theme_options',
        reset_url: '<?php echo admin_url('admin-ajax.php') ?>?action=reset_theme_options',
        fields:<?php echo json_encode($fields); ?>,
        data:<?php echo json_encode(get_option(PUOCK_OPT)); ?>,
    }

    // 重置功能
    ;(function() {
        var resetModalOverlay = document.getElementById('pk-reset-modal-overlay');
        var resetConfirmBtn = document.getElementById('pk-reset-confirm');
        var resetCancelBtn = document.getElementById('pk-reset-cancel');
        var isResetting = false;

        function showResetModal() {
            resetModalOverlay.classList.add('active');
        }

        function hideResetModal() {
            resetModalOverlay.classList.remove('active');
        }

        function doReset() {
            if (isResetting) return;
            isResetting = true;
            resetConfirmBtn.disabled = true;
            resetConfirmBtn.textContent = '<?php _e('重置中...', PUOCK); ?>';

            fetch(window.puockSettingMetaInfo.reset_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(function(response) { return response.json(); })
            .then(function(result) {
                if (result.success) {
                    alert('<?php _e('设置已重置为默认值，页面即将刷新', PUOCK); ?>');
                    window.location.reload();
                } else {
                    alert('<?php _e('重置失败：', PUOCK); ?>' + (result.data || '<?php _e('未知错误', PUOCK); ?>'));
                    isResetting = false;
                    resetConfirmBtn.disabled = false;
                    resetConfirmBtn.textContent = '<?php _e('确认重置', PUOCK); ?>';
                }
            })
            .catch(function(error) {
                alert('<?php _e('重置失败：', PUOCK); ?>' + error.message);
                isResetting = false;
                resetConfirmBtn.disabled = false;
                resetConfirmBtn.textContent = '<?php _e('确认重置', PUOCK); ?>';
            });
        }

        // 点击遮罩层关闭
        resetModalOverlay.addEventListener('click', function(e) {
            if (e.target === resetModalOverlay && !isResetting) {
                hideResetModal();
            }
        });

        resetCancelBtn.addEventListener('click', function() {
            if (!isResetting) hideResetModal();
        });

        resetConfirmBtn.addEventListener('click', doReset);

        // ESC键关闭
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && resetModalOverlay.classList.contains('active') && !isResetting) {
                hideResetModal();
            }
        });

        // 暴露给全局使用
        window.puockShowResetModal = showResetModal;
        window.pkSettingSearchKeyword = window.pkSettingSearchKeyword || '';

        var settingsSearchTimer = null;
        var settingsSearchApplying = false;
        var settingsSearchLastSignature = '';

        function normalizeSearchText(text) {
            return (text || '').toLowerCase().replace(/\s+/g, ' ').trim();
        }

        function setElementDisplay(element, shouldShow) {
            var nextDisplay = shouldShow ? '' : 'none';
            if (element.style.display !== nextDisplay) {
                element.style.display = nextDisplay;
            }
        }

        function getSearchHost() {
            return document.querySelector('#pk-options-box .pk-setting-main-box .n-layout-sider .n-scrollbar-content')
                || document.querySelector('#pk-options-box .pk-setting-main-box .n-layout-sider')
                || document.querySelector('#pk-options-box .pk-setting-menus')?.parentElement
                || null;
        }

        function getSearchMenuItems() {
            return Array.prototype.slice.call(document.querySelectorAll('#pk-options-box .pk-setting-menus .n-menu-item'));
        }

        function getSearchFieldItems() {
            return Array.prototype.slice.call(document.querySelectorAll('#pk-options-box .type-form .panel-view-box, #pk-options-box .type-form .form-item, #pk-options-box .type-form .dynamic-form-item'));
        }

        function ensureSearchBox() {
            var host = getSearchHost();
            if (!host) {
                return null;
            }

            var existing = document.getElementById('pk-setting-search-wrap');
            if (existing && existing.parentElement !== host) {
                existing.remove();
                existing = null;
            }

            if (!existing) {
                var wrap = document.createElement('div');
                wrap.id = 'pk-setting-search-wrap';
                wrap.className = 'pk-setting-search-wrap';

                var input = document.createElement('input');
                input.id = 'pk-setting-search-input';
                input.className = 'pk-setting-search';
                input.type = 'search';
                input.placeholder = '<?php echo esc_js(__('搜索菜单、设置标题、说明', PUOCK)); ?>';

                var tip = document.createElement('div');
                tip.id = 'pk-setting-search-tip';
                tip.className = 'pk-setting-search-tip';
                tip.textContent = '<?php echo esc_js(__('输入关键字后，将同时筛选左侧菜单与当前设置项', PUOCK)); ?>';

                wrap.appendChild(input);
                wrap.appendChild(tip);

                if (host.firstChild) {
                    host.insertBefore(wrap, host.firstChild);
                } else {
                    host.appendChild(wrap);
                }

                input.addEventListener('input', function() {
                    window.pkSettingSearchKeyword = input.value || '';
                    applySettingsSearch(true);
                });
            }

            var searchInput = document.getElementById('pk-setting-search-input');
            if (searchInput && typeof window.pkSettingSearchKeyword === 'string' && searchInput.value !== window.pkSettingSearchKeyword) {
                searchInput.value = window.pkSettingSearchKeyword;
            }

            return document.getElementById('pk-setting-search-wrap');
        }

        function buildSettingsSearchSignature(keyword, menuItems, fieldItems) {
            return [
                keyword,
                menuItems.length,
                fieldItems.length,
                !!document.getElementById('pk-setting-search-wrap')
            ].join('|');
        }

        function applySettingsSearch(force) {
            if (settingsSearchApplying) {
                return;
            }

            ensureSearchBox();

            var input = document.getElementById('pk-setting-search-input');
            var tip = document.getElementById('pk-setting-search-tip');
            if (!input || !tip) {
                return;
            }

            var keyword = normalizeSearchText(input.value);
            var menuItems = getSearchMenuItems();
            var fieldItems = getSearchFieldItems();
            var signature = buildSettingsSearchSignature(keyword, menuItems, fieldItems);
            var visibleMenus = 0;
            var visibleFields = 0;
            var selectedMenu;

            if (!force && !keyword && signature === settingsSearchLastSignature) {
                return;
            }

            settingsSearchApplying = true;

            try {
                menuItems.forEach(function(item) {
                    var matched = !keyword || normalizeSearchText(item.textContent).indexOf(keyword) !== -1;
                    setElementDisplay(item, matched);
                    if (matched) {
                        visibleMenus++;
                    }
                });

                fieldItems.forEach(function(item) {
                    var matched = !keyword || normalizeSearchText(item.textContent).indexOf(keyword) !== -1;
                    setElementDisplay(item, matched);
                    if (matched) {
                        visibleFields++;
                    }
                });

                if (keyword) {
                    selectedMenu = document.querySelector('#pk-options-box .pk-setting-menus .n-menu-item.n-menu-item--selected');
                }

                if (keyword) {
                    if (visibleMenus > 0 && visibleFields === 0 && selectedMenu && selectedMenu.style.display === 'none') {
                        var nextMenu = menuItems.find(function(item) {
                            return item.style.display !== 'none';
                        });
                        if (nextMenu) {
                            nextMenu.click();
                            window.setTimeout(function() {
                                applySettingsSearch(true);
                            }, 80);
                            settingsSearchLastSignature = signature;
                            return;
                        }
                    }

                    tip.textContent = visibleMenus === 0 && visibleFields === 0
                        ? '<?php echo esc_js(__('没有匹配的菜单或设置项，请换个关键词试试', PUOCK)); ?>'
                        : '<?php echo esc_js(__('已匹配', PUOCK)); ?> ' + visibleMenus + ' <?php echo esc_js(__('个菜单 /', PUOCK)); ?> ' + visibleFields + ' <?php echo esc_js(__('项设置', PUOCK)); ?>';
                } else {
                    tip.textContent = '<?php echo esc_js(__('输入关键字后，将同时筛选左侧菜单与当前设置项', PUOCK)); ?>';
                }

                settingsSearchLastSignature = signature;
            } finally {
                settingsSearchApplying = false;
            }
        }

        function scheduleSettingsUiRefresh(delay, forceSearch) {
            window.clearTimeout(settingsSearchTimer);

            settingsSearchTimer = window.setTimeout(function() {
                injectResetButton();
                ensureSearchBox();

                if (forceSearch || normalizeSearchText(window.pkSettingSearchKeyword)) {
                    applySettingsSearch(!!forceSearch);
                }
            }, typeof delay === 'number' ? delay : 120);
        }

        // 在Vue应用加载完成后注入重置按钮
        function injectResetButton() {
            var headerRight = document.querySelector('.pk-setting-header-right');
            if (!headerRight) {
                // 尝试其他可能的选择器
                headerRight = document.querySelector('[class*="header"] [class*="right"]');
            }
            if (!headerRight) {
                // 如果找不到header-right，尝试在保存按钮附近添加
                var saveBtn = document.querySelector('button[class*="primary"]');
                if (saveBtn && saveBtn.parentElement) {
                    headerRight = saveBtn.parentElement;
                }
            }

            if (headerRight) {
                // 检查是否已经添加过
                if (document.getElementById('pk-reset-btn')) return;

                var resetBtn = document.createElement('button');
                resetBtn.id = 'pk-reset-btn';
                resetBtn.type = 'button';
                resetBtn.className = 'n-button n-button--default-type n-button--medium-type';
                resetBtn.style.cssText = 'margin-right: 12px; background: #f5f5f5; border: 1px solid #e0e0e0; color: #666; padding: 0 16px; height: 34px; border-radius: 4px; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;';
                resetBtn.innerHTML = '<span class="dashicons dashicons-image-rotate" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span><?php _e('重置设置', PUOCK); ?>';
                resetBtn.addEventListener('click', showResetModal);
                resetBtn.addEventListener('mouseenter', function() {
                    this.style.background = '#e8e8e8';
                });
                resetBtn.addEventListener('mouseleave', function() {
                    this.style.background = '#f5f5f5';
                });

                // 插入到第一个子元素之前
                if (headerRight.firstChild) {
                    headerRight.insertBefore(resetBtn, headerRight.firstChild);
                } else {
                    headerRight.appendChild(resetBtn);
                }
            }
        }

        // 使用MutationObserver监听DOM变化，等Vue加载完成
        var observer = new MutationObserver(function() {
            scheduleSettingsUiRefresh();
        });

        observer.observe(document.getElementById('app'), {
            childList: true,
            subtree: true
        });

        // 备用：延迟尝试
        setTimeout(function() { scheduleSettingsUiRefresh(0, true); }, 600);
        setTimeout(function() { scheduleSettingsUiRefresh(0, true); }, 1600);
        setTimeout(function() { scheduleSettingsUiRefresh(0, true); }, 3200);
    })();
</script>
<script type="text/javascript" crossorigin src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/language/<?php echo get_user_locale() ?>.js?ver=<?php echo PUOCK_CUR_VER_STR ?>"></script>
<?php
if (file_exists(dirname(__FILE__) . '/template-script-dev.php')) {
    include_once dirname(__FILE__) . '/template-script-dev.php';
} else { ?>

    <script type="module" crossorigin
            src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.js?ver=<?php echo PUOCK_CUR_VER_STR ?>"></script>

    <?php
}
?>
