#!/usr/bin/env bash
#
# Seed the local WordPress site with sample content modelled on
# Global Supernova Solutions (global-supernova.com).
#
# Idempotent: pages/posts/projects are keyed by slug, so re-running updates
# existing entries instead of creating duplicates.
#
# Usage:
#   docker compose up -d
#   ./bin/seed.sh
#
set -euo pipefail

# Run WP-CLI inside the wpcli service. Pass --allow-root friendly flags.
wp() {
	docker compose exec -T wpcli wp "$@"
}

# upsert_page <slug> <title> <content-file> [parent-slug]
# Creates the page if the slug is free, otherwise updates its content/title.
upsert_page() {
	local slug="$1" title="$2" file="$3"
	local id
	id="$(wp post list --post_type=page --name="$slug" --field=ID --format=ids 2>/dev/null | tr -d '\r' || true)"
	if [ -n "$id" ]; then
		wp post update "$id" --post_title="$title" --post_content="$(cat "$file")" >/dev/null
		echo "  updated page: $title (#$id)" >&2
	else
		id="$(wp post create --post_type=page --post_status=publish \
			--post_title="$title" --post_name="$slug" \
			--post_content="$(cat "$file")" --porcelain | tr -d '\r')"
		echo "  created page: $title (#$id)" >&2
	fi
	echo "$id"
}

# upsert_post <slug> <title> <content> [category]
upsert_post() {
	local slug="$1" title="$2" content="$3" category="${4:-Insights}"
	local id
	id="$(wp post list --post_type=post --name="$slug" --field=ID --format=ids 2>/dev/null | tr -d '\r' || true)"
	if [ -n "$id" ]; then
		wp post update "$id" --post_title="$title" --post_content="$content" >/dev/null
		echo "  updated post: $title (#$id)"
	else
		wp post create --post_status=publish --post_title="$title" --post_name="$slug" \
			--post_content="$content" --post_category="$(wp term list category --name="$category" --field=term_id --format=ids 2>/dev/null | tr -d '\r')" \
			--porcelain >/dev/null || \
		wp post create --post_status=publish --post_title="$title" --post_name="$slug" \
			--post_content="$content" --porcelain >/dev/null
		echo "  created post: $title"
	fi
}

# upsert_project <slug> <title> <content>
upsert_project() {
	local slug="$1" title="$2" content="$3"
	local id
	id="$(wp post list --post_type=project --name="$slug" --field=ID --format=ids 2>/dev/null | tr -d '\r' || true)"
	if [ -n "$id" ]; then
		wp post update "$id" --post_title="$title" --post_content="$content" >/dev/null
		echo "  updated project: $title (#$id)"
	else
		wp post create --post_type=project --post_status=publish \
			--post_title="$title" --post_name="$slug" --post_content="$content" \
			--porcelain >/dev/null
		echo "  created project: $title"
	fi
}

echo "==> Waiting for WordPress to be installed..."
if ! wp core is-installed >/dev/null 2>&1; then
	echo "WordPress is not installed yet."
	echo "Finish the install at http://localhost:\${WP_PORT:-8080}, then re-run this script."
	echo "Or install headlessly, e.g.:"
	echo "  docker compose exec -T wpcli wp core install \\"
	echo "    --url=http://localhost:8080 --title='Global Supernova Solutions' \\"
	echo "    --admin_user=admin --admin_password=admin --admin_email=you@example.com"
	exit 1
fi

echo "==> Activating theme and plugin"
wp theme activate supernova >/dev/null && echo "  theme: supernova"
wp plugin activate supernova-core >/dev/null && echo "  plugin: supernova-core"

echo "==> Site identity"
wp option update blogname "Global Supernova Solutions" >/dev/null
wp option update blogdescription "A leading SAP Partner for Enterprise Resource Planning" >/dev/null
wp option update timezone_string "UTC" >/dev/null
wp rewrite structure '/%postname%/' >/dev/null 2>&1 || true

echo "==> Categories"
for cat in "Insights" "SAP" "Case Studies" "Company News"; do
	wp term create category "$cat" >/dev/null 2>&1 || true
done

# Page content lives in ./data on the host. It is read here (on the host) and
# passed to WP-CLI as arguments, so the files do not need to be mounted into
# the container.
LOCAL_DATA="$(cd "$(dirname "$0")/../data" && pwd)"

echo "==> Pages"
HOME_ID="$(upsert_page "home" "Home" "$LOCAL_DATA/page-home.html" | tail -1)"
upsert_page "about-us" "About Us" "$LOCAL_DATA/page-about.html" >/dev/null
upsert_page "services" "Services" "$LOCAL_DATA/page-services.html" >/dev/null
upsert_page "case-studies" "Case Studies" "$LOCAL_DATA/page-case-studies.html" >/dev/null
upsert_page "careers" "Careers" "$LOCAL_DATA/page-careers.html" >/dev/null
upsert_page "contact" "Contact" "$LOCAL_DATA/page-contact.html" >/dev/null
BLOG_ID="$(upsert_page "blog" "Blog" "$LOCAL_DATA/page-blog.html" | tail -1)"

echo "==> Front page settings"
wp option update show_on_front page >/dev/null
wp option update page_on_front "$HOME_ID" >/dev/null
wp option update page_for_posts "$BLOG_ID" >/dev/null

echo "==> Services (Project custom post type)"
upsert_project "sap-consulting" "SAP Consulting" \
	"<p>Deep expertise in the design and implementation of SAP business automation solutions for organisations of all industries and sizes. Our consultants translate business goals into resilient ERP processes.</p>"
upsert_project "implementation-migration" "Implementation &amp; Migration" \
	"<p>End-to-end SAP implementation and migration — from greenfield rollouts to S/4HANA conversions — delivered on time and aligned to your operating model.</p>"
upsert_project "support-service" "Support Service" \
	"<p>Ongoing application management and SAP support that keeps your landscape healthy, secure, and continuously improving.</p>"
upsert_project "resource-augmentation" "Resource Augmentation" \
	"<p>Scale your team with certified SAP specialists exactly when you need them, without the overhead of long hiring cycles.</p>"
upsert_project "data-ai" "Data &amp; Artificial Intelligence" \
	"<p>Advanced data analytics, machine learning, and AI-driven automation that turn enterprise data into decisions.</p>"
upsert_project "customization-development" "Customization &amp; Development" \
	"<p>Tailored development, integrations, and CRM automations that extend standard SAP to fit the way you actually work.</p>"

echo "==> Blog posts"
upsert_post "20-years-of-erp-leadership" "20 Years of ERP Leadership" \
	"<p>For over two decades, Global Supernova Solutions has guided clients across 15 countries through the complexities of enterprise resource planning. With a team whose combined experience exceeds 150 years, we bring hard-won perspective to every engagement.</p><p>This milestone is a reflection of a simple commitment: to Think, Develop and Innovate on behalf of our clients.</p>" \
	"Company News"
upsert_post "choosing-the-right-sap-partner" "How to Choose the Right SAP Partner" \
	"<p>Selecting an SAP partner is one of the most consequential technology decisions an organisation makes. Look for demonstrated delivery across your industry, a transparent methodology, and a team that treats your success as their own.</p><p>Across 40+ industries — from healthcare and food processing to engineering and education — we have learned that fit matters as much as capability.</p>" \
	"SAP"
upsert_post "ai-in-enterprise-operations" "Putting AI to Work in Enterprise Operations" \
	"<p>Artificial intelligence is no longer experimental. Paired with a well-run ERP, machine learning and AI-driven automation streamline operations, surface risk earlier, and free teams to focus on higher-value work.</p>" \
	"Insights"

echo "==> Primary navigation menu"
if ! wp menu list --fields=name --format=csv 2>/dev/null | grep -q '^Primary$'; then
	wp menu create "Primary" >/dev/null
fi
# Rebuild menu items idempotently: clear then re-add.
for item in $(wp menu item list Primary --field=db_id --format=ids 2>/dev/null); do
	wp menu item delete "$item" >/dev/null 2>&1 || true
done
wp menu item add-post Primary "$HOME_ID" >/dev/null 2>&1 || true
for slug in about-us services case-studies careers blog contact; do
	pid="$(wp post list --post_type=page --name="$slug" --field=ID --format=ids 2>/dev/null | tr -d '\r')"
	[ -n "$pid" ] && wp menu item add-post Primary "$pid" >/dev/null 2>&1 || true
done
wp menu location assign Primary primary >/dev/null 2>&1 || true
wp menu location assign Primary footer >/dev/null 2>&1 || true

echo "==> Flushing rewrite rules"
wp rewrite flush >/dev/null 2>&1 || true

echo ""
echo "Done. Visit http://localhost:\${WP_PORT:-8080}"
