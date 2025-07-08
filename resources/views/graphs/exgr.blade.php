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

        <label for="chartStyleSelect">Chart Style:</label>
        <select name="chartStyle" id="chartStyleSelect" onchange="this.form.submit()">
            <option value="bar" {{ request('chartStyle', 'bar') == 'bar' ? 'selected' : '' }}>Columns</option>
            <option value="line" {{ request('chartStyle', 'bar') == 'line' ? 'selected' : '' }}>Lines</option>
        </select>

        <label for="chartTypeSelect">Chart Type:</label>
        <select name="chartType" id="chartTypeSelect" onchange="this.form.submit()">
            <option value="grouped" {{ ($currentChartType ?? 'grouped') == 'grouped' ? 'selected' : '' }}>Grouped</option>
            <option value="stacked" 
                {{ ($currentChartType ?? 'grouped') == 'stacked' ? 'selected' : '' }}
                @if(($currentChartStyle ?? 'bar') !== 'bar') disabled @endif
            >Stacked</option>
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
        const currentChartType = @json($currentChartType ?? []);
        const currentChartStyle = @json($currentChartStyle ?? []);
    </script>
    <script src="{{ asset('js/charts/exgr.js') }}"></script>

</x-layout>
