/**
 * Toggles the navigation menu for mobile devices.
 */
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.querySelector('nav ul');
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true' || false;

    if (menuToggle && navLinks) {
        // Initialize aria-expanded
        menuToggle.setAttribute('aria-expanded', isExpanded);

        menuToggle.addEventListener('click', () => {
            // Toggle the 'show' class to display/hide the menu
            navLinks.classList.toggle('show');

            // Update ARIA attribute for accessibility
            const newExpandedState = navLinks.classList.contains('show');
            menuToggle.setAttribute('aria-expanded', newExpandedState);
        });
    }

    // You can add other scripts here, such as form handling.
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            // Prevent default form submission
            event.preventDefault(); 
            
            // Basic Form Submission Logic (client-side only for this example)
            console.log('Form Submitted!');
            alert('Thank you for your message! We will get back to you soon.');

            // Clear the form fields after submission (optional)
            contactForm.reset(); 
        });
    }
});