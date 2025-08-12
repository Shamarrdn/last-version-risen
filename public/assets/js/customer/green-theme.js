// Apply green theme to blue elements
document.addEventListener('DOMContentLoaded', function() {
    // Set the primary color
    const primaryColor = '#009245';
    const primaryColorRgba = 'rgba(0, 146, 69, 0.1)';
    const primaryColorHover = '#007a3d';

    // Fix the add to cart button (the prominent blue button in screenshot)
    const addToCartButtons = document.querySelectorAll('.btn-primary');
    addToCartButtons.forEach(button => {
        button.style.backgroundColor = primaryColor;
        button.style.borderColor = primaryColor;
    });

    // Fix any blue alerts
    const alerts = document.querySelectorAll('.alert-info');
    alerts.forEach(alert => {
        alert.style.backgroundColor = primaryColorRgba;
        alert.style.borderColor = 'rgba(0, 146, 69, 0.2)';
        alert.style.color = '#0c5f36';
    });

    // Fix any blue badges
    const badges = document.querySelectorAll('.badge.bg-primary');
    badges.forEach(badge => {
        badge.style.backgroundColor = primaryColor + ' !important';
    });

    // Fix any blue text
    const textPrimary = document.querySelectorAll('.text-primary');
    textPrimary.forEach(text => {
        text.style.color = primaryColor + ' !important';
    });

    // Apply to any element with style="background-color: #0d6efd" or similar
    document.querySelectorAll('[style*="background-color"]').forEach(element => {
        const style = element.getAttribute('style');
        if (style.includes('#0d6efd') || style.includes('rgb(13, 110, 253)') ||
            style.includes('rgb(0, 123, 255)') || style.includes('#007bff')) {
            element.style.backgroundColor = primaryColor;
        }
    });

    // Fix inline styles with blue colors
    document.querySelectorAll('[style*="color"]').forEach(element => {
        const style = element.getAttribute('style');
        if (style.includes('#0d6efd') || style.includes('rgb(13, 110, 253)') ||
            style.includes('rgb(0, 123, 255)') || style.includes('#007bff')) {
            element.style.color = primaryColor;
        }
    });

    // Override Bootstrap's default blue focus styles
    const style = document.createElement('style');
    style.textContent = `
        .btn-primary, .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: ${primaryColor} !important;
            border-color: ${primaryColor} !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: ${primaryColor} !important;
            border-color: ${primaryColor} !important;
            color: white !important;
        }
        .btn-outline-primary {
            color: ${primaryColor} !important;
            border-color: ${primaryColor} !important;
        }
        .btn-primary:focus, .btn-outline-primary:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 146, 69, 0.25) !important;
        }
        a {
            color: ${primaryColor};
        }
        a:hover {
            color: ${primaryColorHover};
        }
        .form-control:focus, .form-select:focus {
            border-color: ${primaryColor} !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 146, 69, 0.25) !important;
        }
    `;
    document.head.appendChild(style);
});
