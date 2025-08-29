const state = {
  activeDropdown: null,
  hideTimeout: null,
  isMobile: window.innerWidth <= 1200,
  lastClickedButton: null,
};

const view = {
  toggleActiveClass(element, isActive) {
    element.classList.toggle('_active', isActive);
  },
  toggleShowClass(element, isShown) {
    element.classList.toggle('_show', isShown);
  },
};

const controller = {
  init() {
    const menuButtons = document.querySelectorAll('.menu__btn');

    const checkMobile = () => {
      state.isMobile = window.innerWidth <= 1200;
    };
    window.addEventListener('resize', checkMobile);
    checkMobile();

    menuButtons.forEach((button) => {
      const dropdownId = button.dataset.toggle;
      const dropdown = document.querySelector(`.menu__dropdown_hidden[data-toggle="${dropdownId}"]`);

      if (!dropdown) return;

      const showDropdown = () => {
        clearTimeout(state.hideTimeout);
        if (state.activeDropdown && state.activeDropdown !== dropdownId) {
          const previousButton = document.querySelector(`.menu__btn[data-toggle="${state.activeDropdown}"]`);
          const previousDropdown = document.querySelector(`.menu__dropdown_hidden[data-toggle="${state.activeDropdown}"]`);
          if (previousButton && previousDropdown) {
            view.toggleActiveClass(previousButton, false);
            view.toggleShowClass(previousDropdown, false);
          }
        }
        state.activeDropdown = dropdownId;
        view.toggleActiveClass(button, true);
        view.toggleShowClass(dropdown, true);
      };

      const hideDropdown = () => {
        if (!state.isMobile) {
          state.hideTimeout = setTimeout(() => {
            if (state.activeDropdown === dropdownId) {
              state.activeDropdown = null;
              view.toggleActiveClass(button, false);
              view.toggleShowClass(dropdown, false);
            }
          }, 1000);
        }
      };

      const toggleDropdown = (event) => {
        if (state.isMobile) {
          if (state.lastClickedButton !== button) {
            event.preventDefault();
            showDropdown();
            state.lastClickedButton = button;
          } else {
            state.lastClickedButton = null;
          }
        } else {
          showDropdown();
        }
      };

      button.addEventListener('mouseenter', showDropdown);
      dropdown.addEventListener('mouseenter', showDropdown);

      button.addEventListener('mouseleave', (e) => {
        if (!dropdown.contains(e.relatedTarget)) {
          hideDropdown();
        }
      });

      dropdown.addEventListener('mouseleave', (e) => {
        if (!button.contains(e.relatedTarget)) {
          hideDropdown();
        }
      });

      button.addEventListener('click', toggleDropdown);
    });

    document.addEventListener('click', (e) => {
      if (state.activeDropdown) {
        const activeButton = document.querySelector(`.menu__btn[data-toggle="${state.activeDropdown}"]`);
        const activeDropdown = document.querySelector(`.menu__dropdown_hidden[data-toggle="${state.activeDropdown}"]`);
        if (activeButton && activeDropdown && !activeButton.contains(e.target) && !activeDropdown.contains(e.target)) {
          view.toggleActiveClass(activeButton, false);
          view.toggleShowClass(activeDropdown, false);
          state.activeDropdown = null;
          clearTimeout(state.hideTimeout);
        }
      }
    });
  },
};

export const showHeaderPopup = () => {
  controller.init();
};