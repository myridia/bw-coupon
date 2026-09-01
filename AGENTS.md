# AGENTS.md — bw-coupon

## What this is
WooCommerce plugin ("BW Coupon") that lets sellers create PDF coupons for sale. After purchase, the customer receives an attached PDF coupon.

## Stack
- PHP (requires PHP 8.0+)
- WordPress / WooCommerce
- PDF generation (lib/)
- Docker (dockers/ for dev)

## Build
N/A — WordPress plugin

## Run
Install and activate the plugin in WordPress. Define the coupon under a product via the "BWC coupon" checkbox; configure in BWC Settings.

## Structure
- `bwc.php` — main plugin file
- `admin/` — admin interface
- `includes/` — core logic
- `lib/` — libraries (PDF etc.)
- `assets/` — static resources
- `languages/` — i18n
- `dockers/` — dev docker environments
- `docs/` — documentation
- `make_readme.sh` — generate readme

## Conventions
- No comments in code unless asked.
- Verify: `php -l bwc.php`