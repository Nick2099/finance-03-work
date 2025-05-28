<x-layout>
    <?php
    $placeMinLength = config('appoptions.place_suggest_min_length');
    $locationMinLength = config('appoptions.location_suggest_min_length');
    $debounceDelay = config('appoptions.suggest_debounce_delay', 250);
    ?>
    <x-slot:heading>
        New entry
    </x-slot>
    <h1>New entry</h1>
    <form method="POST" action="/entry">
        @csrf

        <x-form-field name="date" label="Date" required>
            <x-form-input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required />
        </x-form-field>

        <x-form-field name="amount" label="Amount" required>
            <x-form-input type="number" name="amount" id="amount" value="{{ old('amount', '0.00') }}"
                step="0.01" class="decimal" required />
        </x-form-field>

        <x-form-field name="place" label="Place of purchase" required>
            <div>
                <input list="places" name="place" id="place" value="{{ old('place') }}" autocomplete="off"
                    required class="form-input" />
                <datalist id="places"></datalist>
            </div>
        </x-form-field>

        <x-form-field name="location" label="Location" required>
            <div>
                <input list="locations" name="location" id="location" value="{{ old('location') }}" autocomplete="off"
                    required class="form-input" />
                <datalist id="locations"></datalist>
            </div>
        </x-form-field>

        <x-form-field name="description" label="Description">
            <x-form-input type="text" name="description" id="description" value="{{ old('description') }}"
                autocomplete="off" />
        </x-form-field>

        <x-form-field name="group_id" label="Group" required>
            <div>
                <select name="group_id" id="group_id">
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </x-form-field>

        <x-form-field name="subgroup_id" label="Subgroup" required>
            <div>
                <select name="subgroup_id" id="subgroup_id">
                </select>
            </div>
        </x-form-field>

        <x-form-button>Details</x-form-button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Amount input formatting
            const amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.addEventListener('blur', function() {
                    let value = parseFloat(this.value.replace(',', '.'));
                    this.value = !isNaN(value) ? value.toFixed(2) : '0.00';
                });
            }

            // Group/Subgroup logic
            const groupSelect = document.getElementById('group_id');
            const subgroupSelect = document.getElementById('subgroup_id');

            function loadSubgroups(groupId) {
                fetch(`/subgroups/${groupId}`)
                    .then(response => response.json())
                    .then(data => {
                        subgroupSelect.innerHTML = '';
                        data.forEach(function(subgroup) {
                            const option = document.createElement('option');
                            option.value = subgroup.id;
                            option.text = subgroup.name;
                            subgroupSelect.appendChild(option);
                        });
                    });
            }
            if (groupSelect && subgroupSelect) {
                if (groupSelect.value) loadSubgroups(groupSelect.value);
                groupSelect.addEventListener('change', function() {
                    loadSubgroups(this.value);
                });
            }

            // Debounce helper
            function debounce(fn, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // Debounce delay 
            const DEBOUNCE_DELAY = {{ $debounceDelay }};

            // Place suggestions
            const PLACE_MIN_LENGTH = {{ $placeMinLength }};
            const placeInput = document.getElementById('place');
            const placeList = document.getElementById('places');
            let lastPlaceQuery = '';
            if (placeInput && placeList) {
                placeInput.addEventListener('input', debounce(function() {
                    const query = this.value;
                    if (query.length < PLACE_MIN_LENGTH || query === lastPlaceQuery) return;
                    lastPlaceQuery = query;
                    console.log('Fetching places for:', lastPlaceQuery);
                    fetch(`/places/suggest?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            placeList.innerHTML = '';
                            data.forEach(function(place) {
                                const option = document.createElement('option');
                                option.value = place;
                                placeList.appendChild(option);
                            });
                        });
                }, DEBOUNCE_DELAY));
            }

            // Location suggestions
            const LOCATION_MIN_LENGTH = {{ $locationMinLength }};
            const locationInput = document.getElementById('location');
            const locationList = document.getElementById('locations');
            let lastLocationQuery = '';
            if (locationInput && locationList) {
                locationInput.addEventListener('input', debounce(function() {
                    const query = this.value;
                    if (query.length < LOCATION_MIN_LENGTH || query === lastLocationQuery) return;
                    lastLocationQuery = query;
                    console.log('Fetching locations for:', lastLocationQuery);
                    fetch(`/locations/suggest?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            locationList.innerHTML = '';
                            data.forEach(function(location) {
                                const option = document.createElement('option');
                                option.value = location;
                                locationList.appendChild(option);
                            });
                        });
                }, DEBOUNCE_DELAY));
            }
        });
    </script>

</x-layout>
