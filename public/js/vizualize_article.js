document.addEventListener('DOMContentLoaded', function() {
    
    // --- PARTIE 1 : TOGGLE DES SPÉCIFICATIONS (inchangé) ---
    const toggles = document.querySelectorAll('.toggle-specs-btn');

    toggles.forEach(btn => {
        // On vide le texte si besoin, comme dans ton script original
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

    // --- PARTIE 2 : LE NOUVEAU CARROUSEL ---
    const track = document.querySelector(".st-carousel-track");
    const btnLeft = document.querySelector(".st-btn-left");
    const btnRight = document.querySelector(".st-btn-right");

    // Sécurité : si les éléments n'existent pas sur la page, on arrête là
    if (!track || !btnLeft || !btnRight) return;

    // Fonction pour scroller
    function scrollCarousel(direction) {
        // On récupère la largeur d'une carte + l'espace (gap)
        // Le gap dans ton CSS est de 25px
        const card = track.querySelector(".st-card-item");
        const scrollAmount = card ? (card.offsetWidth + 25) : 300; 

        if (direction === 'left') {
            track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }

    btnLeft.addEventListener("click", () => scrollCarousel('left'));
    btnRight.addEventListener("click", () => scrollCarousel('right'));
});