document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('customer-form');

    if (!form) return;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;

    form.addEventListener('submit', function (event) {
        let isValid = true;

        const inputsError = form.querySelectorAll('.error-border');
        inputsError.forEach(el => el.classList.remove('error-border'));
        const msgs = form.querySelectorAll('.error-text');
        msgs.forEach(el => el.remove());

        const inputs = form.querySelectorAll('input:not([type="hidden"]):not([type="submit"])');
        
        inputs.forEach(function(input) {
            let errorMsg = "";

            if (!input.checkValidity()) {
                if (input.validity.valueMissing) errorMsg = "Ce champ est obligatoire.";
                else if (input.validity.typeMismatch) errorMsg = "Format invalide.";
                else errorMsg = "Champ invalide.";
            }

            else if (input.name === 'email' && !emailRegex.test(input.value)) {
                errorMsg = "Veuillez entrer une adresse e-mail valide.";
            }

            else if (input.name === 'tel' && input.value.trim() !== "" && !phoneRegex.test(input.value)) {
                errorMsg = "Numéro invalide (ex: 0612345678).";
            }

            else if (input.name === 'password' && input.value.length < 5) {
                errorMsg = "Le mot de passe doit contenir au moins 5 caractères.";
            }
            
            else if (input.name === 'birthday' && !(((calculerAge(input.value)) >= 18) && calculerAge(input.value) <= 125)) {
                const dateMax = new Date();
                dateMax.setFullYear(dateMax.getFullYear() - 18);

                const dateMin = new Date();
                dateMin.setFullYear(dateMin.getFullYear() - 125);

                const options = { day: '2-digit', month: '2-digit', year: 'numeric' };

                errorMsg = `La date doit être comprise entre le ${dateMin.toLocaleDateString('fr-FR', options)} et le ${dateMax.toLocaleDateString('fr-FR', options)}`;
            }
                

                

            if (errorMsg) {
                isValid = false;
                showError(input, errorMsg);
            }
        });

        const pass = document.getElementById('password');
        const passConf = document.getElementById('password_confirmation');
        if (pass && passConf && passConf.value !== "" && pass.value !== passConf.value) {
            isValid = false;
            showError(passConf, "Les mots de passe ne correspondent pas.");
        }

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

    function calculerAge(dateAnniversaire) {
        const naissance = new Date(dateAnniversaire);
        const aujourdhui = new Date();

        let age = aujourdhui.getFullYear() - naissance.getFullYear();

        const moisDiff = aujourdhui.getMonth() - naissance.getMonth();
        
        if (moisDiff < 0 || (moisDiff === 0 && aujourdhui.getDate() < naissance.getDate())) {
            age--;
        }
        return age;
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