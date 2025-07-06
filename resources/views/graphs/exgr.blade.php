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

        <label for="groupSelect">Select group:</label>
        <select name="group" id="groupSelect" onchange="this.form.submit()">
            @foreach ($groupNames as $group)
                <option value="{{ $group->id }}" @if (request('group', $selectedGroup ?? null) == $group->id) selected @endif>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>

        <label for="chartTypeSelect">Chart Type:</label>
        <select id="chartTypeSelect">
            <option value="grouped" selected>Grouped</option>
            <option value="stacked">Stacked</option>
        </select>

        <label for="chartStyleSelect">Chart Style:</label>
        <select id="chartStyleSelect">
            <option value="bar" selected>Columns</option>
            <option value="line">Lines</option>
        </select>
    </form>

    <div class="chart-container">
        <canvas id="exgrChart" style="width: 100%; height: 100%"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const monthsLabels = @json($months ?? []);
        const groupNames = @json($groupNames ?? []);
        const subgroupData = @json($subgroupData ?? []);
        const subgroupNames = @json($subgroupNames ?? []);
    </script>
    <script src="{{ asset('js/charts/exgr.js') }}"></script>

</x-layout>
