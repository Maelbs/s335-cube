// document.addEventListener('DOMContentLoaded', () => {
//     let container = document.querySelector('#menu-categories-container');
//     let header = document.querySelector('header');
    
//     let btnAccessoire = document.querySelector('#btn-accessoire');
//     let btnVeloMusculaire = document.querySelector('#btn-velo');
//     let btnVeloElectrique = document.querySelector('#btn-elec');

//     let currentActiveMenu = null; 

//     function clearNextColumns(currentLevel) {
//         let allCols = container.querySelectorAll('.menu-col');
//         allCols.forEach(col => {
//             let colLevel = parseInt(col.dataset.level);
//             if (colLevel > currentLevel) {
//                 col.remove();
//             }
//         });
//     }

//     //Pour faire en sorte que quel que soit l'item recup, on puisse acceder au bon parametre
//     function getCategoryData(item) {
//         return {
//             name: item.nom_categorie_accessoire || item.nom_categorie || item.name, 
//             id: item.id_categorie_accessoire || item.id_categorie || item.id
//         };
//     }

//     function loadColumn(linkContent, level, parentId) {
//         let url = '';
//         if (linkContent === 'Accessoires') {
//             url = (level === 0) 
//                 ? '/api/categories-accessoires/parents' 
//                 : `/api/categories-accessoires/${parentId}/subCategories`;
//         } 
//         else if (linkContent === 'Velos') {
//             url = (level === 0) 
//                 ? '/api/categories-velos/parents' 
//                 : `/api/categories-velos/${parentId}/subCategories`;
//         } 
//         else if (linkContent === 'Electrique') {
//              url = (level === 0) 
//                 ? '/api/categories-velos/parents'
//                 : `/api/categories-velos/${parentId}/subCategories`;
//         }

//         if (!url) return;

//         fetch(url)
//             .then(res => {
//                 if (!res.ok) throw new Error(`Erreur HTTP: ${res.status}`);
//                 return res.json();
//             })
//             .then(data => {
//                 if (!data || data.length === 0) 
//                     return;

//                 let col = document.createElement('div');
//                 col.className = 'menu-col';
//                 col.dataset.level = level;
                
//                 let ul = document.createElement('ul');
//                 col.appendChild(ul);

//                 data.forEach(item => {
//                     const categoryData = getCategoryData(item);

//                     if (!categoryData.name || !categoryData.id) {
//                         console.warn("Donnée ignorée (format incorrect):", item);
//                         return;
//                     }

//                     let li = document.createElement('li');
//                     let a = document.createElement('a');
                    
//                     a.textContent = categoryData.name;
//                     a.href = `/boutique/categorie/${categoryData.id}`; //Mettre le vrai lien quand on l'aura
                    
//                     li.appendChild(a);

//                     li.addEventListener('mouseenter', () => {
//                         ul.querySelectorAll('li').forEach(el => el.classList.remove('active'));
//                         li.classList.add('active');

//                         clearNextColumns(level);

//                         loadColumn(linkContent, level + 1, categoryData.id);
//                     });

//                     ul.appendChild(li);
//                 });

//                 container.appendChild(col);
//             })
//             .catch(err => { console.error("Erreur Fetch:", err); });
//     }

//     function openMenu(type) {
//         container.classList.add('active');

//         if (currentActiveMenu !== type || container.innerHTML === '') {
//             container.innerHTML = '';
//             currentActiveMenu = type;
//             loadColumn(type, 0, null);
//         }
//     }

//     if (btnAccessoire) {
//         btnAccessoire.addEventListener('mouseenter', () => openMenu('Accessoires'));
//     }
//     if (btnVeloMusculaire) {
//         btnVeloMusculaire.addEventListener('mouseenter', () => openMenu('Velos'));
//     }
//     if (btnVeloElectrique) {
//         btnVeloElectrique.addEventListener('mouseenter', () => openMenu('Velos')); 
//     }

//     header.addEventListener('mouseleave', () => {
//         container.classList.remove('active');
//         container.innerHTML = '';
//         currentActiveMenu = null;
//     });
// });


document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. INITIALISATION DES TRIGGERS (SURVOL) ---
    function initMegaMenu(wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const rootTriggers = wrapper.querySelectorAll('.root-trigger');
        const subWrappers = wrapper.querySelectorAll('.subs-container');
        const modelWrappers = wrapper.querySelectorAll('.models-container');

        // NIVEAU 1 : RACINES -> SOUS-CATÉGORIES
        rootTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', function() {
                // Reset visuel colonne 1
                rootTriggers.forEach(el => el.classList.remove('active'));
                this.classList.add('active');

                // Cacher tout le reste
                subWrappers.forEach(el => el.classList.add('d-none'));
                modelWrappers.forEach(el => el.classList.add('d-none'));

                // Afficher la colonne 2 correspondante
                const targetId = this.getAttribute('data-target');
                const targetSub = document.getElementById(targetId);
                if (targetSub) {
                    targetSub.classList.remove('d-none');
                    // Ré-attacher les écouteurs sur les nouveaux éléments visibles
                    initSubTriggers(targetSub, wrapper);
                }
            });
        });
    }

    // NIVEAU 2 : SOUS-CATÉGORIES -> MODÈLES
    function initSubTriggers(subContainer, mainWrapper) {
        const subTriggers = subContainer.querySelectorAll('.sub-trigger');
        const modelWrappers = mainWrapper.querySelectorAll('.models-container');

        subTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', function() {
                // Reset visuel colonne 2
                subTriggers.forEach(el => el.classList.remove('active'));
                this.classList.add('active');

                // Cacher colonne 3
                modelWrappers.forEach(el => el.classList.add('d-none'));

                // Afficher la colonne 3 correspondante
                const targetId = this.getAttribute('data-target');
                const targetModel = document.getElementById(targetId);
                if (targetModel) {
                    targetModel.classList.remove('d-none');
                }
            });
        });
    }

    // Initialisation
    initMegaMenu('wrapper-velo');
    initMegaMenu('wrapper-elec');
    initMegaMenu('wrapper-accessoire');


    // --- 2. FONCTION DE RESET (QUAND ON QUITTE LE MENU) ---
    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(navItem => {
        navItem.addEventListener('mouseleave', function() {
            
            // On attend la fin de l'animation CSS (0.3s) pour que le reset ne se voie pas pendant le fondu
            setTimeout(() => {
                // 1. Enlever la classe .active de tous les items survolés
                const activeItems = navItem.querySelectorAll('.menu-item.active');
                activeItems.forEach(item => item.classList.remove('active'));

                // 2. Recacher toutes les colonnes 2 et 3
                const openContainers = navItem.querySelectorAll('.subs-container, .models-container');
                openContainers.forEach(container => container.classList.add('d-none'));

            }, 300); // 300ms correspond à ton 'transition: all 0.3s' dans le CSS
        });
    });

});


function openSearch(e) {
    if(e) e.preventDefault();
    const overlay = document.getElementById('full-search-overlay');
    overlay.classList.add('active');
    // Focus immédiat sur l'input
    setTimeout(() => {
        overlay.querySelector('input').focus();
    }, 100);
}

function closeSearch() {
    const overlay = document.getElementById('full-search-overlay');
    overlay.classList.remove('active');
}