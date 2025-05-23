<nav class="navbar">
    <ul>
        <x-nav-link href="{{ route('home') }}" :active="request()->is('/')">Home</x-nav-link>
        <x-nav-link href="{{ route('about') }}" :active="request()->is('about')">About</x-nav-link>
        <x-nav-link href="{{ route('contact') }}" :active="request()->is('contact')">Contact</x-nav-link>
        @auth
            <x-nav-link href="{{ route('profile') }}" :active="request()->is('profile')">Profile</x-nav-link>
        @endauth
        @auth
            <x-nav-link href="{{ route('logout') }}" :active="request()->is('logout')">Log out</x-nav-link>
        @endauth
        @guest
            <x-nav-link href="{{ route('register') }}" :active="request()->is('register')">Register</x-nav-link>
            <x-nav-link href="{{ route('login') }}" :active="request()->is('login')">Log in</x-nav-link>
        @endguest
    </ul>
</nav>
