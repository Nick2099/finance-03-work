<x-layout>
    <?php
    $placeMinLength = config('appoptions.place_suggest_min_length');
    $locationMinLength = config('appoptions.location_suggest_min_length');
    $debounceDelay = config('appoptions.suggest_debounce_delay', 250);
    $tempGroupSubgroupMap = $groupSubgroupMap;
    $recurringMenu = config('menu-recurring');
    $recurringMenuUntil = config('menu-recurring-until');
    $recurringMenuNew = config('menu-recurring-new');
    // dump($allBadges);
    // dump($groups);
    // dump($listOfItems);
    // dump($groupSubgroupMap);
    // dump($header);
    // dump($recurringData);

    // following data is normally set in the controller, but here is used for testing
    $recurringData = $recurring ? [
        'base' => 'week',
        'frequency' => "2",
        'rule' => "1",
        'day-of-month' => "5",
        'day-of-week' => "5",
        'month' => "5",
        'number-of-occurrences' => "1",
        'date' => '2026-09-25',
        'number' => '10',
    ] : null;

    ?>
    <x-slot:heading>
        {{ empty($header) ? 'New entry' : 'Edit entry' }}
    </x-slot>
    <h1>
        @if ($recurring)
            {{ __('entry.heading-recurring') }}
        @else
            {{ empty($header) ? __('entry.new-entry') : __('entry.edit-entry') }}
        @endif
    </h1>
    <form method="POST" action="/entry">
        @csrf
        @if (!empty($header) && $header->id)
            <input type="hidden" name="header_id" value="{{ $header->id }}" />
        @endif
        @if (!empty($header) && $header->blade)
            <input type="hidden" name="blade" value="{{ $header->blade }}" /> 
        @endif
        @if(request('page'))
            <input type="hidden" name="page" value="{{ request('page') }}">
        @endif
        @if(request('badge-id'))
            <input type="hidden" name="badge-id" value="{{ request('badge-id') }}">
        @endif
        @if(request('badge-id'))
            <input type="hidden" name="badge-id" value="{{ request('badge-id') }}">
        @endif

        <div class="recurring-options-row">
            <x-form-field name="date" :label="__('entry.date')" required>
                <x-form-input type="date" name="date" id="date" value="{{ old('date', $header->date ?? date('Y-m-d')) }}" required />
            </x-form-field>
        </div>

        {{-- New recurrency options: --}}
        @if ($recurring)
            <div class="recurring-options-row">
                <x-form-field name="base" :label="__('entry.base')" required>
                    <div>
                        <select name="base" id="base" class="form-select block w-full mt-1">
                            @foreach($recurringMenuNew['base'] as $key => $option)
                                <option value="{{ $key }}" {{ $recurringData['base'] == $key ? 'selected' : '' }}>{{ __('entry.'.$option['label']) }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="frequency" :label="__('entry.frequency')" required>
                    <div>
                        <select name="frequency" id="frequency" class="form-select block w-full mt-1">
                            @foreach($recurringMenuNew['base'][$recurringData['base']]['frequency'] as $key => $option)
                                <option value="{{ $key }}" {{ $recurringData['frequency'] == $key ? 'selected' : '' }}>{{ __('entry.'.$option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="rule" :label="__('entry.rule')" required>
                    <div>
                        <select name="rule" id="rule" class="form-select block w-full mt-1">
                            @foreach($recurringMenuNew['base'][$recurringData['base']]['rule'] as $key => $option)
                                <option value="{{ $key }}" {{ $recurringData['rule'] == $key ? 'selected' : '' }}>{{ __('entry.'.$option['label']) }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="day-of-month" :label="__('entry.day')" required>
                    <div>
                        <select name="day-of-month" id="day-of-month" class="form-select block w-full mt-1">
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}" {{ $recurringData['day-of-month'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="day-of-week" :label="__('entry.day-of-week')" required>
                    <div>
                        <select name="day-of-week" id="day-of-week" class="form-select block w-full mt-1">
                            @foreach($recurringMenuNew['weekdays-labels'] as $key => $option)
                                <option value="{{ $key }}" {{ $recurringData['day-of-week'] == $key ? 'selected' : '' }}>{{ __('entry.'.$option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="month" :label="__('entry.month-label')" required>
                    <div>
                        <select name="month" id="month" class="form-select block w-full mt-1">
                            @foreach($recurringMenuNew['months-labels'] as $key => $option)
                                <option value="{{ $key }}" {{ $recurringData['month'] == $key ? 'selected' : '' }}>{{ __('entry.'.$option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="from-date" :label="__('entry.from-date')" required>
                    <x-form-input type="date" name="from-date" id="from-date" value="{{ old('from-date', $recurringData['from-date'] ?? date('Y-m-d')) }}" required />
                </x-form-field>
            </div>
            <div class="recurring-options-row">
                <x-form-field name="number-of-occurrences" :label="__('entry.number-of-occurrences.label')" required>
                    <div>
                        <select name="number-of-occurrences" id="number-of-occurrences" class="form-select block w-full mt-1">
                            @foreach($recurringMenuNew['number-of-occurrences'] as $key => $option)
                                <option value="{{ $key }}" {{ $recurringData['number-of-occurrences'] == $key ? 'selected' : '' }}>{{ __('entry.'.$option['label']) }}</option>
                            @endforeach
                        </select>
                    </div>
                </x-form-field>
                <x-form-field name="occurrences-end-date" :label="__('entry.occurrences-end-date')" required>
                    <x-form-input type="date" name="occurrences-end-date" id="occurrences-end-date" value="{{ old('occurrences-end-date', $recurringData['date'] ?? date('Y-m-d')) }}" required />
                </x-form-field>
                <x-form-field name="occurrences-number" :label="__('entry.occurrences-number')" required>
                    <x-form-input type="number" name="occurrences-number" id="occurrences-number" value="{{ old('occurrences-number', $recurringData['number'] ?? 1) }}" required min="2" max="120"/>
                </x-form-field>
            </div>
            <div class="recurring-options-row">
                <x-form-field name="start-date" :label="__('entry.start-date')">
                    <x-form-input type="date" name="start-date" id="start-date" value="{{ old('start-date', $header->date ?? date('Y-m-d')) }}" disabled />
                </x-form-field>
                <x-form-field name="end-date" :label="__('entry.end-date')">
                    <x-form-input type="date" name="end-date" id="end-date" value="{{ old('end-date', $header->date ?? date('Y-m-d')) }}" disabled />
                </x-form-field>
            </div>
        @endif

        <x-form-field name="type" label="Type" required>
            <div>
                <select name="type" id="type" class="form-select block w-full mt-1">
                    {{-- type = 0: state,  1: income, 2: expense, 3: correction --}}
                    @php
                        $selectedType = old('type');
                        if (!$selectedType) {
                            if (!empty($listOfItems) && count($listOfItems) > 0) {
                                // Find the group for the first item
                                $firstItem = $listOfItems[0];
                                $group = collect($tempGroupSubgroupMap)->firstWhere('id', $firstItem->group_id ?? $firstItem['group_id'] ?? null);
                                $selectedType = $group['type'] ?? 2;
                            } elseif (!empty($header) && isset($header->type)) {
                                $selectedType = $header->type;
                            } else {
                                $selectedType = 2;
                            }
                        }
                    @endphp
                    <option value="2" {{ $selectedType == 2 ? 'selected' : '' }}>Expense</option>
                    <option value="1" {{ $selectedType == 1 ? 'selected' : '' }}>Income</option>
                    <option value="0" {{ $selectedType == 0 ? 'selected' : '' }}>State</option>
                    {{-- Temporary enabled for testing--}}
                    {{-- <option value="3" {{ $selectedType == 3 ? 'selected' : '' }} disabled>Correction</option> --}}
                    <option value="3" {{ $selectedType == 3 ? 'selected' : '' }}>Correction</option>
                </select>
            </div>
        </x-form-field>

        <x-form-field name="amount" label="Amount" required>
            <x-form-input type="number" name="amount" id="amount" value="{{ old('amount', $header->amount ?? '0.00') }}"
                step="0.01" class="decimal" required />
        </x-form-field>

        <x-form-field id="place" name="place" label="Place / Institution" required>
            <div>
                <input list="places" name="place" id="place" value="{{ old('place', $header->place_of_purchase ?? '') }}" autocomplete="off" class="form-input" required />
                <datalist id="places"></datalist>
            </div>
        </x-form-field>

        <x-form-field id="loaction" name="location" label="Location" required>
            <div>
                <input list="locations" name="location" id="location" value="{{ old('location', $header->location ?? '') }}" autocomplete="off" class="form-input" required />
                <datalist id="locations"></datalist>
            </div>
        </x-form-field>

        <x-form-field name="note" label="Note">
        <x-form-input type="text" name="note" id="note" value="{{ old('note', $header->note ?? '') }}"
            autocomplete="off" />

        <!-- Badge Modal -->
        <div id="badge-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:2000;">
            <div style="background:#fff; margin:10vh auto; padding:2em; max-width:400px; position:relative; text-align:left;">
                <button type="button" id="close-badge-modal" style="position:absolute; top:10px; right:10px; font-size:1.5em; background:none; border:none;">&times;</button>
                <div id="badge-modal-message" style="margin-bottom:1em;">Badge button clicked</div>
                <div id="badge-checkboxes">
                    @if(isset($allBadges) && count($allBadges) > 0)
                        @foreach($allBadges->sortBy(function($b){ return mb_strtolower($b->name); }) as $badge)
                            <label style="display:block; margin-bottom:0.5em;">
                                <input type="checkbox" class="badge-checkbox" value="{{ $badge->badge_id }}"> {{ $badge->name }}
                            </label>
                        @endforeach
                        <button type="button" id="save-badges-btn" class="btn btn-primary" style="margin-top:1em;">Save</button>
                    @else
                        <div>No badges available.</div>
                    @endif
                </div>
            </div>
        </div>
        </x-form-field>

        <table id="items-list">
            <thead>
                <tr>
                    <th>Group</th>
                    <th>Subgroup</th>
                    <th>Add</th>
                    <th>Amount</th>
                    <th>Action</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody id="items-list-body">
                <!-- Items will be dynamically added here -->
                <tr id="bottom-line">
                    <td>
                        <select name="group_id" id="group_id"></select>
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
                        <button type="button" class="btn btn-badges-modal" id="new-item-badges" title="Select badges">🏷️</button>
                        <input type="hidden" name="new_item_badges" id="new_item_badges" value="[]" />
                    </td>
                    <td>
                        <input type="text" name="item_note" id="item_note" value="{{ old('item_note', '') }}"
                            placeholder="Note for item" autocomplete="off"/>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="items-hidden-fields"></div>
        <x-form-button id="save-entry-btn">{{ empty($header) ? 'Save entry' : 'Update entry' }}</x-form-button>
        <x-form-button type="reset">Reset</x-form-button>
        <div>
            <input type="checkbox" name="negative" id="negative" value="negative" {{ old('negative') ? 'checked' : '' }} />
            <label for="negative"> Allow negative numbers.</label>
        </div>
    </form>

    <script>
        // Make those variables available globally
        const user = @json($user ?? null);
        const headerId = @json($header->id ?? null);
        const listOfItems = @json(old('items', $listOfItems ?? []));
        const groupSubgroupMapJSON = @json($tempGroupSubgroupMap);
        const recurringMenuConfig = @json($recurringMenu);
        const recurringMenuNew = @json($recurringMenuNew ?? []);
        const frequencyTranslations = @json(
            collect($recurringMenuNew['base'] ?? [])->flatMap(function($base) {
                return collect($base['frequency'] ?? [])->mapWithKeys(function($label, $key) {
                    return [$label => __("entry.".$label)];
                });
            })
        );
        const ruleTranslations = @json(
            collect($recurringMenuNew['base'] ?? [])->flatMap(function($base) {
                return collect($base['rule'] ?? [])->mapWithKeys(function($rule, $key) {
                    return [
                        $rule['label'] => __("entry.".($rule['label'] ?? ''))
                    ];
                });
            })
        );
        const weekdaysTranslations = @json(
            collect($recurringMenuNew['weekdays-labels'] ?? [])->mapWithKeys(function($label, $key) {
                return [$key => __("entry.".$label)];
            })
        );
        const monthsTranslations = @json(
            collect($recurringMenuNew['months-labels'] ?? [])->mapWithKeys(function($label, $key) {
                return [$key => __("entry.".$label)];
            })
        );
        const numberOfOccurrencesTranslations = @json(
            collect($recurringMenuNew['number-of-occurrences'] ?? [])->mapWithKeys(function($label, $key) {
                return [$label['label'] => __("entry.".$label['label'])];
            })
        );

        const recurring = @json($recurring ?? false);

        document.addEventListener('DOMContentLoaded', function() {
            const bottomLine = document.getElementById('bottom-line');
            const typeSelect = document.getElementById('type');

            let items = [];
            if (Array.isArray(listOfItems) && listOfItems.length > 0) {
                // Map group and subgroup names for each item
                // Convert any badge IDs from DB (which may be internal IDs) to badge_id if possible
                const badgeIdMap = {};
                @if(isset($allBadges) && count($allBadges) > 0)
                @foreach($allBadges as $badge)
                    badgeIdMap[{{ $badge->id }}] = {{ $badge->badge_id }};
                @endforeach
                @endif
                items = listOfItems.map(item => {
                    const group = groupSubgroupMapJSON.find(g => g.id == item.group_id);
                    const groupText = group ? group.name : '';
                    const subgroupText = group && group.subgroups[item.subgroup_id] ? group.subgroups[item.subgroup_id] : '';
                    // Convert badges to badge_id if needed
                    let badges = Array.isArray(item.badges) ? item.badges.map(bid => badgeIdMap[bid] || bid) : [];
                    return {
                        groupId: item.group_id,
                        groupText: groupText,
                        subgroupId: item.subgroup_id,
                        subgroupText: subgroupText,
                        amount: item.amount,
                        note: item.note || '',
                        badges: badges || [],
                    };
                });
                renderItems();
            }

            // Set focus to the amount field when the form is shown
            const amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.focus();
            }

            // Amount input formatting
            const itemAmountInput = document.getElementById('item_amount');
            itemAmountInput.disabled = true; // Disable item_amount input initially

            // next line was disabling first button of submit type
            // const saveEntryBtn = document.querySelector('button[type="submit"]');
            const saveEntryBtn = document.getElementById('save-entry-btn');
            saveEntryBtn.disabled = true; // Disable save button initially

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

            // Pass PHP array to JS
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

            function updateItemNote(idx) {
                if (items[idx]) {
                    items[idx].note = document.querySelector(`input[name="item_${idx}_note"]`).value;;
                }
            }

            function renderItems(focusField = null, focusIdx = null) {
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
                            <button type="button" class="btn btn-badges-modal" data-idx="${idx}" title="Select badges">🏷️</button>
                            <input type="hidden" name="items[${idx}][badges]" id="item_${idx}_badges" value="${item.badges ? JSON.stringify(item.badges) : '[]'}" />
                        </td>
                        <td>
                            <input type="text" name="item_${idx}_note" value="${item.note}" placeholder="Note for item" autocomplete="off" />
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
                    amountInput.readOnly = items.length > 0;
                    saveEntryBtn.disabled = items.length === 0;
                    typeSelect.disabled = items.length > 0;
                    let focusElement = null;
                    if ((focusField) && (focusIdx !== null)) {
                        if (focusField === 'item_x_amount') {
                            focusElement = document.querySelector(`input[name='item_${focusIdx}_amount']`);
                        } else if (focusField === 'item_x_add') {
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
                                addValue = maxAdd;
                            }
                            if ((addValue < minAdd) && (!negativeValues)) {
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
                            newValue = 0.00;
                            amountInput.value = '0.00';
                            items[idx].amount = newValue.toFixed(2);
                            renderItems('item_x_amount', idx);
                            return;
                        }
                        let maxValue = (parseFloat(items[0].amount) || 0) + (parseFloat(items[idx].amount) || 0);
                        let minValue = 0;

                        if (!isNaN(newValue)) {
                            if ((newValue > maxValue) && (!negativeValues)) {
                                newValue = maxValue;
                            }
                            if ((newValue < minValue) && (!negativeValues)) {
                                newValue = minValue;
                            }
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

                        // Restore group option if not present, in alphabetical order
                        groupSelect = document.getElementById('group_id');
                        // bottomLine = document.getElementById('bottom-line');
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
                        // If all items are deleted, set item_amount to amount value
                        if (items.length === 0) {
                            const amountInput = document.getElementById('amount');
                            const itemAmountInput = document.getElementById('item_amount');
                            if (amountInput && itemAmountInput) {
                                itemAmountInput.value = amountInput.value;
                            }
                        }
                        renderItems();
                    });
                });

                // Add event listeners for note blur only to inputs with names like item_0_note, item_1_note, etc.
                itemsList.querySelectorAll("input[name$='_note']").forEach((input) => {
                    if (/^item_\d+_note$/.test(input.name)) {
                        const idx = parseInt(input.name.match(/^item_(\d+)_note$/)[1], 10);
                        input.addEventListener("blur", () => updateItemNote(idx));
                    }
                });
                // Always update hidden fields after rendering
                updateHiddenItemsFields();
            }

            document.getElementById('add-item').addEventListener('click', function() {
                const amountInput = document.getElementById('item_amount');
                let amount = amountInput.value;
                // Prevent adding item if amount is 0 or empty
                if (!amount || parseFloat(amount) === 0) {
                    return;
                }

                if (!negativeValues && parseFloat(amount) < 0) {
                    amount = '0.00';
                    amountInput.value = '0.00';
                    return;
                }

                const groupSelect = document.getElementById('group_id');
                const subgroupSelect = document.getElementById('subgroup_id');
                const noteInput = document.getElementById('item_note');

                const groupId = groupSelect.value;
                const groupText = groupSelect.options[groupSelect.selectedIndex].text;
                const subgroupId = subgroupSelect.value;
                const subgroupText = subgroupSelect.options[subgroupSelect.selectedIndex]?.text || '';
                const note = noteInput ? noteInput.value : '';

                // Subtract new item_amount from the first item's amount if possible
                if (items.length > 0) {
                    let firstAmount = parseFloat(items[0].amount);
                    let subtractAmount = parseFloat(amount);
                    if (!isNaN(firstAmount) && !isNaN(subtractAmount)) {
                        if ((subtractAmount > firstAmount) && (!negativeValues)) {
                            subtractAmount = firstAmount;
                        }
                    }
                    amount = subtractAmount;
                }

                // Get badges for new item from the hidden input (if any)
                let badges = [];
                try {
                    const newItemBadgesInput = document.getElementById('new_item_badges');
                    if (newItemBadgesInput && newItemBadgesInput.value) {
                        badges = JSON.parse(newItemBadgesInput.value);
                    }
                } catch (e) {
                    badges = [];
                }

                items.push({
                    groupId,
                    groupText,
                    subgroupId,
                    subgroupText,
                    amount,
                    note,
                    badges: Array.isArray(badges) ? badges : []
                });

                // Reset new_item_badges hidden input after adding
                const newItemBadgesInput = document.getElementById('new_item_badges');
                if (newItemBadgesInput) newItemBadgesInput.value = '[]';

                renderItems();

                removeSubgroup(subgroupId)
                removeGroup(groupId)

                // Reset item_amount to 0 after adding an item
                amountInput.value = '0.00';
                if (noteInput) noteInput.value = '';
                // Ensure save button is enabled after adding an item
                saveEntryBtn.disabled = items.length === 0;
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

            function updateHiddenItemsFields() {
                // Always update items array with latest note values and badges from DOM before creating hidden fields
                items.forEach((item, idx) => {
                    const noteInput = document.querySelector(`input[name="item_${idx}_note"]`);
                    if (noteInput) {
                        item.note = noteInput.value;
                    }
                    // Update badges from hidden input
                    const badgesInput = document.getElementById(`item_${idx}_badges`);
                    if (badgesInput) {
                        try {
                            item.badges = JSON.parse(badgesInput.value || '[]');
                        } catch (e) {
                            item.badges = [];
                        }
                    }
                });
                const container = document.getElementById('items-hidden-fields');
                container.innerHTML = '';
                // Add header_id if editing
                if (typeof headerId !== 'undefined' && headerId) {
                    container.innerHTML += `\n<input type="hidden" name="header_id" value="${headerId}">`;
                }
                items.forEach((item, idx) => {
                    container.innerHTML += `\n<input type="hidden" name="items[${idx}][group_id]" value="${item.groupId}">`;
                    container.innerHTML += `\n<input type="hidden" name="items[${idx}][subgroup_id]" value="${item.subgroupId}">`;
                    container.innerHTML += `\n<input type="hidden" name="items[${idx}][amount]" value="${item.amount}">`;
                    container.innerHTML += `\n<input type="hidden" name="items[${idx}][note]" value="${item.note}">`;
                    container.innerHTML += `\n<input type="hidden" name="items[${idx}][badges]" value='${JSON.stringify(item.badges || [])}'>`;
                });
            }

            // Attach to form submit
            const entryForm = document.querySelector('form[action="/entry"]');
            if (entryForm) {
                entryForm.addEventListener('submit', function(e) {
                    try {
                        updateHiddenItemsFields();
                        // Debug: log the hidden fields before submit
                    } catch (err) {
                        alert('A JavaScript error occurred: ' + err.message);
                    }
                });
            }

            // const groupSelect = document.getElementById('group_id');
            // Helper to update group dropdown based on selected type
            function updateGroupDropdown(selectedType) {
                groupSelect.innerHTML = '';
                groupSubgroupMapJSON.forEach(group => {
                    if (String(group.type) === String(selectedType)) {
                        // Get all subgroupIds for this group
                        const allSubgroupIds = Object.keys(group.subgroups);
                        // Get all used subgroupIds for this group in items
                        const usedSubgroupIds = items.filter(item => String(item.groupId) === String(group.id)).map(item => String(item.subgroupId));
                        // If all subgroups are used, skip this group
                        if (allSubgroupIds.length > 0 && allSubgroupIds.every(id => usedSubgroupIds.includes(id))) {
                            return;
                        }
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.text = group.name;
                        groupSelect.appendChild(option);
                    }
                });
                // Trigger change to update subgroups
                if (groupSelect.value) {
                    const event = new Event('change');
                    groupSelect.dispatchEvent(event);
                }
            }
            // Initial filter on page load
            updateGroupDropdown(typeSelect.value);
            // Update group dropdown when type changes
            typeSelect.addEventListener('change', function() {
                updateGroupDropdown(this.value);
                hidePlaceAndLocationFields(this.value);
            });

            // Badge modal logic (now inside the same DOMContentLoaded as items)
            let currentBadgeIdx = null;
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('btn-badges-modal')) {
                    let idx = e.target.getAttribute('data-idx');
                    if (idx === null || typeof idx === 'undefined') idx = 'null';
                    currentBadgeIdx = idx;
                    const modal = document.getElementById('badge-modal');
                    const msg = document.getElementById('badge-modal-message');
                    msg.textContent = 'Badge button clicked for item #' + idx;
                    // Pre-check checkboxes for new item or existing item
                    if (idx === 'null') {
                        const selected = JSON.parse(document.getElementById('new_item_badges').value || '[]');
                        document.querySelectorAll('#badge-checkboxes .badge-checkbox').forEach(cb => {
                            cb.checked = selected.includes(parseInt(cb.value));
                        });
                    } else {
                        const selected = JSON.parse(document.getElementById('item_' + idx + '_badges').value || '[]');
                        document.querySelectorAll('#badge-checkboxes .badge-checkbox').forEach(cb => {
                            cb.checked = selected.includes(parseInt(cb.value));
                        });
                    }
                    modal.style.display = 'block';
                }
                if (e.target && e.target.id === 'close-badge-modal') {
                   document.getElementById('badge-modal').style.display = 'none';
                }
                if (e.target && e.target.id === 'save-badges-btn') {
                    // Save selected badges for new item or existing item
                    const checked = Array.from(document.querySelectorAll('#badge-checkboxes .badge-checkbox:checked')).map(cb => parseInt(cb.value));
                    if (currentBadgeIdx === 'null') {
                        document.getElementById('new_item_badges').value = JSON.stringify(checked);
                    } else {
                        document.getElementById('item_' + currentBadgeIdx + '_badges').value = JSON.stringify(checked);
                        // Also update the badges in the items array for correct re-render
                        if (Array.isArray(items) && items[currentBadgeIdx]) {
                            items[currentBadgeIdx].badges = checked;
                        }
                    }
                    // Always update hidden fields after badge selection
                    updateHiddenItemsFields();
                    // Always close the modal on save
                    document.getElementById('badge-modal').style.display = 'none';
                }
            });

            const placeWrapper = document.getElementById('place-wrapper');
            const locationWrapper = document.getElementById('location-wrapper');
            function hidePlaceAndLocationFields(selectedType) {
                if (selectedType === "0") {
                    if (placeWrapper) {
                        placeWrapper.style.display = 'none';
                        placeInput.required = false;
                    }
                    if (locationWrapper) {
                        locationWrapper.style.display = 'none';
                        locationInput.required = false;
                    }
                } else {
                    if (placeWrapper) {
                        placeWrapper.style.display = 'block';
                        placeInput.required = true;
                    }
                    if (locationWrapper) {
                        locationWrapper.style.display = 'block';
                        locationInput.required = true;
                    }
                }
            }
            hidePlaceAndLocationFields(typeSelect.value);
        });


        // Add after DOMContentLoaded
        const resetBtn = document.querySelector('button[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.reload();
            });
        }


        
    </script>

    <script type="module" src="{{ asset('js/entries/recurrencies.js') }}"></script>
</x-layout>
