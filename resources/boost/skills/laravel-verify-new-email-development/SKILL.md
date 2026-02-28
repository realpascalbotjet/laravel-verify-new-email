---
name: laravel-verify-new-email-development
description: Build and work with protonemedia/laravel-verify-new-email features including verifying changed email addresses, managing pending emails, and customizing the verification flow.
license: MIT
metadata:
  author: ProtoneMedia
---

# Laravel Verify New Email Development

## Overview
Use protonemedia/laravel-verify-new-email to verify a user's new email address before updating the model. The current email stays active until the new one is confirmed via a signed verification link.

## When to Activate
- Activate when working with email-change verification, pending emails, or verification customization in Laravel.
- Activate when code references `MustVerifyNewEmail`, `PendingUserEmail`, or the `verify-new-email` config.
- Activate when the user wants to add, customize, or debug email-change verification on an Eloquent user model.

## Scope
- In scope: model setup, requesting email changes, pending-email queries, verification flow customization, custom mailables, testing patterns.
- Out of scope: modifying this package's internal source code unless the user explicitly says they are contributing to the package.

## Workflow
1. Identify the task (model setup, requesting a change, customizing mail, debugging, tests, etc.).
2. Read `references/laravel-verify-new-email-guide.md` and focus on the relevant section.
3. Apply the patterns from the reference, keeping code minimal and Laravel-native.

## Core Concepts

### Model Setup
Every user model that should support verified email changes must implement `MustVerifyEmail` and use the `MustVerifyNewEmail` trait:

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyNewEmail;
}
```

### Requesting an Email Change
```php
$user->newEmail('new@example.com');
```

### Querying Pending State
```php
$pending = $user->getPendingEmail();          // returns pending address or null
$user->resendPendingEmailVerificationMail();  // resend verification
$user->clearPendingEmail();                   // cancel pending change
```

### Custom Mailables
```php
// config/verify-new-email.php
return [
    'mailable_for_first_verification' => \ProtoneMedia\LaravelVerifyNewEmail\Mail\VerifyFirstEmail::class,
    'mailable_for_new_email' => \ProtoneMedia\LaravelVerifyNewEmail\Mail\VerifyNewEmail::class,
];
```

## Do and Don't

Do:
- Always implement the `MustVerifyEmail` interface alongside the `MustVerifyNewEmail` trait.
- Publish and run the migration to create the `pending_user_emails` table before using the package.
- Use `->newEmail($email)` to initiate a change — never update the `email` column directly when verification is needed.
- Use `getPendingEmail()` to check for an in-progress change before showing UI state.

Don't:
- Don't forget to run `php artisan vendor:publish --provider="ProtoneMedia\LaravelVerifyNewEmail\ServiceProvider"` before migrating.
- Don't invent undocumented methods or options; stick to the reference guide.
- Don't bypass the verification flow by setting `email` directly on the model when using this package.

## References
- `references/laravel-verify-new-email-guide.md`
