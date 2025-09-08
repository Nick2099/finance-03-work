<x-layout>
    <x-slot:heading>
        Profile
    </x-slot>
    <h1>User profile</h1>
    <div>
        <a href="{{ route('profile.badges') }}">Badges</a>
    </div>
    <div>
        <a href="{{ route('profile.payment_methods') }}">Payment methods</a>
    </div>
</x-layout>
