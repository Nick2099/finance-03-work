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
                <th>Date</th>
                <th>Amount</th>
                <th>Place of purchase</th>
                <th>Location</th>
                <th>Desc.</th>
                <th>Actions</th>
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
        {{ $headers->links('pagination::simple-default') }}
    </div>
</x-layout>
