<x-mail::message>
    Hello {{ $firstName }},

    You are receiving this email because we received a password reset request for your account. If you did not request a
    password reset, no further action is required.

    If you did request a password reset, please click the button below to reset your password.

    <x-mail::button :url="url('/reset-password?token=' . $token . '&email=' . urlencode($email))">
        Reset Password
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
