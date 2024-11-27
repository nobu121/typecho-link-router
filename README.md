# LinkRouter 外链转内链插件

## 简介

LinkRouter 是一个适用于 Typecho 的外链转内链插件。它可以自动将文章中的外部链接转换为内部链接，通过中转页面跳转，提高网站安全性并保护访客隐私。

## 功能特点

- 自动转换文章中的外部链接为内部链接
- 支持自定义中转页面模板
- 可选择是否在新标签页打开链接
- 简洁的链接格式：`your-site.com/action/go?url=`

## 安装方法

1. 下载插件
2. 将插件文件夹重命名为 `LinkRouter`
3. 上传至网站的 `/usr/plugins/` 目录
4. 登录后台启用插件

## 配置说明

插件提供以下配置选项：

1. **中转页面模板**

   - 可自定义中转页面的 HTML 模板
   - 使用 `{url}` 作为外链地址的占位符
   - 默认模板包含基础的跳转提示

2. **新标签页打开**

   - 可选择是否在新标签页中打开转换后的链接
   - 启用后会自动添加 `target="_blank"` 属性

## 使用示例

安装并启用插件后，文章中的外链将自动转换
