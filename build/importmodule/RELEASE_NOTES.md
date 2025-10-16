# Release Notes - v1.0.0 (Zabbix 7.0)

**Release Date**: January 15, 2025
**Zabbix Compatibility**: 7.0.x
**PHP Requirement**: 8.0+

## 🎉 Initial Release

This is the first stable release of Zabbix Module Importer, providing a secure web-based interface for importing Zabbix modules directly through the administration panel.

## ✨ Features

### Core Functionality
- **Secure Module Upload** - Upload `.tar.gz` or `.tgz` module packages
- **Automatic Installation** - Extracts and installs modules automatically
- **Module Management** - View list of installed modules
- **Progress Tracking** - Real-time upload progress indicator
- **Drag & Drop Support** - Modern file upload interface

### Security Features
- **Super Admin Only** - Restricted to Super Admin users
- **Multi-Layer Validation**:
  - File extension validation
  - MIME type verification
  - File size limits (50MB max)
  - Archive structure validation
  - manifest.json validation
  - Module ID format checking
  - Namespace format validation
- **Path Security**:
  - Path traversal prevention
  - Directory name sanitization
  - Safe temporary extraction
  - Automatic permission management (755/644)

### User Interface
- **Theme Support** - Compatible with Zabbix Dark and Blue themes
- **Responsive Design** - Works on desktop and tablet devices
- **Error Handling** - Clear, descriptive error messages
- **User Feedback** - Success/failure notifications

### Module Validation
- Validates required `manifest.json` file
- Checks for required fields (id, name, version, namespace)
- Prevents duplicate module installation
- Validates module structure

## 📋 Requirements

- **Zabbix**: 7.0 or higher
- **PHP**: 8.0 or higher
- **PHP Extensions**:
  - PharData (for tar.gz extraction)
  - JSON
  - FileInfo
- **Permissions**: Write access to:
  - `/usr/share/zabbix/modules/`
  - `/tmp/zabbix_module_import/`

## 📦 Installation

### Quick Install

```bash
# Download
wget https://github.com/Monzphere/importmodule/releases/download/v1.0.0/importmodule.tar.gz

# Extract
cd /usr/share/zabbix/modules/
tar -xzf importmodule.tar.gz

# Set permissions
chown -R www-data:www-data importmodule
chmod -R 755 importmodule

# Enable in Zabbix
# Administration → General → Modules → Scan directory → Enable "Module Importer"
```

### Git Clone

```bash
cd /usr/share/zabbix/modules/
git clone -b v1.0.0 https://github.com/Monzphere/importmodule.git
chown -R www-data:www-data importmodule
chmod -R 755 importmodule
```

## 🚀 Usage

1. **Access**: Administration → Import Module
2. **Upload**: Select or drag & drop your module package
3. **Validate**: System checks file format and structure
4. **Install**: Module is extracted and installed
5. **Enable**: Go to Administration → General → Modules to enable

## 📝 Module Package Format

Your module must include:
- `manifest.json` (required)
- `Module.php` (required)
- Valid module structure following Zabbix standards

Example `manifest.json`:
```json
{
    "manifest_version": 2.0,
    "id": "my-module",
    "name": "My Module",
    "author": "Your Name",
    "version": "1.0.0",
    "namespace": "MyModule",
    "description": "Module description",
    "actions": {...}
}
```

## 🐛 Known Issues

None reported for v1.0.0

## 🔄 Upgrade Notes

This is the initial release. No upgrade path needed.

## ⚠️ Important Notes

1. **Super Admin Required** - Only Super Admin users can access this module
2. **Trusted Sources Only** - Only upload modules from trusted sources
3. **Code Review** - Review module code before installation when possible
4. **Backup First** - Always backup your Zabbix installation before installing new modules
5. **Testing** - Test modules in a staging environment first

## 🆘 Troubleshooting

### Common Issues

**Upload fails with "Permission denied"**
```bash
sudo chown -R www-data:www-data /usr/share/zabbix/modules/
sudo chmod 755 /usr/share/zabbix/modules/
```

**"Failed to extract archive" error**
- Verify file is valid tar.gz: `file your-module.tar.gz`
- Check PHP PharData extension is enabled: `php -m | grep Phar`

**Module not appearing after upload**
- Check logs: `tail -f /var/log/zabbix/zabbix_server.log`
- Verify manifest.json is valid JSON
- Check module directory exists and has correct permissions

## 📊 Technical Details

### File Structure
```
importmodule/
├── actions/
│   ├── CControllerModuleImport.php
│   └── CControllerModuleImportUpload.php
├── assets/
│   ├── css/
│   └── js/
├── views/
│   ├── js/
│   └── module.import.view.php
├── Module.php
└── manifest.json
```

### Security Measures
- CSRF protection on view
- Permission checks on every action
- Input validation and sanitization
- Path traversal prevention
- Safe file extraction
- Automatic cleanup on errors

## 🤝 Contributing

Contributions welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## 📄 License

GNU General Public License v3.0 - See [LICENSE](LICENSE) file

## 🙏 Acknowledgments

- Built for Zabbix 7.0 module system
- Follows Zabbix development guidelines
- Compatible with Zabbix coding standards

## 📞 Support

- **GitHub Issues**: https://github.com/Monzphere/importmodule/issues
- **Documentation**: https://github.com/Monzphere/importmodule/wiki
- **Discussions**: https://github.com/Monzphere/importmodule/discussions

---

**MonZphere** - Advanced Zabbix Solutions
Website: https://monzphere.com
