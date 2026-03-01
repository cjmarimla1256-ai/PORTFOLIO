/**
 * Utility Functions
 * 
 * Provides utility functions for time formatting, duration formatting,
 * timezone handling, and search optimization.
 */

/**
 * Format a datetime string to 12-hour format with AM/PM
 * @param {string} datetime - ISO 8601 datetime string
 * @returns {string} Formatted time (e.g., "10:30 AM")
 */
function formatTime12Hour(datetime) {
    const date = new Date(datetime);
    let hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    
    hours = hours % 12;
    hours = hours ? hours : 12; // Convert 0 to 12
    const minutesStr = minutes < 10 ? '0' + minutes : minutes;
    
    return `${hours}:${minutesStr} ${ampm}`;
}

/**
 * Format a datetime string with timezone abbreviation
 * @param {string} datetime - ISO 8601 datetime string
 * @param {string} timezone - Timezone string (e.g., "Asia/Manila")
 * @returns {string} Formatted time with timezone (e.g., "10:30 AM PST")
 */
function formatTimeWithTimezone(datetime, timezone) {
    const time = formatTime12Hour(datetime);
    const tzAbbr = getTimezoneAbbreviation(timezone);
    return `${time} ${tzAbbr}`;
}

/**
 * Extract timezone abbreviation from timezone string
 * @param {string} timezone - Timezone string (e.g., "Asia/Manila", "Europe/Amsterdam")
 * @returns {string} Timezone abbreviation (e.g., "PHT", "CET")
 */
function getTimezoneAbbreviation(timezone) {
    // Map of common timezones to their abbreviations
    const timezoneMap = {
        'Asia/Manila': 'PHT',
        'Asia/Tokyo': 'JST',
        'Asia/Bangkok': 'ICT',
        'Europe/Amsterdam': 'CET',
        'Europe/Zurich': 'CET',
        'Europe/Rome': 'CET'
    };
    
    return timezoneMap[timezone] || timezone.split('/')[1].substring(0, 3).toUpperCase();
}

/**
 * Format duration in minutes to hours and minutes
 * @param {number} minutes - Duration in minutes
 * @returns {string} Formatted duration (e.g., "4h 30m" or "1h 20m")
 */
function formatDuration(minutes) {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    if (hours === 0) {
        return `${mins}m`;
    }
    
    if (mins === 0) {
        return `${hours}h`;
    }
    
    return `${hours}h ${mins}m`;
}

/**
 * Calculate arrival time based on departure and duration
 * @param {string} departure - ISO 8601 departure datetime
 * @param {number} duration - Duration in minutes
 * @returns {Date} Arrival datetime
 */
function calculateArrivalTime(departure, duration) {
    const departureDate = new Date(departure);
    const arrivalDate = new Date(departureDate.getTime() + duration * 60000);
    return arrivalDate;
}

/**
 * Format arrival time with timezone
 * @param {string} departure - ISO 8601 departure datetime
 * @param {number} duration - Duration in minutes
 * @param {string} timezone - Destination timezone
 * @returns {string} Formatted arrival time with timezone
 */
function formatArrivalTime(departure, duration, timezone) {
    const arrivalDate = calculateArrivalTime(departure, duration);
    const arrivalISO = arrivalDate.toISOString();
    return formatTimeWithTimezone(arrivalISO, timezone);
}

/**
 * Format date in readable format (e.g., "Jan 22")
 * @param {Date} date - Date object
 * @returns {string} Formatted date string
 */
function formatDate(date) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = months[date.getMonth()];
    const day = date.getDate();
    return `${month} ${day}`;
}

/**
 * Get timezone difference in hours between two timezones
 * @param {string} departure - ISO 8601 departure datetime
 * @param {number} duration - Duration in minutes
 * @param {string} originTZ - Origin timezone
 * @param {string} destTZ - Destination timezone
 * @returns {number} Timezone difference in hours (can be negative)
 */
function getTimezoneDifference(departure, duration, originTZ, destTZ) {
    // For simplicity, we'll use a mapping of known timezone offsets
    // In a production system, you'd use a library like moment-timezone or date-fns-tz
    const timezoneOffsets = {
        'Asia/Manila': 8,
        'Asia/Tokyo': 9,
        'Asia/Bangkok': 7,
        'Europe/Amsterdam': 1,
        'Europe/Zurich': 1,
        'Europe/Rome': 1
    };
    
    const originOffset = timezoneOffsets[originTZ] || 0;
    const destOffset = timezoneOffsets[destTZ] || 0;
    
    return destOffset - originOffset;
}

/**
 * Debounce function to limit the rate at which a function can fire
 * @param {Function} func - Function to debounce
 * @param {number} delay - Delay in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Check if arrival date differs from departure date
 * @param {string} departure - ISO 8601 departure datetime
 * @param {number} duration - Duration in minutes
 * @returns {boolean} True if arrival is on a different date
 */
function isNextDayArrival(departure, duration) {
    const departureDate = new Date(departure);
    const arrivalDate = calculateArrivalTime(departure, duration);
    
    return departureDate.getDate() !== arrivalDate.getDate() ||
           departureDate.getMonth() !== arrivalDate.getMonth() ||
           departureDate.getFullYear() !== arrivalDate.getFullYear();
}

/**
 * Generate timezone difference indicator HTML
 * @param {string} departure - ISO 8601 departure datetime
 * @param {number} duration - Duration in minutes
 * @param {string} originTZ - Origin timezone
 * @param {string} destTZ - Destination timezone
 * @returns {string} HTML string for timezone difference indicator (empty if same timezone)
 */
function renderTimezoneDifferenceIndicator(departure, duration, originTZ, destTZ) {
    if (originTZ === destTZ) {
        return ''; // No indicator needed for same timezone
    }
    
    const tzDiff = getTimezoneDifference(departure, duration, originTZ, destTZ);
    const sign = tzDiff > 0 ? '+' : '';
    const diffText = `${sign}${tzDiff}h`;
    
    return `<span class="timezone-diff" title="Timezone difference">${diffText}</span>`;
}

/**
 * Generate next day arrival indicator HTML
 * @param {string} departure - ISO 8601 departure datetime
 * @param {number} duration - Duration in minutes
 * @returns {string} HTML string for next day indicator (empty if same day)
 */
function renderNextDayIndicator(departure, duration) {
    if (!isNextDayArrival(departure, duration)) {
        return '';
    }
    
    const arrivalDate = calculateArrivalTime(departure, duration);
    const formattedDate = formatDate(arrivalDate);
    
    return `<span class="next-day-indicator" title="Arrives next day">(${formattedDate})</span>`;
}

/**
 * Initialize lazy loading for images using Intersection Observer API
 * Images with data-src attribute will be loaded when they enter the viewport
 */
function initializeLazyLoading() {
    // Check if Intersection Observer is supported
    if (!('IntersectionObserver' in window)) {
        // Fallback: load all images immediately
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.classList.add('loaded');
        });
        return;
    }
    
    // Create Intersection Observer
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Load the image
                img.src = img.dataset.src;
                
                // Add loaded class when image loads
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });
                
                // Handle image load errors
                img.addEventListener('error', () => {
                    img.src = 'img/default.jpg';
                    img.classList.add('loaded');
                });
                
                // Stop observing this image
                observer.unobserve(img);
            }
        });
    }, {
        // Load images 50px before they enter the viewport
        rootMargin: '50px',
        threshold: 0.01
    });
    
    // Observe all images with data-src attribute
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
}

/**
 * Re-initialize lazy loading for dynamically added images
 * Call this after updating the DOM with new flight cards
 */
function reinitializeLazyLoading() {
    initializeLazyLoading();
}
