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

function getDatasets() {
    // groupNames: {id: name}, groupData: {id: [amounts]}
    const datasets = [];
    for (const groupId in groupNames) {
        if (groupData[groupId]) {
            datasets.push({
                label: groupNames[groupId],
                data: groupData[groupId],
                backgroundColor: getColorForGroup(groupId),
                stack: 'expense',
            });
        }
    }
    return datasets;
}

function getColorForGroup(groupId) {
    // Simple color palette, can be improved
    const palette = [
        '#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc949',
        '#af7aa1', '#ff9da7', '#9c755f', '#bab0ab', '#b07aa1', '#7a9ba1'
    ];
    return palette[groupId % palette.length];
}

function drawChart() {
    const ctx = canvas.getContext("2d");
    if (exChart) {
        exChart.destroy();
    }
    exChart = new Chart(ctx, {
        type: "bar",
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
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true },
            },
        },
    });
}

drawChart();
window.addEventListener("resize", drawChart);
