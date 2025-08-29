document.querySelectorAll('.faq__question').forEach((question) => {
  question.addEventListener('click', () => {
      const answer = question.nextElementSibling;
      const arrow = question.querySelector('.faq__arrow');

      // Скрываем все ответы и убираем класс стрелки для других вопросов
      document.querySelectorAll('.faq__answer').forEach((otherAnswer) => {
          if (otherAnswer !== answer) {
              otherAnswer.classList.remove('show');
              otherAnswer.previousElementSibling.querySelector('.faq__arrow').classList.remove('expanded');
          }
      });

      // Переключаем видимость ответа и стрелки для текущего вопроса
      answer.classList.toggle('show');
      arrow.classList.toggle('expanded');
  });
});

document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".product-details__tab");
    const contents = document.querySelectorAll(".product-details__tab-content");

    tabs.forEach(tab => {
    tab.addEventListener("click", function() {
        tabs.forEach(t => t.classList.remove("active"));
        contents.forEach(c => c.classList.remove("active"));

        this.classList.add("active");
        document.getElementById(this.dataset.tab).classList.add("active");
    });
    });

    new Swiper(".swiper_feedback", {
    navigation: {
        nextEl: ".feedback__swiper-button-next",
        prevEl: ".feedback__swiper-button-prev",
    },
    slidesPerView: 1,
    slidesPerGroup: 1,
    spaceBetween: 20,
    centeredSlides: false,
    loop: false,
    // breakpoints: {
    //     768: {
    //     slidesPerView: 1,
    //     slidesPerGroup: 1,
    //     }
    // }
    });
});
