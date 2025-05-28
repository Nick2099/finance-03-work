<x-layout>
    <?php
    $user = Auth::user();
    $collectionId = $user->collection_id;
    $groups = \App\Models\Group::where('collection_id', $collectionId)->get()->sortBy('name');
    ?>
    <x-slot:heading>
        New entry
    </x-slot>
    <h1>New entry</h1>
    <form method="POST" action="/entry">
        @csrf

        <x-form-field name="date" label="Date" required>
            <x-form-input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                required />
        </x-form-field>

        <x-form-field name="amount" label="Amount" required>
            <x-form-input type="number" name="amount" id="amount" value="{{ old('amount', '0.00') }}" step="0.01"
                class="decimal" required />
        </x-form-field>        

        <x-form-field name="place" label="Place of purchase" required>
            <x-form-input type="text" name="place" id="place" value="{{ old('place') }}" required />
        </x-form-field>    

        <x-form-field name="location" label="Location" required>
            <x-form-input type="text" name="location" id="location" value="{{ old('location') }}" required />
        </x-form-field>    

        <x-form-field name="description" label="Description">
            <x-form-input type="text" name="description" id="description" value="{{ old('description') }}" />
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

        <x-form-button>Save Entry</x-form-button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            if (amountInput) {
                amountInput.addEventListener('blur', function() {
                    let value = parseFloat(this.value.replace(',', '.'));
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    } else {
                        this.value = '0.00';
                    }
                });
            }

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
                // Load subgroups for the initially selected group
                if (groupSelect.value) {
                    loadSubgroups(groupSelect.value);
                }
                groupSelect.addEventListener('change', function() {
                    loadSubgroups(this.value);
                });
            }
        });
    </script>

</x-layout>
