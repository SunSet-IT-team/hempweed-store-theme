export function productStockLabels() {
  const products = document.querySelectorAll('.product'); // класс карточки товара

  if (!products.length) return;

  products.forEach(product => {
    const stockEl = product.querySelector('[data-stock]'); // элемент с атрибутом data-stock
    const btn = product.querySelector('.add-to-cart'); // кнопка "В корзину"

    if (!stockEl) return;

    const stock = parseInt(stockEl.dataset.stock, 10);

    // Убираем старые бейджи (например "new")
    const oldBadge = product.querySelector('.badge');
    if (oldBadge) oldBadge.remove();

    // Создаем новую плашку
    const badge = document.createElement('div');
    badge.classList.add('badge');

    if (stock <= 0) {
      badge.textContent = 'Out of stock';
      if (btn) {
        btn.setAttribute('disabled', 'true');
        btn.classList.add('btn--disabled');
      }
    } else if (stock < 10) {
      badge.textContent = `There are ${stock} pieces left`;
    }

    product.appendChild(badge);
  });
}
