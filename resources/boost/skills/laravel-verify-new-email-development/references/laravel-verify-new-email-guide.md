# Laravel Verify New Email Reference

Complete reference for `protonemedia/laravel-verify-new-email`.

Primary docs: https://github.com/protonemedia/laravel-verify-new-email#readme

## What this package does

Laravel can verify a user’s email address, but by default it does not support verifying a **changed** email before replacing the old one.

This package adds support for verifying **new email addresses**:

- When a user requests an email change, the current email remains active.
- A token + pending email are stored (in `pending_user_emails`).
- Only after the new email is verified does the user model update.

It can also be used to replace Laravel’s default first-time verification flow (optional).

## Installation

```bash
composer require protonemedia/laravel-verify-new-email
```

## Publishing resources (migration/config/views)

```bash
php artisan vendor:publish --provider="ProtoneMedia\LaravelVerifyNewEmail\ServiceProvider"
```

This publishes:

- migration(s) (including the `pending_user_emails` table)
- `verify-new-email.php` config
- email views in `resources/views/vendor/verify-new-email`

## Configuration highlights

- Redirect path after verification is configured in `verify-new-email.php`.
- URL expiry time uses Laravel’s `auth.verification.expire` setting (default 60 minutes).
- Auto-login after verification can be toggled with `login_after_verification`.

## Enabling on your User model

Add the `MustVerifyNewEmail` trait and ensure the model implements Laravel’s `MustVerifyEmail`.

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyNewEmail;
    use Notifiable;
}
```

## User model API (added by the trait)

From README:

```php
$user->newEmail('me@newcompany.com');

$user->getPendingEmail();

$user->resendPendingEmailVerificationMail();

$user->clearPendingEmail();
```

### Flow summary

- `newEmail($email)` creates a token record for the user + new email and sends a verification mail.
- The user’s email is **not** updated yet.
- When the verification link is clicked, the token is validated and the user email is updated.
- The pending token record is removed.

## Login after verification

By default, the verifying user is logged in automatically.

Disable by setting:

- `login_after_verification` → `false`

## Using as a drop-in replacement for Laravel’s first verification (optional)

Laravel’s default verification requires the user to be authenticated to verify.

To use this package’s logic for the first verification too, override `sendEmailVerificationNotification()`:

```php
class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyNewEmail;

    public function sendEmailVerificationNotification()
    {
        $this->newEmail($this->getEmailForVerification());
    }
}
```

## Customization

### Customize email content

Edit the published views:

- `resources/views/vendor/verify-new-email/verifyNewEmail.blade.php` (new email)
- `resources/views/vendor/verify-new-email/verifyFirstEmail.blade.php` (first verification)

### Use custom Mailables

Configure mailables in config:

```php
return [
    'mailable_for_first_verification' => \ProtoneMedia\LaravelVerifyNewEmail\Mail\VerifyFirstEmail::class,
    'mailable_for_new_email' => \ProtoneMedia\LaravelVerifyNewEmail\Mail\VerifyNewEmail::class,
];
```

### Override sending behavior

Override `sendPendingEmailVerificationMail()` to fully customize delivery:

```php
use ProtoneMedia\LaravelVerifyNewEmail\PendingUserEmail;

public function sendPendingEmailVerificationMail(PendingUserEmail $pendingUserEmail)
{
    // send the mail...
}
```

### Custom verification route

You can configure a custom route name used for generating verification URLs.

- The token is passed as a route parameter.
- The URL is signed.

```php
return [
    'route' => 'user.email.verify',
];
```

## Common patterns

- Provide a “Change email” screen that calls `$user->newEmail($request->email)`.
- Show “Pending email” state via `$user->getPendingEmail()`.
- Offer a “Resend verification” button calling `resendPendingEmailVerificationMail()`.

## Pitfalls / gotchas

- **Migrations:** the feature depends on the `pending_user_emails` table. Ensure publishing/running migrations is documented.
- **URL expiry:** if users report expired links, verify `auth.verification.expire`.
- **Mail customization:** if switching to custom Mailables, ensure token + signed URL generation remains correct.
