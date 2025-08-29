export const burgerMenu = () => {
  const burgerButton = document.querySelector('.burger__btn');
  const mobileNav = document.querySelector('.header__nav_wrap._mobile');

  if (!burgerButton || !mobileNav) return;

  mobileNav.classList.remove('_shown');
  burgerButton.classList.remove('active');

  const toggleMenu = () => {
    burgerButton.classList.toggle('active');
    mobileNav.classList.toggle('_shown');
  };

  burgerButton.addEventListener('click', toggleMenu);
}