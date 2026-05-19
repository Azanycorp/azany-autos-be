@component('mail::message')
# Hello {{ $user->first_name }},

You have requested to reset your password.

Your password reset verification code is:

@component('mail::panel')
# {{ $code }}
@endcomponent

This code will expire in 10 minutes.

If you did not request a password reset, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
