document.addEventListener('DOMContentLoaded', function() {
    
    const cookieBanner = document.getElementById('cookieBanner');
    const cookieModal = document.getElementById('cookieModal');
    
    const btnAccept = document.getElementById('acceptCookies');
    const btnCustomize = document.getElementById('customizeCookies');
    const btnDecline = document.getElementById('declineCookies');
    
    const btnCloseModal = document.getElementById('closeModal');
    const btnAcceptAllModal = document.getElementById('acceptAllModal');
    const btnRejectAllModal = document.getElementById('rejectAllModal');
    const btnSavePreferences = document.getElementById('savePreferences');

    if (!localStorage.getItem('cube_cookies_accepted')) {
        setTimeout(() => {
            cookieBanner.classList.add('show');
        }, 500);
    }

    function closeBanner() {
        cookieBanner.classList.remove('show');
    }

    function openPreferences() {
        closeBanner();
        cookieModal.classList.add('active');
    }

    function closePreferences() {
        cookieModal.classList.remove('active');
    }

    function saveAndClose(consentType) {
        localStorage.setItem('cube_cookies_accepted', 'true');
        localStorage.setItem('cube_cookie_type', consentType);
        
        closeBanner();
        closePreferences();
    }

    if(btnAccept) {
        btnAccept.addEventListener('click', () => {
            saveAndClose('all');
        });
    }

    if(btnCustomize) {
        btnCustomize.addEventListener('click', openPreferences);
    }

    if(btnDecline) {
        btnDecline.addEventListener('click', () => {
            saveAndClose('necessary');
        });
    }

    if(btnCloseModal) {
        btnCloseModal.addEventListener('click', () => {
            closePreferences();
            cookieBanner.classList.add('show');
        });
    }

    if(btnAcceptAllModal) {
        btnAcceptAllModal.addEventListener('click', () => {
            document.querySelectorAll('.toggle-switch input:not(:disabled)').forEach(input => input.checked = true);
            saveAndClose('all');
        });
    }

    if(btnRejectAllModal) {
        btnRejectAllModal.addEventListener('click', () => {
            document.querySelectorAll('.toggle-switch input:not(:disabled)').forEach(input => input.checked = false);
            saveAndClose('necessary');
        });
    }

    if(btnSavePreferences) {
        btnSavePreferences.addEventListener('click', () => {
            saveAndClose('custom');
        });
    }
});