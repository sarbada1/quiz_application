document.addEventListener('DOMContentLoaded', function() {
    // Open login modal
    document.querySelectorAll('#startQuizzingBtn, #startQuizzing, #startQuiz').forEach(function(btn) {
        btn.onclick = function(event) {
            event.preventDefault();
            document.getElementById('loginModal').style.display = 'block';
        }
    });

    // Open register modal
    document.getElementById('signUpLink').onclick = function(event) {
        event.preventDefault();
        document.getElementById('registerModal').style.display = 'block';
    }

    // Switch between login and register modals
    document.getElementById('registerNowLink').onclick = function(event) {
        event.preventDefault();
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('registerModal').style.display = 'block';
    }

    document.getElementById('loginNowLink').onclick = function(event) {
        event.preventDefault();
        document.getElementById('registerModal').style.display = 'none';
        document.getElementById('loginModal').style.display = 'block';
    }

    // Close modals
    document.querySelectorAll('.close').forEach(function(closeBtn) {
        closeBtn.onclick = function() {
            var modalId = this.getAttribute('data-modal');
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                // Add navigation back if on quiz or test pages
                if (window.location.pathname.includes('/quiz/') || 
                    window.location.pathname.includes('/mocktest/')) {
                    window.history.back();
                }
            }
        }
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
            // Add navigation back if on quiz or test pages
            if (window.location.pathname.includes('/quiz/') || 
                window.location.pathname.includes('/mocktest/')) {
                window.history.back();
            }
        }
    }

    // Error handling for missing elements
    const handleError = function(id) {
        console.error(`Element with id ${id} not found`);
    }

    // Verify required elements exist
    ['loginModal', 'registerModal', 'signUpLink', 'registerNowLink', 'loginNowLink'].forEach(id => {
        if (!document.getElementById(id)) {
            handleError(id);
        }
    });
});