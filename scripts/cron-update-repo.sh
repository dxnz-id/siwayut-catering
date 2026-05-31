#!/bin/sh
set -e

cd /app || exit 1
if [ ! -d .git ]; then
  echo "No git repository found in /app"
  exit 0
fi

branch=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo main)

echo "[repo-updater] checking branch $branch"
git fetch origin "$branch"
git pull --ff-only origin "$branch"

# npm install
# npm run css:build

echo "[repo-updater] update completed at $(date)"
