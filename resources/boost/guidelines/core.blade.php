{{-- Laravel Verify New Email Guidelines for AI Code Assistants --}}
{{-- Source: https://github.com/protonemedia/laravel-verify-new-email --}}
{{-- License: MIT | (c) ProtoneMedia --}}

## Laravel Verify New Email

- `protonemedia/laravel-verify-new-email` verifies a changed email address before updating the user model, keeping the current email active until the new one is confirmed.
- Always activate the `laravel-verify-new-email-development` skill when working with email verification, pending email changes, or any code that uses the `MustVerifyNewEmail` trait or `PendingUserEmail` model.
