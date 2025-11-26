document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#menu-categories-container');
    const header = document.querySelector('header');
    const btnAccessoire = document.querySelector('#btn-accessoire');

    function clearNextColumns(currentLevel) {
        const allCols = container.querySelectorAll('.menu-col');
        
        allCols.forEach(col => {
            const colLevel = parseInt(col.dataset.level);
            if (colLevel > currentLevel) {
                col.remove();
            }
        });
    }

    function loadColumn(level, parentId) {
        const url = (level === 0) 
            ? '/api/categories/parents' 
            : `/api/categories-accessoires/${parentId}/subCategories`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) return;

                const col = document.createElement('div');
                col.className = 'menu-col';
                col.dataset.level = level;
                
                const ul = document.createElement('ul');
                col.appendChild(ul);

                data.forEach(item => {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.textContent = item.nom_categorie_accessoire;

                    a.href = `/boutique/categorie/${item.id_categorie_accessoire}`; //A changer au cas ou la sang
                    li.appendChild(a);

                    li.addEventListener('mouseenter', () => {
                        ul.querySelectorAll('li').forEach(el => el.classList.remove('active'));
                        li.classList.add('active');

                        clearNextColumns(level);

                        loadColumn(level + 1, item.id_categorie_accessoire);
                    });

                    ul.appendChild(li);
                });

                container.appendChild(col);
            })
            .catch(err => console.error("Erreur:", err));
    }

    if (btnAccessoire) {
        btnAccessoire.addEventListener('mouseenter', () => {
            container.classList.add('active');

            if (container.innerHTML === '') {
                loadColumn(0, null);
            }
        });
    }

    header.addEventListener('mouseleave', () => {
        container.classList.remove('active');
        container.innerHTML = '';
    });
});
