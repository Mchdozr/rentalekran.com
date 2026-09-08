# rentalekran.com

Rental Ekran WordPress presentation and SEO plugin. Production version: 2.2.1.

## Installation
Requires WordPress 6.6+ and PHP 7.4+. Zip the `rentalekran-growth` directory and upload it from WordPress Plugins > Add New > Upload. Replace the existing plugin, then purge this site's LiteSpeed cache. Back up the installed plugin before deployment.

The host WordPress installation supplies the database, settings, legacy content and media uploads. This repository is the custom plugin, not a complete hosting backup. Production credentials, database exports, WordPress core and third-party themes/plugins are intentionally excluded.

## Features
- Shared responsive navigation and five-column footer
- Product catalog, legacy content integration and SEO metadata
- Product slides and accessible image dialog with keyboard navigation
- Mobile technical table layout
- LED cabinet estimator and prefilled contact links

## Validation
PHP syntax and JavaScript syntax checks; route/canonical/structured data integration checks; calculator/menu tests. Responsive layout checked on 21 routes at 360, 390, 768, 1024, 1366 and 1920 pixels. Live product dialog, mobile tables and footer checked separately.

## Deployment
Deploy explicitly through the site's WordPress administration. A GitHub push does not deploy automatically. Retain the previous plugin zip for rollback and verify normal public URLs after cache purge.
