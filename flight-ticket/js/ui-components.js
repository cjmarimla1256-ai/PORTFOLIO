/**
 * UI Components Module
 * 
 * Provides rendering functions for flight cards, status indicators,
 * loading states, error states, and no results messages.
 */

/**
 * Render a flight status indicator with color and text
 * @param {string} status - Flight status (on-time, delayed, cancelled, boarding)
 * @param {number|null} delayMinutes - Delay duration in minutes (if applicable)
 * @returns {string} HTML string for status indicator
 */
function renderStatusIndicator(status, delayMinutes = null) {
    const statusClass = 'status-' + status;
    let statusText = status.split('-').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    let icon = '';
    
    // Determine icon based on status
    switch(status) {
        case 'on-time':
            icon = '✓';
            break;
        case 'delayed':
            icon = '🕐';
            break;
        case 'cancelled':
            icon = '✕';
            break;
        case 'boarding':
            icon = '✈';
            break;
        default:
            icon = '';
    }
    
    // Add delay information if applicable
    if (status === 'delayed' && delayMinutes !== null) {
        statusText += ' (' + delayMinutes + ' min)';
    }
    
    let html = '<div class="status-indicator ' + statusClass + '" role="status" aria-live="polite">';
    html += '<span class="status-icon" aria-hidden="true">' + icon + '</span>';
    html += '<span class="status-text">' + escapeHtml(statusText) + '</span>';
    html += '</div>';
    
    return html;
}

/**
 * Render a flight card with all flight information
 * @param {Object} flight - Flight data object
 * @returns {string} HTML string for flight card
 */
function renderFlightCard(flight) {
    // Validate required flight data
    if (!flight) {
        console.error('Flight data is null or undefined');
        return '';
    }
    
    // Provide defaults for missing data
    const safeFlightData = {
        id: flight.id || 'unknown',
        img: flight.img || 'img/default.jpg',
        from: flight.from || 'N/A',
        to: flight.to || 'N/A',
        fromCity: flight.fromCity || 'Unknown',
        toCity: flight.toCity || 'Unknown',
        flightNo: flight.flightNo || 'N/A',
        airline: flight.airline || 'Unknown Airline',
        originTZ: flight.originTZ || 'UTC',
        destTZ: flight.destTZ || 'UTC',
        departure: flight.departure || new Date().toISOString(),
        duration: flight.duration || 0,
        status: flight.status || 'on-time',
        delayMinutes: flight.delayMinutes || null
    };
    
    try {
        // Format departure time with timezone
        const depTime = formatTimeWithTimezone(safeFlightData.departure, safeFlightData.originTZ);
        
        // Format arrival time with timezone
        const arrTime = formatArrivalTime(safeFlightData.departure, safeFlightData.duration, safeFlightData.destTZ);
        
        // Format duration
        const durationFormatted = formatDuration(safeFlightData.duration);
        
        // Generate status indicator HTML
        const statusHTML = renderStatusIndicator(safeFlightData.status, safeFlightData.delayMinutes);
        
        // Generate timezone difference indicator (if applicable)
        const tzDiffIndicator = renderTimezoneDifferenceIndicator(
            safeFlightData.departure, 
            safeFlightData.duration, 
            safeFlightData.originTZ, 
            safeFlightData.destTZ
        );
        
        // Generate next day indicator (if applicable)
        const nextDayIndicator = renderNextDayIndicator(safeFlightData.departure, safeFlightData.duration);
        
        // Build the flight card HTML
        let html = '<article class="flight-card" role="listitem" aria-label="Flight from ' + escapeHtml(safeFlightData.fromCity) + ' to ' + escapeHtml(safeFlightData.toCity) + '">';
        html += `<img data-src="${escapeHtml(safeFlightData.img)}" alt="${escapeHtml(safeFlightData.toCity)} destination" class="lazy-image" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 400'%3E%3Crect fill='%23e0e0e0' width='640' height='400'/%3E%3C/svg%3E">`;
        html += '<div class="card-section">';
        html += `<h2>${escapeHtml(safeFlightData.from)} → ${escapeHtml(safeFlightData.to)}</h2>`;
        html += statusHTML;
        html += '<p>';
        html += `From ${escapeHtml(safeFlightData.fromCity)} to ${escapeHtml(safeFlightData.toCity)}<br>`;
        html += `<strong>Flight No. ${escapeHtml(safeFlightData.flightNo)}</strong><br>`;
        html += `${escapeHtml(safeFlightData.airline)}<br><br>`;
        html += `<strong>Departure:</strong> ${depTime}<br>`;
        html += `<strong>Arrival:</strong> ${arrTime} ${nextDayIndicator} ${tzDiffIndicator}<br>`;
        html += `<strong>Duration:</strong> ${durationFormatted}`;
        html += '</p>';
        html += '</div>';
        html += '</article>';
        
        return html;
    } catch (error) {
        console.error('Error rendering flight card:', safeFlightData.id, error);
        // Return empty string to skip this card
        return '';
    }
}

/**
 * Escape HTML special characters
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Render loading state with spinner
 * @returns {string} HTML string for loading state
 */
function renderLoadingState() {
    return `
        <div class="loading-container" role="status" aria-live="polite">
            <div class="spinner" aria-hidden="true"></div>
            <p>Loading flight information...</p>
        </div>
    `;
}

/**
 * Render error state with message and retry button
 * @param {string} message - Error message to display
 * @returns {string} HTML string for error state
 */
function renderErrorState(message = 'Unable to load flight information') {
    return `
        <div class="error-container" role="alert">
            <div class="error-icon" aria-hidden="true">⚠</div>
            <h3>Error</h3>
            <p>${message}</p>
            <button onclick="location.reload()" class="retry-button">
                Retry
            </button>
        </div>
    `;
}

/**
 * Render no results message when search returns empty
 * @param {string} searchTerm - The search term that returned no results
 * @returns {string} HTML string for no results state
 */
function renderNoResults(searchTerm = '') {
    const message = searchTerm 
        ? `No flights found matching "${escapeHtml(searchTerm)}"`
        : 'No flights found';
    
    const clearButton = searchTerm 
        ? '<button onclick="clearSearchFromNoResults()" class="btn btn-primary" aria-label="Clear search">Clear Search</button>'
        : '';
    
    return `
        <div class="no-results" role="status">
            <div class="no-results-icon" aria-hidden="true">🔍</div>
            <p>${message}</p>
            <p class="no-results-hint">Try searching by destination, airline, or flight number</p>
            ${clearButton}
        </div>
    `;
}
