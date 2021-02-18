[![X Prober preview](screenshots/preview.webp)](screenshots/preview.webp)

# 😎 X 探針、劉海探針

> 這是一款 PHP 環境探針程式，它不僅可以十分直觀地為您顯示伺服器的信息，而且最重要的是：它跟 📱 **iPhone X/XS/XS Max/XR/11/11 Pro/11 Pro Max** 一樣有醜陋的劉海！

[![Backers on Open Collective](https://opencollective.com/x-prober/backers/badge.svg)](#backers)
[![Sponsors on Open Collective](https://opencollective.com/x-prober/sponsors/badge.svg)](#sponsors)

## 開源協議

- GPL-3.0

## 線上演示

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (同上)

## 下載與使用

- 點擊 [INN 下載節點](https://api.inn-studio.com/download?id=xprober) 或 [GitHub 節點](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) 下載探針文件。
- 您將會得到一個文件，命名為 `x.php` 並上傳到您的伺服器上。
- 通過瀏覽器訪問 `x.php` 即可。

## 環境需求

- 編譯環境：PHP 7.4+
- 運作環境：PHP 5.3+
- 瀏覽器兼容：Chrome、Firefox、Edge、Android
- 系統兼容：Linux、Windows（基礎功能）

## 擴展

- 正在編寫中……

## 開發指引

- Fork 項目。
- Fetch 您的倉庫。
- 安裝 npm 模塊：`$ npm install`。
- 監視腳本：`$ npm run dev`。
- 安裝 composer：`$ composer install && composer dumpautoload -o`。
- 生成多國語言：`$ npm run lang` 來重建 `./languages/lang.pot` 語言模板。
- 編譯 PHP：`$ npm run dev:php`。
- 訪問：`http://localhost:3000` 或 `http://path/to/.tmp/index.php`。

## 編譯生產

- 編譯 JS：`$ npm run build`.
- 編譯 PHP：`$ npm run build:php`.
- 訪問：`http://localhost:3000` 或 `http://path/to/dist/prober.php`。
-

## 參與翻譯更多語言

- Fork 項目。
- 使用 [Poedit](https://poedit.net/) 通過 `./languages/lang.pot` 語言模板來創建和翻譯您的語言。
- 保存翻譯語言文件 （例如：`en_US.po`) 到 `./languages` 目錄裏面。
- Push 您的修改。
- 進行 Pull Request，十分感謝。😘

## 給予參與貢獻者的備註

- 您的 PHP 代碼需要兼容 PHP5.3+ 環境。

## 貢獻者名單

<a href="https://github.com/kmvan/x-prober/graphs/contributors"><img src="https://opencollective.com/x-prober/contributors.svg?width=890&button=false" /></a>

## 支援者

感謝所有支援者 🙏 [[成為支援者](https://opencollective.com/x-prober#backer)]

<a href="https://opencollective.com/x-prober#backers" target="_blank"><img src="https://opencollective.com/x-prober/backers.svg?width=890"></a>

## 贊助

成為贊助商支援這個項目。 您的徽標將顯示在此處，其中包含指向您網站的鏈接。[[成為贊助商](https://opencollective.com/x-prober#sponsor)]

- 感謝 [VPSPlayer.com](https://vpsplayer.com/aff.php?aff=50) - 2021-01-16 - 199 人民幣
- 感謝 1529\*\*\*576 - 2019-04-04 – 150 人民幣
- 感謝 [Vultr.com](https://www.vultr.com/?ref=7256513) - 2019-03-13 - 50 美元

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

## 計劃的功能

- [x] 添加溫度探測。
- [x] 通過使用 Poedit 來添加更多語言。
- [x] 更詳細的跑分結果。
- [ ] 添加郵件發送測試。
- [ ] 添加網路測試。
- [x] 添加更多伺服器跑分結果。
- [x] 添加 PING 功能。
