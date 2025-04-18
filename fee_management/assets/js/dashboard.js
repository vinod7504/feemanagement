document.addEventListener('DOMContentLoaded', function() {
    // Animate stats numbers
    const statNumbers = document.querySelectorAll('.stat-card .number');
    statNumbers.forEach(number => {
        const finalValue = parseInt(number.getAttribute('data-value'));
        animateNumber(number, finalValue);
    });

    // Filter form handling
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your filter logic here
        });
    }
});

function animateNumber(element, final) {
    let current = 0;
    const duration = 1000; // 1 second
    const step = final / (duration / 16); // 60fps

    function update() {
        current += step;
        if (current > final) current = final;
        
        element.textContent = Math.floor(current).toLocaleString();
        
        if (current < final) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

// Toggle mobile menu
function toggleMenu() {
    const nav = document.querySelector('.user-nav');
    nav.classList.toggle('show');
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(amount);
} 