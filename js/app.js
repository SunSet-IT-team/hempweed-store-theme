import {showHeaderPopup} from './funcs/showHeaderPopup.js';
import {adultFunc} from './funcs/adultFunc.js';
import {starsActions} from './funcs/starsActions.js';
import {categoriesItemAlert} from './funcs/categoriesItemAlert.js';
import {openTabPictureModal} from './funcs/openTabPictureModal.js';
import {accordion} from './funcs/accordion.js';
import {burgerMenu} from './funcs/burgerMenu.js';
import { productStockLabels } from './funcs/productStockLabels.js';
import { termImageUpload } from './funcs/cannabinoids-terms-img.js';

const importArray = [
  showHeaderPopup,
  adultFunc,
  starsActions,
  categoriesItemAlert,
  openTabPictureModal,
  accordion,
  burgerMenu,
  productStockLabels,
  termImageUpload,
]

document.addEventListener('DOMContentLoaded', () => {
  importArray.forEach(func => func());
});
