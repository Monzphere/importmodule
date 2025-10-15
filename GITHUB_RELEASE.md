# GitHub Release Instructions

## Creating Release v1.0.0 for Zabbix 7.0

### Step 1: Build Release Package

```bash
cd /usr/share/zabbix/modules/importmodule
./build-release.sh 1.0.0
```

This will create:
- `build/importmodule-1.0.0.tar.gz` (release package)
- `build/importmodule-1.0.0.tar.gz.sha256` (checksum)
- `build/importmodule-1.0.0.tar.gz.md5` (checksum)

### Step 2: Initialize Git Repository

```bash
cd /usr/share/zabbix/modules/importmodule

# Initialize repository
git init

# Add all files
git add .

# Create initial commit
git commit -m "feat: initial release of Module Importer v1.0.0 for Zabbix 7.0

- Secure module upload functionality
- Multi-layer validation system
- Automatic extraction and installation
- Super Admin only access
- Theme support (Dark/Blue)
- Real-time progress tracking
- Comprehensive security measures"

# Add remote
git remote add origin https://github.com/Monzphere/importmodule.git

# Push to main branch
git branch -M main
git push -u origin main
```

### Step 3: Create Git Tag

```bash
git tag -a v1.0.0 -m "Release v1.0.0 for Zabbix 7.0

Initial stable release of Zabbix Module Importer

Features:
- Secure module upload for .tar.gz packages
- Multi-layer validation
- Automatic installation
- Super Admin only access
- Theme support
- Progress tracking

Compatible with Zabbix 7.0.x"

git push origin v1.0.0
```

### Step 4: Create GitHub Release

1. Go to https://github.com/Monzphere/importmodule/releases/new

2. Fill in the release form:

**Tag version**: `v1.0.0`

**Release title**: `v1.0.0 - Zabbix 7.0 Module Importer`

**Description**:
```markdown
## 🎉 Initial Release - Zabbix 7.0

First stable release of **Zabbix Module Importer** - a secure web-based interface for importing Zabbix modules.

### ✨ Features

- 🔒 **Secure Upload** - Multiple validation layers
- 👤 **Super Admin Only** - Restricted access
- ✅ **Format Validation** - Extension, MIME type, and structure checks
- 📦 **Auto Extract** - Automatic module installation
- 🎨 **Theme Support** - Dark and Blue themes
- 📊 **Progress Tracking** - Real-time upload indicator
- 🖱️ **Drag & Drop** - Modern interface

### 📋 Requirements

- Zabbix 7.0 or higher
- PHP 8.0+
- Write permissions on `/usr/share/zabbix/modules/`

### 📦 Installation

```bash
# Download and extract
wget https://github.com/Monzphere/importmodule/releases/download/v1.0.0/importmodule.tar.gz
cd /usr/share/zabbix/modules/
tar -xzf importmodule.tar.gz

# Set permissions
chown -R www-data:www-data importmodule
chmod -R 755 importmodule

# Enable in Zabbix
# Administration → General → Modules → Scan directory → Enable
```

### 🔒 Security

- Super Admin role required
- File extension whitelist
- MIME type validation
- Path traversal prevention
- Safe extraction
- Automatic permission management

### 📖 Documentation

- [README.md](https://github.com/Monzphere/importmodule/blob/main/README.md)
- [CHANGELOG.md](https://github.com/Monzphere/importmodule/blob/main/CHANGELOG.md)
- [CONTRIBUTING.md](https://github.com/Monzphere/importmodule/blob/main/CONTRIBUTING.md)

### 🐛 Known Issues

None reported.

### 📞 Support

- [Issues](https://github.com/Monzphere/importmodule/issues)
- [Discussions](https://github.com/Monzphere/importmodule/discussions)
- [Wiki](https://github.com/Monzphere/importmodule/wiki)

### 🙏 Acknowledgments

Built for Zabbix 7.0 by **MonZphere**

---

**Full Changelog**: https://github.com/Monzphere/importmodule/blob/main/CHANGELOG.md
```

3. Upload assets:
   - `build/importmodule-1.0.0.tar.gz`
   - `build/importmodule-1.0.0.tar.gz.sha256`
   - `build/importmodule-1.0.0.tar.gz.md5`

4. Check "Set as the latest release"

5. Click "Publish release"

### Step 5: Update Package for Installation

After release is published, users can install with:

```bash
wget https://github.com/Monzphere/importmodule/releases/download/v1.0.0/importmodule.tar.gz
```

### Step 6: Verify Installation

Test the installation process:

```bash
# Download
wget https://github.com/Monzphere/importmodule/releases/download/v1.0.0/importmodule.tar.gz

# Verify checksum
wget https://github.com/Monzphere/importmodule/releases/download/v1.0.0/importmodule-1.0.0.tar.gz.sha256
sha256sum -c importmodule-1.0.0.tar.gz.sha256

# Extract
cd /usr/share/zabbix/modules/
tar -xzf importmodule.tar.gz

# Set permissions
chown -R www-data:www-data importmodule
chmod -R 755 importmodule

# Enable in Zabbix and test
```

### Step 7: Announce Release

1. **GitHub Discussions**: Create announcement post
2. **Zabbix Forum**: Post in Zabbix Modules section
3. **Social Media**: Tweet/LinkedIn announcement
4. **Documentation**: Update wiki with installation guide

### Release Checklist

- [ ] Code reviewed and tested
- [ ] All comments in English
- [ ] Documentation complete (README, CHANGELOG, etc.)
- [ ] Build script tested
- [ ] Package created successfully
- [ ] Checksums generated
- [ ] Git repository initialized
- [ ] Code pushed to GitHub
- [ ] Tag created and pushed
- [ ] GitHub release created
- [ ] Assets uploaded
- [ ] Installation tested
- [ ] Documentation verified
- [ ] Release announced

---

**Note**: Keep this file for reference when creating future releases.
