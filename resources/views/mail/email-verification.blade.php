@component('mail::message')
Hello {{ $user->first_name }},

Copy the code below to verify your account.

**{{ $user->token }}**

The code will expire at {{ \Illuminate\Support\Carbon::parse($user->expires_at)->format('H:ia jS F, Y') }}.

Thank you for using our application!
@endcomponent
