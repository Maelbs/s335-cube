let veloMusculaire = document.querySelector("#velo")
let veloElectrique = document.querySelector("#velo_electrique")
let accessoire = document.querySelector("#accessoire")


veloMusculaire.addEventListener("click", function() {
    const parentId = this.value;

    fetch(`/api/categories-accessoires/${parentId}/enfants`)
        .then(response => response.json())                                                                                                                                                                                                                                                                                                                                                                                
        .then(data => {
            data.forEach(categorie => {
                const id = categorie.id_categorie_accessoire; 

                const nom = categorie.libelle; 

                let option = document.createElement('option');
                option.value = id;
                option.text = nom;
                childSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur:', error));
});