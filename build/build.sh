#!/bin/bash
#
# Build script for pkg_accommodation_manager
# Creates installable ZIP packages for Joomla 5:
#   - Individual component and module ZIPs
#   - Unified package ZIP (pkg_) that installs everything at once
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get script directory and project root
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Build configuration
COMPONENT_NAME="com_accommodation_manager"
COMPONENT_VERSION=$(grep -o 'version>[^<]*' "$PROJECT_ROOT/accommodation_manager.xml" | head -1 | sed 's/version>//')
PKG_VERSION=$(grep -o 'version>[^<]*' "$PROJECT_ROOT/pkg_accommodation_manager.xml" | head -1 | sed 's/version>//')
DIST_DIR="$PROJECT_ROOT/dist"

echo -e "${GREEN}Building pkg_accommodation_manager v$PKG_VERSION${NC}"
echo "================================================"

# Clean previous build
echo -e "${YELLOW}Cleaning previous build...${NC}"
rm -rf "$DIST_DIR"
mkdir -p "$DIST_DIR"

# ──────────────────────────────────────────────
# 1. Build component ZIP
# ──────────────────────────────────────────────
echo ""
echo -e "${GREEN}[1/3] Building component...${NC}"
echo "------------------------------------------------"

BUILD_DIR="$PROJECT_ROOT/build/package"
COMP_ZIP_NAME="${COMPONENT_NAME}-${COMPONENT_VERSION}.zip"

rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"

# Copy manifest and script to root of package
echo -e "${YELLOW}  Copying manifest and install script...${NC}"
cp "$PROJECT_ROOT/accommodation_manager.xml" "$BUILD_DIR/"
cp "$PROJECT_ROOT/script.php" "$BUILD_DIR/"

# Copy administrator files
echo -e "${YELLOW}  Copying administrator files...${NC}"
mkdir -p "$BUILD_DIR/administrator"
cp "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/access.xml" "$BUILD_DIR/administrator/"
cp "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/config.xml" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/forms" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/src" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/tmpl" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/services" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/sql" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/assets" "$BUILD_DIR/administrator/"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/layouts" "$BUILD_DIR/administrator/"

# Copy administrator languages
echo -e "${YELLOW}  Copying administrator languages...${NC}"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/language" "$BUILD_DIR/administrator/"

# Copy site files
echo -e "${YELLOW}  Copying site files...${NC}"
mkdir -p "$BUILD_DIR/site"
cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/src" "$BUILD_DIR/site/"
cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/tmpl" "$BUILD_DIR/site/"
cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/layouts" "$BUILD_DIR/site/"

# Copy site languages
if [ -d "$PROJECT_ROOT/src/components/$COMPONENT_NAME/language" ]; then
    echo -e "${YELLOW}  Copying site languages...${NC}"
    cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/language" "$BUILD_DIR/site/"
fi

# Copy media files
echo -e "${YELLOW}  Copying media files...${NC}"
mkdir -p "$BUILD_DIR/media"
cp -r "$PROJECT_ROOT/src/media/$COMPONENT_NAME/"* "$BUILD_DIR/media/"

# Clean up
find "$BUILD_DIR" -name ".DS_Store" -delete 2>/dev/null || true
find "$BUILD_DIR" -name "*.bak" -delete 2>/dev/null || true
find "$BUILD_DIR" -name "index.html" -delete 2>/dev/null || true
rm -f "$BUILD_DIR/administrator/accommodation_manager.xml" 2>/dev/null || true
rm -f "$BUILD_DIR/administrator/script.php" 2>/dev/null || true

# Create component ZIP
cd "$BUILD_DIR"
zip -rq "$DIST_DIR/$COMP_ZIP_NAME" .
rm -rf "$BUILD_DIR"

echo -e "  ${GREEN}✓${NC} $COMP_ZIP_NAME ($(du -h "$DIST_DIR/$COMP_ZIP_NAME" | cut -f1))"

# ──────────────────────────────────────────────
# 2. Build module ZIPs
# ──────────────────────────────────────────────
echo ""
echo -e "${GREEN}[2/3] Building modules...${NC}"
echo "------------------------------------------------"

MODULES_SRC="$PROJECT_ROOT/src/modules"

for MOD_DIR in "$MODULES_SRC"/mod_*; do
    if [ -d "$MOD_DIR" ]; then
        MOD_NAME=$(basename "$MOD_DIR")
        MOD_VERSION=$(grep -o 'version>[^<]*' "$MOD_DIR/$MOD_NAME.xml" | head -1 | sed 's/version>//')
        MOD_BUILD_DIR="$PROJECT_ROOT/build/module_package"
        MOD_ZIP_NAME="${MOD_NAME}-${MOD_VERSION}.zip"

        rm -rf "$MOD_BUILD_DIR"
        mkdir -p "$MOD_BUILD_DIR"

        cp -r "$MOD_DIR/"* "$MOD_BUILD_DIR/"

        # Clean up
        find "$MOD_BUILD_DIR" -name ".DS_Store" -delete 2>/dev/null || true
        find "$MOD_BUILD_DIR" -name "*.bak" -delete 2>/dev/null || true

        cd "$MOD_BUILD_DIR"
        zip -rq "$DIST_DIR/$MOD_ZIP_NAME" .

        rm -rf "$MOD_BUILD_DIR"

        echo -e "  ${GREEN}✓${NC} $MOD_ZIP_NAME ($(du -h "$DIST_DIR/$MOD_ZIP_NAME" | cut -f1))"
    fi
done

# ──────────────────────────────────────────────
# 3. Build unified package ZIP
# ──────────────────────────────────────────────
echo ""
echo -e "${GREEN}[3/3] Building package...${NC}"
echo "------------------------------------------------"

PKG_BUILD_DIR="$PROJECT_ROOT/build/pkg_package"
PKG_ZIP_NAME="pkg_accommodation_manager-${PKG_VERSION}.zip"

rm -rf "$PKG_BUILD_DIR"
mkdir -p "$PKG_BUILD_DIR"

# Copy package manifest
cp "$PROJECT_ROOT/pkg_accommodation_manager.xml" "$PKG_BUILD_DIR/"

# Copy package language files
cp -r "$PROJECT_ROOT/language" "$PKG_BUILD_DIR/"

# Copy all individual ZIPs into the package
cp "$DIST_DIR/$COMP_ZIP_NAME" "$PKG_BUILD_DIR/"
for ZIP_FILE in "$DIST_DIR"/mod_*.zip; do
    if [ -f "$ZIP_FILE" ]; then
        cp "$ZIP_FILE" "$PKG_BUILD_DIR/"
    fi
done

# Clean up
find "$PKG_BUILD_DIR" -name ".DS_Store" -delete 2>/dev/null || true

# Create package ZIP
cd "$PKG_BUILD_DIR"
zip -rq "$DIST_DIR/$PKG_ZIP_NAME" .

rm -rf "$PKG_BUILD_DIR"

echo -e "  ${GREEN}✓${NC} $PKG_ZIP_NAME ($(du -h "$DIST_DIR/$PKG_ZIP_NAME" | cut -f1))"

# ──────────────────────────────────────────────
# Summary
# ──────────────────────────────────────────────
echo ""
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}Build complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo "Packages in $DIST_DIR/:"
for f in "$DIST_DIR"/*.zip; do
    echo -e "  ${YELLOW}$(basename "$f")${NC} ($(du -h "$f" | cut -f1))"
done
echo ""
echo "To install the full package:"
echo "1. Go to Joomla Admin → System → Install → Extensions"
echo -e "2. Upload ${YELLOW}$PKG_ZIP_NAME${NC}"
echo ""
