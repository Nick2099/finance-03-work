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

    <div style="max-width: 100%; margin: 0.5em auto;">
        <canvas id="inexChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const monthsLabels = @json($months ?? []);
        const incomeData = @json($incomeData ?? []);
        const expenseData = @json($expenseData ?? []);
        const correctionData = @json($correctionData ?? []);
        const incomeLabel = @json($income ?? 'Income');
        const expenseLabel = @json($expense ?? 'Expense');
        const correctionLabel = @json($correction ?? 'Correction');
    </script>
    <script src="{{ asset('js/charts/inex.js') }}"></script>

</x-layout>
