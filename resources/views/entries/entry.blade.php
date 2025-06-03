<x-layout>
    <?php
    $placeMinLength = config('appoptions.place_suggest_min_length');
    $locationMinLength = config('appoptions.location_suggest_min_length');
    $debounceDelay = config('appoptions.suggest_debounce_delay', 250);
    $tempGroupSubgroupMap = $groupSubgroupMap;
    // dd($tempGroupSubgroupMap);
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
                    <th>Add</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="items-list-body">
                <!-- Items will be dynamically added here -->
                <tr id="bottom-line">
                    <td>
                        <select name="group_id" id="group_id">
                            @foreach ($tempGroupSubgroupMap as $group)
                                <option value="{{ $group['id'] }}"
                                    {{ old('group_id') == $group['id'] ? 'selected' : '' }}>
                                    {{ $group['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        @php
                            $selectedGroupId = old('group_id') ?? ($tempGroupSubgroupMap[0]['id'] ?? null);
                            $selectedGroup = collect($tempGroupSubgroupMap)->firstWhere('id', $selectedGroupId);
                        @endphp
                        <select name="subgroup_id" id="subgroup_id">
                            @if ($selectedGroup)
                                @foreach ($selectedGroup['subgroups'] as $subgroupId => $subgroupName)
                                    <option value="{{ $subgroupId }}"
                                        {{ old('subgroup_id') == $subgroupId ? 'selected' : ($loop->first && !old('subgroup_id') ? 'selected' : '') }}>
                                        {{ $subgroupName }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td></td>
                    <td>
                        <input type="number" name="item_amount" id="item_amount"
                            value="{{ old('item_amount', '0.00') }}" step="0.01" class="decimal" required />
                    </td>
                    <td>
                        <button type="button" class="btn btn-secondary" id="add-item">Add item</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <x-form-button>Save entry</x-form-button>
        <x-form-button>Reset</x-form-button>
        <div>
            <input type="checkbox" name="negative" id="negative" value="negative" {{ old('negative') ? 'checked' : '' }} />
            <label for="negative"> Allow negative numbers.</label>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let items = [];

            // Set focus to the amount field when the form is shown
            const amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.focus();
            }

            // Amount input formatting
            const itemAmountInput = document.getElementById('item_amount');
            itemAmountInput.disabled = true; // Disable item_amount input initially

            if (amountInput) {
                amountInput.addEventListener('blur', function() {
                    let value = parseFloat(this.value.replace(',', '.'));
                    this.value = !isNaN(value) ? value.toFixed(2) : '0.00';
                    if (!negativeValues && value < 0) {
                        this.value = '0.00'; // Reset to 0 if negative values are not allowed
                    }
                    // If there are no items yet, update item_amount to match amount
                    if (itemAmountInput && items.length === 0) {
                        itemAmountInput.value = this.value;
                    }
                });
            }

            // Group/Subgroup logic
            let groupSelect = document.getElementById('group_id');
            let subgroupSelect = document.getElementById('subgroup_id');

            // Bottom line for adding items
            let bottomLine = document.getElementById('bottom-line');

            // Pass PHP array to JS
            const groupSubgroupMapJSON = @json($tempGroupSubgroupMap);

            function loadSubgroupsFromMap(groupId) {
                subgroupSelect.innerHTML = '';
                const group = groupSubgroupMapJSON.find(g => g.id == groupId);
                if (group && group.subgroups) {
                    // Get all subgroupIds already used in items
                    const usedSubgroupIds = items.map(item => String(item.subgroupId));
                    // Convert to array and sort by name
                    const sortedSubgroups = Object.entries(group.subgroups)
                        .sort((a, b) => a[1].localeCompare(b[1], undefined, {
                            sensitivity: 'base'
                        }));
                    sortedSubgroups.forEach(([subgroupId, subgroupName]) => {
                        // Only add subgroup if not already used in items
                        if (!usedSubgroupIds.includes(String(subgroupId))) {
                            const option = document.createElement('option');
                            option.value = subgroupId;
                            option.text = subgroupName;
                            subgroupSelect.appendChild(option);
                        }
                    });
                }
            }
            
            // Load subgroups for the initially selected group
            if (groupSelect && subgroupSelect) {
                if (groupSelect.value) loadSubgroupsFromMap(groupSelect.value);
                groupSelect.addEventListener('change', function() {
                    loadSubgroupsFromMap(this.value);
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

            function recalculateFirstItemAmount() {
                const amountInput = document.getElementById('amount');
                let total = 0;
                for (let i = 1; i < items.length; i++) {
                    total += parseFloat(items[i].amount) || 0;
                }
                let mainAmount = parseFloat(amountInput.value.replace(',', '.')) || 0;
                if (items.length > 0) {
                    let newFirstAmount = mainAmount - total;
                    items[0].amount = newFirstAmount.toFixed(2);
                }
            }

            function renderItems(foculField = null, focusIdx = null) {
                recalculateFirstItemAmount();
                const itemsList = document.getElementById('items-list-body');
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
                        <td>${idx === 0 ? '' : `<input type=\"number\" name=\"item_${idx}_add\" class=\"decimal item-add-input\" value=\"0.00\" step=\"0.01\" style=\"width:80px;display:inline-block;\" /> <button type=\"button\" class=\"btn btn-success btn-add-amount\" data-idx=\"${idx}\">+</button>`}</td>
                        <td>${idx === 0
                            ? Number(item.amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                            : `<input type=\"number\" name=\"item_${idx}_amount\" class=\"decimal item-amount-input\" value=\"${Number(item.amount).toFixed(2)}\" step=\"0.01\" style=\"width:80px;display:inline-block;\" /> <button type=\"button\" class=\"btn btn-primary btn-set-amount\" data-idx=\"${idx}\">&#10003;</button>`}
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-delete-item"
                                    data-remove='{"idx":${idx},"groupId":"${item.groupId}","groupText":"${item.groupText}","subgroupId":"${item.subgroupId}","subgroupText":"${item.subgroupText}"}' name=\"item_${idx}_remove\">Remove</button>
                        </td>
                    `;
                    itemsList.insertBefore(newRow, bottomLine);
                });
                // Remove the bottom line if the first item's amount is 0 or if there are no more group options
                // setTimeout(..., 0) works because it doesn't actually wait for 0 milliseconds in the sense of "do this immediately." Instead, it tells the browser: "Run this code after the current call stack is finished and the DOM has had a chance to update."
                setTimeout(() => {
                    if ((items.length > 0 && Number(items[0].amount) === 0 && bottomLine) || (groupSelect &&
                            groupSelect.options.length === 0 && bottomLine)) {
                        bottomLine.style.display = 'none';
                    } else if (bottomLine) {
                        bottomLine.style.display = '';
                    }
                    // Set focus to group_id select after rendering items
                    if (groupSelect) {
                        groupSelect.focus();
                    }
                    itemAmountInput.disabled = items.length === 0;
                    amountInput.disabled = items.length > 0;
                    let focusElement = null;
                    if ((foculField) && (focusIdx !== null)) {
                        if (foculField === 'item_x_amount') {
                            focusElement = document.querySelector(`input[name='item_${focusIdx}_amount']`);
                        } else if (foculField === 'item_x_add') {
                            focusElement = document.querySelector(`input[name='item_${focusIdx}_add']`);
                        } 
                        if (focusElement) focusElement.focus();
                    }
                }, 0);

                // Add event delegation for the new buttons after rendering items
                itemsList.querySelectorAll('.btn-add-amount').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-idx'));
                        const addInput = itemsList.querySelector(`input[name='item_${idx}_add']`);
                        const amountInput = itemsList.querySelector(`input[name='item_${idx}_amount']`);
                        let addValue = parseFloat(addInput.value.replace(',', '.'));
                        if (addValue < 0 && !negativeValues) {
                            console.log('Add amount: Negative values not allowed, resetting to 0.00');
                            addValue = 0.00;
                            addInput.value = '0.00';
                            // items[idx].amount = newValue.toFixed(2);
                            renderItems('item_x_add', idx);
                            return;
                        }
                        let maxAdd = parseFloat(items[0].amount) || 0;
                        let minAdd = -1 * (parseFloat(items[idx].amount) || 0);
                        if (!isNaN(addValue) && addValue !== 0) {
                            // Limit addValue to the range [minAdd, maxAdd]
                            if ((addValue > maxAdd) && (!negativeValues)) {
                                console.log(`Add amount: Negative values not allowed, setting addValue to maxAdd: ${maxAdd}`);
                                addValue = maxAdd;
                            }
                            if ((addValue < minAdd) && (!negativeValues)) {
                                console.log(`Add amount: Negative values not allowed, setting addValue to minAdd: ${minAdd}`);
                                addValue = minAdd;
                            }
                            let currentAmount = parseFloat(items[idx].amount);
                            let newAmount = currentAmount + addValue;
                            items[idx].amount = newAmount.toFixed(2);
                            renderItems();
                        }
                    });
                });
                itemsList.querySelectorAll('.btn-set-amount').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-idx'));
                        const amountInput = itemsList.querySelector(`input[name='item_${idx}_amount']`);
                        let newValue = parseFloat(amountInput.value.replace(',', '.'));
                        if (newValue < 0 && !negativeValues) {
                            console.log('Set amount: Negative values not allowed, resetting to 0.00');
                            newValue = 0.00;
                            amountInput.value = '0.00';
                            items[idx].amount = newValue.toFixed(2);
                            renderItems('item_x_amount', idx);
                            return;
                        }
                        let maxValue = (parseFloat(items[0].amount) || 0) + (parseFloat(items[idx].amount) || 0);
                        let minValue = 0;
                        console.log(`Setting amount for item ${idx}: newValue=${newValue}, maxValue=${maxValue}, minValue=${minValue}`);
                        if (!isNaN(newValue)) {
                            if ((newValue > maxValue) && (!negativeValues)) {
                                console.log(`Set amount: Negative values not allowed, setting newValue to maxValue: ${maxValue}`);
                                newValue = maxValue;
                            }
                            if ((newValue < minValue) && (!negativeValues)) {
                                console.log(`Set amount: Negative values not allowed, setting newValue to minValue: ${minValue}`);
                                newValue = minValue;
                            }
                            // Update the item's amount
                            items[idx].amount = newValue.toFixed(2);
                            renderItems();
                        }
                    });
                });
                itemsList.querySelectorAll('.btn-delete-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = JSON.parse(btn.getAttribute('data-remove'));
                        // Remove the item from the items array
                        items.splice(data.idx, 1);
                        console.log(`Removing item at index ${data.idx}:`, data);

                        // Restore group option if not present, in alphabetical order
                        groupSelect = document.getElementById('group_id');
                        bottomLine = document.getElementById('bottom-line');
                        // Now restore group option if not present
                        let groupExists = false;
                        for (let i = 0; i < groupSelect.options.length; i++) {
                            if (groupSelect.options[i].value == data.groupId) {
                                groupExists = true;
                                break;
                            }
                        }
                        if (!groupExists) {
                            const newOption = document.createElement('option');
                            newOption.value = data.groupId;
                            newOption.text = data.groupText;
                            let inserted = false;
                            for (let i = 0; i < groupSelect.options.length; i++) {
                                if (groupSelect.options[i].text.localeCompare(data.groupText, undefined, { sensitivity: 'base' }) > 0) {
                                    groupSelect.insertBefore(newOption, groupSelect.options[i]);
                                    inserted = true;
                                    break;
                                }
                            }
                            if (!inserted) {
                                groupSelect.appendChild(newOption);
                            }
                        }
                        // Reload subgroups
                        loadSubgroupsFromMap(groupSelect.value);
                        renderItems();
                    });
                });
            }

            document.getElementById('add-item').addEventListener('click', function() {
                const amountInput = document.getElementById('item_amount');
                let amount = amountInput.value;
                // Prevent adding item if amount is 0 or empty
                if (!amount || parseFloat(amount) === 0) {
                    return;
                }

                if (!negativeValues && parseFloat(amount) < 0) {
                    console.log('Negative values not allowed, resetting amount to 0.00');
                    amount = '0.00';
                    amountInput.value = '0.00';
                    return;
                }
                // Ensure amount is a valid number

                const groupSelect = document.getElementById('group_id');
                const subgroupSelect = document.getElementById('subgroup_id');

                const groupId = groupSelect.value;
                const groupText = groupSelect.options[groupSelect.selectedIndex].text;
                const subgroupId = subgroupSelect.value;
                const subgroupText = subgroupSelect.options[subgroupSelect.selectedIndex]?.text || '';

                // Subtract new item_amount from the first item's amount if possible
                if (items.length > 0) {
                    let firstAmount = parseFloat(items[0].amount);
                    let subtractAmount = parseFloat(amount);
                    if (!isNaN(firstAmount) && !isNaN(subtractAmount)) {
                        console.log(`Subtracting ${subtractAmount} from first item amount ${firstAmount}`);
                        if ((subtractAmount > firstAmount) && (!negativeValues)) {
                            subtractAmount = firstAmount;
                        }
                    }
                    amount = subtractAmount;
                }

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
                removeGroup(groupId)

                // Reset item_amount to 0 after adding an item
                amountInput.value = '0.00';
            });

            // Remove subgroup if it has no items left
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

            // Remove group if it has no subgroups left
            function removeGroup(groupId) {
                const groupSelect = document.getElementById('group_id');
                const subgroupSelect = document.getElementById('subgroup_id');
                if (!groupSelect || !subgroupSelect) return;
                if (subgroupSelect.options.length === 0) {
                    for (let i = 0; i < groupSelect.options.length; i++) {
                        if (groupSelect.options[i].value == groupId) {
                            groupSelect.remove(i);
                            break;
                        }
                    }
                    // After removing the group, update subgroups for the new selected group
                    loadSubgroupsFromMap(groupSelect.value);
                }
            }

            // Handle negative checkbox
            const negativeCheckbox = document.getElementById('negative');
            let negativeValues = negativeCheckbox && negativeCheckbox.checked;
            if (negativeCheckbox) {
                negativeCheckbox.addEventListener('change', function() {
                    negativeValues = this.checked;
                });
            }
        });
    </script>

</x-layout>
