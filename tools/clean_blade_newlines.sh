#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT="$PWD"
echo "Backup and cleanup of literal \\n sequences in resources/views"
mkdir -p .blade_backup
# Copy then replace \n and \r in all blade.php files
while IFS= read -r -d '' file; do
  echo "Backing up $file"
  cp "$file" ".blade_backup/$(echo "$file" | sed 's@/@_@g')"
  sed -i "s/\\\\n//g" "$file"
  sed -i "s/\\\\r//g" "$file"
done < <(find resources/views -type f -name '*.blade.php' -print0)

echo "Cleanup done. Listing modified files (git diff if available):"
if command -v git >/dev/null 2>&1; then
  git --no-pager diff --name-only
else
  echo "git not available"
fi
