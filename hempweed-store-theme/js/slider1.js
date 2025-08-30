document.addEventListener("DOMContentLoaded", function () {
  const swiper = new Swiper(".new-items__slider", {
    slidesPerView: 1,
    spaceBetween: 30,
    navigation: {
      nextEl: ".new-items__swiper-button-next",
      prevEl: ".new-items__swiper-button-prev",
    },
    loop: true,
    breakpoints: {
      1023.98: { slidesPerView: 1, },
      768: { slidesPerView: 1, },
      768: { slidesPerView: 1, },
    },
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const swiper = new Swiper(".swiper_feedback", {
    slidesPerView: 1,
    spaceBetween: 30,
    navigation: {
      nextEl: ".feedback__swiper-button-next",
      prevEl: ".feedback__swiper-button-prev",
    },
    loop: true,
    breakpoints: {
      1023.98: { slidesPerView: 1, },
      768: { slidesPerView: 1, },
      768: { slidesPerView: 1, },
    },
  });
});
  
document.addEventListener("DOMContentLoaded", function () {
  new Swiper(".popular__slider", {
      slidesPerView: 1,
      spaceBetween: 40,
      navigation: {
          nextEl: ".popular__swiper-button-next",
          prevEl: ".popular__swiper-button-prev"
      },
      loop: true,
      breakpoints: {
        1023.98: { slidesPerView: 3 },
        768: { slidesPerView: 2 },
        480: { slidesPerView: 1 }
      }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  new Swiper(".new__slider", {
      slidesPerView: 1,
      spaceBetween: 0,
      navigation: {
          nextEl: ".new__swiper-button-prev",
          prevEl: ".new__swiper-button-next"
      },
      loop: true,
      breakpoints: {
          1200: { slidesPerView: 3 },
          1023.98: { slidesPerView: 2 },
          480: { slidesPerView: 1 }
      }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const swiper = new Swiper(".new-items__slider", {
    slidesPerView: 1,
    spaceBetween: 30,
    navigation: {
      nextEl: ".new-items__swiper-button-next",
      prevEl: ".new-items__swiper-button-prev",
    },
    loop: true,
    breakpoints: {
      1024: {
        slidesPerView: 1,
      },
      768: {
        slidesPerView: 1,
      },
      400: {
        slidesPerView: 1,
      },
    },
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // alert('test');
  const generalSwiper = new Swiper(".product__other_slider", {
    slidesPerView: 1,
    spaceBetween: 0,
    navigation: {
      nextEl: ".product__other-button-next",
      prevEl: ".product__other-button-prev",
    },
    loop: true,
  });
});

