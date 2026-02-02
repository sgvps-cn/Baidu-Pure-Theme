# Baidu Pure Theme (星耀云优化版)

[![Version](https://img.shields.io/badge/version-1.2.0-blue.svg)](https://github.com/sgvps-cn/Baidu-Pure-Theme)
[![Author](https://img.shields.io/badge/author-%E6%98%9F%E8%80%80%E4%BA%91-orange.svg)](https://github.com/sgvps-cn/Baidu-Pure-Theme)
[![WordPress](https://img.shields.io/badge/WordPress-Theme-21759b.svg)](https://wordpress.org/)

**Baidu Pure Theme** 是一款专为百度 SEO 优化的轻量级 WordPress 主题。它摒弃了繁重的框架依赖，采用现代化的毛玻璃拟态（Glassmorphism）设计风格，提供极致的加载速度和优秀的用户体验。

## ✨ 核心特性 (Features)

### 🚀 极致性能
- **零依赖**: 这是一个真正的 "Pure" 主题。移除 jQuery、Bootstrap 及 FontAwesome，所有交互均由原生 JavaScript 实现。
- **高速加载**: 核心 CSS/JS 体积极小，秒开体验。

### 🔍 深度 SEO 引擎
主题内置了强大的 SEO 功能模块，无需安装额外插件：
- **百度主动推送**: 发布文章时自动通过 API 推送至百度站长平台。
- **自动内链系统**: 支持自定义关键词与链接规则，文章发布时自动构建内链网络。
- **智能 Meta 标签**: 自动生成标题 (Title)、关键词 (Keywords)、描述 (Description) 和 Canonical 标签。
- **结构化数据**: 内置 Schema.org JSON-LD，利于搜索引擎理解文章结构。
- **图片 SEO**: 自动补全图片 Alt/Title 属性。

### 🎨 现代 UI 设计
- **毛玻璃拟态 (Glassmorphism)**: 独特的半透明磨砂质感，界面清爽高级。
- **原生深色模式 (Dark Mode)**: 跟随系统或手动切换，完美的夜间阅读体验。
- **响应式布局**: 完美适配手机、平板及桌面端显示。
- **代码高亮**: 优化过的 `<pre>` 代码块样式，适合技术博客。

## 🛠️ 安装与配置 (Installation)

### 1. 安装主题
1. 下载源码包或 `git clone` 到您的本地。
2. 将 `baidu-pure-theme` 文件夹上传至 WordPress 的 `/wp-content/themes/` 目录。
3. 在 WordPress 后台 **外观 -> 主题** 中激活 "Baidu Pure Theme"。

### 2. 配置 SEO 选项
激活主题后，后台左侧会出现 **"百度 SEO"** 菜单：
- **站点域名**: 填写您的完整域名（如 `https://www.example.com`）。
- **百度准入密钥 (Token)**: 填写百度站长平台提供的 API Token。
- **自动内链规则**: 按 `关键词|链接` 格式配置（每行一条）。

## 📂 文件结构说明

```text
baidu-pure-theme/
├── style.css           # 核心样式表 (含 Glassmorphism 定义)
├── functions.php       # 主题核心逻辑 & SEO 模块挂载
├── header.php          # 页头 (SEO Meta, 导航栏)
├── footer.php          # 页脚
├── index.php           # 首页/默认模板
├── single.php          # 文章详情页 (修复了标签重复与评论显示)
├── page.php            # 独立页面模板
├── archive.php         # 分类/归档页模板 (包含标题头)
├── search.php          # 搜索结果页模板
├── comments.php        # 评论区域模板
├── js/
│   └── main.js         # 原生交互脚本 (深色模式, 移动端菜单)
└── inc/                # 功能模块目录
    ├── daily-push.php  # 每日手动推送工具
    ├── image-seo.php   # 图片 SEO 优化
    ├── sitemap.php     # 简易站点地图生成
    └── spider.php      # 蜘蛛访问日志 (可选)
```

## 📝 更新日志 (Changelog)

### v1.2.0 (Current)
- **[新增]** 不完善的模板补充：`archive.php` (归档) 与 `search.php` (搜索)。
- **[优化]** 修复子菜单 (Submenu) 在部分分辨率下对应的 CSS 显示异常。
- **[优化]** 修复 `single.php` 中标签重复显示的问题。
- **[优化]** 更新作者信息为 "星耀云"。
- **[UI]** 调整导航栏布局，防止文字换行。

## 🤝 贡献与反馈
欢迎提交 Issue 或 Pull Request。
项目地址: [https://github.com/sgvps-cn/Baidu-Pure-Theme](https://github.com/sgvps-cn/Baidu-Pure-Theme)

---
© 2024-2026 星耀云. All Rights Reserved.
