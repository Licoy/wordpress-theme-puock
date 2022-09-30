中文 | [English](./README_EN.md)
## 介绍
![cover](./cover.png)

<div align="center">
    <h1>WordPress Theme - Puock</h1>
    <p>一款基于WordPress开发的高颜值的自适应主题，支持白天与黑夜模式。</p>
      <a href="https://github.com/Licoy/wordpress-theme-puock/releases/latest">
        <img src="https://img.shields.io/github/v/release/Licoy/wordpress-theme-puock.svg?logo=git" alt="Release-Version">
      </a>
    <a href="https://github.com/Licoy/wordpress-theme-puock">
        <img src="https://img.shields.io/badge/WordPress-V5.0+-0099CC.svg?logo=wordpress" alt="WordPress-Version">
      </a>
    <a href="https://github.com/Licoy/wordpress-theme-puock">
        <img src="https://img.shields.io/badge/PHP-V7.0+-666699.svg?logo=php" alt="PHP-Version">
      </a>
     <a href="https://gitee.com/licoy/wordpress-theme-puock">
        <img src="https://img.shields.io/badge/Gitee-码云-CC3333.svg?logo=gitee" alt="Gitee">
      </a>
    <a href="https://github.com/Licoy">
        <img src="https://img.shields.io/badge/author-Licoy-ff69b4.svg?logo=github" alt="Author">
      </a>
    <br>
    <a href="https://licoy.cn/go/zs/">
        <img src="https://img.shields.io/badge/赞赏-开发不易-CC3333.svg?logo=Buy-Me-A-Coffee" alt="赞赏支持">
      </a>
    <a href="https://licoy.cn/go/zs/">
        <img src="https://img.shields.io/badge/捐赠-微信-68b600.svg?logo=WeChat" alt="微信捐赠">
      </a>
    <a href="https://licoy.cn/go/zs/">
        <img src="https://img.shields.io/badge/捐赠-支付宝-00a2ef.svg?logo=AliPay" alt="支付宝捐赠">
      </a>
    <br><br>
    <img src='https://repobeats.axiom.co/api/embed/5f966833712409c00d4269bf2800b2d4762e09ea.svg'></img>
    <br><br>
    <a href="https://www.producthunt.com/posts/puock-wordpress-theme?utm_source=badge-featured&utm_medium=badge&utm_souce=badge-puock-wordpress-theme" target="_blank"><img src="https://api.producthunt.com/widgets/embed-image/v1/featured.svg?post_id=327798&theme=light" alt="Puock WordPress Theme - A high-value adaptive theme based on WordPress | Product Hunt" style="width: 250px; height: 54px;" width="250" height="54" /></a>
    
</div>

## 配置截图
![theme-options.png](./.screenshot/options.png)

## 安装
请到 [发行版本](https://github.com/Licoy/wordpress-theme-puock/releases) 中进行下载最新版本，然后到WordPress管理后台中的「外观」-「主题」中点击「添加」，选择Puock的主题包进行上传安装并启用即可。

**提示：为了防止主题不兼容，请在安装主题前进行数据备份，防止数据字段重复覆盖等情况发生。**

**重要：请不要直接克隆或直接下载仓库进行使用，请到发行版中进行下载使用**
### PHP扩展
- `fileinfo`: 用于获取文件的MIME类型
- `exif`: 用于获取图片的EXIF信息
## 版本迭代
- 1.5及以下版本升级至1.6+配置不兼容处理方法：

因为在1.6版本中将配置字段更改为了`puock_options`，所以会导致配置读取不到，用户可以重新进行配置或恢复配置，恢复配置SQL（**执行前请先备份数据库，原配置字段名为`optionsframework`，~~若其他主题或插件使用了同名字段为配置名则会覆盖~~，原则上若使用旧版本不会存在其他插件或主题同名字段，因为`option_name`字段为主键，是不允许重复的！**）：
```sql
UPDATE `wp_options` SET `option_name` = 'puock_options' WHERE `option_name` = 'optionsframework'
```
  
## 主题特性
- [x] 支持白天与暗黑模式
- [x] 全局无刷新加载
- [x] 支持博客与CMS布局
- [x] 内置WP优化策略
- [x] 一键全站变灰
- [x] 网页压缩成一行
- [x] 后台防恶意登录
- [x] 内置出色的SEO功能
- [x] 评论Ajax加载
- [x] 文章点赞、打赏
- [x] 支持Twemoji集成
- [x] 支持QQ登录
- [x] 丰富的广告位
- [x] 丰富的小工具
- [x] 自动百度链接提交
- [x] 众多页面模板
- [x] 支持评论可见
- [x] 支持密码可见
- [x] 支持Dplayer播放器
- [x] 仿MacOS的代码风格及拷贝代码 `v2.5.6`
- [x] Vue3+NaiveUI集成的高颜值后台配置 `v2.6.0`
- [x] 文章多级目录生成 `v2.6.2`
- [x] 侧边栏粘性滚动 `v2.6.2`
- [x] 支持Github/Gitee/微博登录 `v2.6.2`
- [x] WP缓存支持 `v2.6.2`
- [x] 自定义主色调 `v2.6.3`
- [x] 更多特性更新请查阅版本发布说明：[releases](https://github.com/Licoy/wordpress-theme-puock/releases)
- [x] 更多功能等你的[提议](https://github.com/Licoy/wordpress-theme-puock/issues)
## 文档
- 主题使用文档：[立即使用](https://www.licoy.cn/puock-doc.html)
- 建议或BUG反馈：[立即进入](https://github.com/Licoy/wordpress-theme-puock/issues)
- QQ交流群：[点我加入](https://licoy.cn/go/puock-update.php?r=qq_qun) （此群皆为大家提供交流使用的地方，有BUG请直接提交ISSUE）
- **若您有任何建议或BUG发现，并且您也有解决或实现的思路，欢迎直接提交PR！**
## 支持
- 打赏主题以支持：[点我进入](https://licoy.cn/go/zs/)
## 趋势
[![Stargazers over time](https://starchart.cc/Licoy/wordpress-theme-puock.svg)](https://starchart.cc/Licoy/wordpress-theme-puock)
## 鸣谢
[Jetbrains](https://www.jetbrains.com/?from=wordpress-theme-puock)
## 开源协议
- [GPL V3.0](./LICENSE)
- 请遵守开源协议，保留主题底部的署名
