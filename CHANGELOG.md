# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-15

### Added
- Initial release of Zabbix Module Importer
- Secure file upload functionality for `.tar.gz` and `.tgz` modules
- Multiple validation layers (extension, MIME type, size, structure)
- Automatic module extraction and installation
- Super Admin only access control
- Real-time upload progress indicator
- Drag and drop file upload support
- Module listing interface
- Dark theme and Blue theme support
- Comprehensive error handling and user feedback
- Path traversal prevention
- Directory name sanitization
- Automatic permission setting (755/644)
- Temporary directory cleanup
- manifest.json validation
- Module ID and namespace format validation
- Duplicate module prevention

### Security
- Super Admin role requirement
- CSRF protection disabled for upload (uses permission check)
- File extension whitelist
- MIME type validation
- Maximum file size enforcement (50MB)
- Path sanitization
- Safe archive extraction
- Permission management

### Technical Details
- Compatible with Zabbix 7.0+
- Requires PHP 8.0+
- Uses PharData for archive handling
- JSON response for upload operations
- Follows Zabbix module development standards

---

## Future Releases

### [Unreleased]

#### Planned Features
- Module update functionality
- Backup before installation
- Module dependency checking
- Rollback capability
- Module information preview before installation
- Batch module import
- Import history log
- Module signature verification
- Compressed backup of replaced modules

---

For detailed information about each release, visit:
https://github.com/yourusername/zabbix-module-importer/releases
