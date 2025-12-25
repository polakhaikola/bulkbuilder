// Tracker Functionality

document.addEventListener('DOMContentLoaded', function () {

    // 1. Chart.js Initialization
    const ctx = document.getElementById('calChart').getContext('2d');

    // Calculate remaining (don't let it be negative for the chart's sake)
    const consumed = chartData.cal;
    const remaining = Math.max(0, chartData.goal - consumed);
    const chartColor = consumed > chartData.goal ? '#dc3545' : '#28a745'; // Red if over, Green if under

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Consumed', 'Remaining'],
            datasets: [{
                data: [consumed, remaining],
                backgroundColor: [
                    chartColor,
                    '#333333' // Dark grey for empty part
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '80%', // Thinner ring
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        }
    });

    // 2. Recipe Auto-fill Logic
    const recipeSelect = document.getElementById('recipeSelect');
    const foodName = document.getElementById('foodName');
    const calInput = document.getElementById('calInput');
    const proInput = document.getElementById('proInput');
    const carbsInput = document.getElementById('carbsInput');
    const fatsInput = document.getElementById('fatsInput');

    if (recipeSelect) {
        recipeSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {
                // Populate fields from data attributes
                foodName.value = selectedOption.getAttribute('data-title');
                calInput.value = selectedOption.getAttribute('data-cal');
                proInput.value = selectedOption.getAttribute('data-pro');
                carbsInput.value = selectedOption.getAttribute('data-carbs');
                fatsInput.value = selectedOption.getAttribute('data-fats');
            } else {
                // Clear fields if reset
                foodName.value = '';
                calInput.value = '';
                proInput.value = '';
                carbsInput.value = '';
                fatsInput.value = '';
            }
        });
    }

    // 3. Handle Auto-fill from URL (Integration)
    const urlParams = new URLSearchParams(window.location.search);
    const prefillId = urlParams.get('prefill_recipe_id');

    if (prefillId && recipeSelect) {
        recipeSelect.value = prefillId;
        // Trigger change event manually to update inputs
        // Wait a tick to ensure options are loaded/rendered if needed, though they are static here
        setTimeout(() => {
            recipeSelect.dispatchEvent(new Event('change'));
            // Scroll to form
            const logForm = document.getElementById('logForm');
            if (logForm) logForm.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    }
});
