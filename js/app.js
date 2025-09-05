import {showHeaderPopup} from './funcs/showHeaderPopup.js';
import {adultFunc} from './funcs/adultFunc.js';
import {starsActions} from './funcs/starsActions.js';
import {categoriesItemAlert} from './funcs/categoriesItemAlert.js';
import {openTabPictureModal} from './funcs/openTabPictureModal.js';
import {accordion} from './funcs/accordion.js';
import {burgerMenu} from './funcs/burgerMenu.js';
import {scrollFix} from './funcs/scroll-fix.js';

const importArray = [
  showHeaderPopup,
  adultFunc,
  starsActions,
  categoriesItemAlert,
  openTabPictureModal,
  accordion,
  burgerMenu,
  scrollFix
]

document.addEventListener('DOMContentLoaded', () => {
  importArray.forEach(func => func());
});