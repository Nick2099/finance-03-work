<x-layout>
    <?php
    // dd($groups);
    ?>
    <x-slot:heading>
        {{ __('list-only-recurrences.heading') }}
    </x-slot>
    <h1>{{ __('list-only-recurrences.h1',  ['recurrenceName' => $recurrenceName]) }}{{ __('list-only-recurrences.h1.ending') }}</h1>
    <div class="table">
        <table id="header-list">
            <thead>
                <tr>
                    <th>{{ __('list-only-recurrences.date') }}</th>
                    <th>{{ __('list-only-recurrences.amount') }}</th>
                    <th>{{ __('list-only-recurrences.place_of_purchase') }}</th>
                    <th>{{ __('list-only-recurrences.location') }}</th>
                    <th>{{ __('list-only-recurrences.note') }}</th>
                    <th>{{ __('list-only-recurrences.actions') }}</th>
                </tr>
            </thead>
            <tbody id="header-list-body">
                @foreach ($headers as $header)
                    @php
                        // type = 0: state,  1: income, 2: expense, 3: correction
                        $lineClass = "state";
                        if ($header->type() == 1) {
                            $lineClass = "income";
                        } elseif ($header->type() == 2) {
                            $lineClass = "expense";
                        } elseif ($header->type() == 3) {
                            $lineClass = "correction";
                        }
                    @endphp
                    <tr class="{{ $lineClass }}">
                        <td>{{ \Carbon\Carbon::parse($header->date)->format($dateFormat) }}</td>
                        <td class="amount">{{ number_format($header->amount, 2) }}</td>
                        <td>
                            @if ($header->type() == 0)
                                {{ __('list-only-recurrences.state') }}
                            @else
                                {{ $header->place_of_purchase }}
                            @endif
                        </td>
                        <td>{{ $header->location }}</td>
                        <td>{{ $header->description }}</td>
                        <td>
                            <form action="{{ route('entry.edit', $header->id) }}" method="GET"
                                style="display:inline-block;">
                                <input type="hidden" name="blade" value="list-only-recurrences" />
                                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                <button type="submit" class="btn btn-primary">{{ __('list-only-recurrences.edit') }}</button>
                            </form>
                            <form action="{{ route('entry.destroy', $header->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('{{ __('list-only-recurrences.delete_confirmation') }}');">{{ __('list-only-recurrences.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-wrapper">
            {{ $headers->links('pagination::custom') }}
        </div>
    </div>
</x-layout>
