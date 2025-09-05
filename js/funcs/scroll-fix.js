// Убеждаемся, что только один элемент имеет скролл
document.addEventListener('DOMContentLoaded', function() {
    // Базовые настройки
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
    
    // Обновляем при изменении размера окна
    window.addEventListener('resize', function() {
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
    });
    
    // Фикс для модальных окон
    function handleModalScroll(open) {
        const scrollContainer = document.querySelector('.scroll-container');
        if (scrollContainer) {
            scrollContainer.style.overflowY = open ? 'hidden' : 'auto';
        }
    }
    
    // Предотвращаем zoom на мобильных устройствах
    document.addEventListener('touchmove', function(e) {
        if (e.touches.length > 1) {
            e.preventDefault();
        }
    }, { passive: false });
    
    // Экспортируем функцию для модальных окон
    window.modalScrollFix = handleModalScroll;
});

// Альтернативный вариант - если нужно управлять скроллом вручную
function disableBodyScroll() {
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
}

function enableBodyScroll() {
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
}

// Фикс для iOS Safari
function iosScrollFix() {
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        document.body.style.height = 'calc(100% + 1px)';
        
        setTimeout(function() {
            document.body.style.height = '100%';
        }, 500);
    }
}