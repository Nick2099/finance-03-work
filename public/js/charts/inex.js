const canvas = document.getElementById("inexChart");
let inexChart;
function drawChart() {
    const ctx = canvas.getContext("2d");
    if (inexChart) {
        inexChart.destroy();
    }
    inexChart = new Chart(ctx, {
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
            responsive: false,
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

function resizeCanvas() {
    if (canvas.width < canvas.height) {
        let w = window.innerWidth;
        let h = window.innerHeight;
        let size = Math.min(w, h);
        canvas.width = size * 0.9;
        canvas.height = size * 0.9;
    } else {
        canvas.width = window.innerWidth * 0.9;
        canvas.height = window.innerHeight * 0.8;
    }
    
    console.log(`Canvas resized to ${canvas.width}x${canvas.height}`);
    drawChart();
}

resizeCanvas();
window.addEventListener("resize", resizeCanvas);
