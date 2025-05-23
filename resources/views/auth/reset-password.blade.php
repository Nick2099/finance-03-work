<x-layout>
    <x-slot:heading>
        Reset password
    </x-slot>
    <h1>Reset password</h1>
    <form method="POST" action="/update-password">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <x-form-field name="password" label="Password" required>
            <x-form-input type="password" name="password" id="password" required />
        </x-form-field>

        <x-form-field name="password_confirmation" label="Password confirmation" required>
            <x-form-input type="password" name="password_confirmation" id="password_confirmation" required />
        </x-form-field>

        <x-form-button>Reset password</x-form-button>
</x-layout>
