# Bulk Product Uploader website: progress and handoff

Read this first when starting a new session. Last updated: 2026-10-15 (built locally).
Technical overview: `../README.md`. Owner's guide (Hinglish): `OWNER-GUIDE.md`.
Code on GitHub: https://github.com/Munish86/bulk-uploader-website (`main`). Not in git: `local-login.txt`, `tools/.shots/`,
`tools/wp-cli.phar` (see `.gitignore`); the WordPress database and uploads live only in XAMPP.

## Goal

A professional marketing website for the Bulk Product Uploader Shopify app (`../../shopify-bulk-uploader-app/`), in
WordPress so the owner can edit pages, write blog posts and collect early-access sign-ups. Built locally on XAMPP
first; moved to real hosting once the domain is chosen.

## Decisions

| Topic | Decision |
|---|---|
| Base | Copy of `../reorder-desk-website/` (theme, plugin, tools), prefixes renamed `rd-`/`rds_` -> `bu-`/`bus_`, brand colour indigo `#3A4AD6` instead of green |
| Platform | WordPress 7.1.3 (core copied from the Reorder Desk install), own block theme + small own plugin, Yoast SEO |
| Local URL | http://localhost:8089 via an Apache vhost on port 8089 (root install) |
| Database | `bulkuploader_wp`, XAMPP `root` |
| Source of truth | Theme and plugin live in this folder; WordPress has junctions to them. Page/post bodies in `content/` |
| CTA | Every main button has class `bu-cta` -> /early-access/; Settings > Bulk Product Uploader site > App Store URL switches them all to "Install free on Shopify" |
| Forms | Own plugin: saved in `{prefix}bu_leads` (fields: email, store, how they add products today, catalogue size); Leads admin page + CSV |
| Screenshots | Real app UI from the uploader's browser test at 2x, cut by `tools/make-shots.php` |
| Copy | English; every claim checked against the uploader's code (see README "Conventions") |

## State on 2026-10-15

- Pages: home, features, how-it-works, pricing, shopify-csv-import-alternative, help, early-access, contact,
  privacy-policy, terms, blog (posts page). Posts: 4 guides (Excel upload, bulk images, import mistakes, metafields)
  with generated covers.
- Verified: `tools/shots.mjs` all pages 200 at 1440px and 390px, no console errors, no horizontal overflow;
  `tools/check-editor.mjs` all 30 pages/templates/parts/patterns valid; early-access form submitted once, row saved in
  `wp_bu_leads` (test row deleted); Yoast title/description/OG image on pages.
- Fixed after the first screenshots: hero mock-up file name wrapping, image-matching box gaps (one paragraph now).
- Shopify CSV comparison: "blank cell clears the value with overwrite" is from Shopify's Help Center; the 15 MB file
  limit is only in third-party sources, so it is not stated.

## Open points for the owner

1. **Prices are placeholders:** Pro $19/month and Unlimited $49/month (home, pricing). The app's prices are not decided
   (`../shopify-bulk-uploader-app/docs/ROADMAP.md`). No free-trial length is promised anywhere.
2. Promises to confirm: "reply within one working day" (contact), "help setting up your first sheet" (early access),
   early-access emails deleted within 12 months of launch (privacy), legal entity and governing law (terms/privacy).
3. Domain + hosting, then move with All-in-One WP Migration (OWNER-GUIDE.md section 7).
4. Support email and notify email are empty in Settings > Bulk Product Uploader site (pages then link to /contact/).
5. Same XAMPP notes as the Reorder Desk site: OPcache off (slow local pages), `mysql.db` marked crashed.

## Gotchas

- Same as `../reorder-desk-website/docs/PROGRESS.md` (Git Bash path rewriting, Edge for headless screenshots,
  `--user=bulkadmin` for content, `force` overwrites WordPress edits).
- Apache runs as a plain process, not a service: `httpd -k restart` fails. To apply vhost changes, stop the httpd
  processes and start `C:\xampp\apache\bin\httpd.exe` again (or Stop/Start in the XAMPP Control Panel).
- `tools/wp.sh db query` fails (no `mysql` on PATH for WP-CLI); use `tools/wp.sh eval` with `$wpdb` instead.
- `bu-formula` uses `white-space: pre-wrap`: put the whole text in one paragraph with `<br>`, not several blocks.
