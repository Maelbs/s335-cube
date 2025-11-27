// // Cookie Banner Management
// document.addEventListener('DOMContentLoaded', function() {
//     const cookieBanner = document.getElementById('cookieBanner');
//     const cookieModal = document.getElementById('cookieModal');
//     const acceptBtn = document.getElementById('acceptCookies');
//     const declineBtn = document.getElementById('declineCookies');
//     const customizeBtn = document.getElementById('customizeCookies');
//     const closeModalBtn = document.getElementById('closeModal');
//     const acceptAllModalBtn = document.getElementById('acceptAllModal');
//     const savePreferencesBtn = document.getElementById('savePreferences');
//     const rejectAllModalBtn = document.getElementById('rejectAllModal');

//     // Check if user has already made a cookie choice
//     const cookieChoice = getCookie('cookie_consent');
    
//     if (cookieChoice) {
//         hideCookieBanner();
//         loadSavedPreferences();
//     } else {
//         showCookieBanner();
//     }

//     // Accept all cookies
//     acceptBtn.addEventListener('click', function() {
//         setCookie('cookie_consent', 'accepted', 365);
//         setCookie('cookie_analytics', 'true', 365);
//         setCookie('cookie_marketing', 'true', 365);
//         setCookie('cookie_preferences', 'true', 365);
//         hideCookieBanner();
//         console.log('All cookies accepted');
//         enableAnalytics();
//     });

//     // Decline all non-essential cookies
//     declineBtn.addEventListener('click', function() {
//         setCookie('cookie_consent', 'declined', 365);
//         setCookie('cookie_analytics', 'false', 365);
//         setCookie('cookie_marketing', 'false', 365);
//         setCookie('cookie_preferences', 'false', 365);
//         hideCookieBanner();
//         console.log('All non-essential cookies declined');
//     });

//     // Open customization modal
//     customizeBtn.addEventListener('click', function() {
//         openModal();
//         loadSavedPreferences();
//     });

//     // Close modal
//     closeModalBtn.addEventListener('click', function() {
//         closeModal();
//     });

//     // Click outside modal to close
//     cookieModal.addEventListener('click', function(e) {
//         if (e.target === cookieModal) {
//             closeModal();
//         }
//     });

//     // Accept all from modal
//     acceptAllModalBtn.addEventListener('click', function() {
//         document.getElementById('analyticsCookies').checked = true;
//         document.getElementById('marketingCookies').checked = true;
//         document.getElementById('preferenceCookies').checked = true;
//         saveCustomPreferences();
//         closeModal();
//         hideCookieBanner();
//     });

//     // Save custom preferences
//     savePreferencesBtn.addEventListener('click', function() {
//         saveCustomPreferences();
//         closeModal();
//         hideCookieBanner();
//     });

//     // Reject all from modal
//     rejectAllModalBtn.addEventListener('click', function() {
//         document.getElementById('analyticsCookies').checked = false;
//         document.getElementById('marketingCookies').checked = false;
//         document.getElementById('preferenceCookies').checked = false;
//         saveCustomPreferences();
//         closeModal();
//         hideCookieBanner();
//     });

//     // Save custom cookie preferences
//     function saveCustomPreferences() {
//         const analytics = document.getElementById('analyticsCookies').checked;
//         const marketing = document.getElementById('marketingCookies').checked;
//         const preferences = document.getElementById('preferenceCookies').checked;

//         setCookie('cookie_consent', 'custom', 365);
//         setCookie('cookie_analytics', analytics.toString(), 365);
//         setCookie('cookie_marketing', marketing.toString(), 365);
//         setCookie('cookie_preferences', preferences.toString(), 365);

//         console.log('Cookie preferences saved:', {
//             analytics: analytics,
//             marketing: marketing,
//             preferences: preferences
//         });

//         // Enable analytics if accepted
//         if (analytics) {
//             enableAnalytics();
//         }

//         // Enable marketing if accepted
//         if (marketing) {
//             enableMarketing();
//         }
//     }

//     // Load saved preferences into modal
//     function loadSavedPreferences() {
//         const analytics = getCookie('cookie_analytics') === 'true';
//         const marketing = getCookie('cookie_marketing') === 'true';
//         const preferences = getCookie('cookie_preferences') === 'true';

//         document.getElementById('analyticsCookies').checked = analytics;
//         document.getElementById('marketingCookies').checked = marketing;
//         document.getElementById('preferenceCookies').checked = preferences;
//     }

//     // Open modal
//     function openModal() {
//         cookieModal.classList.add('active');
//         document.body.style.overflow = 'hidden';
//     }

//     // Close modal
//     function closeModal() {
//         cookieModal.classList.remove('active');
//         document.body.style.overflow = '';
//     }

//     // Show cookie banner
//     function showCookieBanner() {
//         cookieBanner.classList.remove('hidden');
//         setTimeout(() => {
//             cookieBanner.style.transform = 'translateY(0)';
//         }, 100);
//     }

//     // Hide cookie banner
//     function hideCookieBanner() {
//         cookieBanner.style.transform = 'translateY(100%)';
//         setTimeout(() => {
//             cookieBanner.classList.add('hidden');
//         }, 400);
//     }

//     // Set cookie function
//     function setCookie(name, value, days) {
//         const date = new Date();
//         date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
//         const expires = "expires=" + date.toUTCString();
//         document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
//     }

//     // Get cookie function
//     function getCookie(name) {
//         const nameEQ = name + "=";
//         const cookies = document.cookie.split(';');
//         for (let i = 0; i < cookies.length; i++) {
//             let cookie = cookies[i];
//             while (cookie.charAt(0) === ' ') {
//                 cookie = cookie.substring(1, cookie.length);
//             }
//             if (cookie.indexOf(nameEQ) === 0) {
//                 return cookie.substring(nameEQ.length, cookie.length);
//             }
//         }
//         return null;
//     }

//     // Function to enable analytics
//     function enableAnalytics() {
//         console.log('Analytics enabled - Google Analytics would be initialized here');
//         // Example: Initialize Google Analytics
//         // gtag('config', 'GA_MEASUREMENT_ID');
//     }

//     // Function to enable marketing
//     function enableMarketing() {
//         console.log('Marketing cookies enabled - Facebook Pixel, etc. would be initialized here');
//         // Example: Initialize Facebook Pixel
//         // fbq('init', 'YOUR_PIXEL_ID');
//     }
// });

// // Smooth scroll for anchor links
// document.querySelectorAll('a[href^="#"]').forEach(anchor => {
//     anchor.addEventListener('click', function (e) {
//         e.preventDefault();
//         const target = document.querySelector(this.getAttribute('href'));
//         if (target) {
//             target.scrollIntoView({
//                 behavior: 'smooth',
//                 block: 'start'
//             });
//         }
//     });
// });

// // Add scroll effect to header
// let lastScroll = 0;
// const header = document.querySelector('.header');

// window.addEventListener('scroll', () => {
//     const currentScroll = window.pageYOffset;
    
//     if (currentScroll > 100) {
//         header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.5)';
//     } else {
//         header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.3)';
//     }
    
//     lastScroll = currentScroll;
// });

// // Add animation on scroll for category cards
// const observerOptions = {
//     threshold: 0.1,
//     rootMargin: '0px 0px -50px 0px'
// };

// const observer = new IntersectionObserver(function(entries) {
//     entries.forEach(entry => {
//         if (entry.isIntersecting) {
//             entry.target.style.opacity = '1';
//             entry.target.style.transform = 'translateY(0)';
//         }
//     });
// }, observerOptions);

// document.querySelectorAll('.category-card').forEach(card => {
//     card.style.opacity = '0';
//     card.style.transform = 'translateY(30px)';
//     card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
//     observer.observe(card);
// });

document.addEventListener('DOMContentLoaded', function() {
    
    // ÉLÉMENTS DU DOM
    const cookieBanner = document.getElementById('cookieBanner');
    const cookieModal = document.getElementById('cookieModal');
    
    // Boutons Bannière Principale
    const btnAccept = document.getElementById('acceptCookies');
    const btnCustomize = document.getElementById('customizeCookies');
    const btnDecline = document.getElementById('declineCookies');
    
    // Boutons Modal Préférences
    const btnCloseModal = document.getElementById('closeModal');
    const btnAcceptAllModal = document.getElementById('acceptAllModal');
    const btnRejectAllModal = document.getElementById('rejectAllModal');
    const btnSavePreferences = document.getElementById('savePreferences');

    // 1. VÉRIFICATION AU CHARGEMENT
    // On vérifie si l'utilisateur a déjà fait un choix
    if (!localStorage.getItem('cube_cookies_accepted')) {
        // Petit délai pour l'animation d'entrée
        setTimeout(() => {
            cookieBanner.classList.add('show');
        }, 500);
    }

    // 2. FONCTIONS DE FERMETURE
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
        // Enregistre le choix dans le navigateur
        localStorage.setItem('cube_cookies_accepted', 'true');
        localStorage.setItem('cube_cookie_type', consentType);
        
        closeBanner();
        closePreferences();

        // Optionnel: Recharger la page si nécessaire pour activer les scripts
        // window.location.reload(); 
    }

    // 3. ÉCOUTEURS D'ÉVÉNEMENTS (BANNER)
    
    // Tout Accepter (depuis la bannière)
    if(btnAccept) {
        btnAccept.addEventListener('click', () => {
            saveAndClose('all');
        });
    }

    // Personnaliser
    if(btnCustomize) {
        btnCustomize.addEventListener('click', openPreferences);
    }

    // Continuer sans accepter (Refuser tout sauf nécessaire)
    if(btnDecline) {
        btnDecline.addEventListener('click', () => {
            saveAndClose('necessary');
        });
    }

    // 4. ÉCOUTEURS D'ÉVÉNEMENTS (MODAL)

    if(btnCloseModal) {
        btnCloseModal.addEventListener('click', () => {
            // Si on ferme le modal sans sauver, on rouvre la bannière principale
            closePreferences();
            cookieBanner.classList.add('show');
        });
    }

    if(btnAcceptAllModal) {
        btnAcceptAllModal.addEventListener('click', () => {
            // Cocher visuellement toutes les cases
            document.querySelectorAll('.toggle-switch input:not(:disabled)').forEach(input => input.checked = true);
            saveAndClose('all');
        });
    }

    if(btnRejectAllModal) {
        btnRejectAllModal.addEventListener('click', () => {
            // Décocher visuellement
            document.querySelectorAll('.toggle-switch input:not(:disabled)').forEach(input => input.checked = false);
            saveAndClose('necessary');
        });
    }

    if(btnSavePreferences) {
        btnSavePreferences.addEventListener('click', () => {
            // Ici, tu pourrais récupérer l'état de chaque checkbox pour un traitement précis
            saveAndClose('custom');
        });
    }
});