<x-layout>
    <x-slot:heading>
        Forgot Username
    </x-slot>
    <h1>Forgot Username</h1>
    <form method="POST" action="{{ route('login.email-username') }}">
        @csrf
        <x-form-field name="email" label="E-mail" required>
            <x-form-input type="email" name="email" id="email" required />
        </x-form-field>
        <x-form-button>Send Username</x-form-button>
    </form>
</x-layout>