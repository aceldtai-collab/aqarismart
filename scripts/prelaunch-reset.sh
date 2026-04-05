#!/bin/bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "======================================================"
echo " Aqari Smart - Prelaunch Reset"
echo " $(date '+%Y-%m-%d %H:%M:%S')"
echo "======================================================"
echo ""
echo "This workflow will reset the database and reseed the"
echo "production-ready Iraq dataset."
echo ""

cd "$APP_DIR"
php artisan app:prelaunch-reset "$@"
