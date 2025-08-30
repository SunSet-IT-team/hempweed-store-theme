const createModalState = () => {
  let isOpen = false;
  let imageUrl = null;
  let currentImageIndex = 0;
  let allImageUrls = [];

  const getIsOpen = () => isOpen;
  const getImageUrl = () => imageUrl;
  const getCurrentImageIndex = () => currentImageIndex;
  const getAllImageUrls = () => allImageUrls;

  const openModal = (url, index, urls) => {
    isOpen = true;
    imageUrl = url;
    currentImageIndex = index;
    allImageUrls = urls;
  };

  const closeModal = () => {
    isOpen = false;
    imageUrl = null;
    currentImageIndex = 0;
    allImageUrls = [];
  };

  const nextImage = () => {
    currentImageIndex = (currentImageIndex + 1) % allImageUrls.length;
    imageUrl = allImageUrls[currentImageIndex];
  };

  const prevImage = () => {
    currentImageIndex = (currentImageIndex - 1 + allImageUrls.length) % allImageUrls.length;
    imageUrl = allImageUrls[currentImageIndex];
  };

  return {
    getIsOpen,
    getImageUrl,
    getCurrentImageIndex,
    getAllImageUrls,
    openModal,
    closeModal,
    nextImage,
    prevImage,
  };
};

const createModalView = (modalState) => {
  let modalElement = null;

  const renderModal = () => {
    if (modalState.getIsOpen()) {
      if (!modalElement) {
        modalElement = createModalElement(modalState.getImageUrl());
        document.body.appendChild(modalElement);
        document.body.style.overflow = 'hidden';
      } else {
        const image = modalElement.querySelector('._modal_image');
        image.src = modalState.getImageUrl();
        updateCounter(modalElement, modalState.getCurrentImageIndex(), modalState.getAllImageUrls().length);
      }
    } else {
      if (modalElement) {
        document.body.removeChild(modalElement);
        modalElement = null;
        document.body.style.overflow = 'auto';
      }
    }
  };

  const updateCounter = (modal, current, total) => {
    const currentCountSpan = modal.querySelector('.current__picture_count');
    const allCountSpan = modal.querySelector('.all__picture_count');
    if (currentCountSpan && allCountSpan) {
      currentCountSpan.textContent = current + 1;
      allCountSpan.textContent = total;
    }
  };

  const createModalElement = (imageUrl) => {
    const modal = document.createElement('div');
    modal.className = '_modal_picture_generate _p_fix _modal_overlay';

    const imageContainer = document.createElement('div');
    imageContainer.className = '_modal_image_container _bg_fff _p_rel';
    modal.appendChild(imageContainer);

    const image = document.createElement('img');
    image.src = imageUrl;
    image.className = '_modal_image';
    imageContainer.appendChild(image);

    const closeButton = document.createElement('button');
    closeButton.className = '_modal_close_button _color_11548 _btn _p_abs';
    closeButton.innerHTML = '&times;';
    imageContainer.appendChild(closeButton);

    const prevButton = document.createElement('button');
    prevButton.className = '_modal_prev_button _p_abs';
    imageContainer.appendChild(prevButton);

    const nextButton = document.createElement('button');
    nextButton.className = '_modal_next_button _p_abs';
    imageContainer.appendChild(nextButton);

    const counterContainer = document.createElement('div');
    counterContainer.className = 'modal__picture_counter _flex _p_abs';
    counterContainer.innerHTML = `
      <span class="current__picture_count _fs_24 _fw_300 _color_000"></span>
      <span class="line__count _fs_30 _fw_300 _color_000">/</span>
      <span class="all__picture_count _fs_24 _fw_300 _color_00041"></span>`;
    imageContainer.appendChild(counterContainer);

    updateCounter(modal, modalState.getCurrentImageIndex(), modalState.getAllImageUrls().length);

    closeButton.addEventListener('click', () => {
      modalState.closeModal();
      renderModal();
    });

    prevButton.addEventListener('click', () => {
        modalState.prevImage();
        renderModal();
    });

    nextButton.addEventListener('click', () => {
        modalState.nextImage();
        renderModal();
    });

    return modal;
  };

  return { renderModal };
};

const createModalController = (model, view) => {
  const imageContainers = document.querySelectorAll('.product-details__description-img-cntr');
  const imageUrls = Array.from(document.querySelectorAll('.product-details__description-img')).map(img => img.src);

  imageContainers.forEach((container, index) => {
    container.addEventListener('click', (event) => {
      event.preventDefault();
      const img = container.querySelector('.product-details__description-img');

      if (img) {
        model.openModal(img.src, index, imageUrls);
        view.renderModal();
      }
    });
  });
};

export const openTabPictureModal = () => {
  const modalState = createModalState();
  const modalView = createModalView(modalState);
  createModalController(modalState, modalView);
};