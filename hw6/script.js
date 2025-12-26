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

function setupEventListeners() {
    kolichInput.addEventListener('input', calculate);
    
    serviceRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateForm(this.value);
            calculate();
        });
    });
    
    optionSelect.addEventListener('change', calculate);
    
    checkCheckbox.addEventListener('change', calculate);
}

function updateForm(serviceType) {
    optionField.style.display = 'none';
    checkField.style.display = 'none';
    
    if (serviceType === 'second') {
        optionField.style.display = 'block';
    } else if (serviceType === 'third') {
        checkField.style.display = 'block';
    }
}

function calculate() {
    const kolich = parseInt(kolichInput.value) || 1;
    const serviceType = document.querySelector('input[name="service"]:checked').value;
    const optionValue = optionSelect.value;
    const hasCheck = checkCheckbox.checked;
    
    const basePrice = prices[serviceType];
    let total = basePrice * kolich;
    
    if (serviceType === 'second') {
        const optionPrice = optionPrices[optionValue];
        total += optionPrice * kolich;
    }
    
    if (serviceType === 'third' && hasCheck) {
        total = total * 1.25;
    }
    
    updatePriceDisplay(kolich, basePrice, total, serviceType, optionValue, hasCheck);
}

function updatePriceDisplay(kolich, basePrice, total, serviceType, optionValue, hasCheck) {
    function format(price) {
        return Math.round(price) + ' руб.';
    }
    
    totalPriceElement.textContent = format(total);
    
    let details = kolich + ' × ' + format(basePrice);
    
    if (serviceType === 'second') {
        const optionPrice = optionPrices[optionValue];
        if (optionPrice > 0) {
            details += ' + ' + kolich + ' × ' + format(optionPrice) + ' (опция)';
        }
    }
    
    if (serviceType === 'third' && hasCheck) {
        details += ' + 25% (свойство)';
    }
    
    details += ' = ' + format(total);
    detailsElement.textContent = details;
}

document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    calculate();
});