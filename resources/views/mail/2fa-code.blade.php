@component('mail::message')
# Hello {{ $user['first_name'] }},

Your 2FA verification code is:

@component('mail::panel')
# {{ $user['verification_code'] }}
@endcomponent

This code will expire in 10 minutes.

If you did not request a password reset, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
