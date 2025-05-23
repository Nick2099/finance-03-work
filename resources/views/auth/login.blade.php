<x-layout>
    <x-slot:heading>
        Login
    </x-slot>
    <h1>Login</h1>
    <form method="POST" action="/login">
        @csrf
        <x-form-field name="username" label="User name">
            <x-form-input type="text" name="username" id="username" value="{{ old('username') }}" required />
        </x-form-field>

        <x-form-field name="password" label="Password" required>
            <x-form-input type="password" name="password" id="password" required />
        </x-form-field>

        <x-form-field name="remember" label="Remember me" required>
            <x-form-input type="checkbox" name="remember" id="remember" />
        </x-form-field>

        <x-form-button>Log in</x-form-button>

        <div style="margin-top: 1em;">
            <a href="{{ route('login.forgot-password') }}">Forgot password?</a> |
            <a href="{{ route('login.forgot-username') }}">Forgot username?</a>
        </div>
</x-layout>
