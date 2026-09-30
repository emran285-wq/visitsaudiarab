#!/bin/bash
# =============================================================================
# VisitSaudiArab — cPanel Git Version Control deployment script
# =============================================================================
# This script is invoked by .cpanel.yml. It copies only the production files
# needed by WordPress from the repository checkout into the live document root.
#
# SAFETY FEATURES:
#   * The Git checkout and the live site are separate by default.
#   * The destination is validated before any file is copied.
#   * Uploads, logs, wp-config.php, and .env are never touched.
#   * No --delete is used, so files created on the server are preserved.
# =============================================================================

set -euo pipefail

# --- USER MUST CONFIGURE THIS ------------------------------------------------
# Primary domain example:   /home/yourcpaneluser/public_html
# Addon domain example:     /home/yourcpaneluser/public_html/addondomain.com
# Subdomain example:        /home/yourcpaneluser/public_html/subdomain
#
# Replace the placeholder below with your real cPanel document root.
# Do not leave the $USER fallback for production; set the exact path.
DEST_DIR="${DEPLOY_DEST:-/home/YOUR_CPANEL_USER/public_html}"
# -----------------------------------------------------------------------------

# Resolve repository root from this script's location.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

echo "=========================================="
echo "VisitSaudiArab deployment"
echo "Repository: $REPO_ROOT"
echo "Destination: $DEST_DIR"
echo "=========================================="

# 0. Ensure the placeholder has been replaced.
if [[ "$DEST_DIR" == *"YOUR_CPANEL_USER"* ]]; then
    echo "ERROR: DEST_DIR still contains the placeholder YOUR_CPANEL_USER." >&2
    echo "       Edit $0 and set it to your real cPanel document root." >&2
    exit 1
fi

# 1. Validate destination exists.
if [[ ! -d "$DEST_DIR" ]]; then
    echo "ERROR: Destination directory '$DEST_DIR' does not exist." >&2
    echo "       Edit DEST_DIR in $0 to match your cPanel document root." >&2
    exit 1
fi

# 2. Basic sanity check that this is a WordPress document root.
if [[ ! -f "$DEST_DIR/wp-config.php" && ! -f "$DEST_DIR/wp-load.php" ]]; then
    echo "ERROR: '$DEST_DIR' does not appear to be a WordPress document root." >&2
    echo "       Expected wp-config.php or wp-load.php." >&2
    echo "       Deployment aborted to prevent writing to the wrong location." >&2
    exit 1
fi

# 3. Deploy the theme.
THEME_SRC="$REPO_ROOT/theme"
THEME_DEST="$DEST_DIR/wp-content/themes/visitsaudiarab"
if [[ -d "$THEME_SRC" ]]; then
    echo "[1/4] Deploying theme to $THEME_DEST ..."
    mkdir -p "$THEME_DEST"
    if command -v rsync >/dev/null 2>&1; then
        rsync -a --no-perms \
            --exclude='.git*' \
            --exclude='.env*' \
            --exclude='README.md' \
            --exclude='DEPLOYMENT_CPANEL.md' \
            --exclude='.cpanel.yml' \
            --exclude='deploy' \
            --exclude='node_modules' \
            --exclude='package*.json' \
            --exclude='generate-favicon.js' \
            "$THEME_SRC/" "$THEME_DEST/"
    else
        # Fallback for hosts without rsync: copy then remove excluded files.
        cp -R "$THEME_SRC/"* "$THEME_DEST/"
        find "$THEME_DEST" -maxdepth 1 -type f \( \
            -name '.git*' -o \
            -name '.env*' -o \
            -name 'README.md' -o \
            -name 'DEPLOYMENT_CPANEL.md' -o \
            -name '.cpanel.yml' \
        \) -delete 2>/dev/null || true
        rm -rf "$THEME_DEST/node_modules" "$THEME_DEST/package.json" "$THEME_DEST/package-lock.json" "$THEME_DEST/generate-favicon.js" 2>/dev/null || true
    fi
else
    echo "ERROR: Theme source directory '$THEME_SRC' not found." >&2
    exit 1
fi

# 4. Deploy must-use plugins.
MU_SRC="$REPO_ROOT/mu-plugins"
MU_DEST="$DEST_DIR/wp-content/mu-plugins"
if [[ -d "$MU_SRC" ]]; then
    echo "[2/4] Deploying mu-plugins to $MU_DEST ..."
    mkdir -p "$MU_DEST"
    for file in "$MU_SRC"/*.php; do
        [[ -f "$file" ]] || continue
        cp -p "$file" "$MU_DEST/"
    done
else
    echo "ERROR: mu-plugins source directory '$MU_SRC' not found." >&2
    exit 1
fi

# 5. Deploy robots.txt only if the server does not already have one.
if [[ -f "$REPO_ROOT/robots.txt" ]]; then
    if [[ ! -f "$DEST_DIR/robots.txt" ]]; then
        echo "[3/4] Copying robots.txt ..."
        cp -p "$REPO_ROOT/robots.txt" "$DEST_DIR/robots.txt"
    else
        echo "[3/4] Skipping robots.txt (already exists in destination)."
    fi
else
    echo "[3/4] No robots.txt in repository."
fi

# 6. Remind about .htaccess additions.
echo "[4/4] Deployment complete."
echo ""
echo "NOTE: If this is the first deployment, merge htaccess-additions.txt"
echo "      into $DEST_DIR/.htaccess above the '# BEGIN WordPress' block."
echo "      Future deployments do not touch .htaccess automatically."
