document.addEventListener('DOMContentLoaded', function() {
    function initMegaMenu(wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const rootTriggers = wrapper.querySelectorAll('.root-trigger');
        const subWrappers = wrapper.querySelectorAll('.subs-container');
        const modelWrappers = wrapper.querySelectorAll('.models-container');

        rootTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', function() {
                rootTriggers.forEach(el => el.classList.remove('active'));
                this.classList.add('active');

                subWrappers.forEach(el => el.classList.add('d-none'));
                modelWrappers.forEach(el => el.classList.add('d-none'));

                const targetId = this.getAttribute('data-target');
                const targetSub = document.getElementById(targetId);
                if (targetSub) {
                    targetSub.classList.remove('d-none');
                    initSubTriggers(targetSub, wrapper);
                }
            });
        });
    }

    function initSubTriggers(subContainer, mainWrapper) {
        const subTriggers = subContainer.querySelectorAll('.sub-trigger');
        const modelWrappers = mainWrapper.querySelectorAll('.models-container');

        subTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', function() {
                subTriggers.forEach(el => el.classList.remove('active'));
                this.classList.add('active');

                modelWrappers.forEach(el => el.classList.add('d-none'));

                const targetId = this.getAttribute('data-target');
                const targetModel = document.getElementById(targetId);
                if (targetModel) {
                    targetModel.classList.remove('d-none');
                }
            });
        });
    }

    initMegaMenu('wrapper-velo');
    initMegaMenu('wrapper-elec');
    initMegaMenu('wrapper-accessoire');

    const navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(navItem => {
        navItem.addEventListener('mouseleave', function() {
            
            setTimeout(() => {
                const activeItems = navItem.querySelectorAll('.menu-item.active');
                activeItems.forEach(item => item.classList.remove('active'));

                const openContainers = navItem.querySelectorAll('.subs-container, .models-container');
                openContainers.forEach(container => container.classList.add('d-none'));

            }, 300); 
        });
    });

});


function openSearch(e) {
    if(e) e.preventDefault();
    const overlay = document.getElementById('full-search-overlay');
    overlay.classList.add('active');
    setTimeout(() => {
        overlay.querySelector('input').focus();
    }, 100);
}

function closeSearch() {
    const overlay = document.getElementById('full-search-overlay');
    overlay.classList.remove('active');
}