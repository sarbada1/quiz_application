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


document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    titleInput.addEventListener('input', function() {
        const slug = generateSlug(this.value);
        slugInput.value = slug;
    });

    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')       // Replace spaces with -
            .replace(/[^\w\-]+/g, '')   // Remove all non-word chars
            .replace(/\-\-+/g, '-')     // Replace multiple - with single -
            .replace(/^-+/, '')         // Trim - from start of text
            .replace(/-+$/, '');        // Trim - from end of text
    }
});

