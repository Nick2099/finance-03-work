<x-layout>
    <?php
    // dd($groups);
    ?>
    <x-slot:heading>
        List
    </x-slot>
    <h1>List</h1>
    <table id="header-list">
        <thead>
            <tr>
                <th>{{ __('list.date') }}</th>
                <th>{{ __('list.amount') }}</th>
                <th>{{ __('list.place_of_purchase') }}</th>
                <th>{{ __('list.location') }}</th>
                <th>{{ __('list.description') }}</th>
                <th>{{ __('list.actions') }}</th>
            </tr>
        </thead>
        <tbody id="header-list-body">
            @foreach ($headers as $header)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($header->date)->format($dateFormat) }}</td>
                    <td>{{ number_format($header->amount, 2) }}</td>
                    <td>{{ $header->place_of_purchase }}</td>
                    <td>{{ $header->location }}</td>
                    <td>{{ $header->description }}</td>
                    <td>
                        <!-- Add action buttons/links here -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">
        {{ $headers->links('pagination::custom') }}
    </div>
</x-layout>
