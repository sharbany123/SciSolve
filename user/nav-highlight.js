// nav-highlight.js
document.addEventListener('DOMContentLoaded', function() {
    // Get current page filename (e.g. "simulation.html")
    const currentPage = window.location.pathname.split('/').pop();
    
    // Highlight matching nav link
    document.querySelectorAll('.nav-links a').forEach(link => {
        const linkPage = link.getAttribute('href');
        
        // Check for exact match OR homepage exception
        if(linkPage === currentPage || 
           (currentPage === '' && linkPage === 'index.html')) {
            link.classList.add('active');
        }
    });
});