<x-layout>
    <?php
    // dd($groups);
    ?>
    <x-slot:heading>
        {{ __('list-badges.heading') }}
    </x-slot>
    <h1>{{ __('list-badges.h1') }}</h1>
    <form method="GET" action="">        
        <label for="badge-select">{{ $labelBadge }}:</label>
        <select name="badge-id" id="badge-select" onchange="this.form.submit()">
            @foreach ($listOfBadges ?? [] as $badge)
                <option value="{{ $badge->badge_id }}" @if (request('badge-id', $selectedBadge ?? null) == $badge->id) selected @endif>
                    {{ $badge->name }}
                </option>
            @endforeach
        </select>

    </form>
    <div class="table">
        <table id="header-list">
            <thead>
                <tr>
                    <th>{{ __('list-badges.date') }}</th>
                    <th>{{ __('list-badges.amount') }}</th>
                    <th>{{ __('list-badges.place_of_purchase') }}</th>
                    <th>{{ __('list-badges.location') }}</th>
                    <th>{{ __('list-badges.amount_badge') }}</th>
                    <th>{{ __('list-badges.note') }}</th>
                    <th>{{ __('list-badges.actions') }}</th>
                </tr>
            </thead>
            <tbody id="header-list-body">
                @foreach ($headers as $header)
                    @php
                        // type = 0: state,  1: income, 2: expense, 3: correction
                        $lineClass = 'state';
                        if ($header->type() == 1) {
                            $lineClass = 'income';
                        } elseif ($header->type() == 2) {
                            $lineClass = 'expense';
                        } elseif ($header->type() == 3) {
                            $lineClass = 'correction';
                        }
                    @endphp
                    <tr class="{{ $lineClass }}">
                        <td>{{ \Carbon\Carbon::parse($header->date)->format($dateFormat) }}</td>
                        <td class="amount">{{ number_format($header->amount, 2) }}</td>
                        <td>
                            @if ($header->type() == 0)
                                {{ __('list-badges.state') }}
                            @else
                                {{ $header->place_of_purchase }}
                            @endif
                        </td>
                        <td>{{ $header->location }}</td>
                        <td class="amount">{{ number_format($header->badges()[$selectedBadge] ?? 0, 2) }}</td>
                        <td>{{ $header->description }}</td>
                        <td>
                            <form action="{{ route('entry.create', $header->id) }}" method="GET"
                                style="display:inline-block;">
                                <input type="hidden" name="blade" value="list-badges" />
                                <button type="submit" class="btn btn-primary">{{ __('list-badges.edit') }}</button>
                            </form>
                            <form action="{{ route('entry.destroy', $header->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('{{ __('list-badges.delete_confirmation') }}');">{{ __('list-badges.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination">
            {{ $headers->links('pagination::custom') }}
        </div>
    </div>
</x-layout>
