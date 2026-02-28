# laravel-verify-new-email development guide

For full documentation, see the README: https://github.com/protonemedia/laravel-verify-new-email#readme

## At a glance
Adds support for verifying **new** email addresses before replacing the old email on the user model.

## Local setup
- Install dependencies: `composer install`
- Keep the dev loop package-focused (avoid adding app-only scaffolding).

## Testing
- Run: `composer test` (preferred) or the repository’s configured test runner.
- Add regression tests for bug fixes.

## Notes & conventions
- Keep route/notification behavior stable; it's user-facing.
- Verify flows: unauthenticated verification, auto-login, expiration, and security tokens.
- Ensure compatibility across supported Laravel versions.
