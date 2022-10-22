[![X Prober preview](screenshots/preview.webp)](screenshots/preview.webp)

- [Simplified Chinese | 简体中文](README-zh_CN.md)
- [Traditional Chinese(Taiwan) | 正體中文（臺灣）](README-zh_TW.md)
- [Traditional Chinese(Hong Kong) | 正體中文（香港）](README-zh_HK.md)
- [Japanese | 日本語](README-jp.md)

# 😎 X Prober

> これは **PHP 環境** のプローブプログラムです。 サーバー情報を表示し、簡単に読み取ることができます。

[![オープンコレクティブの支持者](https://opencollective.com/x-prober/backers/badge.svg)](#backers)
[![オープンコレクティブのスポンサー](https://opencollective.com/x-prober/sponsors/badge.svg)](#sponsors)

## ライセンス

- GPL-3.0

## オンラインデモ

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (同じ)

## ダウンロードと使用法

- Click [INN STUDIO mirror](https://api.inn-studio.com/download?id=xprober) or [GitHub mirror](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) to download the probe file.
- You will get a single file of `x.php` and upload it to your server.
- Access `x.php` via http browser.

## 必要な環境

- 開発環境: PHP 8.1+
- 生産環境: PHP 5.4+
- ブラウザのサポート: Chrome / Firefox / Edge / Android
- OS サポート: Linux / Windows(basic features)

## 拡張機能

- 準備中...

## 開発

- フォークプロジェクト。
- プロジェクトを取得します。
- npm モジュールをインストールします：`$ npm install`。
- 監視スクリプト：`$ npm run dev`。
- Composer をインストールします：`$ composer install; composer dumpautoload -o`。
- PHP をコンパイル: `$ npm run dev:php`
- Access `http://localhost:8000` or `http://path/to/.tmp/index.php`.
- Generate languages: `$ npm run lang` to remake `./languages/lang.pot` language template and build `*.po`.
- Enjoy it. 😄

## Compile production

- Compile JS: `$ npm run build`.
- Compile PHP: `$ npm run build:php`.
- Access: `http://localhost:8000` or `http://path/to/dist/prober.php`.

## Help and translate more languages

- Fork project.
- Fetch your project.
- Use [Poedit](https://poedit.net/) to create your language from `./languages/lang.pot` language template file and translates it.
- Save your language file (like: `en_US.po`) into `./languages`.
- Push your changes.
- Pull Request and thank you. 😘

## Note for participate in contribution

- Code compatible with PHP 5.3+

## Contributors

[![Contributors](https://opencollective.com/x-prober/contributors.svg?width=890&button=false)](https://github.com/kmvan/x-prober/graphs/contributors)

## Backers

Thank you to all our backers! 🙏 [[Become a backer](https://opencollective.com/x-prober#backer)]

[![Contributors](https://opencollective.com/x-prober/backers.svg?width=890)](https://opencollective.com/x-prober#backers)

## Sponsors

Support this project by becoming a sponsor. Your logo will show up here with a link to your website. [[Become a sponsor](https://opencollective.com/x-prober#sponsor)]

- Thanks [VPSPlayer.com](https://vpsplayer.com/aff.php?aff=50) - 2021-01-16 - 199 RMB
- Thanks 1529\*\*\*576 - 2019-04-04 – 150 RMB
- Thanks [Vultr.com](https://www.vultr.com/?ref=7256513) - 2019-03-13 - 50 Dollars

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

## TODO

- [x] Temperature sensor.
- [x] More languages with Poedit.
- [x] Detail benchmark result.
- [ ] Add Email send testing.
- [ ] Add network speed testing.
- [x] Add more servers benchmark.
- [x] Add PING feature.

## Keywords

X-Prober/PHP 探针/X 探针/刘海探针
