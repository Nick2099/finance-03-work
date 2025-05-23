<x-mail::message>
    # Introduction

    The body of your message.

    <x-mail::button :url="url('/verify-email?token=' . $token . '&email=' . urlencode($user->email))">
        Verify Email
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
