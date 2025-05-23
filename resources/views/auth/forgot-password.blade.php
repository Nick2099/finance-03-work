<x-layout>
    <x-slot:heading>
        Forgot Password
    </x-slot>
    <h1>Forgot Password</h1>
    <form method="POST" action="{{ route('login.email-password') }}">
        @csrf
        <x-form-field name="username" label="User name" required>
            <x-form-input type="text" name="username" id="username" required />
        </x-form-field>
        <x-form-button>Send Reset Link</x-form-button>
    </form>
</x-layout>