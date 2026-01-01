document.addEventListener('DOMContentLoaded', () => {
    const draggables = document.querySelectorAll('.draggable');
    const dropZones = document.querySelectorAll('.drop-zone');
    const answerInput = document.getElementById('dragAndDropAnswers');
    const questionForm = document.getElementById('questionForm');

    if (draggables.length > 0) {
        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', draggable.getAttribute('data-value'));
                e.dataTransfer.effectAllowed = 'move';
            });
        });

        dropZones.forEach(zone => {
            zone.addEventListener('dragover', e => {
                e.preventDefault();
                zone.style.border = '2px dashed #007bff';
            });

            zone.addEventListener('dragleave', e => {
                zone.style.border = 'none';
            });

            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.style.border = 'none';
                const value = e.dataTransfer.getData('text/plain');
                zone.textContent = value;
                zone.setAttribute('data-user-answer', value);

                // Update hidden input with user answers
                if (answerInput) {
                    const answers = Array.from(dropZones).map(z => ({
                        zone: z.getAttribute('data-zone'),
                        answer: z.getAttribute('data-user-answer')
                    }));
                    answerInput.value = JSON.stringify(answers);
                }
            });
        });
    }

    // Form validation before submission
    if (questionForm && answerInput) {
        questionForm.addEventListener('submit', function (e) {
            // Only validate if it's a drag and drop question (checked via existence of answerInput)
            if (answerInput) {
                const dragAndDropAnswers = answerInput.value;
                try {
                    const parsed = JSON.parse(dragAndDropAnswers || '[]');
                    const isComplete = parsed.length > 0 && parsed.every(z => z.answer && z.answer.trim() !== '');

                    if (!isComplete) {
                        e.preventDefault();
                        alert("Harap isi semua zona jawaban!");
                        // Re-enable submit button if it was disabled by other listeners
                        const btn = document.getElementById('checkAnswerBtn');
                        if (btn) {
                            setTimeout(() => {
                                btn.disabled = false;
                                btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Periksa Jawaban';
                            }, 100);
                        }
                    }
                } catch (error) {
                    // JSON parse error or empty
                    // Depending on logic, might just let it proceed or block
                }
            }
        });
    }
});
