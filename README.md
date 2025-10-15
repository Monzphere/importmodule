# Zabbix Module Importer

A secure web-based interface for importing Zabbix modules packaged as `.tar.gz` or `.tgz` archives.

![Zabbix Version](https://img.shields.io/badge/Zabbix-7.0+-blue)
![License](https://img.shields.io/badge/license-GPL%20v3-green)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple)

## Features

- 🔒 **Secure Upload** - Multiple layers of validation and security checks
- 👤 **Super Admin Only** - Restricted access for maximum security
- ✅ **Format Validation** - Validates file extensions, MIME types, and structure
- 📦 **Auto Extract** - Automatically extracts and installs modules
- 🎨 **Theme Support** - Compatible with Zabbix Dark and Blue themes
- 📊 **Progress Tracking** - Real-time upload progress indicator
- 🖱️ **Drag & Drop** - Modern file upload interface
- 📋 **Module Management** - View installed modules

## Requirements

- Zabbix 7.0 or higher
- PHP 8.0 or higher
- Write permissions on `/usr/share/zabbix/modules/`
- Write permissions on `/tmp/zabbix_module_import/`

## Installation

### Method 1: Manual Installation

1. Download the latest release:
```bash
wget https://github.com/Monzphere/importmodule/releases/latest/download/module-importer.tar.gz
```

2. Extract to Zabbix modules directory:
```bash
cd /usr/share/zabbix/modules/
tar -xzf module-importer.tar.gz
```

3. Set correct permissions:
```bash
chown -R www-data:www-data /usr/share/zabbix/modules/importmodule
chmod -R 755 /usr/share/zabbix/modules/importmodule
```

4. Enable the module in Zabbix:
   - Navigate to **Administration → General → Modules**
   - Click **Scan directory**
   - Find **Module Importer** and click **Enable**

### Method 2: Git Clone

```bash
cd /usr/share/zabbix/modules/
git clone https://github.com/yourusername/zabbix-module-importer.git importmodule
chown -R www-data:www-data importmodule
chmod -R 755 importmodule
```

Then enable in Zabbix as described above.

## Usage

### Accessing the Module

1. Log in to Zabbix as **Super Admin**
2. Navigate to **Administration → Import Module**
3. Upload your module package (`.tar.gz` or `.tgz`)

### Uploading a Module

1. Click **Choose File** or drag and drop your module archive
2. Validate the file meets requirements:
   - Format: `.tar.gz` or `.tgz`
   - Size: Maximum 50MB
   - Contains valid `manifest.json`
3. Click **Upload and Import**
4. Wait for validation and extraction
5. On success, enable the module in **Administration → General → Modules**

### Module Package Requirements

Your module package must contain:

```
module-name/
├── manifest.json          (required)
├── Module.php            (required)
├── actions/
│   └── CController*.php
├── views/
│   ├── *.php
│   └── js/
│       └── *.js.php
└── assets/
    ├── css/
    │   └── *.css
    └── js/
        └── *.js
```

#### manifest.json Example

```json
{
    "manifest_version": 2.0,
    "id": "my-module",
    "name": "My Module",
    "author": "Your Name",
    "version": "1.0.0",
    "namespace": "MyModule",
    "description": "Module description",
    "actions": {
        "my.action": {
            "class": "CControllerMyAction",
            "view": "my.view"
        }
    },
    "assets": {
        "css": ["style.css"],
        "js": ["script.js"]
    }
}
```

## Security Features

### Upload Validation
- File extension check (`.tar.gz`, `.tgz` only)
- MIME type validation
- File size limit (50MB)
- Archive integrity check

### Module Validation
- Required `manifest.json` presence
- Valid JSON structure
- Required fields validation
- Module ID format validation
- Namespace format validation
- Duplicate module prevention

### Path Security
- Path traversal prevention
- Directory name sanitization
- Safe extraction to temporary directory
- Secure file permissions (755/644)

### Access Control
- Super Admin role required
- Permission checks on every action
- No anonymous access

## Configuration

### PHP Configuration

Adjust these settings in `php.ini` if needed:

```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

Restart PHP-FPM after changes:
```bash
systemctl restart php8.2-fpm
```

### File Permissions

Ensure these directories are writable:
```bash
chmod 755 /usr/share/zabbix/modules/
chmod 755 /tmp/zabbix_module_import/
```

## Troubleshooting

### Upload Fails with "Permission Denied"

Check directory permissions:
```bash
ls -la /usr/share/zabbix/modules/
sudo chown -R www-data:www-data /usr/share/zabbix/modules/
```

### "Invalid File Type" Error

Verify the file is a valid gzipped tar archive:
```bash
file your-module.tar.gz
# Should output: gzip compressed data
```

### "Failed to Extract Archive" Error

This usually means PharData couldn't read the archive. Check:
- File is not corrupted
- Archive contains a valid directory structure
- PHP has PharData support enabled

### Module Not Appearing After Import

1. Check module was uploaded:
```bash
ls -la /usr/share/zabbix/modules/
```

2. Verify manifest.json is valid:
```bash
cat /usr/share/zabbix/modules/your-module/manifest.json | python3 -m json.tool
```

3. Check Zabbix logs:
```bash
tail -f /var/log/zabbix/zabbix_server.log
```

## Development

### Building a Module Package

```bash
cd your-module/
tar -czf my-module.tar.gz --exclude='.git' --exclude='.gitignore' ./*
```

### Testing

1. Create a test module package
2. Upload via the interface
3. Check module appears in Administration → General → Modules
4. Enable and test functionality

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## Support

- **Issues**: [GitHub Issues](https://github.com/yourusername/zabbix-module-importer/issues)
- **Documentation**: [Wiki](https://github.com/yourusername/zabbix-module-importer/wiki)
- **Zabbix Forum**: [Official Forum](https://www.zabbix.com/forum)

## Acknowledgments

- Built for Zabbix 7.0+ module system
- Follows Zabbix module development guidelines
- Compatible with Zabbix coding standards

## Security

If you discover a security vulnerability, please send an email to support@monzphere.com instead of using the issue tracker.

---

**Note**: This module requires Super Admin privileges and should only be used in trusted environments. Always review module code before installation.
