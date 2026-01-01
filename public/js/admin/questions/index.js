function viewFullQuestion(questionId) {
    // Find the question by ID
    // questionsData must be defined in the blade view before including this script
    const question = questionsData.find(q => q.id === questionId);

    if (question) {
        // Set the modal content
        document.getElementById('fullQuestionContent').innerHTML = question.text;

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('fullQuestionModal'));
        modal.show();
    }
}
