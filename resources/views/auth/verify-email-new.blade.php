<x-layout>
    <x-slot:heading>
        Reverify email
    </x-slot>
    <h1>Reverify email</h1>
    <p> You stil didn't verified your email address. Please check your inbox for the verification email.</p>
    <p> If you didn't receive the email, please check your spam folder or click the button below and we will send you the new verification email.</p>

    <form method="POST" action="/resend-verification-email">
        @csrf
        <input type="hidden" name="username" value="{{ $username }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <x-form-button>Send new verification e-mail</x-form-button>
</x-layout>
