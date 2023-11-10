@component('mail::message')
# Password Reset OTP Code

Your OTP code for password reset is: **{{ $code }}**

This code is valid for a limited time.

Thanks,<br>
{{ config('app.name') }}
@endcomponent