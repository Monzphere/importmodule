#!/bin/bash
# Build script for Zabbix Module Importer release package
# Usage: ./build-release.sh [version]

set -e

VERSION=${1:-"1.0.0"}
PACKAGE_NAME="importmodule"
BUILD_DIR="build"
RELEASE_NAME="${PACKAGE_NAME}-${VERSION}"

echo "==================================="
echo "Building Zabbix Module Importer"
echo "Version: ${VERSION}"
echo "==================================="
echo ""

# Clean previous builds
echo "[1/5] Cleaning previous builds..."
rm -rf ${BUILD_DIR}
mkdir -p ${BUILD_DIR}/${PACKAGE_NAME}

# Copy files
echo "[2/5] Copying module files..."
cp -r actions ${BUILD_DIR}/${PACKAGE_NAME}/
cp -r assets ${BUILD_DIR}/${PACKAGE_NAME}/
cp -r views ${BUILD_DIR}/${PACKAGE_NAME}/
cp manifest.json ${BUILD_DIR}/${PACKAGE_NAME}/
cp Module.php ${BUILD_DIR}/${PACKAGE_NAME}/
cp README.md ${BUILD_DIR}/${PACKAGE_NAME}/
cp LICENSE ${BUILD_DIR}/${PACKAGE_NAME}/
cp CHANGELOG.md ${BUILD_DIR}/${PACKAGE_NAME}/
cp CONTRIBUTING.md ${BUILD_DIR}/${PACKAGE_NAME}/
cp RELEASE_NOTES.md ${BUILD_DIR}/${PACKAGE_NAME}/
cp .gitignore ${BUILD_DIR}/${PACKAGE_NAME}/

# Set correct permissions
echo "[3/5] Setting permissions..."
find ${BUILD_DIR}/${PACKAGE_NAME} -type d -exec chmod 755 {} \;
find ${BUILD_DIR}/${PACKAGE_NAME} -type f -exec chmod 644 {} \;

# Create tarball
echo "[4/5] Creating release package..."
cd ${BUILD_DIR}
tar -czf ${RELEASE_NAME}.tar.gz ${PACKAGE_NAME}/
cd ..

# Calculate checksums
echo "[5/5] Generating checksums..."
cd ${BUILD_DIR}
sha256sum ${RELEASE_NAME}.tar.gz > ${RELEASE_NAME}.tar.gz.sha256
md5sum ${RELEASE_NAME}.tar.gz > ${RELEASE_NAME}.tar.gz.md5
cd ..

# Display results
echo ""
echo "==================================="
echo "Build completed successfully!"
echo "==================================="
echo ""
echo "Release package:"
echo "  ${BUILD_DIR}/${RELEASE_NAME}.tar.gz"
echo ""
echo "Checksums:"
echo "  SHA256: ${BUILD_DIR}/${RELEASE_NAME}.tar.gz.sha256"
echo "  MD5:    ${BUILD_DIR}/${RELEASE_NAME}.tar.gz.md5"
echo ""
echo "Package size:"
ls -lh ${BUILD_DIR}/${RELEASE_NAME}.tar.gz | awk '{print "  " $5}'
echo ""
echo "Checksums:"
cat ${BUILD_DIR}/${RELEASE_NAME}.tar.gz.sha256
echo ""
echo "To create a GitHub release:"
echo "1. git tag -a v${VERSION} -m 'Release version ${VERSION}'"
echo "2. git push origin v${VERSION}"
echo "3. Upload ${BUILD_DIR}/${RELEASE_NAME}.tar.gz to GitHub release"
echo ""
