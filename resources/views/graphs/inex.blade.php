<x-layout>
    <x-slot:heading>
        Months
    </x-slot>

    <h1>Months</h1>

    <form method="GET" action="" style="text-align: center; margin-bottom: 2em;">
        <label for="year-select" style="font-weight: bold; margin-right: 0.5em;">Year:</label>
        <select name="year" id="year-select" onchange="this.form.submit()" style="padding: 0.3em 1em; font-size: 1em;">
            @foreach(($years ?? []) as $year)
                <option value="{{ $year }}" @if(request('year', $selectedYear ?? null) == $year) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
    </form>

    <div style="max-width: 700px; margin: 2em auto;">
        <canvas id="monthsChart" width="600" height="350"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('monthsChart').getContext('2d');
        const monthsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months ?? []) !!},
                datasets: [
                    {
                        label: '{{ $income }}',
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        data: {!! json_encode($incomeData ?? []) !!},
                        stack: 'Income',
                    },
                    {
                        label: '{{ $expense }}',
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        data: {!! json_encode($expenseData ?? []) !!},
                        stack: 'Expense+Correction',
                    },
                    {
                        label: '{{ $correction }}',
                        backgroundColor: 'rgba(255, 205, 86, 0.7)',
                        data: {!! json_encode($correctionData ?? []) !!},
                        stack: 'Expense+Correction',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    </script>
</x-layout>
