<x-layout>
    <x-slot:heading>
        Register
    </x-slot>
    <h1>Register</h1>
    <form method="POST" action="/register">
        @csrf
        <x-form-field name="first_name" label="First name" required>
            <x-form-input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autofocus />
        </x-form-field>

        <x-form-field name="last_name" label="Last name">
            <x-form-input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required/>
        </x-form-field>

        <x-form-field name="username" label="User name">
            <x-form-input type="text" name="username" id="username" value="{{ old('username') }}" required />
        </x-form-field>

        <x-form-field name="email" label="E-mail" required>
            <x-form-input type="email" name="email" id="email" value="{{ old('email') }}" required />
        </x-form-field>

        <x-form-field name="password" label="Password" required>
            <x-form-input type="password" name="password" id="password" required />
        </x-form-field>

        <x-form-field name="password_confirmation" label="Password confirmation" required>
            <x-form-input type="password" name="password_confirmation" id="password_confirmation" required />
        </x-form-field>

        <x-form-field name="language" label="Language" required>
            <div>
                <select name="language" id="language" class="form-select block w-full mt-1">
                    @php
                        $languages = [
                            'en' => 'English',
                            'de' => 'German',
                            'fr' => 'French',
                            'es' => 'Spanish',
                            'it' => 'Italian',
                            // Add more languages as needed
                        ];
                    @endphp
                    @foreach ($languages as $code => $name)
                        <option value="{{ $code }}" {{ old('language', 'en') === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </x-form-field>

        <x-form-field name="timezone" label="Time Zone" required>
            <div>
                <select name="timezone" id="timezone" class="form-select block w-full mt-1">
                    @foreach (\DateTimeZone::listIdentifiers() as $timezone)
                    <option value="{{ $timezone }}" {{ old('timezone', 'Europe/Berlin') === $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                    @endforeach
                </select>
            </div>
        </x-form-field>

        <x-form-field name="currency" label="Currency" required>
            <div>
                <select name="currency" id="currency" class="form-select block w-full mt-1">
                    @php
                        $currencies = [
                            /*
                            'USD' => 'US Dollar',
                            'EUR' => 'Euro',
                            'GBP' => 'British Pound Sterling',
                            'CAD' => 'Canadian Dollar',
                            'AUD' => 'Australian Dollar',
                            'CHF' => 'Swiss Franc',
                            'CNY' => 'Chinese Yuan Renminbi',
                            'INR' => 'Indian Rupee',
                            'JPY' => 'Japanese Yen',
                            'BRL' => 'Brazilian Real',
                            'MXN' => 'Mexican Peso',
                            'ZAR' => 'South African Rand',
                            'HKD' => 'Hong Kong Dollar',
                            'SGD' => 'Singapore Dollar',
                            'SEK' => 'Swedish Krona',
                            'NOK' => 'Norwegian Krone',
                            'NZD' => 'New Zealand Dollar',
                            'RUB' => 'Russian Ruble',
                            'SAR' => 'Saudi Riyal',
                            'KRW' => 'South Korean Won',
                            */
                            'AUD' => 'AUD',
                            'BRL' => 'BRL',
                            'CAD' => 'CAD',
                            'CHF' => 'CHF',
                            'CNY' => 'CNY',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'HKD' => 'HKD',
                            'INR' => 'INR',
                            'JPY' => 'JPY',
                            'KRW' => 'KRW',
                            'MXN' => 'MXN',
                            'NOK' => 'NOK',
                            'NZD' => 'NZD',
                            'RUB' => 'RUB',
                            'SAR' => 'SAR',
                            'SEK' => 'SEK',
                            'SGD' => 'SGD',
                            'USD' => 'USD',
                            'ZAR' => 'ZAR'
                        ];
                    @endphp
                    @foreach ($currencies as $code => $name)
                        <option value="{{ $code }}" {{ old('currency', 'EUR') === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </x-form-field>

        <x-form-button>Register</x-form-button>
</x-layout>
