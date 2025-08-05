<x-layout>
    <?php
    // dump($recurrences);
    ?>
    <x-slot:heading>
        {{ __('list-recurrences.heading') }}
    </x-slot>
    <h1>{{ __('list-recurrences.h1') }}</h1>
    <div class="table">
        <table id="recurrence-list">
            <thead>
                <tr>
                    <th>{{ __('list-recurrences.name') }}</th>
                    <th>{{ __('list-recurrences.amount') }}</th>
                    <th>{{ __('list-recurrences.place_of_purchase') }}</th>
                    <th>{{ __('list-recurrences.location') }}</th>
                    <th>{{ __('list-recurrences.note') }}</th>
                    <th>{{ __('list-recurrences.actions') }}</th>
                </tr>
            </thead>
            <tbody id="header-list-body">
                @foreach ($recurrences as $recurrence)
                    <tr>
                        <td>{{ $recurrence->name }}</td>
                        <td>{{ optional($recurrence->recurrencyHeader)->amount }}</td>
                        <td>{{ optional($recurrence->recurrencyHeader)->place_of_purchase }}</td>
                        <td>{{ optional($recurrence->recurrencyHeader)->location }}</td>
                        <td>{{ optional($recurrence->recurrencyHeader)->note }}</td>
                        <td>
                            <form action="{{ route('entry.list-only-recurrences') }}" method="get"
                                style="display:inline;">
                                <input type="hidden" name="recurrence-id" value="{{ $recurrence->id }}">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('list-recurrences.view_entries') }}
                                </button>
                            </form>
                            <form action="{{ route('entry.add-recurring', 0) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                <input type="hidden" name="blade" value="list-recurrences" />
                                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                <input type="hidden" name="recurrence-id" value="{{ $recurrence->id }}" />
                                <button type="submit"
                                    class="btn btn-secondary">{{ __('list.edit-recurring') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination">
            {{ $recurrences->links('pagination::custom') }}
        </div>
    </div>
</x-layout>
