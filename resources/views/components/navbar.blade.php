<nav>
    <ul>
        <x-nav-link href="{{ route('home') }}" :active="Route::is('home')">Home</x-nav-link>
        <x-nav-link href="{{ route('about') }}" :active="Route::is('about')">About</x-nav-link>
        <x-nav-link href="{{ route('contact') }}" :active="Route::is('contact')">Contact</x-nav-link>
        @auth
            <x-nav-link href="{{ route('entry.create') }}" :active="Route::is('entry.create')">New entry</x-nav-link>
            <x-nav-links href="#" text="Views">
                <ul>
                    <x-nav-link href="{{ route('entry.list') }}" :active="Route::is('entry.list')">List</x-nav-link>
                    <li><a href="#">Themes</a></li>
                    <li><a href="#">Plugins</a></li>
                    <li><a href="#">Tutorials</a></li>
                </ul>   
            </x-nav-links>
            <x-nav-link href="{{ route('profile') }}" :active="Route::is('profile')">Profile</x-nav-link>
        @endauth
        @auth
        <x-nav-link href="{{ route('logout') }}" :active="Route::is('logout')">Log out</x-nav-link>
        @endauth
        @guest
        <x-nav-link href="{{ route('register') }}" :active="Route::is('register')">Register</x-nav-link>
        <x-nav-link href="{{ route('login') }}" :active="Route::is('login')">Log in</x-nav-link>
        @endguest
    </ul>
    <div style="position: relative; display: inline-block; float: right; margin-right: 2em;">
        <button id="lang-btn" style="background: none; border: none; cursor: pointer; font-size: 1.5em; margin-top: 0;" title="Change language">
            🌐
        </button>
        <div id="lang-menu" style="display: none; position: fixed; right: 0; background: #fff; border: 1px solid #ccc; z-index: 1000; min-width: 120px;">
            <form method="POST" action="{{ route('set-locale') }}" style="margin: 0;">
                @csrf
                <button type="submit" name="locale" value="en" style="width: 100%; text-align: left; padding: 0.5em 1em; background: none; border: none; cursor: pointer;">English</button>
                <button type="submit" name="locale" value="de" style="width: 100%; text-align: left; padding: 0.5em 1em; background: none; border: none; cursor: pointer;">Deutsch</button>
                <button type="submit" name="locale" value="hr" style="width: 100%; text-align: left; padding: 0.5em 1em; background: none; border: none; cursor: pointer;">Hrvatski</button>
                <!-- Add more languages as needed -->
            </form>
        </div>
    </div>
    <script>
        const langBtn = document.getElementById('lang-btn');
        const langMenu = document.getElementById('lang-menu');
        if (langBtn && langMenu) {
            langBtn.addEventListener('click', function(e) {
                e.preventDefault();
                langMenu.style.display = langMenu.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function(e) {
                if (!langBtn.contains(e.target) && !langMenu.contains(e.target)) {
                    langMenu.style.display = 'none';
                }
            });
        }
    </script>
</nav>
