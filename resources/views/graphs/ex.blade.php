<x-layout>
    <x-slot:heading>
        {{ $heading }}
    </x-slot>

    <h1>{{ $heading }}</h1>

    <form method="GET" action="">
        <label for="year-select">{{ $year }}:</label>
        <select name="year" id="year-select" onchange="this.form.submit()">
            @foreach ($years ?? [] as $year)
                <option value="{{ $year }}" @if (request('year', $selectedYear ?? null) == $year) selected @endif>{{ $year }}
                </option>
            @endforeach
        </select>
    </form>

    <div class="chart-container">
        <canvas id="exChart" style="width: 100%; height: 100%"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const monthsLabels = @json($months ?? []);
        const groupData = @json($groupData ?? []);
        const groupNames = @json($groupNames ?? []);
    </script>
    <script src="{{ asset('js/charts/ex.js') }}"></script>

</x-layout>
