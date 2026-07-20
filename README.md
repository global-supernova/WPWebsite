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

### Optional: seed some demo content

```bash
docker compose exec wpcli wp post generate --count=5
docker compose exec wpcli wp option update posts_per_page 6
```

## Project layout

```
.
├── docker-compose.yml            # Local dev stack (WordPress + MariaDB + WP-CLI)
├── .env.example                  # Copy to .env for local configuration
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
