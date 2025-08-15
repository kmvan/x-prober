[![X Prober プレビュー](screenshots/preview.webp)](screenshots/preview.webp)

# 😎 Xプローブ、ノッチプローブ

> これはPHP環境プローブプログラムです。サーバー情報を直感的に表示できるだけでなく。

[![Open Collective バッカー](https://opencollective.com/x-prober/backers/badge.svg)](#バッカー)
[![Open Collective スポンサー](https://opencollective.com/x-prober/sponsors/badge.svg)](#スポンサー)

## オープンソースライセンス

- GPL-3.0

## オンラインデモ

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (同上)

## ダウンロードと使用方法

- [INN ダウンロードノード](https://api.inn-studio.com/download?id=xprober) または [GitHub ノード](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) をクリックしてプローブファイルをダウンロード
- `x.php` というファイルをサーバーにアップロード
- ブラウザで `x.php` にアクセス

## 動作環境

- ビルド環境：PHP 8.4+
- 実行環境：PHP 5.4+
- ブラウザ互換性：Chrome、Firefox、Edge、Android
- OS互換性：Linux、Windows（基本機能）

## 拡張機能

- 開発中...

## 開発ガイド

1. プロジェクトをフォーク
2. リポジトリをフェッチ
3. npmモジュールをインストール：`$ npx pnpm i`
4. composerをインストール：`$ composer install && composer dumpautoload -o`
5. 多言語生成：`$ npm run lang` で `./languages/lang.pot` テンプレート再生成
6. PHPバックエンド監視：`$ npx pnpm dev:php`
7. Viteフロントエンド監視：`$ npx pnpm dev`
8. アクセス：`http://localhost:5173/`

## 本番ビルド

- フロントエンドビルド：`$ npx pnpm build`
- ファイルビルド：`$ npx pnpm build:php` で単一ファイル `./dist/prober.php` 生成
- アクセス：`http://localhost:8001/prober.php` または `http://path/to/dist/prober.php`

## 翻訳協力

1. プロジェクトをフォーク
2. [Poedit](https://poedit.net/) で `./languages/lang.pot` を基に翻訳
3. 翻訳ファイル（例：`ja_JP.po`）を `./languages` に保存
4. 変更をプッシュ
5. プルリクエストを送信（ご協力に感謝😘）

## コントリビューターへの注意

- PHPコードはPHP5.4+環境と互換性が必要です

## コントリビューター一覧

<a href="https://github.com/kmvan/x-prober/graphs/contributors"><img src="https://opencollective.com/x-prober/contributors.svg?width=890&button=false" /></a>

## バッカー

すべてのサポーターに感謝 🙏 [[バッカーになる]](https://opencollective.com/x-prober#backer)

<a href="https://opencollective.com/x-prober#backers" target="_blank"><img src="https://opencollective.com/x-prober/backers.svg?width=890"></a>

## スポンサー

スポンサーになってプロジェクトを支援。ロゴとリンクを掲載します。[[スポンサーになる]](https://opencollective.com/x-prober#sponsor)

- [VPSPlayer.com](https://vpsplayer.com/aff.php?aff=50) 様 - 2021年1月16日 - 199元
- 1529\*\*\*576 様 - 2019年4月4日 - 150元
- [Vultr.com](https://www.vultr.com/?ref=7256513) 様 - 2019年3月13日 - 50ドル

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

## 開発予定機能

- [x] 温度検出機能追加
- [x] Poeditによる多言語対応
- [x] 詳細なベンチマーク結果
- [ ] メール送信テスト機能
- [ ] ネットワーク速度テスト
- [x] 追加サーバーベンチマーク
- [x] PING機能実装