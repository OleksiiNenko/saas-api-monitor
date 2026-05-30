# AI Page Connector (WordPress mu-plugin)

A single-file connector that lets the SaaS app create ACF-based **draft** pages on
your WordPress site safely. It is **ACF- and WPML-aware** and never touches
existing content — it only ever creates new pages with `status=draft`.

## Install

1. On your WordPress server, create the folder `wp-content/mu-plugins/` if it does
   not exist (mu = "must-use"; plugins here load automatically, no activation needed).
2. Copy `ai-page-connector.php` into `wp-content/mu-plugins/`.
3. Create an **Application Password**: WordPress admin → Users → Profile →
   *Application Passwords* → add one (e.g. "SaaS App"). Copy the generated password.
   The account must be able to edit/publish pages (Editor or Administrator).

## Connect in the app

In the SaaS UI, "Connect site" with:

- **Site URL** — `https://your-site.com`
- **Username** — your WP login
- **Application Password** — the one you generated

The app calls `GET /wp-json/ai-page/v1/ping` to verify. It reports whether ACF and
WPML are detected.

## Endpoints

| Method | Route | Purpose |
| --- | --- | --- |
| GET | `/wp-json/ai-page/v1/ping` | Health + ACF/WPML detection + version |
| GET | `/wp-json/ai-page/v1/meta` | Pages (for parent), categories, tags, authors, WPML languages |
| POST | `/wp-json/ai-page/v1/pages` | Create a **draft** page (title, slug, parent, author, ACF by key, language, taxonomies) |

## Safety

- Only **creates** pages; never updates or deletes existing posts/pages.
- Every created page is a **draft** — you review and publish manually in WP admin.
- Requires the `edit_pages` + `publish_pages` capability.
- If filling fields fails, the just-created draft is rolled back.
- Images/galleries are never modified.
