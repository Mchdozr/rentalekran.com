# rentalekran.com

Rental Ekran WordPress presentation and SEO plugin. Production version: 2.3.0.

## Installation
Requires WordPress 6.6+ and PHP 7.4+. Canlıya alma: `main` push (aşağıdaki Deployment). Zip yalnızca yedek/rollback içindir.

The host WordPress installation supplies the database, settings, legacy content and media uploads. This repository is the custom plugin, not a complete hosting backup. Production credentials, database exports, WordPress core and third-party themes/plugins are intentionally excluded.

## Features
- Shared responsive navigation and five-column footer
- Product catalog, legacy content integration and SEO metadata
- Product slides and accessible image dialog with keyboard navigation
- Mobile technical table layout
- LED cabinet estimator and prefilled contact links

## Validation
PHP syntax and JavaScript syntax checks; `node tests/audit.test.mjs`; route/canonical/structured data integration checks; calculator/menu tests. Responsive layout checked on 21 routes at 360, 390, 768, 1024, 1366 and 1920 pixels. Live product dialog, mobile tables and footer checked separately.

## Deployment
`main` dalına push (veya Actions → Test and deploy plugin → Run workflow) eklentiyi Natro Plesk’teki `wp-content/plugins/rentalekran-growth/` dizinine kopyalar. Zip yüklemeye gerek yok.

GitHub → Settings → Secrets and variables → Actions:

| Secret | Örnek |
|---|---|
| `FTP_SERVER` | `ftp.rentalekran.com` veya `194.36.84.221` |
| `FTP_USERNAME` | Plesk FTP kullanıcısı |
| `FTP_PASSWORD` | Plesk FTP şifresi |
| `FTP_SERVER_DIR` | `/httpdocs/wp-content/plugins/rentalekran-growth/` (sondaki `/` zorunlu) |

Varsayılan protokol `ftps`. Natro SFTP isterse workflow’da `protocol: sftp` ve `port: 22` yap.

Plesk’te mümkünse yalnızca plugin klasörüne yetkili ayrı FTP kullanıcısı oluştur. İlk deploy’dan sonra `https://rentalekran.com/` ve `/iletisim/` kontrol et.
