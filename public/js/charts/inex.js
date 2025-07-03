const canvas = document.getElementById("inexChart");
function resizeCanvas() {
    canvas.width = window.innerWidth * 0.9;
    canvas.height = window.innerHeight * 0.8;
    if (canvas.width < canvas.height) canvas.height = canvas.width;
}
resizeCanvas();
window.addEventListener('resize', function() {
    resizeCanvas();
    monthsChart.resize();
});
const ctx = canvas.getContext("2d");
const monthsChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: monthsLabels,
        datasets: [
            {
                label: incomeLabel,
                backgroundColor: "rgba(54, 162, 235, 0.7)",
                data: incomeData,
                stack: "Income",
            },
            {
                label: expenseLabel,
                backgroundColor: "rgba(255, 99, 132, 0.7)",
                data: expenseData,
                stack: "Expense+Correction",
            },
            {
                label: correctionLabel,
                backgroundColor: "rgba(255, 205, 86, 0.7)",
                data: correctionData,
                stack: "Expense+Correction",
            },
        ],
    },
    options: {
        responsive: true,
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
