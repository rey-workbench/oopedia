// Konfeti untuk peringkat teratas
document.addEventListener('DOMContentLoaded', function () {
    // Check if configuration exists
    if (typeof leaderboardConfig !== 'undefined' && leaderboardConfig.currentUserRank && leaderboardConfig.currentUserRank <= 3) {
        // Konfeti untuk peringkat 1-3
        const colors = [
            ['#004e98', '#0074d9'], // Dark blue - peringkat 1
            ['#0074d9', '#3498db'], // Medium blue - peringkat 2
            ['#3498db', '#4fc3f7']  // Light blue - peringkat 3
        ];

        const selectedColors = colors[leaderboardConfig.currentUserRank - 1];

        if (typeof confetti === 'function') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: selectedColors,
                startVelocity: 30,
                gravity: 0.5,
                ticks: 200,
                shapes: ['square', 'circle'],
                zIndex: 1000
            });
        }
    }
});

function showFeedback(result, score, attemptNumber) {
    // Placeholder function if needed by other components, though redundant here
}
