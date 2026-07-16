#!/bin/bash
set -euo pipefail

# -----------------------------------------------------------------
# Actual script directory (works regardless of where it's called
# from, including via symlink in crontab)
# -----------------------------------------------------------------
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# -----------------------------------------------------------------
# Local configuration (not versioned) — see cron-job.env.example
# -----------------------------------------------------------------
ENV_FILE="${SCRIPT_DIR}/cron-job.env"
if [ -f "$ENV_FILE" ]; then
    # shellcheck disable=SC1090
    source "$ENV_FILE"
fi

# -----------------------------------------------------------------
# PHP binary discovery: uses PHP_PATH if set in .env,
# otherwise tries to auto-detect it in the system PATH.
# -----------------------------------------------------------------
if [ -z "${PHP_PATH:-}" ]; then
    if command -v php > /dev/null 2>&1; then
        PHP_PATH="$(command -v php)"
    else
        echo "❌ PHP not found. Set PHP_PATH in ${ENV_FILE}." >&2
        exit 1
    fi
fi

if [ ! -x "$PHP_PATH" ]; then
    echo "❌ Invalid PHP_PATH or missing execute permission: ${PHP_PATH}" >&2
    exit 1
fi

# -----------------------------------------------------------------
# Scripts to run (path relative to the repository itself —
# works on any machine/folder where the project is cloned)
# -----------------------------------------------------------------
SCRIPTS=(
    "${SCRIPT_DIR}/app/cli/remember_me.php"
    "${SCRIPT_DIR}/app/cli/forgot_password.php"
)

LOG_FILE="${LOG_FILE:-${SCRIPT_DIR}/storage/logs/cron-job.log}"
mkdir -p "$(dirname "$LOG_FILE")"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"
}

log "Starting record cleanup..."

EXIT_CODE=0

for SCRIPT in "${SCRIPTS[@]}"; do
    if [ ! -f "$SCRIPT" ]; then
        log "⚠️  Script not found, skipping: ${SCRIPT}"
        EXIT_CODE=1
        continue
    fi

    log "➡️  Running: ${SCRIPT}"

    if ! "$PHP_PATH" "$SCRIPT" >> "$LOG_FILE" 2>&1; then
        log "❌ Failed: ${SCRIPT}"
        EXIT_CODE=1
    fi
done

log "Process finished."

# -----------------------------------------------------------------
# Interactive pause only when run manually in a terminal
# (there's no TTY in cron/CI, so this is skipped automatically)
# -----------------------------------------------------------------
if [ -t 0 ]; then
    read -r -p "Press ENTER to close..."
fi

exit $EXIT_CODE