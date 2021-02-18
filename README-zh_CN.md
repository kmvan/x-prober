[![X Prober preview](screenshots/preview.webp)](screenshots/preview.webp)

# 😎 X 探针、刘海探针

> 这是一款 PHP 环境探针程序，它不仅可以十分直观地为您显示服务器的信息，而且最重要的是：它跟 📱 **iPhone X/XS/XS Max/XR** 一样有丑陋的刘海！

[![Backers on Open Collective](https://opencollective.com/x-prober/backers/badge.svg)](#backers)
[![Sponsors on Open Collective](https://opencollective.com/x-prober/sponsors/badge.svg)](#sponsors)

## 开源协议

- GPL-3.0

## 线上演示

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (同上)

## 下载与使用

- 点击 [INN 下载节点](https://api.inn-studio.com/download?id=xprober) 或 [GitHub 节点](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) 下载探針文件。
- 您将会得到一个文件，命名为 `x.php` 并上传到您的服务器上。
- 通过浏览器访问 `x.php` 即可。

## 环境需求

- 编译环境：PHP 7.4+
- 运行环境：PHP 5.3+
- 浏览器兼容：Chrome、Firefox、Edge、Android
- 系统兼容：Linux、Windows（基础功能）

## 扩展

- 正在编写中……

## 开发指引

- Fork 项目。
- Fetch 您的仓库。
- 安装 npm 模块：`$ npm install`。
- 监听脚本：`$ npm run dev`。
- 安装 composer：`$ composer install && composer dumpautoload -o`。
- 生成多国语言：`$ npm run lang` 来重建 `./languages/lang.pot` 语言模板。
- 编译 PHP：`$ npm run dev:php`。
- 访问：`http://localhost:3000` 或 `http://path/to/.tmp/index.php`。

## 编译生产

- 编译 JS：`$ npm run build`.
- 编译 PHP：`$ npm run build:php`.
- 访问：`http://localhost:3000` 或 `http://path/to/dist/prober.php`。

## 参与翻译更多语言

- Fork 项目。
- 使用 [Poedit](https://poedit.net/) 通过 `./languages/lang.pot` 语言模板来创建和翻译您的语言。
- 保存翻译语言文件 _（例如：`en_US.po`)_ 到 `./languages` 目录里面。
- Push 您的修改。
- 进行 Pull Request，十分感谢。😘

## 给予参与贡献者的备注

- 您的 PHP 代码需要兼容 PHP5.3+ 环境。

## 贡献者名单

<a href="https://github.com/kmvan/x-prober/graphs/contributors"><img src="https://opencollective.com/x-prober/contributors.svg?width=890&button=false" /></a>

## 支持者

感谢所有支持者 🙏 [[成为支持者](https://opencollective.com/x-prober#backer)]

<a href="https://opencollective.com/x-prober#backers" target="_blank"><img src="https://opencollective.com/x-prober/backers.svg?width=890"></a>

## 赞助

成为赞助商支持这个项目。 您的徽标将显示在此处，其中包含指向您网站的链接。[[成为赞助商](https://opencollective.com/x-prober#sponsor)]

- 感谢 [VPSPlayer.com](https://vpsplayer.com/aff.php?aff=50) - 2021-01-16 - 199 人民币
- 感谢 1529\*\*\*576 - 2019-04-04 – 150 人民币
- 感谢 [Vultr.com](https://www.vultr.com/?ref=7256513) - 2019-03-13 - 50 美元

<a href="https://opencollective.com/x-prober/sponsor/0/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/0/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/1/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/1/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/2/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/2/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/3/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/3/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/4/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/4/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/5/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/5/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/6/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/6/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/7/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/7/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/8/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/8/avatar.svg"></a>
<a href="https://opencollective.com/x-prober/sponsor/9/website" target="_blank"><img src="https://opencollective.com/x-prober/sponsor/9/avatar.svg"></a>

## 计划的功能

- [x] 添加温度探测。
- [x] 通过使用 Poedit 来添加更多语言。
- [x] 更详细的跑分结果。
- [ ] 添加邮件发送测试。
- [ ] 添加网速测试。
- [x] 添加更多服务器跑分结果。
- [x] 添加 PING 功能。
