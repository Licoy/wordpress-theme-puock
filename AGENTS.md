# Puock WordPress Theme - 开发指南

## 项目概述

Puock 是一款基于 WordPress 开发的高颜值自适应主题，支持白天与黑夜模式、无刷新加载、多种布局模式等特性。

**项目仓库**: https://github.com/Licoy/wordpress-theme-puock

## 技术栈

### 后端
- **PHP**: 7.4+
- **WordPress**: 6.0+
- **Composer**: 依赖管理

### 前端
- **JavaScript**: ES6+
- **CSS预处理器**: Less
- **后台框架**: Vue3 + NaiveUI
- **图标库**: Font Awesome, Strawberry Icon

### 构建工具
- **Gulp**: 任务运行器
- **Babel**: JavaScript转译器
- **包管理器**: npm/pnpm

## 项目结构

```
puock/
├── inc/                   # 核心功能目录
│   ├── ajax/             # Ajax处理
│   │   ├── ai.php        # AI相关接口
│   │   ├── page-poster.php  # 海报生成
│   │   └── ...
│   ├── classes/          # 类文件
│   ├── ext/              # 扩展功能
│   │   └── moments.php   # 朋友圈功能
│   ├── fun/              # 功能函数
│   │   ├── ajax.php      # Ajax公共函数
│   │   ├── cache.php     # 缓存函数
│   │   ├── comment.php   # 评论功能
│   │   ├── short-code.php # 短代码
│   │   └── ...
│   ├── oauth/            # OAuth登录
│   │   └── callback/
│   ├── page/             # 页面功能
│   │   └── user-center.php
│   ├── setting/          # 后台设置
│   │   ├── options/      # 选项配置
│   │   └── template.php  # 模板文件
│   ├── init.php          # 初始化
│   ├── seo.php           # SEO功能
│   └── metas.php         # 元数据
├── assets/               # 前端资源
│   ├── dist/             # 编译后的文件(不修改)
│   ├── fonts/            # 字体文件
│   ├── img/              # 图片资源
│   ├── js/               # JavaScript源码
│   │   ├── admin.js      # 后台JS
│   │   ├── puock.js      # 前台核心JS
│   │   └── page-ai.js    # AI页面JS
│   ├── libs/             # 第三方库
│   └── style/            # Less源码
├── templates/            # 主题模板
│   ├── module-*.php      # 模块组件
│   ├── box-*.php         # 盒子组件
│   └── content-none.php
├── pages/                # 自定义页面模板
│   ├── template-*.php    # 各类页面模板
├── gutenberg/            # Gutenberg区块
│   └── components/
├── languages/            # 语言包
├── vendor/               # Composer依赖(不修改)
├── update-checker/       # 更新检查器(不修改)
├── ad/                   # 广告位模板
├── libs/                 # 前端库源码
├── cache/                # 缓存目录
├── .github/              # GitHub配置
│   └── workflows/        # GitHub Actions
├── functions.php         # 主题主文件
├── style.css             # 主题样式声明
├── gulpfile.js           # Gulp构建配置
├── .babelrc              # Babel配置
├── package.json          # npm包配置
├── composer.json         # Composer配置
└── README.md             # 项目说明
```

## 开发环境搭建

### 环境要求
- Node.js 14+
- PHP 7.4+
- WordPress 6.0+
- Composer (可选)

### 安装步骤

1. **克隆项目**
```bash
git clone https://github.com/Licoy/wordpress-theme-puock.git
cd wordpress-theme-puock/wp-content/themes/puock
```

2. **安装前端依赖**
```bash
npm install
# 或使用 pnpm
pnpm install
```

3. **安装PHP依赖** (可选)
```bash
composer install
```

## 开发命令

### 构建命令
```bash
# 构建生产版本
npm run build

# 开发模式 (监听文件变化)
npm run dev
```

构建过程会执行以下任务:
- 编译 Less 文件到 `assets/dist/style/`
- 转译并压缩 JS 文件到 `assets/dist/js/`
- 合并并压缩第三方库到 `assets/dist/`

### 修改源文件时的注意事项
- **修改 Less**: 编辑 `assets/style/*.less`，构建后生成 `.min.css`
- **修改 JS**: 编辑 `assets/js/*.js`，构建后生成 `.min.js`
- **库文件**: 修改 `assets/libs/basic/` 中的文件
- **不要直接修改**: `assets/dist/` 中的文件会被构建覆盖

## 核心功能模块

### 1. 主题设置系统
- **位置**: `inc/setting/`
- **配置文件**: `inc/setting/options/*.php`
- **模板**: `inc/setting/template.php`
- **后台框架**: Vue3 + NaiveUI

### 2. Ajax 功能
- **入口**: `inc/ajax/index.php`
- **功能**:
  - AI 对话: `inc/ajax/ai.php`
  - 海报生成: `inc/ajax/page-poster.php`
  - OAuth登录: `inc/ajax/page-oauth-login.php`
  - 前台登录: `inc/ajax/page-front-login.php`

### 3. 评论系统
- **功能函数**: `inc/fun/comment.php`
- **Ajax处理**: `inc/fun/comment-ajax.php`
- **邮件通知**: `inc/fun/comment-notify.php`

### 4. OAuth 登录
- **主文件**: `inc/oauth/oauth.php`
- **回调**: `inc/oauth/callback/*.php`
- **支持的平台**: QQ, Github, Gitee, 微博

### 5. 短代码系统
- **位置**: `inc/fun/short-code.php`
- **支持功能**:
  - 下载按钮
  - 评论后可见
  - 登录后可见
  - 提示框
  - Github卡片
  - 隐藏内容

### 6. SEO 优化
- **主文件**: `inc/seo.php`
- **分类SEO**: `inc/category-seo.php`
- **功能**: 标题优化、描述优化、关键词优化

### 7. 用户中心
- **类文件**: `inc/classes/PuockUserCenter.php`
- **页面**: `inc/page/user-center.php`

### 8. 扩展功能
- **朋友圈**: `inc/ext/moments.php`
- **AI功能**: `inc/ajax/ai.php`

## 主题模板

### 主要模板文件
- `header.php` - 头部模板
- `footer.php` - 底部模板
- `index.php` - 首页
- `single.php` - 文章页
- `page.php` - 页面
- `category.php` - 分类页
- `tag.php` - 标签页
- `search.php` - 搜索页
- `author.php` - 作者页
- `404.php` - 404页面
- `sidebar.php` - 侧边栏

### 自定义页面模板
- `pages/template-chatgpt.php` - ChatGPT页面
- `pages/template-moments.php` - 朋友圈页面
- `pages/template-links.php` - 友情链接
- `pages/template-archives.php` - 文章归档
- `pages/template-reads.php` - 读者墙
- `pages/template-tags.php` - 标签页
- `pages/template-book.php` - 书籍推荐
- `pages/template-random.php` - 随机文章

### 模块组件
- `templates/module-post.php` - 文章模块
- `templates/module-posts.php` - 文章列表
- `templates/module-cms.php` - CMS模块
- `templates/module-banners.php` - 轮播图
- `templates/module-menus.php` - 导航菜单
- `templates/module-links.php` - 友情链接
- `templates/module-andb.php` - 广告位

## 代码规范

### PHP 代码规范
- 遵循 WordPress 编码规范
- 使用函数前缀 `pk_` (Puock)
- 使用常量 `PUOCK` 作为主题名称
- 所有文本使用国际化: `__('文本', PUOCK)`

### JavaScript 代码规范
- 使用 ES6+ 语法
- 使用 Babel 转译确保兼容性
- 代码风格保持一致

### Less 代码规范
- 使用变量定义颜色、字体等
- 保持嵌套层级合理
- 注释清晰

## 修改主题样式

### 1. 修改主色调
- 编辑 `assets/style/common.less`
- 查找并修改主题色变量
- 运行 `npm run build` 编译

### 2. 修改布局
- 编辑对应的 Less 文件
- 支持博客、CMS、企业三种布局模式

### 3. 自定义样式
- 推荐在 `assets/style/custom.less` (需创建) 中添加
- 在 `assets/style/common.less` 中引入

## 添加新功能

### 1. 添加新的 Ajax 接口
1. 在 `inc/ajax/` 创建新文件
2. 在 `inc/ajax/index.php` 中注册
3. 在前端 JavaScript 中调用

### 2. 添加新的短代码
1. 在 `inc/fun/short-code.php` 中添加短代码处理函数
2. 使用 `add_shortcode()` 注册

### 3. 添加新的页面模板
1. 在 `pages/` 创建 `template-*.php`
2. 添加模板注释:
```php
/*
Template Name: 模板名称
*/
```

### 4. 添加新的设置选项
1. 在 `inc/setting/options/` 创建新的选项类
2. 在 `inc/setting/index.php` 中注册
3. 使用 `pk_get_option()` 获取选项值

## 常用函数

### 主题选项
```php
pk_get_option($key)           // 获取选项
pk_is_checked($key)           // 检查是否启用
```

### 缓存函数
```php
pk_cache_get($key)            // 获取缓存
pk_cache_set($key, $value)    // 设置缓存
```

### Ajax 响应
```php
pk_ajax_resp($data, $msg, $code)      // 成功响应
pk_ajax_resp_error($msg, $data)       // 错误响应
```

### 辅助函数
```php
get_post_images($post)       // 获取文章图片
get_post_category_link()     // 获取分类链接
get_post_tags()              // 获取文章标签
pk_breadcrumbs()             // 面包屑导航
pk_get_seo_title()           // SEO标题
```

## 测试

### 本地开发测试
1. 配置 WordPress 本地环境
2. 启用主题
3. 运行 `npm run dev` 监听文件变化
4. 在浏览器中测试功能

### 功能测试清单
- [ ] 主题切换(白天/黑夜)
- [ ] 页面布局切换
- [ ] 登录/注册
- [ ] 评论功能
- [ ] Ajax 加载
- [ ] SEO 功能
- [ ] 短代码功能
- [ ] OAuth 登录

## 发布流程

### 1. 更新版本号
- 编辑 `style.css` 中的版本号
- 编辑 `package.json` 中的版本号

### 2. 构建生产版本
```bash
npm run build
```

### 3. 提交代码
```bash
git add .
git commit -m "版本更新说明"
git push
```

### 4. 创建 Release
- 在 GitHub 上创建新的 Release
- 上传主题压缩包
- 编写更新日志

## 调试技巧

### PHP 调试
- 使用 `error_log()` 输出日志
- 检查 WordPress debug 模式
- 查看浏览器控制台

### JavaScript 调试
- 使用浏览器开发者工具
- 检查控制台错误
- 使用 console.log 调试

### Less 调试
- 使用浏览器开发工具检查样式
- 使用 Less 变量方便调试

## 常见问题

### Q: 如何修改主题颜色?
A: 编辑 `assets/style/common.less` 中的颜色变量，然后运行 `npm run build`

### Q: 如何禁用某些功能?
A: 在主题设置中关闭对应选项，或在 `functions.php` 中注释相关代码

### Q: 如何添加新的图标?
A: 在 `assets/img/icons/` 添加图标文件，并在 CSS 中引用

### Q: 构建失败怎么办?
A: 检查 Node.js 版本，删除 `node_modules` 和 `package-lock.json`，重新安装依赖

### Q: 如何自定义模板?
A: 在主题根目录创建子主题，或直接修改对应模板文件

## 依赖管理

### npm 依赖
主要依赖构建工具:
- gulp
- gulp-babel
- gulp-less
- gulp-uglify
- @babel/core
- @babel/preset-env

### Composer 依赖
- yurunsoft/yurun-oauth-login - OAuth登录
- zoujingli/ip2region - IP地址库
- orhanerday/open-ai - OpenAI集成
- rahul900day/gpt-3-encoder - GPT-3编码器

## 性能优化建议

1. **启用缓存**: 使用主题内置缓存功能
2. **压缩图片**: 使用 timthumb.php 或其他图片优化工具
3. **合并文件**: 构建时已自动合并JS和CSS
4. **使用CDN**: 配置CDN加速静态资源
5. **开启Gzip**: 在服务器配置中开启Gzip压缩

## 安全建议

1. 定期更新 WordPress 和主题
2. 不要直接修改 `vendor/` 目录
3. 检查文件权限
4. 启用主题内置的安全功能
5. 定期备份数据库和文件

## 贡献指南

1. Fork 项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

## 联系方式

- **GitHub**: https://github.com/Licoy/wordpress-theme-puock
- **Issues**: https://github.com/Licoy/wordpress-theme-puock/issues
- **文档**: https://www.licoy.cn/puock-doc.html

## 许可证

GPL V3.0 - 请遵守开源协议，保留主题底部的署名
