// Data passed from Blade (ex.blade.php) via @json
// Example:
// let monthsLabels = [...];
// let groupNames = {...}; // {1: 'Food', 2: 'Transport', ...}
// let groupData = {...}; // {1: [100,120,...], 2: [80,90,...], ...}

console.log("ExController.js loaded");
console.log("Months Labels:", monthsLabels);
console.log("Group Names:", groupNames);
console.log("Group Data:", groupData);

const canvas = document.getElementById("exChart");
let exChart;
let currentChartType = "grouped"; // default
let currentChartStyle = "bar"; // default

function getDatasets() {
    // groupNames: {id: name}, groupData: {id: [amounts]}
    const datasets = [];
    for (const groupId in groupNames) {
        if (groupData[groupId]) {
            const color = getColorForGroup(groupId);
            const dataset = {
                label: groupNames[groupId],
                data: groupData[groupId],
                backgroundColor: color,
                stack: currentChartType === "stacked" ? "expense" : undefined,
            };
            // If line chart, set borderColor and pointBackgroundColor
            if (currentChartStyle === "line") {
                dataset.borderColor = color;
                dataset.pointBackgroundColor = color;
                dataset.backgroundColor = color + "33"; // semi-transparent fill
                dataset.fill = false;
            }
            datasets.push(dataset);
        }
    }
    return datasets;
}

function getColorForGroup(groupId) {
    // Simple color palette, can be improved
    const palette = [
        "#4e79a7",
        "#f28e2b",
        "#e15759",
        "#76b7b2",
        "#59a14f",
        "#edc949",
        "#af7aa1",
        "#ff9da7",
        "#9c755f",
        "#bab0ab",
        "#b07aa1",
        "#7a9ba1",
    ];
    return palette[groupId % palette.length];
}

function drawChart() {
    const ctx = canvas.getContext("2d");
    if (exChart) {
        exChart.destroy();
    }
    const isStacked = currentChartType === "stacked";
    exChart = new Chart(ctx, {
        type: currentChartStyle,
        data: {
            labels: monthsLabels,
            datasets: getDatasets(),
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: "top" },
                title: { display: false },
            },
            scales: {
                x: { stacked: isStacked },
                y: { stacked: isStacked, beginAtZero: true },
            },
        },
    });
}

// Dropdown for chart type
document.addEventListener("DOMContentLoaded", function () {
    const chartTypeSelect = document.getElementById("chartTypeSelect");
    if (chartTypeSelect) {
        chartTypeSelect.addEventListener("change", function (e) {
            currentChartType = e.target.value;
            drawChart();
        });
    }

    const chartStyleSelect = document.getElementById("chartStyleSelect");
    if (chartStyleSelect) {
        chartStyleSelect.addEventListener("change", function (e) {
            currentChartStyle = e.target.value;
            drawChart();
        });
    }

    drawChart();
});

window.addEventListener("resize", drawChart);
