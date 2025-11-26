
const carouselImages = document.querySelectorAll('.carousel-image');
let currentImageIndex = 0;

const showImage = (index) => {
  carouselImages.forEach((img, i) => {
    if (i === index) {
      img.classList.add('active');
    } else {
      img.classList.remove('active');
    }
  });
};

const showNextImage = () => {
  currentImageIndex = (currentImageIndex + 1) % carouselImages.length;
  showImage(currentImageIndex);
};

const showPrevImage = () => {
  currentImageIndex = (currentImageIndex - 1 + carouselImages.length) % carouselImages.length;
  showImage(currentImageIndex);
};

// Auto-slide every 4 seconds
setInterval(showNextImage, 4000);

// Initialize the first image
showImage(currentImageIndex);