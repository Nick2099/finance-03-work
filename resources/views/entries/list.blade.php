<x-layout>
    <?php
    // dd($groups);
    ?>
    <x-slot:heading>
        {{ __('list.heading') }}
    </x-slot>
    <h1>{{ __('list.h1') }}</h1>
    <div class="table">
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
                            <form action="{{ route('entry.create', $header->id) }}" method="GET" style="display:inline-block;">
                                <button type="submit" class="btn btn-primary" >{{ __('list.edit') }}</button>
                            </form>
                            <form action="{{ route('entry.destroy', $header->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('list.delete_confirmation') }}');">{{ __('list.delete') }}</button>
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
