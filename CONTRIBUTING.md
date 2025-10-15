# Contributing to Zabbix Module Importer

Thank you for considering contributing to Zabbix Module Importer! This document outlines the guidelines for contributing to this project.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and collaborative environment.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce**
- **Expected vs actual behavior**
- **Zabbix version**
- **PHP version**
- **Browser and OS** (if UI-related)
- **Error messages or logs**
- **Screenshots** (if applicable)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Clear and descriptive title**
- **Detailed description** of the suggested enhancement
- **Use case** explaining why this would be useful
- **Possible implementation** (if you have ideas)

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow the coding standards** (see below)
3. **Add tests** if applicable
4. **Update documentation** if needed
5. **Ensure all tests pass**
6. **Write clear commit messages**
7. **Submit your pull request**

## Development Setup

```bash
# Clone your fork
git clone https://github.com/monzphere/zabbix-module-importer.git
cd zabbix-module-importer

# Set up development environment
cp -r . /usr/share/zabbix/modules/importmodule/
chown -R www-data:www-data /usr/share/zabbix/modules/importmodule
chmod -R 755 /usr/share/zabbix/modules/importmodule

# Enable module in Zabbix
# Administration → General → Modules → Scan directory → Enable
```

## Coding Standards

### PHP

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use strict types: `declare(strict_types = 0);`
- Add PHPDoc blocks for all classes and methods
- Use type hints for parameters and return types
- Keep functions focused and small
- Use meaningful variable and function names

### JavaScript

- Use modern ES6+ syntax
- Use `const` and `let`, avoid `var`
- Add JSDoc comments for complex functions
- Use arrow functions where appropriate
- Follow consistent indentation (tabs)

### CSS

- Use BEM naming convention
- Organize by component
- Support both dark and blue themes
- Use CSS custom properties where possible

### File Structure

```
actions/            # PHP controllers
  CController*.php  # One controller per file
views/             # PHP views
  *.php            # View files
  js/              # JavaScript views
    *.js.php       # JS files with PHP templating
assets/            # Static assets
  css/             # Stylesheets
    dark-theme.css
    blue-theme.css
  js/              # JavaScript
    *.js           # Pure JS files
```

### Commit Messages

Use conventional commits format:

```
type(scope): subject

body (optional)

footer (optional)
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

Examples:
```
feat(upload): add module signature verification

fix(extraction): handle nested directory structures

docs(readme): update installation instructions
```

## Security

### Reporting Security Issues

**DO NOT** report security vulnerabilities using public GitHub issues.

Instead, send an email to security@example.com with:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if you have one)

### Security Guidelines

When contributing code:

- **Never trust user input** - Always validate and sanitize
- **Use parameterized queries** - Prevent SQL injection
- **Validate file uploads** - Check extensions, MIME types, and content
- **Prevent path traversal** - Sanitize file paths
- **Check permissions** - Verify user has required access
- **Use HTTPS** - For any external requests
- **Log security events** - For audit trails

## Testing

### Manual Testing

1. Install module in clean Zabbix 7.0 instance
2. Test with valid module package
3. Test with invalid packages (wrong format, missing files, etc.)
4. Test permission restrictions
5. Test UI in both themes
6. Test error handling

### Test Cases to Cover

- Valid module upload and installation
- Invalid file extension rejection
- Invalid MIME type rejection
- Oversized file rejection
- Missing manifest.json rejection
- Invalid manifest.json rejection
- Duplicate module prevention
- Permission check (non-super-admin)
- Path traversal attempts
- Special characters in filenames

## Documentation

### Code Comments

- Add comments for complex logic
- Explain "why" not "what"
- Keep comments up-to-date
- Use English for all comments

### README Updates

When adding features, update:
- Feature list
- Usage instructions
- Configuration options
- Troubleshooting section

### Changelog

Follow [Keep a Changelog](https://keepachangelog.com/) format:

```markdown
## [Version] - YYYY-MM-DD

### Added
- New features

### Changed
- Changes in existing functionality

### Deprecated
- Soon-to-be removed features

### Removed
- Removed features

### Fixed
- Bug fixes

### Security
- Security fixes
```

## Questions?

Feel free to open an issue for:
- Questions about contributing
- Clarifications on guidelines
- Discussion of new features

## License

By contributing, you agree that your contributions will be licensed under the GNU General Public License v3.0.

---

Thank you for contributing to Zabbix Module Importer!
