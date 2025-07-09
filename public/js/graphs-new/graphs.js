const canvas = document.getElementById("chart");
let chart;

console.log("Months Labels:", monthsLabels);
console.log("Graph Labels:", graphLabels);
console.log("Graph Data:", graphData);

function getDatasets() {
    const datasets = [];
    for (const labelId in graphLabels) {
        if (graphData[labelId]) {
            const color = getColorForGroup(labelId);
            const dataset = {
                label: graphLabels[labelId],
                data: graphData[labelId],
                backgroundColor: color,
                stack: currentChartType === "stacked" ? "expense" : undefined,
            };
            if (currentChartStyle === "line") {
                dataset.borderColor = color;
                dataset.pointBackgroundColor = color;
                dataset.backgroundColor = color + "33"; // semi-transparent fill
                dataset.fill = false;
            }
            datasets.push(dataset);
        }
    }
    console.log("Datasets:", datasets);
    return datasets;
}

function getColorForGroup(labelId) {
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
    return palette[labelId % palette.length];
}

function drawChart() {
    const ctx = canvas.getContext("2d");
    if (chart) {
        chart.destroy();
    }
    const isStacked = currentChartType === "stacked";
    chart = new Chart(ctx, {
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
            if (currentChartStyle === "line") {
                chartTypeSelect.value = "grouped"; // Reset to grouped for line charts
                currentChartType = "grouped";
                // Submit the form to update the URL and reload with correct params
                chartStyleSelect.form.submit();
                return; // Prevent drawChart() since page will reload
            }
            drawChart();
        });
    }

    const groupSelect = document.getElementById("groupSelect");
    if (groupSelect) {
        groupSelect.addEventListener("change", function (e) {
            currentGroup = e.target.value;
            drawChart();
        });
    }

    drawChart();
});

window.addEventListener("resize", drawChart);
