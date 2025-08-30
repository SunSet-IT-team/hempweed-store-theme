export const adultFunc = () => {
  const modal = document.querySelector('._modal_background');
  const adultCntr = document.querySelector('[modal-adult-desc]');
  const agreeBtn = document.querySelector('.agree__btn');
  const modalCntrs = document.querySelectorAll('._modal_cntr:not([modal-adult-desc])');
  const html = document.documentElement;
  const body = document.body;

  const state = {
    isVisible: false,
  };

  const applyInitialStyles = () => {
    modalCntrs.forEach(cntr => {
      cntr.style.display = 'none';
    });
    html.style.overflow = 'hidden';
    body.style.overflow = 'hidden';
  };

  const removeStyles = () => {
    setTimeout(() => {
      modalCntrs.forEach(cntr => {
        cntr.style.display = '';
      });
      html.style.overflow = '';
      body.style.overflow = '';
    }, 200);
  };

  const showModal = () => {
    if (!state.isVisible) {
      modal.classList.add('_show');
      state.isVisible = true;
    }
  };

  const hideModal = () => {
    if (state.isVisible) {
      modal.classList.remove('_show');
      modal.style.display = 'none'; // Скрываем вместо удаления
      state.isVisible = false;
      removeStyles();
    }
  };

  const handleAgreeClick = () => {
    try {
      localStorage.setItem('ageConfirmed', 'true'); // Запоминаем выбор пользователя
      hideModal();
    } catch (error) {
      console.error('Error hiding modal:', error);
    }
  };

  const init = () => {
    try {
      if (localStorage.getItem('ageConfirmed') === 'true') {
        modal.style.display = 'none'; // Если подтверждено, скрываем окно
        return;
      }

      applyInitialStyles();
      showModal();
      agreeBtn.addEventListener('click', handleAgreeClick);
    } catch (error) {
      console.error('Error initializing modal:', error);
    }
  };

  init();
};
