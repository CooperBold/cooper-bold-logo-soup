#!/usr/bin/env bash
# Build a clean plugin ZIP using .distignore (for manual wp.org submission).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PLUGIN_SLUG="balanced-logos"
DISTIGNORE="$ROOT/.distignore"
OUT_DIR="$ROOT/dist"
TMP_DIR=""

cleanup() {
	if [[ -n "$TMP_DIR" && -d "$TMP_DIR" ]]; then
		rm -rf "$TMP_DIR"
	fi
}
trap cleanup EXIT

if [[ ! -f "$DISTIGNORE" ]]; then
	echo "Missing .distignore at $DISTIGNORE" >&2
	exit 1
fi

VERSION="$(grep -m1 'Version:' "$ROOT/balanced-logos.php" | sed -E 's/.*Version:[[:space:]]*//' | tr -d '\r')"
if [[ -z "$VERSION" ]]; then
	echo "Could not read plugin version from balanced-logos.php" >&2
	exit 1
fi

TMP_DIR="$(mktemp -d)"
TARGET="$TMP_DIR/$PLUGIN_SLUG"
mkdir -p "$TARGET" "$OUT_DIR"

rsync -a \
	--exclude-from="$DISTIGNORE" \
	"$ROOT/" \
	"$TARGET/"

ZIP_PATH="$OUT_DIR/${PLUGIN_SLUG}-${VERSION}.zip"
rm -f "$ZIP_PATH"
(
	cd "$TMP_DIR"
	zip -rq "$ZIP_PATH" "$PLUGIN_SLUG"
)

echo "Created $ZIP_PATH"
echo "Contents:"
unzip -l "$ZIP_PATH" | head -20
