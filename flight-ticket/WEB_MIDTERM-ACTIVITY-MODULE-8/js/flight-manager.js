/**
 * Flight Manager Class
 * 
 * Manages flight data, filtering, sorting, and display coordination.
 * Handles both international and domestic flights separately.
 */

class FlightManager {
    /**
     * Create a FlightManager instance
     * @param {Array} internationalFlights - Array of international flight objects
     * @param {Array} domesticFlights - Array of domestic flight objects
     */
    constructor(internationalFlights, domesticFlights) {
        this.internationalFlights = internationalFlights || [];
        this.domesticFlights = domesticFlights || [];
        this.filteredInternational = [...this.internationalFlights];
        this.filteredDomestic = [...this.domesticFlights];
        this.currentSearchTerm = '';
        this.currentSortBy = 'departure';
        this.currentSortOrder = 'asc';
    }

    /**
     * Filter flights based on search term
     * Searches across destination city, airline name, and flight number
     * @param {string} searchTerm - Search term to filter by
     * @param {string} flightType - 'international' or 'domestic' or 'both'
     * @returns {Object} Object with filtered international and domestic arrays
     */
    filterFlights(searchTerm, flightType = 'both') {
        this.currentSearchTerm = searchTerm;
        
        if (!searchTerm || searchTerm.trim() === '') {
            this.filteredInternational = [...this.internationalFlights];
            this.filteredDomestic = [...this.domesticFlights];
        } else {
            const term = searchTerm.toLowerCase().trim();
            
            const filterFunction = (flight) => {
                return flight.toCity.toLowerCase().includes(term) ||
                       flight.airline.toLowerCase().includes(term) ||
                       flight.flightNo.toLowerCase().includes(term);
            };
            
            if (flightType === 'international' || flightType === 'both') {
                this.filteredInternational = this.internationalFlights.filter(filterFunction);
            }
            
            if (flightType === 'domestic' || flightType === 'both') {
                this.filteredDomestic = this.domesticFlights.filter(filterFunction);
            }
        }
        
        // Apply current sort after filtering
        this.sortFlights(this.currentSortBy, this.currentSortOrder);
        
        return {
            international: this.filteredInternational,
            domestic: this.filteredDomestic
        };
    }

    /**
     * Sort flights by specified criteria
     * @param {string} sortBy - Sort criteria: 'departure', 'arrival', 'duration', 'destination'
     * @param {string} order - Sort order: 'asc' or 'desc'
     * @returns {Object} Object with sorted international and domestic arrays
     */
    sortFlights(sortBy = 'departure', order = 'asc') {
        this.currentSortBy = sortBy;
        this.currentSortOrder = order;
        
        const sortFunction = (a, b) => {
            let comparison = 0;
            
            switch(sortBy) {
                case 'departure':
                    comparison = new Date(a.departure) - new Date(b.departure);
                    break;
                    
                case 'arrival':
                    const arrivalA = calculateArrivalTime(a.departure, a.duration);
                    const arrivalB = calculateArrivalTime(b.departure, b.duration);
                    comparison = arrivalA - arrivalB;
                    break;
                    
                case 'duration':
                    comparison = a.duration - b.duration;
                    break;
                    
                case 'destination':
                    comparison = a.toCity.localeCompare(b.toCity);
                    break;
                    
                default:
                    comparison = new Date(a.departure) - new Date(b.departure);
            }
            
            return order === 'asc' ? comparison : -comparison;
        };
        
        this.filteredInternational.sort(sortFunction);
        this.filteredDomestic.sort(sortFunction);
        
        return {
            international: this.filteredInternational,
            domestic: this.filteredDomestic
        };
    }

    /**
     * Get a flight by its ID
     * @param {string} id - Flight ID
     * @returns {Object|null} Flight object or null if not found
     */
    getFlightById(id) {
        const allFlights = [...this.internationalFlights, ...this.domesticFlights];
        return allFlights.find(flight => flight.id === id) || null;
    }

    /**
     * Update the display with current filtered and sorted flights
     * @param {string} internationalContainerId - ID of container for international flights
     * @param {string} domesticContainerId - ID of container for domestic flights
     */
    updateDisplay(internationalContainerId = 'international-flights', 
                   domesticContainerId = 'domestic-flights') {
        const intContainer = document.getElementById(internationalContainerId);
        const domContainer = document.getElementById(domesticContainerId);
        
        if (!intContainer || !domContainer) {
            console.error('Flight containers not found');
            return;
        }
        
        try {
            // Render international flights
            if (this.filteredInternational.length === 0) {
                intContainer.innerHTML = renderNoResults(this.currentSearchTerm);
            } else {
                intContainer.innerHTML = this.filteredInternational
                    .map(flight => {
                        try {
                            return renderFlightCard(flight);
                        } catch (error) {
                            console.error('Error rendering flight card:', flight.id, error);
                            // Skip this flight card if rendering fails
                            return '';
                        }
                    })
                    .filter(html => html !== '') // Remove empty strings from failed renders
                    .join('');
            }
            
            // Render domestic flights
            if (this.filteredDomestic.length === 0) {
                domContainer.innerHTML = renderNoResults(this.currentSearchTerm);
            } else {
                domContainer.innerHTML = this.filteredDomestic
                    .map(flight => {
                        try {
                            return renderFlightCard(flight);
                        } catch (error) {
                            console.error('Error rendering flight card:', flight.id, error);
                            // Skip this flight card if rendering fails
                            return '';
                        }
                    })
                    .filter(html => html !== '') // Remove empty strings from failed renders
                    .join('');
            }
            
            // Reinitialize lazy loading for newly added images
            if (typeof reinitializeLazyLoading === 'function') {
                reinitializeLazyLoading();
            }
        } catch (error) {
            console.error('Error updating display:', error);
            // Show error state if rendering completely fails
            intContainer.innerHTML = renderErrorState('Unable to display flights. Please refresh the page.');
            domContainer.innerHTML = '';
        }
    }

    /**
     * Get current filter and sort state
     * @returns {Object} Current state object
     */
    getState() {
        return {
            searchTerm: this.currentSearchTerm,
            sortBy: this.currentSortBy,
            sortOrder: this.currentSortOrder,
            internationalCount: this.filteredInternational.length,
            domesticCount: this.filteredDomestic.length
        };
    }

    /**
     * Reset filters and sorting to defaults
     */
    reset() {
        this.currentSearchTerm = '';
        this.currentSortBy = 'departure';
        this.currentSortOrder = 'asc';
        this.filteredInternational = [...this.internationalFlights];
        this.filteredDomestic = [...this.domesticFlights];
        this.sortFlights('departure', 'asc');
    }
}
