# Global Supernova — WordPress Website

A self-contained WordPress website project. It ships with:

- **Supernova** — a custom, lightweight, responsive theme (`wp-content/themes/supernova`)
- **Supernova Core** — a companion plugin with a `Project` custom post type and a `[supernova_cta]` shortcode (`wp-content/plugins/supernova-core`)
- A **Docker Compose** development environment (WordPress + MariaDB + WP-CLI)

WordPress core itself is **not** committed — it is provided by the official
`wordpress` Docker image. Only the code we own (theme + plugin) lives in this
repo, which keeps the repository small and upgrade-safe.

## Requirements

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose v2

## Quick start

```bash
# 1. Create your local env file
cp .env.example .env

# 2. Start WordPress, the database, and WP-CLI
docker compose up -d

# 3. Open the site
open http://localhost:8080
```

The first time you visit, WordPress runs its famous 5-minute install. Pick a
site title, admin username, and password.

### Activate the theme and plugin

In **wp-admin**:

1. **Appearance → Themes** → activate **Supernova**.
2. **Plugins** → activate **Supernova Core**.

Or from the command line with WP-CLI:

```bash
docker compose exec wpcli wp theme activate supernova
docker compose exec wpcli wp plugin activate supernova-core
```

### Seed sample content

The repo ships with sample content modelled on
[Global Supernova Solutions](https://global-supernova.com) — a Home page, About,
Services, Case Studies, Careers, Contact, a Blog, six Service entries (the
`Project` custom post type), a few blog posts, and a primary navigation menu.

Once WordPress is installed (finish the setup wizard, or install headlessly as
shown below), run:

```bash
./bin/seed.sh
```

The script is **idempotent** — it keys everything by slug, so re-running it
updates existing content instead of creating duplicates. It also activates the
theme and plugin and configures the static front page.

To install WordPress headlessly first (no browser needed):

```bash
docker compose exec -T wpcli wp core install \
  --url=http://localhost:8080 \
  --title="Global Supernova Solutions" \
  --admin_user=admin --admin_password=admin \
  --admin_email=you@example.com --skip-email
./bin/seed.sh
```

> The page copy lives in `data/*.html` as editable WordPress block markup, so you
> can tweak the seed content without touching the script.

### Contact form

The Contact page includes a working contact form, provided by the Supernova Core
plugin via the `[supernova_contact_form]` shortcode. It validates input, blocks
bots with a honeypot and nonce, and emails the submission to the site's admin
address (Reply-To set to the sender). Drop the shortcode on any page to reuse it,
optionally overriding the recipient: `[supernova_contact_form to="sales@example.com"]`.

Submissions are sent with `wp_mail()`. A stock Docker WordPress container has no
mail transport, so in local development messages won't actually be delivered
unless you add one — e.g. run [MailHog](https://github.com/mailhog/MailHog) and
point WordPress at it, or install an SMTP plugin. Validation, the success/error
states, and the redirect all work regardless.

## Project layout

```
.
├── docker-compose.yml            # Local dev stack (WordPress + MariaDB + WP-CLI)
├── .env.example                  # Copy to .env for local configuration
├── bin/
│   └── seed.sh                   # Idempotent WP-CLI content seeder
├── data/                         # Seed page content (WordPress block markup)
├── wp-content/
│   ├── themes/
│   │   └── supernova/            # Custom theme
│   │       ├── style.css
│   │       ├── functions.php
│   │       ├── header.php / footer.php
│   │       ├── front-page.php    # Homepage with hero + features + latest posts
│   │       ├── index.php / single.php / page.php / archive.php / search.php / 404.php
│   │       ├── template-parts/   # Reusable content partials
│   │       ├── inc/              # Template tags
│   │       └── assets/           # CSS + JS
│   └── plugins/
│       └── supernova-core/       # Site-specific plugin
└── README.md
```

## Why keep custom code in the repo but not WordPress core?

WordPress core, default themes, and uploads change frequently and are managed
by the platform. Committing only the theme and plugin means:

- The repo stays small and reviewable.
- Core and default plugins are updated through the Docker image / wp-admin.
- Anyone can clone, run `docker compose up`, and get an identical dev site.

The `.gitignore` reflects this: core files, `wp-config.php`, uploads, and
bundled default themes are ignored.

## Common commands

```bash
docker compose up -d            # start
docker compose down             # stop (keeps the database volume)
docker compose down -v          # stop and delete the database
docker compose logs -f wordpress
docker compose exec wpcli wp --info
```

## Deploying

For production, deploy the `wp-content/themes/supernova` and
`wp-content/plugins/supernova-core` directories into a managed WordPress
install (or build them into your host's image), then activate them. Do not
copy the local `.env` or the development database.
