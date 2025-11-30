document.addEventListener('DOMContentLoaded', function() {
    
    const toggles = document.querySelectorAll('.toggle-specs-btn');

    toggles.forEach(btn => {
        btn.textContent = ''; 

        const headerRow = btn.closest('.specs-header-row');
        const content = headerRow.nextElementSibling;

        if (content) {
            content.classList.add('toggle-content');
        }

        btn.addEventListener('click', function() {
            if (content) {
                const isOpen = content.classList.contains('is-visible');

                if (isOpen) {
                    content.style.maxHeight = null; 
                    content.classList.remove('is-visible');
                    this.classList.remove('is-active');

                } else {
                    content.classList.add('is-visible');
                    content.style.maxHeight = content.scrollHeight + "px";
                    this.classList.add('is-active');
                }
            }
        });
    });
});