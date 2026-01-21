document.addEventListener('DOMContentLoaded', function() {
    // Si pas de cookie, on affiche la modale
    if (!getCookie('cube_consent')) {
        setTimeout(() => {
            document.getElementById('cookieOverlay').style.display = 'flex';
        }, 500);
    }
});


function setCookie(name, valueObject, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    // MODIFICATION ICI : on ajoute encodeURIComponent
    const jsonValue = encodeURIComponent(JSON.stringify(valueObject));
    document.cookie = name + "=" + jsonValue + expires + "; path=/";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for(let i=0;i < ca.length;i++) {
        let c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        // MODIFICATION ICI : on ajoute decodeURIComponent
        if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length,c.length));
    }
    return null;
}



function acceptCookies() {
    const choices = { analytics: true };
    setCookie('cube_consent', choices, 365);
    closeModal();
}


function refuseCookies() {
    const choices = { analytics: false };
    setCookie('cube_consent', choices, 365);
    closeModal();
}


function saveCustomPreferences() {
    const groups = document.querySelectorAll('.accordion-group');
    

    let analyticsAccepted = false;

    if (groups[1]) {
        // On regarde le header du groupe Mesure
        const headerBtns = groups[1].querySelector('.accordion-header .toggle-container').querySelectorAll('.toggle-btn');
     
        if (headerBtns[1].classList.contains('selected')) {
            analyticsAccepted = true;
        }
    }

    const choices = { analytics: analyticsAccepted };
    setCookie('cube_consent', choices, 365);
    closeModal();
}




function goToStep2() {
    document.getElementById('step1-container').style.display = 'none';
    document.getElementById('step2-container').style.display = 'flex';
    const modal = document.querySelector('.cookie-modal');
    modal.style.width = '600px'; 
    modal.style.height = 'auto';
    modal.style.maxHeight = '90vh';
    
    // On rebranche le bouton ENREGISTRER sur la fonction custom
    const btnSave = document.querySelector('.btn-save-choices');
    btnSave.onclick = saveCustomPreferences; 
}

function closeModal() {
    document.getElementById('cookieOverlay').style.display = 'none';
}

function toggleAccordion(header) {
    const group = header.parentElement;
    group.classList.toggle('active');
    const icon = header.querySelector('.icon');
    if(icon) icon.textContent = group.classList.contains('active') ? '-' : '+';
}

function toggleOption(btn) {
    const parent = btn.parentElement;
    const buttons = parent.querySelectorAll('.toggle-btn');
    buttons.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    updateParentState(btn);
}

function toggleGlobal(btn, action) {
    const parent = btn.parentElement;
    const buttons = parent.querySelectorAll('.toggle-btn');
    buttons.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    const group = btn.closest('.accordion-group');
    const body = group.querySelector('.accordion-body');
    const subSwitches = body.querySelectorAll('.toggle-container');

    subSwitches.forEach(container => {
        const btns = container.querySelectorAll('.toggle-btn');
        btns.forEach(b => b.classList.remove('selected'));
        if (action === 'accept') { btns[1].classList.add('selected'); } 
        else { btns[0].classList.add('selected'); }
    });
}

function updateParentState(childBtn) {
    const group = childBtn.closest('.accordion-group');
    if (!group) return;
    const headerToggle = group.querySelector('.accordion-header .toggle-container');
    const globalBtns = headerToggle.querySelectorAll('.toggle-btn');
    const subContainers = group.querySelector('.accordion-body').querySelectorAll('.toggle-container');

    let allAccepted = true;
    subContainers.forEach(container => {
        if (!container.querySelectorAll('.toggle-btn')[1].classList.contains('selected')) allAccepted = false;
    });

    globalBtns.forEach(b => b.classList.remove('selected'));
    if (allAccepted) { globalBtns[1].classList.add('selected'); } 
    else { globalBtns[0].classList.add('selected'); }
}