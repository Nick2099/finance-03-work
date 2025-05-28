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

        <table id="items-list">
            <thead>
                <tr>
                    <th>Group</th>
                    <th>Subgroup</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="items-list-body">
                <!-- Items will be dynamically added here -->
                <tr id="bottom-line">
                    <td>
                        <select name="group_id" id="group_id">
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"
                                    {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="subgroup_id" id="subgroup_id">
                            @if (old('group_id'))
                                @foreach ($groups->find(old('group_id'))->subgroups as $subgroup)
                                    <option value="{{ $subgroup->id }}"
                                        {{ old('subgroup_id') == $subgroup->id ? 'selected' : '' }}>
                                        {{ $subgroup->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <x-form-input type="number" name="item_amount" id="item_amount"
                            value="{{ old('item_amount', '0.00') }}" step="0.01" class="decimal" required />
                    </td>
                    <td>
                        <button type="button" class="btn btn-secondary" id="add-item">Add item</button>
                    </td>
                </tr>
            </tbody>
        </table>
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
                    // console.log('Fetching places for:', lastPlaceQuery);
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
                    // console.log('Fetching locations for:', lastLocationQuery);
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

            let items = [];

            function renderItems() {
                const itemsList = document.getElementById('items-list-body');
                const bottomLine = document.getElementById('bottom-line');
                // Remove all rows except the bottom line
                Array.from(itemsList.querySelectorAll('tr')).forEach(row => {
                    if (row.id !== 'bottom-line') row.remove();
                });
                // Add each item before the bottom line
                items.forEach((item, idx) => {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${item.groupText}</td>
                        <td>${item.subgroupText}</td>
                        <td>${item.amount}</td>
                        <td>
                            <button type="button" class="btn btn-danger" onclick="items.splice(${idx}, 1); renderItems();">Remove</button>
                        </td>
                    `;
                    itemsList.insertBefore(newRow, bottomLine);
                });
            }

            document.getElementById('add-item').addEventListener('click', function() {
                const groupSelect = document.getElementById('group_id');
                const subgroupSelect = document.getElementById('subgroup_id');
                const amountInput = document.getElementById('item_amount');

                const groupId = groupSelect.value;
                const groupText = groupSelect.options[groupSelect.selectedIndex].text;
                const subgroupId = subgroupSelect.value;
                const subgroupText = subgroupSelect.options[subgroupSelect.selectedIndex]?.text || '';
                const amount = amountInput.value;

                // Add to items array
                items.push({
                    groupId,
                    groupText,
                    subgroupId,
                    subgroupText,
                    amount
                });

                renderItems();

                removeSubgroup(subgroupId)

                console.log('Items:', items);
            });

            function removeSubgroup(subgroupId) {
                const subgroupSelect = document.getElementById('subgroup_id');
                if (!subgroupSelect) return;
                for (let i = 0; i < subgroupSelect.options.length; i++) {
                    if (subgroupSelect.options[i].value == subgroupId) {
                        subgroupSelect.remove(i);
                        break;
                    }
                }
            }
        });
    </script>

</x-layout>
