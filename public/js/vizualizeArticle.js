document.addEventListener("DOMContentLoaded", function () {
  const toggles = document.querySelectorAll(".toggle-specs-btn");

  toggles.forEach((btn) => {
    if (btn.textContent.trim() === "") btn.textContent = "";

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

  const trackSimple = document.querySelector(".st-carousel-track");
  const btnLeft = document.querySelector(".st-btn-left");
  const btnRight = document.querySelector(".st-btn-right");

  if (trackSimple && btnLeft && btnRight) {
    function scrollCarousel(direction) {
      const card = trackSimple.querySelector(".st-card-item");
      const scrollAmount = card ? card.offsetWidth + 25 : 300;
      if (direction === "left") {
        trackSimple.scrollBy({ left: -scrollAmount, behavior: "smooth" });
      } else {
        trackSimple.scrollBy({ left: scrollAmount, behavior: "smooth" });
      }
    }
    btnLeft.addEventListener("click", () => scrollCarousel("left"));
    btnRight.addEventListener("click", () => scrollCarousel("right"));
  }

  const track = document.querySelector(".carousel-track");
  if (track && track.children.length > 1) {
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

    const updateDotsByIndex = (index) => {
      if (!dotsNav) return;
      dots.forEach((d) => d.classList.remove("current-slide"));
      if (dots[index]) {
        dots[index].classList.add("current-slide");
      }
    };

    const moveToSlide = (track, currentSlide, targetSlide) => {
      const img = targetSlide.querySelector("img");
      if (img && img.hasAttribute("data-src")) {
        img.src = img.getAttribute("data-src");
        img.removeAttribute("data-src");
        img.classList.remove("lazy-image");
      }

      track.style.transform = "translateX(-" + targetSlide.style.left + ")";

      currentSlide.classList.remove("current-slide");
      targetSlide.classList.add("current-slide");

      const targetIndex = slides.indexOf(targetSlide);
      updateDotsByIndex(targetIndex);
    };

    if (nextButton) {
      nextButton.addEventListener("click", () => {
        const currentSlide = track.querySelector(".current-slide");
        let nextSlide = currentSlide.nextElementSibling;

        if (!nextSlide) nextSlide = slides[0];

        moveToSlide(track, currentSlide, nextSlide);
      });
    }

    if (prevButton) {
      prevButton.addEventListener("click", () => {
        const currentSlide = track.querySelector(".current-slide");
        let prevSlide = currentSlide.previousElementSibling;

        if (!prevSlide) prevSlide = slides[slides.length - 1];

        moveToSlide(track, currentSlide, prevSlide);
      });
    }

    if (dotsNav) {
      dotsNav.addEventListener("click", (e) => {
        const targetDot = e.target.closest("button");
        if (!targetDot) return;

        const currentSlide = track.querySelector(".current-slide");
        const targetIndex = dots.findIndex((dot) => dot === targetDot);
        const targetSlide = slides[targetIndex];

        moveToSlide(track, currentSlide, targetSlide);
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
  }

  const sizeSelectors = document.querySelectorAll(".size-btn");
  const btnPanier = document.getElementById("btn-panier");

  sizeSelectors.forEach((selector) => {
    selector.addEventListener("click", function () {
      sizeSelectors.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

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

  const openBtn = document.getElementById("open-3d-btn");
  const closeBtn = document.getElementById("close-3d-btn");
  const lightbox = document.getElementById("lightbox-3d");

  if (openBtn) {
    const folderPath = openBtn.dataset.folder.trim();
    const extension = ".webp";
    const totalImages = 20;

    const testImageSrc = `${folderPath}01${extension}`;
    const tester = new Image();
    tester.onload = () => {
      openBtn.style.display = "flex";
    };
    tester.onerror = () => {
      openBtn.style.display = "none";
    };
    tester.src = testImageSrc;

    openBtn.addEventListener("click", () => {
      if (lightbox) {
        lightbox.classList.add("active");
        document.body.classList.add("zoom-is-open");
        init3DViewer(folderPath, extension, totalImages);
      }
    });

    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        lightbox.classList.remove("active");
        document.body.classList.remove("zoom-is-open");
      });
    }

    if (lightbox) {
      lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) {
          lightbox.classList.remove("active");
          document.body.classList.remove("zoom-is-open");
        }
      });
    }
  }

  function init3DViewer(folderPath, extension, totalImages) {
    const sensitivity = 10;
    const viewer = document.getElementById("product-viewer");
    const loaderWrapper = document.getElementById("loader-wrapper");
    const loaderText = document.getElementById("loader-text");
    let images = [];
    let currentFrame = 1;
    let isDragging = false;
    let startX = 0;
    let loadedCount = 0;

    if (loaderWrapper) loaderWrapper.style.display = "flex";

    for (let i = 1; i <= totalImages; i++) {
      const imageNumber = i.toString().padStart(2, "0");
      const imgSrc = `${folderPath}${imageNumber}${extension}`;
      const img = new Image();
      img.src = imgSrc;
      img.onload = () => {
        loadedCount++;
        if (loaderText)
          loaderText.innerText = `Chargement ${Math.floor(
            (loadedCount / totalImages) * 100
          )}%`;
        if (loadedCount === totalImages) startViewer();
      };
      images.push(imgSrc);
    }

    function startViewer() {
      if (loaderWrapper) loaderWrapper.style.display = "none";
      updateImage(1);

      const newViewer = viewer.cloneNode(true);
      viewer.parentNode.replaceChild(newViewer, viewer);

      newViewer.addEventListener("mousedown", (e) => {
        isDragging = true;
        startX = e.pageX;
        newViewer.style.cursor = "grabbing";
        e.preventDefault();
      });
      window.addEventListener("mouseup", () => {
        isDragging = false;
        if (newViewer) newViewer.style.cursor = "grab";
      });
      window.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        const change = e.pageX - startX;
        if (Math.abs(change) > sensitivity) {
          change > 0 ? prevFrame() : nextFrame();
          startX = e.pageX;
        }
      });

      newViewer.addEventListener(
        "touchstart",
        (e) => {
          isDragging = true;
          startX = e.touches[0].pageX;
        },
        { passive: false }
      );
      window.addEventListener("touchend", () => {
        isDragging = false;
      });
      window.addEventListener(
        "touchmove",
        (e) => {
          if (!isDragging) return;
          const change = e.touches[0].pageX - startX;
          if (Math.abs(change) > sensitivity) {
            change > 0 ? prevFrame() : nextFrame();
            startX = e.touches[0].pageX;
          }
        },
        { passive: false }
      );
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
    function updateImage(frame) {
      const imgEl = document.getElementById("bike-image");
      if (imgEl) imgEl.src = images[frame - 1];
    }
  }
});

window.openZoom = function () {
  const overlay = document.getElementById("zoomModalOverlay");
  const zoomImg = document.getElementById("zoomImageFull");
  const activeSlide = document.querySelector(
    ".carousel-slide.current-slide img"
  );

  if (activeSlide && overlay && zoomImg) {
    const imgSrc = activeSlide.hasAttribute("data-src")
      ? activeSlide.getAttribute("data-src")
      : activeSlide.src;

    zoomImg.src = imgSrc;
    overlay.style.display = "flex";
    document.body.classList.add("zoom-is-open");
    setTimeout(() => {
      overlay.classList.add("active");
    }, 10);
  }
};

window.closeZoom = function (e) {
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

  if (overlay && zoomImg) {
    overlay.classList.remove("active");
    zoomImg.classList.remove("zoomed-in");
    zoomImg.style.transformOrigin = "center center";
    document.body.classList.remove("zoom-is-open");
    setTimeout(() => {
      overlay.style.display = "none";
    }, 300);
  }
};

window.changeZoomImage = function (direction) {
  const track = document.querySelector(".carousel-track");
  if (!track) return;

  const slides = Array.from(track.children);
  const currentSlide = track.querySelector(".current-slide");
  let currentIndex = slides.indexOf(currentSlide);
  let newIndex = currentIndex + direction;

  if (newIndex < 0) newIndex = slides.length - 1;
  else if (newIndex >= slides.length) newIndex = 0;

  const targetSlide = slides[newIndex];

  const targetImgInSlider = targetSlide.querySelector("img");
  if (targetImgInSlider && targetImgInSlider.hasAttribute("data-src")) {
    targetImgInSlider.src = targetImgInSlider.getAttribute("data-src");
    targetImgInSlider.removeAttribute("data-src");
    targetImgInSlider.classList.remove("lazy-image");
  }

  const slideWidth = slides[0].getBoundingClientRect().width;

  track.style.transform = "translateX(-" + slideWidth * newIndex + "px)";
  currentSlide.classList.remove("current-slide");
  targetSlide.classList.add("current-slide");

  const dotsNav = document.querySelector(".carousel-nav");
  if (dotsNav) {
    const dots = Array.from(dotsNav.children);
    dots.forEach((d) => d.classList.remove("current-slide"));
    if (dots[newIndex]) dots[newIndex].classList.add("current-slide");
  }

  const zoomImg = document.getElementById("zoomImageFull");
  if (zoomImg) {
    zoomImg.style.opacity = 0.5;
    setTimeout(() => {
      zoomImg.src = targetImgInSlider.src;
      zoomImg.style.opacity = 1;
    }, 150);
  }
};

window.toggleZoomState = function (e) {
  if (e.target.tagName === "BUTTON") return;
  const img = document.getElementById("zoomImageFull");
  if (
    img &&
    (e.target === img || e.target.classList.contains("zoom-container"))
  ) {
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
};

window.selectionnerTaille = function (
  tailleNom,
  qtyWeb,
  qtyGlobal,
  qtyLocal,
  isStoreSelected
) {
  const inputTaille = document.getElementById("input-taille-selected");
  if (inputTaille) inputTaille.value = tailleNom;

  const formPanier = document.getElementById("form-ajout-panier");
  const btnMagasin = document.getElementById("btn-contact-magasin");
  const msgIndispo = document.getElementById("msg-indisponible");

  const dotWeb = document.getElementById("dot-web");
  const textWeb = document.getElementById("text-web");
  const dotMagasin = document.getElementById("dot-magasin");
  const textMagasin = document.getElementById("text-magasin");

  if (qtyWeb > 0) {
    if (formPanier) formPanier.style.display = "inline-block";
    if (dotWeb) {
      dotWeb.style.backgroundColor = "#28a745";
      textWeb.textContent = "Disponible en ligne";
      textWeb.style.color = "#333";
    }
  } else {
    if (formPanier) formPanier.style.display = "none";
    if (dotWeb) {
      dotWeb.style.backgroundColor = "#dc3545";
      textWeb.textContent = "Indisponible en ligne";
      textWeb.style.color = "#6b7280";
    }
  }

  if (btnMagasin) btnMagasin.style.display = "inline-block";

  if (isStoreSelected) {
    if (qtyLocal > 0) {
      if (dotMagasin) {
        dotMagasin.style.backgroundColor = "#28a745";
        textMagasin.textContent = "Disponible dans votre magasin";
      }
    } else if (qtyGlobal > 0) {
      if (dotMagasin) {
        dotMagasin.style.backgroundColor = "#ffc107";
        textMagasin.textContent = "Disponible dans d'autres magasins";
      }
    } else {
      if (dotMagasin) {
        dotMagasin.style.backgroundColor = "#dc3545";
        textMagasin.textContent = "Indisponible dans votre magasin";
      }
    }
  } else {
    if (qtyGlobal > 0) {
      if (dotMagasin) {
        dotMagasin.style.backgroundColor = "#28a745";
        textMagasin.textContent = "Disponible en magasin";
      }
    } else {
      if (dotMagasin) {
        dotMagasin.style.backgroundColor = "#dc3545";
        textMagasin.textContent = "Indisponible en magasin";
      }
    }
  }

  if (qtyWeb <= 0 && qtyGlobal <= 0) {
    if (msgIndispo) msgIndispo.style.display = "block";
  } else {
    if (msgIndispo) msgIndispo.style.display = "none";
  }
};

window.addToCartAjax = function () {
  const form = document.getElementById("form-ajout-panier");
  if (!form) return;

  const url = form.dataset.action;
  const formData = new FormData(form);
  const metaCsrf = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = metaCsrf ? metaCsrf.getAttribute("content") : "";

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
};

window.fillAndOpenModal = function (data) {
  const modal = document.getElementById("cartModal");
  if (!modal) return;

  document.getElementById("modalName").textContent = data.product.name;
  document.getElementById("modalPrice").textContent =
    new Intl.NumberFormat("fr-FR", {
      style: "currency",
      currency: "EUR",
    }).format(data.product.price) + " TTC";

  if (document.getElementById("modalImg"))
    document.getElementById("modalImg").src = data.product.image;
  if (document.getElementById("modalSize"))
    document.getElementById("modalSize").textContent = data.product.taille;
  if (document.getElementById("modalQty"))
    document.getElementById("modalQty").textContent = data.product.qty;

  if (document.getElementById("cartCount"))
    document.getElementById("cartCount").textContent = data.cart.count;

  const formattedTotal = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "EUR",
  }).format(data.cart.total);
  const formattedTax = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "EUR",
  }).format(data.cart.total * 0.2);

  if (document.getElementById("cartSubtotal"))
    document.getElementById("cartSubtotal").textContent = formattedTotal;
  if (document.getElementById("cartTotal"))
    document.getElementById("cartTotal").textContent = formattedTotal;
  if (document.getElementById("cartTax"))
    document.getElementById("cartTax").textContent = formattedTax;

  modal.style.display = "flex";
};

window.closeModalAndRefresh = function () {
  const modal = document.getElementById("cartModal");
  if (modal) modal.style.display = "none";
  location.reload();
};
