<nav>
    <ul>
        <x-nav-link href="{{ route('home') }}" :active="Route::is('home')">Home</x-nav-link>
        <x-nav-link href="{{ route('about') }}" :active="Route::is('about')">About</x-nav-link>
        <x-nav-link href="{{ route('contact') }}" :active="Route::is('contact')">Contact</x-nav-link>
        @auth
            <x-nav-link href="{{ route('entry.create') }}" :active="Route::is('entry.create')">New entry</x-nav-link>
            <x-nav-links href="#" text="Views">
                <ul>
                    <x-nav-link href="{{ route('entry.list') }}" :active="Route::is('entry.list')">List (edit)</x-nav-link>
                    <x-nav-link href="{{ route('graphs.inex') }}" :active="Route::is('graphs.inex')">Income-Expenses</x-nav-link>
                    <x-nav-link href="{{ route('graphs.ex') }}" :active="Route::is('graphs.ex')">Expenses</x-nav-link>
                    <x-nav-link href="{{ route('graphs.exgr') }}" :active="Route::is('graphs.exgr')">Monthly by group</x-nav-link>
                    <li style="pointer-events:none;opacity:0.6;"><hr style="margin:4px 0;"></li>
                    <x-nav-link href="{{ route('graphs-new.groups') }}" :active="Route::is('graphs-new.groups')">Monthly by group</x-nav-link>
                    <x-nav-link href="{{ route('graphs-new.income-vs-expense') }}" :active="Route::is('graphs-new.income-vs-expense')">Income vs expenses</x-nav-link>
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
    <div class="language">
        <button type="button" id="lang-btn" name="lang-btn" title="Change language">
            🌐
        </button>
        <div id="lang-menu">
            <form method="POST" action="{{ route('set-locale') }}">
                @csrf
                <button type="submit" name="locale" value="en">English</button>
                <button type="submit" name="locale" value="de">Deutsch</button>
                <button type="submit" name="locale" value="hr">Hrvatski</button>
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
