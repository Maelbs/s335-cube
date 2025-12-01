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



document.addEventListener("DOMContentLoaded", () => {
    const track = document.querySelector(".carousel-track");
    const cards = document.querySelectorAll(".similar-card");
    const btnLeft = document.querySelector(".left-btn");
    const btnRight = document.querySelector(".right-btn");
 
    if (!track || cards.length === 0) return;
 
    let index = 0;
    const cardWidth = cards[0].offsetWidth + 20; // largeur + gap
    const maxIndex = cards.length - 4; // car tu affiches 4 cartes
 
    function updateCarousel() {
        track.style.transform = `translateX(-${index * cardWidth}px)`;
    }
 
    btnRight.addEventListener("click", () => {
        if (index < maxIndex) {
            index++;
            updateCarousel();
        }
    });
 
    btnLeft.addEventListener("click", () => {
        if (index > 0) {
            index--;
            updateCarousel();
        }
    });
 
    // Ajustement au redimensionnement (responsive)
    window.addEventListener("resize", () => {
        const newWidth = cards[0].offsetWidth + 20;
        track.style.transform = `translateX(-${index * newWidth}px)`;
    });
});