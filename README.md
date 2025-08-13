[![X Prober preview](screenshots/preview.webp)](screenshots/preview.webp)

- [简体中文](README-zh_CN.md)
- [繁体體中文（中國臺灣）](README-zh_TW.md)
- [繁体體中文（中國香港）](README-zh_HK.md)
- [日本語](README-jp.md)

[![X Prober Preview](screenshots/preview.webp)](screenshots/preview.webp)

# 😎 X Prober, Notch Prober

> A PHP environment probe program that not only displays server information intuitively, but most importantly: it has an ugly notch just like 📱 **iPhone X/XS/XS Max/XR**!

[![Backers on Open Collective](https://opencollective.com/x-prober/backers/badge.svg)](#backers)
[![Sponsors on Open Collective](https://opencollective.com/x-prober/sponsors/badge.svg)](#sponsors)

## Open Source License

- GPL-3.0

## Online Demo

- [https://prober.inn-studio.com](https://prober.inn-studio.com)
- [https://tz.inn-studio.com](https://tz.inn-studio.com) (Same as above)

## Download & Usage

- Click [INN Download Node](https://api.inn-studio.com/download?id=xprober) or [GitHub Node](https://github.com/kmvan/x-prober/raw/master/dist/prober.php) to download the probe file
- You'll get a single file. Rename it to `x.php` and upload to your server
- Access via browser: `your-domain/x.php`

## Requirements

- Build Environment: PHP 8.4+
- Runtime Environment: PHP 5.4+
- Browser Compatibility: Chrome, Firefox, Edge, Android
- OS Compatibility: Linux, Windows (basic features)

## Extensions

- In development...

## Development Guide

1. Fork the project
2. Fetch your repository
3. Install npm modules: `$ npx pnpm i`
4. Install composer: `$ composer install && composer dumpautoload -o`
5. Generate multilingual files: `$ npm run lang` to rebuild `./languages/lang.pot` template
6. Start PHP backend: `$ npx pnpm dev:php`
7. Start Vite frontend: `$ npx pnpm dev`
8. Access: `http://localhost:5173/`

## Production Build

- Build frontend: `$ npx pnpm build`
- Compile single file: `$ npx pnpm build:php` to get `./dist/prober.php`
- Access: `http://localhost:8001/prober.php` or `http://path/to/dist/prober.php`

## Contribute Translations

1. Fork the project
2. Use [Poedit](https://poedit.net/) with `./languages/lang.pot` to translate
3. Save translation file (e.g. `en_US.po`) in `./languages`
4. Push your changes
5. Submit Pull Request. Much appreciated! 😘

## Notes for Contributors

- Your PHP code must be compatible with PHP 5.4+ environments

## Contributors

<a href="https://github.com/kmvan/x-prober/graphs/contributors"><img src="https://opencollective.com/x-prober/contributors.svg?width=890&button=false" /></a>

## Backers

Thank you to all our backers! 🙏 [[Become a backer](https://opencollective.com/x-prober#backer)]

<a href="https://opencollective.com/x-prober#backers" target="_blank"><img src="https://opencollective.com/x-prober/backers.svg?width=890"></a>

## Sponsors

Support this project by becoming a sponsor. Your logo will show up here with a link to your website. [[Become a sponsor](https://opencollective.com/x-prober#sponsor)]

- Thanks to [VPSPlayer.com](https://vpsplayer.com/aff.php?aff=50) - Jan 16, 2021 - ¥199
- Thanks to 1529\*\*\*576 - Apr 4, 2019 – ¥150
- Thanks to [Vultr.com](https://www.vultr.com/?ref=7256513) - Mar 13, 2019 - $50

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

## Planned Features

- [x] Temperature detection
- [x] Multilingual support via Poedit
- [x] Detailed benchmark results
- [ ] Email sending test
- [ ] Network speed test
- [x] Additional server benchmarks
- [x] PING functionality

## Keywords

X-Prober/PHP 探针/X 探针/刘海探针
