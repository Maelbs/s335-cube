document.addEventListener('DOMContentLoaded', () => {
    let container = document.querySelector('#menu-categories-container');
    let header = document.querySelector('header');
    
    let btnAccessoire = document.querySelector('#btn-accessoire');
    let btnVeloMusculaire = document.querySelector('#btn-velo');
    let btnVeloElectrique = document.querySelector('#btn-elec');

    let currentActiveMenu = null; 

    function clearNextColumns(currentLevel) {
        let allCols = container.querySelectorAll('.menu-col');
        allCols.forEach(col => {
            let colLevel = parseInt(col.dataset.level);
            if (colLevel > currentLevel) {
                col.remove();
            }
        });
    }

    //Pour faire en sorte que quel que soit l'item recup, on puisse acceder au bon parametre
    function getCategoryData(item) {
        return {
            name: item.nom_categorie_accessoire || item.nom_categorie || item.name, 
            id: item.id_categorie_accessoire || item.id_categorie || item.id
        };
    }

    function loadColumn(linkContent, level, parentId) {
        let url = '';
        if (linkContent === 'Accessoires') {
            url = (level === 0) 
                ? '/api/categories-accessoires/parents' 
                : `/api/categories-accessoires/${parentId}/subCategories`;
        } 
        else if (linkContent === 'Velos') {
            url = (level === 0) 
                ? '/api/categories-velos/parents' 
                : `/api/categories-velos/${parentId}/subCategories`;
        } 
        else if (linkContent === 'Electrique') {
             url = (level === 0) 
                ? '/api/categories-velos/parents'
                : `/api/categories-velos/${parentId}/subCategories`;
        }

        if (!url) return;

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error(`Erreur HTTP: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!data || data.length === 0) 
                    return;

                let col = document.createElement('div');
                col.className = 'menu-col';
                col.dataset.level = level;
                
                let ul = document.createElement('ul');
                col.appendChild(ul);

                data.forEach(item => {
                    const categoryData = getCategoryData(item);

                    if (!categoryData.name || !categoryData.id) {
                        console.warn("Donnée ignorée (format incorrect):", item);
                        return;
                    }

                    let li = document.createElement('li');
                    let a = document.createElement('a');
                    
                    a.textContent = categoryData.name;
                    a.href = `/boutique/categorie/${categoryData.id}`; //Mettre le vrai lien quand on l'aura
                    
                    li.appendChild(a);

                    li.addEventListener('mouseenter', () => {
                        ul.querySelectorAll('li').forEach(el => el.classList.remove('active'));
                        li.classList.add('active');

                        clearNextColumns(level);

                        loadColumn(linkContent, level + 1, categoryData.id);
                    });

                    ul.appendChild(li);
                });

                container.appendChild(col);
            })
            .catch(err => { console.error("Erreur Fetch:", err); });
    }

    function openMenu(type) {
        container.classList.add('active');

        if (currentActiveMenu !== type || container.innerHTML === '') {
            container.innerHTML = '';
            currentActiveMenu = type;
            loadColumn(type, 0, null);
        }
    }

    if (btnAccessoire) {
        btnAccessoire.addEventListener('mouseenter', () => openMenu('Accessoires'));
    }
    if (btnVeloMusculaire) {
        btnVeloMusculaire.addEventListener('mouseenter', () => openMenu('Velos'));
    }
    if (btnVeloElectrique) {
        btnVeloElectrique.addEventListener('mouseenter', () => openMenu('Velos')); 
    }

    header.addEventListener('mouseleave', () => {
        container.classList.remove('active');
        container.innerHTML = '';
        currentActiveMenu = null;
    });
});