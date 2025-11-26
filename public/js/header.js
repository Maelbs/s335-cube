
let btnAccessoire = document.querySelector("#btn-accessoire");
let zoneAffichage = document.querySelector("#zone-affichage");


btnAccessoire.addEventListener("mouseenter", function() {
    fetch(`/api/categories/parents`)
        .then(response => response.json())
        .then(data => {
            zoneAffichage.innerHTML = "";

            if(data.length === 0) {
                zoneAffichage.innerHTML = "<li>Aucune catégorie parent trouvée</li>";
                return;
            }

            // Pour chaque Parent trouvé (ex: Casques, Antivols...)
            data.forEach(categorie => {
                let li = document.createElement('li');
                
                // Texte du lien
                li.textContent = categorie.nom_categorie_accessoire;
                
                // IMPORTANT pour la suite : On stocke son ID
                // Cela servira plus tard quand on voudra afficher SES enfants
                li.dataset.id = categorie.id_categorie_accessoire;

                // Style rapide pour montrer que c'est cliquable/survolable
                li.style.cursor = "pointer"; 
                li.style.color = "blue";
                
                // On l'ajoute à la liste
                zoneAffichage.appendChild(li);
                
                // -----------------------------------------------------------
                // PREPARATION POUR L'ETAPE SUIVANTE (Afficher les enfants)
                // -----------------------------------------------------------
                // On ajoute déjà un écouteur sur ce NOUVEAU li
                li.addEventListener('mouseenter', function() {
                    console.log("Survol du parent ID : " + this.dataset.id);
                    // Ici, plus tard, on fera le fetch pour récupérer les sous-catégories
                    // fetch(`/api/categories-accessoires/${this.dataset.id}/subCategories`)...
                });
            });
        })
        .catch(error => console.error('Erreur:', error));
});

