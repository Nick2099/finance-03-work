<x-layout>
    <x-slot:heading>
        {{ $labelHeading }}
    </x-slot>
    <h1>{{ $labelHeader }}</h1>

    <form method="GET" action="">

        @if (isset($chooseYear) && $chooseYear)
            <label for="year-select">{{ $labelYear }}:</label>
            <select name="year" id="year-select" onchange="this.form.submit()">
                @foreach ($years ?? [] as $year)
                    <option value="{{ $year }}" @if (request('year', $selectedYear ?? null) == $year) selected @endif>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        @endif

        @if (isset($chooseGroup) && $chooseGroup)
            <label for="groupSelect">{{ $labelGroup }}:</label>
            <select name="group" id="groupSelect" onchange="this.form.submit()">
                @foreach ($groupNames as $group)
                    <option value="{{ $group->id }}" @if (request('group', $selectedGroup ?? null) == $group->id) selected @endif>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        @endif

        @if (isset($chooseChartType) && $chooseChartType)
            <label for="chartTypeSelect">{{ $labelChartType }}:</label>
            <select name="chartType" id="chartTypeSelect" onchange="this.form.submit()">
                <option value="grouped" {{ ($currentChartType ?? 'grouped') == 'grouped' ? 'selected' : '' }}>
                    {{ $labelGrouped ?? 'Grouped' }}
                </option>
                <option value="stacked" {{ ($currentChartType ?? 'grouped') == 'stacked' ? 'selected' : '' }}
                    @if (($currentChartStyle ?? 'bar') !== 'bar') disabled @endif>{{ $labelStacked ?? "Stacked"}}</option>
            </select>
        @endif

        @if (isset($chooseChartStyle) && $chooseChartStyle)
            <label for="chartStyleSelect">{{ $labelChartStyle }}:</label>
            <select name="chartStyle" id="chartStyleSelect" onchange="this.form.submit()">
                <option value="bar" {{ ($currentChartStyle ?? 'bar') == 'bar' ? 'selected' : '' }}>{{ $labelColumns ?? "Columns"}}</option>
                <option value="line" {{ ($currentChartStyle ?? 'bar') == 'line' ? 'selected' : '' }}>{{ $labelLines ?? "Lines"}}</option>
            </select>
        @endif

    </form>

    <div class="chart-container">
        <canvas id="chart" style="width: 100%; height: 100%"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const monthsLabels = @json($months ?? []);
        const graphLabels = @json($graphLabels ?? []);
        const graphData = @json($graphData ?? []);
        const currentChartType = @json($currentChartType ?? []);
        const currentChartStyle = @json($currentChartStyle ?? []);
        const stackedGroups = @json($stackedGroups ?? []);
    </script>

    <script type="module" src="{{ asset('js/graphs-new/graphs.js') }}"></script>
</x-layout>
