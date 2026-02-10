#!/bin/bash
#
# Build script for com_accommodation_manager
# Creates an installable ZIP package for Joomla 5
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
VERSION=$(grep -o 'version>[^<]*' "$PROJECT_ROOT/accommodation_manager.xml" | head -1 | sed 's/version>//')
BUILD_DIR="$PROJECT_ROOT/build/package"
DIST_DIR="$PROJECT_ROOT/dist"
ZIP_NAME="${COMPONENT_NAME}-${VERSION}.zip"

echo -e "${GREEN}Building $COMPONENT_NAME v$VERSION${NC}"
echo "================================================"

# Clean previous build
echo -e "${YELLOW}Cleaning previous build...${NC}"
rm -rf "$BUILD_DIR"
rm -rf "$DIST_DIR"
mkdir -p "$BUILD_DIR"
mkdir -p "$DIST_DIR"

# Copy manifest and script to root of package
echo -e "${YELLOW}Copying manifest and install script...${NC}"
cp "$PROJECT_ROOT/accommodation_manager.xml" "$BUILD_DIR/"
cp "$PROJECT_ROOT/script.php" "$BUILD_DIR/"

# Copy administrator files
echo -e "${YELLOW}Copying administrator files...${NC}"
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

# Copy administrator languages (inside component folder, not global)
echo -e "${YELLOW}Copying administrator languages...${NC}"
cp -r "$PROJECT_ROOT/src/administrator/components/$COMPONENT_NAME/language" "$BUILD_DIR/administrator/"

# Copy site files
echo -e "${YELLOW}Copying site files...${NC}"
mkdir -p "$BUILD_DIR/site"
cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/src" "$BUILD_DIR/site/"
cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/tmpl" "$BUILD_DIR/site/"

# Copy site languages (inside component folder, not global)
echo -e "${YELLOW}Copying site languages...${NC}"
if [ -d "$PROJECT_ROOT/src/components/$COMPONENT_NAME/language" ]; then
    cp -r "$PROJECT_ROOT/src/components/$COMPONENT_NAME/language" "$BUILD_DIR/site/"
fi

# Copy media files
echo -e "${YELLOW}Copying media files...${NC}"
mkdir -p "$BUILD_DIR/media"
cp -r "$PROJECT_ROOT/src/media/$COMPONENT_NAME/"* "$BUILD_DIR/media/"

# Copy root language files (if exist)
if [ -d "$PROJECT_ROOT/language" ]; then
    echo -e "${YELLOW}Copying root language files...${NC}"
    cp -r "$PROJECT_ROOT/language" "$BUILD_DIR/"
fi

# Remove unwanted files
echo -e "${YELLOW}Cleaning up...${NC}"
find "$BUILD_DIR" -name ".DS_Store" -delete 2>/dev/null || true
find "$BUILD_DIR" -name "*.bak" -delete 2>/dev/null || true
find "$BUILD_DIR" -name "index.html" -delete 2>/dev/null || true

# Remove duplicate manifest from admin folder if exists
rm -f "$BUILD_DIR/administrator/accommodation_manager.xml" 2>/dev/null || true
rm -f "$BUILD_DIR/administrator/script.php" 2>/dev/null || true

# Create ZIP
echo -e "${YELLOW}Creating ZIP package...${NC}"
cd "$BUILD_DIR"
zip -rq "$DIST_DIR/$ZIP_NAME" .

# Cleanup build directory
rm -rf "$BUILD_DIR"

# Show result
echo ""
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}Build complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo -e "Package: ${YELLOW}$DIST_DIR/$ZIP_NAME${NC}"
echo -e "Size: $(du -h "$DIST_DIR/$ZIP_NAME" | cut -f1)"
echo ""
echo "To install:"
echo "1. Go to Joomla Admin → System → Install → Extensions"
echo "2. Upload the ZIP file"
echo ""

# Build modules
echo -e "${GREEN}Building modules...${NC}"
echo "================================================"

MODULES_SRC="$PROJECT_ROOT/src/modules"

for MOD_DIR in "$MODULES_SRC"/mod_*; do
    if [ -d "$MOD_DIR" ]; then
        MOD_NAME=$(basename "$MOD_DIR")
        MOD_BUILD_DIR="$PROJECT_ROOT/build/module_package"
        MOD_ZIP_NAME="${MOD_NAME}-1.0.0.zip"

        echo -e "${YELLOW}Building $MOD_NAME...${NC}"

        rm -rf "$MOD_BUILD_DIR"
        mkdir -p "$MOD_BUILD_DIR"

        cp -r "$MOD_DIR/"* "$MOD_BUILD_DIR/"

        # Clean up
        find "$MOD_BUILD_DIR" -name ".DS_Store" -delete 2>/dev/null || true
        find "$MOD_BUILD_DIR" -name "*.bak" -delete 2>/dev/null || true

        cd "$MOD_BUILD_DIR"
        zip -rq "$DIST_DIR/$MOD_ZIP_NAME" .

        rm -rf "$MOD_BUILD_DIR"

        echo -e "  Package: ${YELLOW}$DIST_DIR/$MOD_ZIP_NAME${NC}"
        echo -e "  Size: $(du -h "$DIST_DIR/$MOD_ZIP_NAME" | cut -f1)"
    fi
done

echo ""
echo -e "${GREEN}All packages built!${NC}"
echo ""
