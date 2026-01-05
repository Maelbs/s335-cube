document.addEventListener("DOMContentLoaded", function() {
    const overlay = document.getElementById('cookieOverlay');
    // Vérification du cookie existant
    if (localStorage.getItem('cubeCookiesChoice')) {
        overlay.style.display = 'none';
    }
});

/* --- NAVIGATION --- */

function goToStep2() {
    document.getElementById('step1-container').style.display = 'none';
    document.getElementById('step2-container').style.display = 'flex';
    
    // Redimensionnement pour l'étape 2 (Format portrait)
    const modal = document.querySelector('.cookie-modal');
    modal.style.width = '600px';
    modal.style.height = '650px';
}

function acceptCookies() {
    localStorage.setItem('cubeCookiesChoice', 'accepted');
    closeModal();
}

function refuseCookies() {
    localStorage.setItem('cubeCookiesChoice', 'refused');
    closeModal();
}

function closeModal() {
    document.getElementById('cookieOverlay').style.display = 'none';
}

/* --- LOGIQUE ACCORDÉON --- */

function toggleAccordion(header) {
    const group = header.parentElement;
    group.classList.toggle('active');

    // Changement visuel du + en -
    const icon = header.querySelector('.icon');
    if(icon) {
        icon.textContent = group.classList.contains('active') ? '-' : '+';
    }
}

/* --- LOGIQUE DES SWITCHS (C'est ici que ça change) --- */

// 1. Clic sur un bouton INDIVIDUEL (Enfant)
function toggleOption(btn) {
    // A. Gestion visuelle du bouton cliqué
    const parent = btn.parentElement;
    const buttons = parent.querySelectorAll('.toggle-btn');
    buttons.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    // B. Synchronisation remontante (Enfants -> Parent)
    updateParentState(btn);
}

// 2. Clic sur un bouton GLOBAL (Parent)
function toggleGlobal(btn, action) {
    // A. Gestion visuelle du bouton parent
    // (On utilise toggleOption juste pour le visuel du bouton cliqué)
    const parent = btn.parentElement;
    const buttons = parent.querySelectorAll('.toggle-btn');
    buttons.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    // B. Synchronisation descendante (Parent -> Enfants)
    const group = btn.closest('.accordion-group');
    const body = group.querySelector('.accordion-body');
    
    // Trouver tous les switchs enfants
    const subSwitches = body.querySelectorAll('.toggle-container');

    subSwitches.forEach(container => {
        const btns = container.querySelectorAll('.toggle-btn');
        btns.forEach(b => b.classList.remove('selected'));

        // Si action 'accept', on active le 2ème bouton (index 1), sinon le 1er (index 0)
        if (action === 'accept') {
            btns[1].classList.add('selected'); 
        } else {
            btns[0].classList.add('selected');
        }
    });
}

// 3. Fonction de vérification (Magie Enfants -> Parent)
function updateParentState(childBtn) {
    // On remonte au groupe complet
    const group = childBtn.closest('.accordion-group');
    if (!group) return;

    // On récupère le header (pour changer les boutons globaux)
    const headerToggle = group.querySelector('.accordion-header .toggle-container');
    const globalBtns = headerToggle.querySelectorAll('.toggle-btn'); // [0]=Refuser, [1]=Accepter

    // On récupère tous les choix enfants actuels
    const body = group.querySelector('.accordion-body');
    const subContainers = body.querySelectorAll('.toggle-container');

    let allAccepted = true;
    let allRefused = true;

    subContainers.forEach(container => {
        const btns = container.querySelectorAll('.toggle-btn');
        // btns[0] est "Refuser", btns[1] est "Accepter"
        const isRefused = btns[0].classList.contains('selected');
        const isAccepted = btns[1].classList.contains('selected');

        if (!isAccepted) allAccepted = false; // S'il y en a un qui n'est pas accepté
        if (!isRefused) allRefused = false;   // S'il y en a un qui n'est pas refusé
    });

    // Mise à jour du bouton Global
    globalBtns.forEach(b => b.classList.remove('selected'));

    if (allAccepted) {
        // Si TOUT est accepté -> Global Accepter
        globalBtns[1].classList.add('selected');
    } else {
        // Si tout est refusé OU mélange -> Global Refuser (Par défaut dans les CMP)
        globalBtns[0].classList.add('selected');
    }
}