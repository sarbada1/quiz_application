function createSet(quizId) {
    if (confirm('Create new set?')) {
        const form = document.querySelector(`form[action="/admin/quiz/sets-create/${quizId}"]`);
        if (form) {
            form.submit();
        }
    }
    return false; // Prevent default form submission
}

function generateQuestions(setId) {
    fetch(`/admin/quiz/sets/${setId}/generate`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(handleResponse);
}

function publishSet(setId) {
    if (confirm('Publish this set?')) {
        fetch(`/admin/quiz/sets/${setId}/publish`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(handleResponse);
    }
}

function deleteSet(setId) {
    if (confirm('Delete this set?')) {
        fetch(`/admin/quiz/sets/${setId}/delete`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(handleResponse);
    }
}

function handleResponse(data) {
    if (data.success) {
        location.reload();
    } else {
        alert(data.error);
    }
}