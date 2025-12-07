document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('customer-form');

    if (!form) return;

    // --- NOS REGEX FIABLES (DÉFINIES EN JS) ---
    // Email : Format standard
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Tel : Accepte 0612345678, 06 12 34 56 78, 06.12..., +33 6...
    const phoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;

    form.addEventListener('submit', function (event) {
        let isValid = true;

        // 1. Nettoyage des erreurs visuelles
        const inputsError = form.querySelectorAll('.error-border');
        inputsError.forEach(el => el.classList.remove('error-border'));
        const msgs = form.querySelectorAll('.error-text');
        msgs.forEach(el => el.remove());

        // 2. Boucle sur tous les champs
        const inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="submit"])');
        
        inputs.forEach(function(input) {
            let errorMsg = "";

            // A. Vérification standard (vide ?)
            if (!input.checkValidity()) {
                if (input.validity.valueMissing) errorMsg = "Ce champ est obligatoire.";
                else if (input.validity.typeMismatch) errorMsg = "Format invalide.";
                else errorMsg = "Champ invalide.";
            }

            // B. Vérification Spécifique EMAIL (via Regex JS)
            else if (input.name === 'email' && !emailRegex.test(input.value)) {
                errorMsg = "Veuillez entrer une adresse e-mail valide.";
            }

            // C. Vérification Spécifique TÉLÉPHONE (via Regex JS)
            else if (input.name === 'tel' && input.value.trim() !== "" && !phoneRegex.test(input.value)) {
                errorMsg = "Numéro invalide (ex: 0612345678).";
            }
            
            // D. Vérification Spécifique MOT DE PASSE (Longueur)
            else if (input.name === 'password' && input.value.length < 5) {
                errorMsg = "Le mot de passe doit contenir au moins 5 caractères.";
            }

            // Si on a détecté une erreur via A, B, C ou D
            if (errorMsg) {
                isValid = false;
                showError(input, errorMsg);
            }
        });

        // 3. Vérification de la correspondance des mots de passe
        const pass = document.getElementById('password');
        const passConf = document.getElementById('password_confirmation');
        if (pass && passConf && passConf.value !== "" && pass.value !== passConf.value) {
            isValid = false;
            showError(passConf, "Les mots de passe ne correspondent pas.");
        }

        // 4. Blocage si erreur
        if (!isValid) {
            event.preventDefault();
            const firstError = form.querySelector('.error-border');
            if(firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function showError(input, message) {
        input.classList.add('error-border');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-text';
        errorDiv.innerText = message;
        
        const parent = input.closest('.form-group') || input.parentElement;
        if(parent) parent.appendChild(errorDiv);
    }

    form.addEventListener('input', function(e) {
        if (e.target.classList.contains('error-border')) {
            e.target.classList.remove('error-border');
            const group = e.target.closest('.form-group') || e.target.parentElement;
            const msg = group.querySelector('.error-text');
            if(msg) msg.remove();
        }
    });
});