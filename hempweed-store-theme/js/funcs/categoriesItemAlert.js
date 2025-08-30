const createCategoriesModel = () => {
  let isWarningConfirmed = false;

  const getIsWarningConfirmed = () => isWarningConfirmed;
  const setIsWarningConfirmed = () => {
    isWarningConfirmed = true;
  };

  return {
    getIsWarningConfirmed,
    setIsWarningConfirmed,
  };
};

const createCategoriesView = () => {
  let categoriesContainer;
  try {
    categoriesContainer = document.querySelector('.categories');
    if (!categoriesContainer) {
        throw new Error('Element with class "categories" not found');
    }
  } catch (e) {
    console.error(e.message);
    return null;
  }

  let warningContainer = document.querySelector('.categories__warning_cntr');
  const categoriesItems = document.querySelectorAll('.categories__item');

  const createWarningContainer = () => {
    const newWarningContainer = document.createElement('div');
    newWarningContainer.className = 'categories__warning_cntr _p_abs';
    newWarningContainer.innerHTML = `
      <div class="categories__warn_wrap _flex_col _bg_d9d9d9">
        <p class="_color_000 _fs_24 _fw_200">We are against drug use. They have a negative impact on your health.</p>
        <button class="warning__btn _btn _green_btn _btn_hover _fs_20 _fw_500 _color_fff" type="button">Continue</button>
      </div>
    `;
    categoriesContainer.appendChild(newWarningContainer);
    return newWarningContainer;
  };

  const hideWarningContainer = () => {
    if (warningContainer) {
      warningContainer.remove();
      warningContainer = null;
    }
  };

  const enableCategoryLinks = () => {
    categoriesItems.forEach(item => {
      item.style.pointerEvents = 'auto';
    });
  };

  const disableCategoryLinks = () => {
    categoriesItems.forEach(item => {
      item.style.pointerEvents = 'none';
    });
  };

  const render = () => {
    if (!warningContainer) {
      warningContainer = createWarningContainer();
    }
    disableCategoryLinks();
  };

  return { render, hideWarningContainer, enableCategoryLinks, disableCategoryLinks, warningContainer };
};

const createCategoriesController = (model, view) => {
  if (!view) return;

  const categoriesItems = document.querySelectorAll('.categories__item');
  const warningButton = document.querySelector('.warning__btn');

  categoriesItems.forEach((item) => {
    item.addEventListener('click', (e) => {
      if (!model.getIsWarningConfirmed()) {
        e.preventDefault();
      }
    });
  });

  warningButton.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();

    model.setIsWarningConfirmed();
    view.hideWarningContainer();
    view.enableCategoryLinks();
  });
};

export const categoriesItemAlert = () => {
  document.addEventListener('DOMContentLoaded', () => {
    const model = createCategoriesModel();
    const view = createCategoriesView();

    if (view) {
      view.render();
      createCategoriesController(model, view);
    }
  });
};