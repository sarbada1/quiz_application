// Get the modals
var loginModal = document.getElementById("loginModal");
var registerModal = document.getElementById("registerModal");

// Get the buttons that open the modals
var startQuizzingBtn = document.getElementById("startQuizzingBtn");
var registerNowLink = document.getElementById("registerNowLink");

// Get the <span> elements that close the modals
var closeButtons = document.getElementsByClassName("close");

// When the user clicks the "Start Quizzing Now" button, open the login modal 
startQuizzingBtn.onclick = function(event) {
    event.preventDefault(); // Prevent default link behavior
    loginModal.style.display = "block";
}

// When the user clicks the "Register now" link, close the login modal and open the register modal
registerNowLink.onclick = function(event) {
    event.preventDefault(); // Prevent default link behavior
    loginModal.style.display = "none";
    registerModal.style.display = "block";
}
// When the user clicks the "Login now" link, close the register modal and open the login modal
loginNowLink.onclick = function(event) {
    event.preventDefault(); // Prevent default link behavior
    registerModal.style.display = "none";
    loginModal.style.display = "block";
}

// When the user clicks on <span> (x), close the current modal
for (var i = 0; i < closeButtons.length; i++) {
    closeButtons[i].onclick = function() {
        this.parentElement.parentElement.style.display = "none";
    }
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == loginModal) {
        loginModal.style.display = "none";
    } else if (event.target == registerModal) {
        registerModal.style.display = "none";
    }
}
// alert
const closealertButtons = document.querySelectorAll('.closealert'); // Select all close buttons

closealertButtons.forEach(closealertButton => {
    closealertButton.addEventListener('click', () => {
    const successAlert = closealertButton.parentElement; // Get the parent alert element
    successAlert.style.display = 'none';
  });
});

