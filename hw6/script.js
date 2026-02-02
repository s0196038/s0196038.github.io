const kolichInput = document.getElementById('kolich');
const serviceRadios = document.querySelectorAll('input[name="service"]');
const optionField = document.getElementById('option-field');
const optionSelect = document.getElementById('options');
const checkField = document.getElementById('check-field');
const checkCheckbox = document.getElementById('check');
const totalPriceElement = document.getElementById('total-price');
const detailsElement = document.getElementById('details');

const prices = {
    first: 100,
    second: 200,
    third: 150
};

const optionPrices = {
    first1: 0,
    second2: 50,
    third3: 100
};

function validateInput(value) {
    if (!value) return { isValid: false, message: 'Введите количество' };//пустая строка
    if (!/^\d+$/.test(value)) return { isValid: false, message: 'Введите только цифры' };//только цифры в выражении
    if (value === '0') return { isValid: false, message: 'Количество не может быть нулем' };
    if (value.length > 1 && value.startsWith('0')) return { isValid: false, message: 'Некорректное значение' };
    
    return { isValid: true, value: parseInt(value) };
}

function updateFormVisibility(serviceType) { //скрываем блоки
    optionField.style.display = serviceType === 'second' ? 'block' : 'none';
    checkField.style.display = serviceType === 'third' ? 'block' : 'none';
}

function calculateTotal() { 
    const validation = validateInput(kolichInput.value);
    if (!validation.isValid) {//проверка ошибок в вводе
        showError(validation.message);
        hideResults();
        return;
    }
    
    clearError();
    
    const quantity = validation.value;
    const serviceType = document.querySelector('input[name="service"]:checked').value;//радиокнопка
    const optionValue = optionSelect.value;//доп опция
    const hasCheck = checkCheckbox.checked;//проверка чекбокса
    
    const basePrice = prices[serviceType]; //для первого типа
    let total = basePrice * quantity;
    
    if (serviceType === 'second') { //для второго типа
        total += optionPrices[optionValue] * quantity;
    }
    
    if (serviceType === 'third' && hasCheck) { // для третьего типа
        total *= 1.25;
    }
    
    showResults(quantity, basePrice, total, serviceType, optionValue, hasCheck);
}

function formatPrice(price) {
    return Math.round(price) + ' руб.';
}

function showResults(quantity, basePrice, total, serviceType, optionValue, hasCheck) { //строка результата для разных типов
    //Очищаем предыдущие результаты
    detailsElement.innerHTML = '';
    
    //Основная информация
    const totalPrice = formatPrice(total);
    totalPriceElement.textContent = `Итог: ${totalPrice}`;
    

    let detailsHTML = `
        <div class="result-container">
            <div class="details-container">
                <div class="calculation">
    `;
    
    let calculation = `${quantity} × ${formatPrice(basePrice)}`;
    
    if (serviceType === 'second') {
        const optionPrice = optionPrices[optionValue];
        if (optionPrice > 0) {
            calculation += ` + ${quantity} × ${formatPrice(optionPrice)} (дополнительная опция)`;
        }
    }
    
    if (serviceType === 'third' && hasCheck) {
        calculation += ` + 25% (дополнительная услуга)`;
    }
    
    calculation += ` = ${totalPrice}`;
    
    detailsHTML += calculation + '</div></div></div>';//добавляем строку расчета, закрываем дивы
    detailsElement.innerHTML = detailsHTML;
    detailsElement.style.display = 'block';
}

function showError(message) {
    // Убираем старую ошибку
    const oldError = document.querySelector('.error-message');
    if (oldError) oldError.remove();
    
    // Создаем новую с оформлением
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    errorElement.style.color = 'red';
    errorElement.style.marginTop = '5px';
    
    kolichInput.parentNode.appendChild(errorElement);
    kolichInput.style.borderColor = 'red';
}

function clearError() { //отмена ошибки и ее дизайна
    const errorElement = document.querySelector('.error-message');
    if (errorElement) errorElement.remove();
    kolichInput.style.borderColor = '';
}

function hideResults() { //прячем результаты 
    totalPriceElement.textContent = '';
    detailsElement.innerHTML = '';
    detailsElement.style.display = 'none';
}

// Настройка обработчиков событий
kolichInput.addEventListener('input', () => {
    clearError();
    hideResults();
});

kolichInput.addEventListener('blur', calculateTotal);

serviceRadios.forEach(radio => {//перерасчет радио кнопки
    radio.addEventListener('change', function() {
        updateFormVisibility(this.value);
        calculateTotal();
    });
});

optionSelect.addEventListener('change', calculateTotal);//изменения в выпадающем списке
checkCheckbox.addEventListener('change', calculateTotal); //изменения в чекбоксе

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    // Настраиваем видимость полей в зависимости от выбранной услуги
    const selectedService = document.querySelector('input[name="service"]:checked').value;
    updateFormVisibility(selectedService);
    
    // Проверяем начальное значение
    if (kolichInput.value.trim()) {
        calculateTotal();
    }
});