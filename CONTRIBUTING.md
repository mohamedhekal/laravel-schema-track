# Contributing to Laravel SchemaTrack

Thank you for your interest in contributing to Laravel SchemaTrack! This document provides guidelines and information for contributors.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

- Use the GitHub issue tracker
- Include detailed steps to reproduce the bug
- Include your Laravel version, PHP version, and database type
- Include any error messages or stack traces

### Suggesting Enhancements

- Use the GitHub issue tracker
- Describe the enhancement in detail
- Explain why this enhancement would be useful
- Include any mockups or examples if applicable

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for your changes
5. Ensure all tests pass
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `vendor/bin/phpunit`
4. Run static analysis: `vendor/bin/phpstan analyse`
5. Run code style fixer: `vendor/bin/pint`

## Testing

- Write tests for all new features
- Ensure all existing tests pass
- Follow the existing test structure and naming conventions
- Use descriptive test method names

## Code Style

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Write clear, descriptive commit messages
- Add proper PHPDoc comments for public methods

## Documentation

- Update README.md if adding new features
- Add inline documentation for complex logic
- Update configuration documentation if adding new options

## Release Process

1. Update version in `composer.json`
2. Update CHANGELOG.md
3. Create a release tag
4. Publish to Packagist

## Questions?

If you have questions about contributing, please open an issue or contact the maintainers. 