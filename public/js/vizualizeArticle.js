document.addEventListener("DOMContentLoaded", function () {
  /* =========================================
     GESTION DES BOUTONS SPECS (ACCORDÉON)
     ========================================= */
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

  /* =========================================
     CAROUSEL SIMPLE (Track Scroll)
     ========================================= */
  const track = document.querySelector(".st-carousel-track");
  const btnLeft = document.querySelector(".st-btn-left");
  const btnRight = document.querySelector(".st-btn-right");

  if (track && btnLeft && btnRight) {
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
  }
});

/* =========================================
   CAROUSEL AVANCÉ (Slides + Dots)
   ========================================= */
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
      const currentDot = dotsNav ? dotsNav.querySelector(".current-slide") : null;
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
      const currentDot = dotsNav ? dotsNav.querySelector(".current-slide") : null;
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

/* =========================================
   ZOOM IMAGE
   ========================================= */
function openZoom() {
  const overlay = document.getElementById("zoomModalOverlay");
  const zoomImg = document.getElementById("zoomImageFull");
  const activeSlide = document.querySelector(".carousel-slide.current-slide img");

  if (activeSlide) {
    zoomImg.src = activeSlide.src;
    overlay.style.display = "flex";
    document.body.classList.add("zoom-is-open");
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
    if (e.target.id === "zoomImageFull" || e.target.classList.contains("zoom-nav")) return;
  }
  const overlay = document.getElementById("zoomModalOverlay");
  const zoomImg = document.getElementById("zoomImageFull");
  overlay.classList.remove("active");
  zoomImg.classList.remove("zoomed-in");
  zoomImg.style.transformOrigin = "center center";
  document.body.classList.remove("zoom-is-open");
  setTimeout(() => {
    overlay.style.display = "none";
  }, 300);
}

/* =========================================
   PANIER & SÉLECTION TAILLE (CORRIGÉ ICI)
   ========================================= */
const sizeSelectors = document.querySelectorAll(".size-btn");
const btnPanier = document.getElementById("btn-panier");

sizeSelectors.forEach((selector) => {
  selector.addEventListener("click", function () {
    // Gestion visuelle de la sélection du bouton
    sizeSelectors.forEach(btn => btn.classList.remove('active')); // Reset autres boutons
    this.classList.add('active'); // Active le bouton cliqué

    if (btnPanier) {
      btnPanier.style.display = "inline-block";
      btnPanier.style.opacity = "0";
      btnPanier.style.transition = "opacity 0.5s";
      setTimeout(() => {
        btnPanier.style.opacity = "1";
      }, 10);
    }
  });
});

// --- FONCTION CORRIGÉE : GESTION DES 5 ARGUMENTS ET COULEURS ---
function selectionnerTaille(tailleNom, qtyWeb, qtyGlobal, qtyLocal, isStoreSelected) {
  
  document.getElementById("input-taille-selected").value = tailleNom;

  const formPanier = document.getElementById("form-ajout-panier");
  const btnMagasin = document.getElementById("btn-contact-magasin");
  const msgIndispo = document.getElementById("msg-indisponible");

  const dotWeb = document.getElementById("dot-web");
  const textWeb = document.getElementById("text-web");
  const dotMagasin = document.getElementById("dot-magasin");
  const textMagasin = document.getElementById("text-magasin");

  // 1. GESTION STOCK WEB
  if (qtyWeb > 0) {
    if(formPanier) formPanier.style.display = "inline-block";
    dotWeb.style.backgroundColor = "#28a745"; // Vert
    textWeb.textContent = "Disponible en ligne";
    textWeb.style.color = "#333";
  } else {
    if(formPanier) formPanier.style.display = "none";
    dotWeb.style.backgroundColor = "#dc3545"; // Rouge
    textWeb.textContent = "Indisponible en ligne";
    textWeb.style.color = "#6b7280";
  }

  // 2. GESTION STOCK MAGASIN (LOGIQUE INTELLIGENTE)
  if (btnMagasin) btnMagasin.style.display = "inline-block"; // Toujours afficher le bouton (Contacter ou Changer)

  if (isStoreSelected) {
    // CAS A : UN MAGASIN EST DÉJÀ CHOISI (ex: Cycles Mari)
    if (qtyLocal > 0) {
      // Dispo chez moi
      dotMagasin.style.backgroundColor = "#28a745"; // Vert
      textMagasin.textContent = "Disponible dans votre magasin";
    } else if (qtyGlobal > 0) {
      // Pas chez moi, mais dispo ailleurs
      dotMagasin.style.backgroundColor = "#ffc107"; // Orange
      textMagasin.textContent = "Disponible dans d'autres magasins";
    } else {
      // Dispo nulle part
      dotMagasin.style.backgroundColor = "#dc3545"; // Rouge
      textMagasin.textContent = "Indisponible dans votre magasin";
    }
  } else {
    // CAS B : AUCUN MAGASIN CHOISI (Mode visiteur global)
    if (qtyGlobal > 0) {
      dotMagasin.style.backgroundColor = "#28a745"; // Vert
      textMagasin.textContent = "Disponible en magasin";
    } else {
      dotMagasin.style.backgroundColor = "#dc3545"; // Rouge
      textMagasin.textContent = "Indisponible en magasin";
    }
  }

  // 3. MESSAGE GLOBAL INDISPONIBLE
  // On affiche "Indisponible" en gros seulement si indispo web ET indispo partout en magasin
  if (qtyWeb <= 0 && qtyGlobal <= 0) {
    if(msgIndispo) msgIndispo.style.display = "block";
  } else {
    if(msgIndispo) msgIndispo.style.display = "none";
  }
}

function addToCartAjax() {
  const form = document.getElementById("form-ajout-panier");
  const url = form.dataset.action;
  const formData = new FormData(form);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  fetch(url, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": csrfToken,
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        fillAndOpenModal(data);
      } else {
        alert(data.message || "Une erreur est survenue.");
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      alert("Erreur technique lors de l'ajout au panier.");
    });
}

function fillAndOpenModal(data) {
  document.getElementById("modalName").textContent = data.product.name;
  document.getElementById("modalPrice").textContent = new Intl.NumberFormat("fr-FR", {
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

/* =========================================
   3D VIEWER
   ========================================= */
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
    const tester = new Image();
    tester.onload = () => {
      openBtn.style.display = "flex";
    };
    tester.onerror = () => {
      openBtn.style.display = "none";
    };
    tester.src = testImageSrc;
  }

  checkModelExists();

  openBtn.addEventListener("click", () => {
    lightbox.classList.add("active");
    document.body.classList.add("zoom-is-open");

    const sensitivity = 10;
    const viewer = document.getElementById("product-viewer");
    const loaderWrapper = document.getElementById("loader-wrapper");
    const loaderText = document.getElementById("loader-text");

    let images = [];
    let currentFrame = 1;
    let isDragging = false;
    let startX = 0;
    let loadedCount = 0;

    loadedCount = 0;
    images = [];
    if (loaderWrapper) loaderWrapper.style.display = "flex";

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
      if (v) v.style.cursor = "grab";
    }
    function handleMove(e) {
      if (!isDragging) return;
      const x = e.pageX || e.touches[0].pageX;
      const change = x - startX;
      if (Math.abs(change) > sensitivity) {
        if (change > 0) prevFrame();
        else nextFrame();
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
      if (currentImg) currentImg.src = images[frameIndex - 1];
    }
    preloadImages();
  });

  closeBtn.addEventListener("click", () => {
    lightbox.classList.remove("active");
    document.body.classList.remove("zoom-is-open");
  });

  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) {
      lightbox.classList.remove("active");
      document.body.classList.remove("zoom-is-open");
    }
  });
});

/* =========================================
   STORE LOCATOR & FILTRAGE
   ========================================= */
let storeLocatorTimeout = null;

function toggleStoreLocator() {
  const overlay = document.getElementById("store-locator-overlay");
  const header = document.querySelector("header");
  const body = document.body;

  if (!overlay) return;

  if (storeLocatorTimeout) {
    clearTimeout(storeLocatorTimeout);
    storeLocatorTimeout = null;
  }

  const isVisible = overlay.classList.contains("visible");

  if (isVisible) {
    overlay.classList.remove("visible");
    if (header) header.classList.remove("header-hidden");
    body.style.overflow = "";
    storeLocatorTimeout = setTimeout(() => {
      overlay.style.visibility = "hidden";
    }, 300);
  } else {
    overlay.style.visibility = "visible";
    body.style.overflow = "hidden";
    if (header) header.classList.add("header-hidden");
    requestAnimationFrame(() => {
      overlay.classList.add("visible");
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // Le listener pour le bouton "btn-contact-magasin" n'est plus nécessaire ici
  // car il est géré directement par le 'onclick' dans le Blade.
  
  const overlay = document.getElementById("store-locator-overlay");
  if (overlay) {
    overlay.addEventListener("click", function (e) {
      if (e.target.id === "store-locator-overlay") {
        toggleStoreLocator();
      }
    });
  }

  const closeBtn = document.querySelector(".close-store-locator");
  if (closeBtn) {
    closeBtn.addEventListener("click", toggleStoreLocator);
  }

  const stockToggle = document.getElementById("stockToggle");
  const searchInput = document.getElementById("storeSearchInput");
  const cards = document.querySelectorAll(".sl-card");

  function filterMagasins() {
    if (!cards.length) return;
    const showOnlyStock = stockToggle ? stockToggle.checked : false;
    const searchText = searchInput ? searchInput.value.toLowerCase() : "";

    cards.forEach((card) => {
      const hasStock = card.getAttribute("data-has-stock") === "true";
      const searchString = card.getAttribute("data-searchString") || "";
      let matchesStock = true;
      let matchesSearch = true;

      if (showOnlyStock && !hasStock) matchesStock = false;
      if (searchText.length > 0 && !searchString.includes(searchText)) matchesSearch = false;

      if (matchesStock && matchesSearch) {
        card.classList.remove("hidden-item");
      } else {
        card.classList.add("hidden-item");
      }
    });
  }

  if (stockToggle) stockToggle.addEventListener("change", filterMagasins);
  if (searchInput) searchInput.addEventListener("keyup", filterMagasins);
});