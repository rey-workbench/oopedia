document.addEventListener('DOMContentLoaded', function () {
    // Function to calculate total questions
    function calculateTotal() {
        const beginnerInput = document.getElementsByName('beginner_count')[0];
        const mediumInput = document.getElementsByName('medium_count')[0];
        const hardInput = document.getElementsByName('hard_count')[0];

        const beginnerCount = beginnerInput ? (parseInt(beginnerInput.value) || 0) : 0;
        const mediumCount = mediumInput ? (parseInt(mediumInput.value) || 0) : 0;
        const hardCount = hardInput ? (parseInt(hardInput.value) || 0) : 0;

        const total = beginnerCount + mediumCount + hardCount;
        const totalCountEl = document.getElementById('totalCount');
        if (totalCountEl) totalCountEl.textContent = total;

        // Visual feedback
        const totalEl = document.getElementById('totalQuestions');
        if (totalEl) {
            if (total <= 0) {
                totalEl.classList.remove('alert-info');
                totalEl.classList.add('alert-danger');
            } else {
                totalEl.classList.remove('alert-danger');
                totalEl.classList.add('alert-info');
            }
        }
    }

    // Add event listeners to all count inputs
    const beginnerInput = document.getElementsByName('beginner_count')[0];
    const mediumInput = document.getElementsByName('medium_count')[0];
    const hardInput = document.getElementsByName('hard_count')[0];

    if (beginnerInput) beginnerInput.addEventListener('input', calculateTotal);
    if (mediumInput) mediumInput.addEventListener('input', calculateTotal);
    if (hardInput) hardInput.addEventListener('input', calculateTotal);

    // Calculate on page load
    calculateTotal();
});
