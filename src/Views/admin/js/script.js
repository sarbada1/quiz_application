document.addEventListener('DOMContentLoaded', function() {
    const submenuToggles = document.querySelectorAll('.submenu-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLi = this.closest('li');
            parentLi.classList.toggle('active');

            const submenu = parentLi.querySelector('.submenu');
            if (submenu) {
                if (parentLi.classList.contains('active')) {
                    submenu.style.display = 'block';
                } else {
                    submenu.style.display = 'none';
                }
            }
        });
    });
});

const closeButtons = document.querySelectorAll('.close'); // Select all close buttons

closeButtons.forEach(closeButton => {
  closeButton.addEventListener('click', () => {
    const successAlert = closeButton.parentElement; // Get the parent alert element
    successAlert.style.display = 'none';
  });
});




