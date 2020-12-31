@component('mail::message')
Hello There,

Your Email Address and Password is below for Login Checkmate site.

Email Address: {{ $user->email }} <br />
Password: {{ $password }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
