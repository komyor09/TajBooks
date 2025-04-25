document.addEventListener('DOMContentLoaded', function() {

    const minPrice = document.getElementById('min_price');
    const maxPrice = document.getElementById('max_price');
    const minPriceValue = document.getElementById('min_price_value');
    const maxPriceValue = document.getElementById('max_price_value');
    
    const minYear = document.getElementById('min_year');
    const maxYear = document.getElementById('max_year');
    const minYearValue = document.getElementById('min_year_value');
    const maxYearValue = document.getElementById('max_year_value');
    
    minPrice.addEventListener('input', () => {
        minPriceValue.value = minPrice.value;
        if (parseInt(minPrice.value) > parseInt(maxPrice.value)) {
            maxPrice.value = minPrice.value;
            maxPriceValue.value = minPrice.value;
        }
    });
    
    maxPrice.addEventListener('input', () => {
        maxPriceValue.value = maxPrice.value;
        if (parseInt(maxPrice.value) < parseInt(minPrice.value)) {
            minPrice.value = maxPrice.value;
            minPriceValue.value = maxPrice.value;
        }
    });
    
    minPriceValue.addEventListener('change', () => {
        minPrice.value = minPriceValue.value;
        if (parseInt(minPrice.value) > parseInt(maxPrice.value)) {
            maxPrice.value = minPrice.value;
            maxPriceValue.value = minPrice.value;
        }
    });
    
    maxPriceValue.addEventListener('change', () => {
        maxPrice.value = maxPriceValue.value;
        if (parseInt(maxPrice.value) < parseInt(minPrice.value)) {
            minPrice.value = maxPrice.value;
            minPriceValue.value = maxPrice.value;
        }
    });
    
    minYear.addEventListener('input', () => {
        minYearValue.value = minYear.value;
        if (parseInt(minYear.value) > parseInt(maxYear.value)) {
            maxYear.value = minYear.value;
            maxYearValue.value = minYear.value;
        }
    });
    
    maxYear.addEventListener('input', () => {
        maxYearValue.value = maxYear.value;
        if (parseInt(maxYear.value) < parseInt(minYear.value)) {
            minYear.value = maxYear.value;
            minYearValue.value = maxYear.value;
        }
    });
    
    minYearValue.addEventListener('change', () => {
        minYear.value = minYearValue.value;
        if (parseInt(minYear.value) > parseInt(maxYear.value)) {
            maxYear.value = minYear.value;
            maxYearValue.value = minYear.value;
        }
    });
    
    maxYearValue.addEventListener('change', () => {
        maxYear.value = maxYearValue.value;
        if (parseInt(maxYear.value) < parseInt(minYear.value)) {
            minYear.value = maxYear.value;
            minYearValue.value = maxYear.value;
        }
    });
});