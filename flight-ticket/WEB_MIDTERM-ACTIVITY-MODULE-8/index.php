<?php
/**
 * Flight Schedule System - Main Application File
 * 
 * This is the main entry point for the Flight Schedule System.
 * Displays international and domestic flight departures with:
 * - Real-time search and filtering
 * - Multi-criteria sorting
 * - Flight status indicators
 * - Responsive design
 * - Accessibility features
 * - Performance optimizations
 * 
 * @author Marimla, Chleo Jae G.
 * @course CYB-201
 * @version 1.0
 */

date_default_timezone_set("Asia/Manila");

// Include flight data layer
require_once 'data/flights.php';

// Include flight card rendering function
require_once 'includes/flight-card.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Schedule</title>
    <link rel="stylesheet" href="css/base.min.css">
    <link rel="stylesheet" href="css/layout.min.css">
    <link rel="stylesheet" href="css/components.min.css">
    <link rel="stylesheet" href="css/print.min.css" media="print">
</head>
<body>

<?php include 'includes/header.php'; ?> <!-- Flight Schedule System + date/time -->

<!-- Search and Filter Section -->
<section class="search-section" aria-label="Search flights">
    <div class="search-container">
        <span class="search-icon" aria-hidden="true">🔍</span>
        <input 
            type="search" 
            id="flight-search"
            class="search-input" 
            placeholder="Search by destination, airline, or flight number..."
            aria-label="Search flights by destination, airline, or flight number"
            role="searchbox"
            autocomplete="off"
            aria-describedby="search-results-status">
        <button 
            class="search-clear" 
            id="search-clear-btn"
            aria-label="Clear search"
            type="button">✕</button>
    </div>
    
    <!-- Screen reader announcement for search results -->
    <div id="search-results-status" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>
    
    <!-- Sort Controls -->
    <div class="sort-controls">
        <label for="sort-by" class="sort-label">Sort by:</label>
        <select 
            id="sort-by" 
            class="sort-select"
            aria-label="Sort flights by"
            aria-controls="international-flights domestic-flights">
            <option value="departure" selected>Departure Time</option>
            <option value="arrival">Arrival Time</option>
            <option value="duration">Duration</option>
            <option value="destination">Destination</option>
        </select>
        
        <button 
            id="sort-order-toggle"
            class="sort-order-btn"
            aria-label="Toggle sort order between ascending and descending"
            aria-pressed="false"
            title="Sort order: Ascending">
            <span class="sort-icon" aria-hidden="true">↑</span>
            <span class="sort-order-text">Ascending</span>
        </button>
    </div>
</section>

<main role="main">
    <section aria-labelledby="international-heading">
        <h1 id="international-heading">International Departures</h1>
        <div class="card-grid" id="international-flights" role="list" aria-live="polite" aria-atomic="false">
            <?php foreach ($flightsInternational as $flight): ?>
                <?= renderFlightCard($flight) ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section aria-labelledby="domestic-heading">
        <h1 id="domestic-heading">Domestic Departures</h1>
        <div class="card-grid" id="domestic-flights" role="list" aria-live="polite" aria-atomic="false">
            <?php foreach ($flightsDomestic as $flight): ?>
                <?= renderFlightCard($flight) ?>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<!-- Output flight data as JSON for JavaScript consumption -->
<script>
    window.flightData = {
        international: <?= json_encode($flightsInternational) ?>,
        domestic: <?= json_encode($flightsDomestic) ?>
    };
</script>

<!-- JavaScript modules -->
<script src="js/utils.min.js"></script>
<script src="js/ui-components.min.js"></script>
<script src="js/flight-manager.min.js"></script>

<!-- Initialize Flight Manager and Search -->
<script>
    /**
     * Error logging function for debugging
     * Logs errors with timestamp and context for troubleshooting
     * @param {Error|string} error - Error object or message
     * @param {string} context - Context where error occurred
     */
    function logError(error, context = '') {
        const timestamp = new Date().toISOString();
        const errorDetails = {
            timestamp: timestamp,
            context: context,
            message: error.message || error,
            stack: error.stack || 'No stack trace available'
        };
        
        // Log to console for debugging
        console.error('[Flight Schedule Error]', errorDetails);
        
        // In production, this could send to a logging service
        // Example: sendToLoggingService(errorDetails);
    }
    
    /**
     * Validate and handle flight data
     * Ensures flight data structure is correct before initialization
     * @param {Object} data - Flight data object with international and domestic arrays
     * @returns {boolean} True if validation passes
     * @throws {Error} If data is invalid
     */
    function validateFlightData(data) {
        if (!data) {
            throw new Error('Flight data is null or undefined');
        }
        
        if (!data.international || !Array.isArray(data.international)) {
            throw new Error('International flights data is missing or malformed');
        }
        
        if (!data.domestic || !Array.isArray(data.domestic)) {
            throw new Error('Domestic flights data is missing or malformed');
        }
        
        return true;
    }
    
    /**
     * Initialize application with error handling
     * Sets up FlightManager and handles initialization errors gracefully
     * @returns {FlightManager|null} FlightManager instance or null if initialization fails
     */
    function initializeApp() {
        try {
            // Show loading state initially
            const intContainer = document.getElementById('international-flights');
            const domContainer = document.getElementById('domestic-flights');
            
            if (!intContainer || !domContainer) {
                throw new Error('Flight containers not found in DOM');
            }
            
            // Validate flight data
            validateFlightData(window.flightData);
            
            // Initialize FlightManager with flight data
            const flightManager = new FlightManager(
                window.flightData.international,
                window.flightData.domestic
            );
            
            return flightManager;
        } catch (error) {
            logError(error, 'Application initialization');
            
            // Display error state
            const intContainer = document.getElementById('international-flights');
            const domContainer = document.getElementById('domestic-flights');
            
            if (intContainer) {
                intContainer.innerHTML = renderErrorState('We\'re having trouble loading flight information. Please try again.');
            }
            if (domContainer) {
                domContainer.innerHTML = '';
            }
            
            // Disable search and sort controls
            const searchInput = document.getElementById('flight-search');
            const sortBySelect = document.getElementById('sort-by');
            const sortOrderBtn = document.getElementById('sort-order-toggle');
            
            if (searchInput) searchInput.disabled = true;
            if (sortBySelect) sortBySelect.disabled = true;
            if (sortOrderBtn) sortOrderBtn.disabled = true;
            
            return null;
        }
    }
    
    // Initialize the application
    const flightManager = initializeApp();
    
    // Initialize lazy loading for images
    if (typeof initializeLazyLoading === 'function') {
        initializeLazyLoading();
    } else {
        logError('initializeLazyLoading function not found', 'Lazy loading initialization');
    }
    
    // Only proceed with event handlers if initialization succeeded
    if (!flightManager) {
        // Exit early if initialization failed
        console.warn('Flight manager initialization failed. Event handlers will not be attached.');
    } else {
        // Proceed with event handler setup
        setupEventHandlers(flightManager);
    }
    
    /**
     * Setup all event handlers for search, sort, and keyboard navigation
     * @param {FlightManager} flightManager - Initialized FlightManager instance
     */
    function setupEventHandlers(flightManager) {
        try {
            // Get DOM elements
            const searchInput = document.getElementById('flight-search');
            const searchClearBtn = document.getElementById('search-clear-btn');
            const sortBySelect = document.getElementById('sort-by');
            const sortOrderBtn = document.getElementById('sort-order-toggle');
            
            // Verify all required elements exist
            if (!searchInput || !searchClearBtn || !sortBySelect || !sortOrderBtn) {
                throw new Error('Required UI elements not found');
            }
            
            // Track current sort order
            let currentSortOrder = 'asc';
            
            /**
             * Debounced search function (300ms delay)
             * Filters flights and updates display based on search term
             * @param {string} searchTerm - Search term entered by user
             */
            const performSearch = debounce((searchTerm) => {
                try {
                    // Filter flights based on search term
                    flightManager.filterFlights(searchTerm, 'both');
                    
                    // Update display
                    flightManager.updateDisplay('international-flights', 'domestic-flights');
                    
                    // Announce results to screen readers
                    const state = flightManager.getState();
                    const totalResults = state.internationalCount + state.domesticCount;
                    const statusElement = document.getElementById('search-results-status');
                    
                    if (statusElement) {
                        if (searchTerm.trim()) {
                            statusElement.textContent = `Found ${totalResults} flight${totalResults !== 1 ? 's' : ''} matching "${searchTerm}"`;
                        } else {
                            statusElement.textContent = '';
                        }
                    }
                    
                    // Toggle clear button visibility
                    if (searchTerm.trim()) {
                        searchClearBtn.classList.add('visible');
                    } else {
                        searchClearBtn.classList.remove('visible');
                    }
                } catch (error) {
                    logError(error, 'Search operation');
                }
            }, 300);
            
            // Attach event listener to search input
            searchInput.addEventListener('input', (e) => {
                performSearch(e.target.value);
            });
            
            // Clear search functionality
            searchClearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.focus();
                performSearch('');
            });
            
            // Global function for clearing search from no results button
            window.clearSearchFromNoResults = function() {
                searchInput.value = '';
                searchInput.focus();
                performSearch('');
            };
            
            // Sort by select change handler
            sortBySelect.addEventListener('change', (e) => {
                try {
                    const sortBy = e.target.value;
                    flightManager.sortFlights(sortBy, currentSortOrder);
                    flightManager.updateDisplay('international-flights', 'domestic-flights');
                } catch (error) {
                    logError(error, 'Sort operation');
                }
            });
            
            // Sort order toggle handler
            sortOrderBtn.addEventListener('click', () => {
                try {
                    // Toggle sort order
                    currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                    
                    // Update button appearance
                    const icon = sortOrderBtn.querySelector('.sort-icon');
                    const text = sortOrderBtn.querySelector('.sort-order-text');
                    
                    if (currentSortOrder === 'desc') {
                        icon.textContent = '↓';
                        text.textContent = 'Descending';
                        sortOrderBtn.setAttribute('aria-pressed', 'true');
                        sortOrderBtn.setAttribute('title', 'Sort order: Descending');
                    } else {
                        icon.textContent = '↑';
                        text.textContent = 'Ascending';
                        sortOrderBtn.setAttribute('aria-pressed', 'false');
                        sortOrderBtn.setAttribute('title', 'Sort order: Ascending');
                    }
                    
                    // Apply sort with new order
                    const sortBy = sortBySelect.value;
                    flightManager.sortFlights(sortBy, currentSortOrder);
                    flightManager.updateDisplay('international-flights', 'domestic-flights');
                } catch (error) {
                    logError(error, 'Sort order toggle');
                }
            });
            
            // Keyboard support for sort order button
            sortOrderBtn.addEventListener('keydown', (e) => {
                // Enter or Space activates the button
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    sortOrderBtn.click();
                }
            });
            
            // Keyboard support for sort select
            sortBySelect.addEventListener('keydown', (e) => {
                // Arrow keys navigate options (native behavior)
                // Enter activates selection (native behavior)
                // Just ensure proper focus management
                if (e.key === 'Enter') {
                    sortBySelect.blur();
                }
            });
            
            // Keyboard support for search
            searchInput.addEventListener('keydown', (e) => {
                // Escape key clears search
                if (e.key === 'Escape') {
                    e.preventDefault();
                    searchInput.value = '';
                    performSearch('');
                    searchInput.blur();
                }
                
                // Enter key focuses first result
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const firstCard = document.querySelector('.flight-card');
                    if (firstCard) {
                        // Make the first card focusable and focus it
                        firstCard.setAttribute('tabindex', '0');
                        firstCard.focus();
                        
                        // Scroll into view
                        firstCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
            
            // Add focus styles to flight cards when focused via keyboard
            document.addEventListener('focusin', (e) => {
                if (e.target.classList.contains('flight-card')) {
                    e.target.style.outline = '2px solid var(--color-primary)';
                    e.target.style.outlineOffset = '4px';
                }
            });
            
            document.addEventListener('focusout', (e) => {
                if (e.target.classList.contains('flight-card')) {
                    e.target.style.outline = '';
                    e.target.style.outlineOffset = '';
                }
            });
        } catch (error) {
            logError(error, 'Event handler setup');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>

</body>
</html>
