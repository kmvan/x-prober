[![X Prober 預覽](screenshots/preview.webp)](screenshots/preview.webp)

# 😎 X 探針、瀏海探針

> 這是一款 PHP 環境探針程式，它不僅能直觀顯示伺服器資訊，最重要的是：它和 📱 **iPhone X/XS/XS Max/XR** 一樣有醜陋的瀏海！

[![Open Collective 支持者](https://opencollective.com/x-prober/backers/badge.svg)](#支持者)
[![Open Collective 贊助商](https://opencollective.com/x-prober/sponsors/badge.svg)](#贊助)

## 開源授權

- GPL-3.0

## 線上演示

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (同上)

## 下載與使用

- 點擊 [INN 下載節點](https://api.inn-studio.com/download?id=xprober) 或 [GitHub 節點](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) 下載探針檔案。
- 您將得到一個檔案，命名為 `x.php` 並上傳至您的伺服器。
- 透過瀏覽器存取 `x.php` 即可。

## 環境需求

- 編譯環境：PHP 8.4+
- 執行環境：PHP 5.4+
- 瀏覽器相容：Chrome、Firefox、Edge、Android
- 系統相容：Linux、Windows（基礎功能）

## 擴充功能

- 編寫中……

## 開發指引

- Fork 專案。
- 拉取您的儲存庫。
- 安裝 npm 模組：`$ npx pnpm i`。
- 安裝 composer：`$ composer install && composer dumpautoload -o`。
- 產生多國語言：`$ npm run lang` 重建 `./languages/lang.pot` 語言模板。
- 使用 php 監聽專案後端：`$ npx pnpm dev:php`。
- 使用 vite 監聽專案前端：`$ npx pnpm dev`。
- 存取：`http://localhost:5173/`。

## 編譯生產版本

- 編譯前端：`$ npx pnpm build`。
- 編譯檔案：`$ npx pnpm build:php` 取得單檔案 `./dist/prober.php`。
- 存取：`http://localhost:8001/prober.php` 或 `http://path/to/dist/prober.php`。

## 參與翻譯更多語言

- Fork 專案至您的儲存庫。
- 使用 [Poedit](https://poedit.net/) 透過 `./languages/lang.pot` 語言模板建立並翻譯您的語言。
- 儲存翻譯語言檔案 _（例：`en_US.po`）_ 至 `./languages` 目錄內。
- 推送您的修改。
- 提交 Pull Request，感謝您的貢獻。😘

## 給貢獻者的備註

- 您的 PHP 程式碼需相容 PHP5.4+ 環境。

## 貢獻者名單

<a href="https://github.com/kmvan/x-prober/graphs/contributors"><img src="https://opencollective.com/x-prober/contributors.svg?width=890&button=false" /></a>

## 支持者

感謝所有支持者 🙏 [[成為支持者](https://opencollective.com/x-prober#backer)]

<a href="https://opencollective.com/x-prober#backers" target="_blank"><img src="https://opencollective.com/x-prober/backers.svg?width=890"></a>

## 贊助

成為贊助商支持此專案。您的標誌將顯示於此處並附上網站連結。[[成為贊助商](https://opencollective.com/x-prober#sponsor)]

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

## 計畫功能

- [x] 新增溫度偵測
- [x] 透過 Poedit 新增更多語言
- [x] 更詳細的效能評分結果
- [ ] 新增郵件發送測試
- [ ] 新增網速測試
- [x] 新增更多伺服器效能評分結果
- [x] 新增 PING 功能