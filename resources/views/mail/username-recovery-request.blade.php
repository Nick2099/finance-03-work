<x-mail::message>
    Hello {{ $firstName }},

    you requested to recover your username. Here it is:

    {{ $username }}

    <x-mail::button :url="url('/login')">
        Login
    </x-mail::button>
    
    <x-mail::button :url="url('/forgot-password')">
        Forgot Password
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
