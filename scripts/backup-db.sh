#!/usr/bin/env bash
set -euo pipefail

DB="${SIRAT_DB:-/home/ubuntu/sirat/shared/database.sqlite}"
DEST="${SIRAT_BACKUP_DIR:-/home/ubuntu/sirat/shared/backup}"
KEEP_DAYS=7

mkdir -p "$DEST"
sqlite3 "$DB" ".backup '$DEST/database-$(date +%Y%m%d-%H%M).sqlite'"
find "$DEST" -name 'database-*.sqlite' -mtime +$((KEEP_DAYS - 1)) -delete
