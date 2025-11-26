
const carouselImages = document.querySelectorAll('.carousel-image');
let currentImageIndex = 0;

const showImage = (index) => {
  carouselImages.forEach((img, i) => {
    img.classList.toggle('active', i === index);
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


setInterval(showNextImage, 4000);


showImage(currentImageIndex);