document.addEventListener("DOMContentLoaded", function () {
  const toggles = document.querySelectorAll(".toggle-specs-btn");

  toggles.forEach((btn) => {
    btn.textContent = "";

    const headerRow = btn.closest(".specs-header-row");
    const content = headerRow.nextElementSibling;

    if (content) {
      content.classList.add("toggle-content");
    }

    btn.addEventListener("click", function () {
      if (content) {
        const isOpen = content.classList.contains("is-visible");

        if (isOpen) {
          content.style.maxHeight = null;
          content.classList.remove("is-visible");
          this.classList.remove("is-active");
        } else {
          content.classList.add("is-visible");
          content.style.maxHeight = content.scrollHeight + "px";
          this.classList.add("is-active");
        }
      }
    });
  });

  const track = document.querySelector(".st-carousel-track");
  const btnLeft = document.querySelector(".st-btn-left");
  const btnRight = document.querySelector(".st-btn-right");

  if (!track || !btnLeft || !btnRight) return;

  function scrollCarousel(direction) {
    const card = track.querySelector(".st-card-item");
    const scrollAmount = card ? card.offsetWidth + 25 : 300;

    if (direction === "left") {
      track.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    } else {
      track.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  }

  btnLeft.addEventListener("click", () => scrollCarousel("left"));
  btnRight.addEventListener("click", () => scrollCarousel("right"));
});

document.addEventListener("DOMContentLoaded", () => {
  const track = document.querySelector(".carousel-track");
  if (!track || track.children.length <= 1) return;

  const slides = Array.from(track.children);
  const nextButton = document.querySelector(".carousel-button--right");
  const prevButton = document.querySelector(".carousel-button--left");
  const dotsNav = document.querySelector(".carousel-nav");
  const dots = dotsNav ? Array.from(dotsNav.children) : [];

  const slideWidth = slides[0].getBoundingClientRect().width;

  const setSlidePosition = (slide, index) => {
    slide.style.left = slideWidth * index + "px";
  };
  slides.forEach(setSlidePosition);

  const moveToSlide = (track, currentSlide, targetSlide) => {
    track.style.transform = "translateX(-" + targetSlide.style.left + ")";
    currentSlide.classList.remove("current-slide");
    targetSlide.classList.add("current-slide");
  };

  const updateDots = (currentDot, targetDot) => {
    if (currentDot && targetDot) {
      currentDot.classList.remove("current-slide");
      targetDot.classList.add("current-slide");
    }
  };

  if (nextButton) {
    nextButton.addEventListener("click", (e) => {
      const currentSlide = track.querySelector(".current-slide");
      let nextSlide = currentSlide.nextElementSibling;
      const currentDot = dotsNav
        ? dotsNav.querySelector(".current-slide")
        : null;
      let nextDot = currentDot ? currentDot.nextElementSibling : null;

      if (!nextSlide) {
        nextSlide = slides[0];
        if (dots.length) nextDot = dots[0];
      }

      moveToSlide(track, currentSlide, nextSlide);
      updateDots(currentDot, nextDot);
    });
  }

  if (prevButton) {
    prevButton.addEventListener("click", (e) => {
      const currentSlide = track.querySelector(".current-slide");
      let prevSlide = currentSlide.previousElementSibling;
      const currentDot = dotsNav
        ? dotsNav.querySelector(".current-slide")
        : null;
      let prevDot = currentDot ? currentDot.previousElementSibling : null;

      if (!prevSlide) {
        prevSlide = slides[slides.length - 1];
        if (dots.length) prevDot = dots[dots.length - 1];
      }

      moveToSlide(track, currentSlide, prevSlide);
      updateDots(currentDot, prevDot);
    });
  }

  if (dotsNav) {
    dotsNav.addEventListener("click", (e) => {
      const targetDot = e.target.closest("button");
      if (!targetDot) return;

      const currentSlide = track.querySelector(".current-slide");
      const currentDot = dotsNav.querySelector(".current-slide");
      const targetIndex = dots.findIndex((dot) => dot === targetDot);
      const targetSlide = slides[targetIndex];

      moveToSlide(track, currentSlide, targetSlide);
      updateDots(currentDot, targetDot);
    });
  }

  window.addEventListener("resize", () => {
    const newSlideWidth = slides[0].getBoundingClientRect().width;
    slides.forEach((slide, index) => {
      slide.style.left = newSlideWidth * index + "px";
    });
    const currentSlide = track.querySelector(".current-slide");
    track.style.transform = "translateX(-" + currentSlide.style.left + ")";
  });
});

function openZoom() {
  const overlay = document.getElementById("zoomModalOverlay");
  const zoomImg = document.getElementById("zoomImageFull");

  const activeSlide = document.querySelector(
    ".carousel-slide.current-slide img"
  );

  if (activeSlide) {
    zoomImg.src = activeSlide.src;
    overlay.style.display = "flex";

    setTimeout(() => {
      overlay.classList.add("active");
    }, 10);
  }
}

function changeZoomImage(direction) {
  const track = document.querySelector(".carousel-track");
  const slides = Array.from(track.children);

  const currentSlide = track.querySelector(".current-slide");
  let currentIndex = slides.indexOf(currentSlide);

  let newIndex = currentIndex + direction;

  if (newIndex < 0) {
    newIndex = slides.length - 1; 
  } else if (newIndex >= slides.length) {
    newIndex = 0; 
  }

  const targetSlide = slides[newIndex];
  const slideWidth = slides[0].getBoundingClientRect().width;

  track.style.transform = "translateX(-" + slideWidth * newIndex + "px)";
  currentSlide.classList.remove("current-slide");
  targetSlide.classList.add("current-slide");

  const dotsNav = document.querySelector(".carousel-nav");
  if (dotsNav) {
    const dots = Array.from(dotsNav.children);
    dots[currentIndex].classList.remove("current-slide");
    dots[newIndex].classList.add("current-slide");
  }

  const zoomImg = document.getElementById("zoomImageFull");

  zoomImg.style.opacity = 0.5;
  setTimeout(() => {
    zoomImg.src = targetSlide.querySelector("img").src;
    zoomImg.style.opacity = 1;
  }, 150);
}

function toggleZoomState(e) {
  if (e.target.tagName === "BUTTON") return;

  const img = document.getElementById("zoomImageFull");

  if (e.target === img || e.target.classList.contains("zoom-container")) {
    img.classList.toggle("zoomed-in");

    if (img.classList.contains("zoomed-in")) {
      img.onmousemove = function (evt) {
        const { left, top, width, height } = img.getBoundingClientRect();
        const x = ((evt.clientX - left) / width) * 100;
        const y = ((evt.clientY - top) / height) * 100;
        img.style.transformOrigin = `${x}% ${y}%`;
      };
    } else {
      img.onmousemove = null;
      img.style.transformOrigin = "center center";
    }
  }
}

function closeZoom(e) {
  if (e) {
    e.stopPropagation();
    if (
      e.target.id === "zoomImageFull" ||
      e.target.classList.contains("zoom-nav")
    )
      return;
  }

  const overlay = document.getElementById("zoomModalOverlay");
  const zoomImg = document.getElementById("zoomImageFull");

  overlay.classList.remove("active");
  zoomImg.classList.remove("zoomed-in");
  zoomImg.style.transformOrigin = "center center";

  setTimeout(() => {
    overlay.style.display = "none";
  }, 300);
}

function selectionnerTaille(tailleNom, qtyWeb, qtyMagasin) {
  document.getElementById("input-taille-selected").value = tailleNom;

  const formPanier = document.getElementById("form-ajout-panier"); 
  const btnMagasin = document.getElementById("btn-contact-magasin"); 
  const msgIndispo = document.getElementById("msg-indisponible"); 

  const dotWeb = document.getElementById("dot-web");
  const textWeb = document.getElementById("text-web");
  const dotMagasin = document.getElementById("dot-magasin");
  const textMagasin = document.getElementById("text-magasin");

  if (qtyWeb > 0) {
    formPanier.style.display = "inline-block";

    dotWeb.className = "status-dot active-green";
    textWeb.textContent = "Disponible en ligne";
    textWeb.style.color = "#15803d";
  } else {
    formPanier.style.display = "none";

    dotWeb.className = "status-dot inactive-gray";
    textWeb.textContent = "Indisponible en ligne";
    textWeb.style.color = "#6b7280";
  }

  if (qtyMagasin > 0) {
    btnMagasin.style.display = "inline-block";

    dotMagasin.className = "status-dot active-green";
    textMagasin.textContent = "Disponible en magasin";
    textMagasin.style.color = "#15803d";
  } else {
    btnMagasin.style.display = "none";

    dotMagasin.className = "status-dot inactive-gray";
    textMagasin.textContent = "Indisponible en magasin";
    textMagasin.style.color = "#6b7280";
  }

  if (qtyWeb <= 0 && qtyMagasin <= 0) {
    msgIndispo.style.display = "block";
  } else {
    msgIndispo.style.display = "none";
  }
}

function addToCartAjax() {
    const form = document.getElementById('form-ajout-panier');
    const url = form.dataset.action;
    const formData = new FormData(form);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json', 
            'X-Requested-With': 'XMLHttpRequest' 
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fillAndOpenModal(data); 
        }
        else {
            alert(data.message || 'Une erreur est survenue.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur technique lors de l\'ajout au panier.');
    });
}

function fillAndOpenModal(data) {
  document.getElementById("modalName").textContent = data.product.name;
  document.getElementById("modalPrice").textContent =
    new Intl.NumberFormat("fr-FR", {
      style: "currency",
      currency: "EUR",
    }).format(data.product.price) + " TTC";
  document.getElementById("modalImg").src = data.product.image;
  document.getElementById("modalSize").textContent = data.product.taille;
  document.getElementById("modalQty").textContent = data.product.qty;

  document.getElementById("cartCount").textContent = data.cart.count;
  const formattedTotal = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "EUR",
  }).format(data.cart.total);
  const formattedTax = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "EUR",
  }).format(data.cart.total * 0.2);

  document.getElementById("cartSubtotal").textContent = formattedTotal;
  document.getElementById("cartTotal").textContent = formattedTotal;
  document.getElementById("cartTax").textContent = formattedTax;

  document.getElementById("cartModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("cartModal").style.display = "none";
}

window.onclick = function (event) {
  const modal = document.getElementById("cartModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.getElementById("open-3d-btn");
    const closeBtn = document.getElementById("close-3d-btn");
    const lightbox = document.getElementById("lightbox-3d");

    if (!openBtn) return;

    const folderPath = openBtn.dataset.folder.trim(); 
    const extension = ".webp";
    const totalImages = 20;
    
    function checkModelExists() {
        const testImageSrc = `${folderPath}01${extension}`;
        
        console.log("Test URL: " + testImageSrc); 

        const tester = new Image();
        tester.onload = () => {
            openBtn.style.display = "flex"; 
            
            console.log("Modèle trouvé ! Bouton activé.");
        };
        tester.onerror = () => {
            console.log("Pas de modèle à : " + testImageSrc);
            openBtn.style.display = "none";
        };
        tester.src = testImageSrc;
    }

    checkModelExists();

    openBtn.addEventListener("click", () => {
        lightbox.classList.add("active");

        const sensitivity = 10;
        const viewer = document.getElementById("product-viewer");
        const imgElement = document.getElementById("bike-image");
        const loaderWrapper = document.getElementById("loader-wrapper");
        const loaderText = document.getElementById("loader-text");

        let images = [];
        let currentFrame = 1;
        let isDragging = false;
        let startX = 0;
        let loadedCount = 0;

        loadedCount = 0;
        images = [];
        if(loaderWrapper) loaderWrapper.style.display = "flex";

        function preloadImages() {
            for (let i = 1; i <= totalImages; i++) {
                const imageNumber = i.toString().padStart(2, "0");
                
                const imgSrc = `${folderPath}${imageNumber}${extension}`;
                
                const img = new Image();
                img.src = imgSrc;

                img.onload = () => {
                    loadedCount++;
                    if (loaderText) {
                        loaderText.innerText = `Chargement ${Math.floor((loadedCount / totalImages) * 100)}%`;
                    }
                    if (loadedCount === totalImages) initViewer();
                };
                
                images.push(imgSrc);
            }
        }

        function initViewer() {
            if (loaderWrapper) loaderWrapper.style.display = "none";
            updateImage(1);

            const newViewer = viewer.cloneNode(true);
            viewer.parentNode.replaceChild(newViewer, viewer);
            
            newViewer.addEventListener("mousedown", startDrag);
            window.addEventListener("mouseup", stopDrag);
            window.addEventListener("mousemove", handleMove);
            newViewer.addEventListener("touchstart", startDrag, { passive: false });
            window.addEventListener("touchend", stopDrag);
            window.addEventListener("touchmove", handleMove, { passive: false });
        }
        
        function startDrag(e) { 
             if (e.cancelable) e.preventDefault();
             isDragging = true;
             startX = e.pageX || e.touches[0].pageX;
             document.getElementById("product-viewer").style.cursor = "grabbing";
        }
        function stopDrag() { 
             isDragging = false;
             const v = document.getElementById("product-viewer");
             if(v) v.style.cursor = "grab";
        }
        function handleMove(e) { 
             if (!isDragging) return;
             const x = e.pageX || e.touches[0].pageX;
             const change = x - startX;
             if (Math.abs(change) > sensitivity) {
                 if (change > 0) prevFrame(); else nextFrame();
                 startX = x;
             }
        }
        function nextFrame() { 
             currentFrame++;
             if (currentFrame > totalImages) currentFrame = 1;
             updateImage(currentFrame);
        }
        function prevFrame() { 
             currentFrame--;
             if (currentFrame < 1) currentFrame = totalImages;
             updateImage(currentFrame);
        }

        function updateImage(frameIndex) {
            const currentImg = document.getElementById("bike-image");
            if(currentImg) currentImg.src = images[frameIndex - 1];
        }

        preloadImages();
    });

    closeBtn.addEventListener("click", () => {
        lightbox.classList.remove("active");
    });
    
    lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) lightbox.classList.remove("active");
    });
});
