[![X Prober 預覽](screenshots/preview.webp)](screenshots/preview.webp)

# 😎 X 探針、M字額探針

> 這款 PHP 環境探測程式，除咗可以好直觀噉顯示伺服器資訊，最重要嘅係：佢同 📱 **iPhone X/XS/XS Max/XR** 一樣有醜樣嘅 M字額！

[![Open Collective 支持者](https://opencollective.com/x-prober/backers/badge.svg)](#支持者)
[![Open Collective 贊助商](https://opencollective.com/x-prober/sponsors/badge.svg)](#贊助)

## 開源授權

- GPL-3.0

## 線上演示

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (同上)

## 下載與使用

- 點擊 [INN 下載節點](https://api.inn-studio.com/download?id=xprober) 或 [GitHub 節點](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) 下載探測檔案。
- 你會得到一個檔案，命名做 `x.php` 並上載到你嘅伺服器。
- 透過瀏覽器存取 `x.php` 即可。

## 環境需求

- 編譯環境：PHP 8.4+
- 執行環境：PHP 5.4+
- 瀏覽器兼容：Chrome、Firefox、Edge、Android
- 系統兼容：Linux、Windows（基本功能）

## 擴充功能

- 編寫緊……

## 開發指引

- Fork 專案。
- 提取你嘅儲存庫。
- 安裝 npm 模組：`$ npx pnpm i`。
- 安裝 composer：`$ composer install && composer dumpautoload -o`。
- 產生多國語言：`$ npm run lang` 重建 `./languages/lang.pot` 語言範本。
- 用 php 監聽專案後端：`$ npx pnpm dev:php`。
- 用 vite 監聽專案前端：`$ npx pnpm dev`。
- 存取：`http://localhost:5173/`。

## 編譯生產版本

- 編譯前端：`$ npx pnpm build`。
- 編譯檔案：`$ npx pnpm build:php` 獲得單檔案 `./dist/prober.php`。
- 存取：`http://localhost:8001/prober.php` 或 `http://path/to/dist/prober.php`。

## 參與翻譯更多語言

- Fork 專案到你嘅儲存庫。
- 用 [Poedit](https://poedit.net/) 透過 `./languages/lang.pot` 語言範本建立同翻譯你嘅語言。
- 儲存翻譯語言檔案 _（例如：`en_US.po`）_ 到 `./languages` 目錄入面。
- 推送你嘅修改。
- 提交 Pull Request，多謝晒。😘

## 畀貢獻者嘅備註

- 你嘅 PHP 程式碼需要兼容 PHP5.4+ 環境。

## 貢獻者名單

<a href="https://github.com/kmvan/x-prober/graphs/contributors"><img src="https://opencollective.com/x-prober/contributors.svg?width=890&button=false" /></a>

## 支持者

多謝所有支持者 🙏 [[成為支持者](https://opencollective.com/x-prober#backer)]

<a href="https://opencollective.com/x-prober#backers" target="_blank"><img src="https://opencollective.com/x-prober/backers.svg?width=890"></a>

## 贊助

成為贊助商支持呢個專案。你嘅商標會顯示喺呢度，並附上網站連結。[[成為贊助商](https://opencollective.com/x-prober#sponsor)]

- 多謝 [VPSPlayer.com](https://vpsplayer.com/aff.php?aff=50) - 2021年1月16日 - 199 人民幣
- 多謝 1529\*\*\*576 - 2019年4月4日 – 150 人民幣
- 多謝 [Vultr.com](https://www.vultr.com/?ref=7256513) - 2019年3月13日 - 50 美元

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

## 計劃功能

- [x] 加入溫度探測
- [x] 透過 Poedit 加入更多語言
- [x] 更詳細嘅跑分結果
- [ ] 加入電郵發送測試
- [ ] 加入網速測試
- [x] 加入更多伺服器跑分結果
- [x] 加入 PING 功能