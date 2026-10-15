# Bulk Product Uploader website (WordPress)

Marketing website for the Bulk Product Uploader Shopify app (`../shopify-bulk-uploader-app/`): home, features, how it
works, pricing, comparison with Shopify CSV import, help centre, early access, contact, privacy, terms and a blog with
four posts. Built from the Reorder Desk website (`../reorder-desk-website/`) with the same structure.
Owner's step-by-step guide (Hinglish): `docs/OWNER-GUIDE.md`. State and decisions: `docs/PROGRESS.md`.

## Local site

| | |
|---|---|
| Site | http://localhost:8089 (Apache vhost added to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`) |
| Admin | http://localhost:8089/wp-admin/ (login in `local-login.txt`, not for sharing) |
| WordPress | 7.1.3 in `C:\xampp\htdocs\bulkuploader` (core copied from the Reorder Desk install) |
| Database | `bulkuploader_wp` on XAMPP MariaDB, user `root` (local only) |

The theme and plugin folders in WordPress are **junctions** pointing at this folder, so editing files here changes the
site at once:

- `C:\xampp\htdocs\bulkuploader\wp-content\themes\bulk-uploader` -> `theme/bulk-uploader`
- `C:\xampp\htdocs\bulkuploader\wp-content\plugins\bulk-uploader-site` -> `plugin/bulk-uploader-site`

## What is here

```
theme/bulk-uploader/      Block theme. Tokens in theme.json (indigo brand), components (bu-*) in assets/css/site.css.
  templates/              front-page, page, page-landing (full width, no title), single, home (blog), archive, search, 404
  parts/                  header, footer
  patterns/               cta-band, feature-cards, faq, screenshot-split (Editor > Patterns > Bulk Product Uploader)
  assets/images/          app screenshots (app-*.webp), logo-mark.svg, favicon.svg, og-image.png
plugin/bulk-uploader-site/
  includes/leads.php      [bu_waitlist], [bu_contact], [bu_support_email]; table {prefix}bu_leads
  includes/admin.php      Leads page (list, CSV, delete)
  includes/settings.php   Settings > Bulk Product Uploader site (App Store URL, live label, support email, notify email)
  includes/cta.php        Launch switch: buttons with class bu-cta link to the App Store URL once it is set
  includes/meta.php       SoftwareApplication schema on the home page; meta/OG fallback when no SEO plugin
content/                  Page and post bodies (block markup), site.php (titles, slugs, templates, meta), covers/
tools/
  wp.sh                   WP-CLI for the local site (XAMPP PHP)
  setup-content.php       content/ -> WordPress (only missing pages unless "force")
  make-images.php         draws blog covers and og-image.png (PHP GD + Windows Segoe UI)
  make-shots.php          cuts the app screenshots out of the uploader's browser-test screenshots
  shots.mjs               screenshots at desktop and phone width + console/404/overflow checks
  check-editor.mjs        opens every page in the block editor and reports invalid blocks
```

## Commands (Git Bash, from this folder)

```
tools/wp.sh <any wp-cli command>                                    # e.g. tools/wp.sh plugin list
tools/wp.sh eval-file tools/setup-content.php --user=bulkadmin      # add missing pages/posts
tools/wp.sh eval-file tools/setup-content.php force only=pricing --user=bulkadmin   # overwrite one page from content/
php tools/make-images.php                                           # redraw covers and og-image
node tools/shots.mjs pricing/ blog/                                 # screenshots into tools/.shots/ (no args = home)
node tools/check-editor.mjs                                         # block validation in the editor
```

`force` overwrites edits made in the WordPress editor for those pages; without it, existing pages are kept.

### New app screenshots

The `app-*.webp` images come from the uploader's browser test (`../shopify-bulk-uploader-app/test/browser/run.mjs`,
which renders the real `Uploader` page with a fake API). For sharp images, run a copy of it with
`deviceScaleFactor: 2` that saves into a scratch folder (do not change the app's own test), then:

```
php -d memory_limit=1G tools/make-shots.php <folder with 1-loaded.png ... 8-restored.png>
```

Crop boxes are in `tools/make-shots.php` (1x page pixels); check them if the app's layout changes.

## Conventions

- Links inside pages and theme files are root-relative (`/pricing/`), so the site must live at the root of a domain
  (or subdomain), not in a sub-folder. Images in pages point at the theme folder
  (`/wp-content/themes/bulk-uploader/assets/images/...`).
- Main buttons carry the class `bu-cta`; never hard-code the App Store URL in pages, set it in
  Settings > Bulk Product Uploader site instead.
- Headings with `bu-ic bu-ic--<name>` get an icon tile (names in `patterns/feature-cards.php`).
- No third-party requests: the font (Inter, OFL) is self-hosted, no analytics, no cookies for visitors.
- Facts on the site come from `../shopify-bulk-uploader-app` (plans in `app/lib/plans.ts`, fields and auto-fill
  patterns in `app/lib/uploader.ts`, defaults in `app/.server/core/config.ts`, image matching in
  `app/.server/core/images.ts`, privacy in `app/routes/privacy.tsx`, `docs/LISTING.md`). Change the site when those change.
