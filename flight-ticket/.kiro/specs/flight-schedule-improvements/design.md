# Design Document: Flight Schedule Improvements

## Overview

This design document outlines the technical approach for enhancing the existing flight schedule display system. The system is a PHP-based web application that displays international and domestic flight information using a card-based layout. The improvements address critical bugs, enhance user experience through interactive features, improve visual design, ensure accessibility compliance, and optimize performance.

### Current System Analysis

The existing system consists of:
- `index.html` (incorrectly named, contains PHP code)
- `css/styles.css` (single stylesheet)
- `includes/header.php` (page header with live clock)
- `includes/footer.php` (page footer)
- `img/` directory (flight destination images)

The current implementation has flight data hardcoded in the main file, uses basic CSS grid layout, lacks interactive features, and has no accessibility considerations.

### Design Goals

1. **Fix Critical Bug**: Rename index.html to index.php to enable proper PHP execution
2. **Enhance Interactivity**: Add search, filter, and sort capabilities using JavaScript
3. **Improve User Experience**: Implement responsive design, loading states, and error handling
4. **Ensure Accessibility**: Add ARIA labels, keyboard navigation, and semantic HTML
5. **Optimize Performance**: Implement lazy loading, image optimization, and efficient DOM manipulation
6. **Improve Maintainability**: Separate concerns, extract reusable components, and organize code structure

## Architecture

### High-Level Architecture

The system follows a traditional server-side rendering architecture with client-side enhancements:

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser (Client)                      │
│  ┌────────────────┐  ┌──────────────┐  ┌─────────────────┐ │
│  │  HTML/DOM      │  │  JavaScript  │  │  CSS Styles     │ │
│  │  Structure     │◄─┤  Interactivity│◄─┤  Presentation  │ │
│  └────────────────┘  └──────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              ▲
                              │ HTTP Request/Response
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Web Server (PHP)                        │
│  ┌────────────────┐  ┌──────────────┐  ┌─────────────────┐ │
│  │  index.php     │  │  Data Layer  │  │  Includes       │ │
│  │  (Main Page)   │─►│  (flights)   │  │  (header/footer)│ │
│  └────────────────┘  └──────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Component Architecture

The system is organized into the following components:

1. **Data Layer** (`data/flights.php`)
   - Stores flight data arrays
   - Provides data structure for international and domestic flights
   - Includes flight status information

2. **Presentation Layer** (`index.php`)
   - Renders initial HTML structure
   - Includes header and footer components
   - Outputs flight data as JSON for JavaScript consumption

3. **Business Logic Layer** (`js/flight-manager.js`)
   - Handles search and filter operations
   - Manages sort functionality
   - Controls DOM updates and rendering

4. **UI Components** (`js/ui-components.js`)
   - Flight card rendering
   - Loading state display
   - Error message handling
   - Status indicator rendering

5. **Style Layer**
   - `css/base.css` - Reset and base styles
   - `css/layout.css` - Grid and responsive layout
   - `css/components.css` - Component-specific styles
   - `css/print.css` - Print-specific styles

### Technology Stack

- **Backend**: PHP 7.4+ (server-side rendering, data preparation)
- **Frontend**: Vanilla JavaScript (ES6+) for interactivity
- **Styling**: CSS3 with CSS Grid and Flexbox
- **Accessibility**: ARIA attributes, semantic HTML5
- **Performance**: Intersection Observer API for lazy loading

## Components and Interfaces

### 1. Data Structure

#### Flight Data Model

```php
[
    "id" => string,              // Unique identifier
    "img" => string,             // Image path
    "from" => string,            // Origin airport code
    "to" => string,              // Destination airport code
    "fromCity" => string,        // Origin city name
    "toCity" => string,          // Destination city name
    "flightNo" => string,        // Flight number
    "airline" => string,         // Airline name
    "originTZ" => string,        // Origin timezone
    "destTZ" => string,          // Destination timezone
    "departure" => string,       // Departure datetime (ISO 8601)
    "duration" => int,           // Duration in minutes
    "status" => string,          // "on-time"|"delayed"|"cancelled"|"boarding"
    "delayMinutes" => int|null   // Delay duration if applicable
]
```

### 2. File Structure

```
/
├── index.php                    # Main application file (renamed from index.html)
├── data/
│   └── flights.php             # Flight data arrays
├── includes/
│   ├── header.php              # Page header component
│   ├── footer.php              # Page footer component
│   └── flight-card.php         # Flight card rendering function
├── js/
│   ├── flight-manager.js       # Core flight management logic
│   ├── ui-components.js        # UI rendering functions
│   └── utils.js                # Utility functions
├── css/
│   ├── base.css                # Reset and base styles
│   ├── layout.css              # Grid and responsive layout
│   ├── components.css          # Component styles
│   └── print.css               # Print stylesheet
└── img/                        # Flight destination images
```

### 3. JavaScript Modules

#### FlightManager Class

```javascript
class FlightManager {
    constructor(internationalFlights, domesticFlights)
    filterFlights(searchTerm, flightType)
    sortFlights(flights, sortBy, order)
    getFlightById(id)
    updateDisplay()
}
```

**Responsibilities**:
- Maintain flight data state
- Execute search and filter operations
- Apply sorting algorithms
- Coordinate with UI components for rendering

#### UIComponents Module

```javascript
const UIComponents = {
    renderFlightCard(flight, timezone)
    renderLoadingState()
    renderErrorState(message)
    renderNoResults()
    renderStatusIndicator(status, delayMinutes)
    formatTime(datetime, timezone)
    formatDuration(minutes)
}
```

**Responsibilities**:
- Generate HTML for flight cards
- Display loading and error states
- Format time and duration displays
- Render status indicators

### 4. Search and Filter Implementation

**Search Algorithm**:
- Case-insensitive substring matching
- Search across: destination city, airline name, flight number
- Real-time filtering with debouncing (300ms delay)
- Separate filtering for international and domestic flights

**Implementation**:
```javascript
function filterFlights(flights, searchTerm) {
    if (!searchTerm.trim()) return flights;
    
    const term = searchTerm.toLowerCase();
    return flights.filter(flight => 
        flight.toCity.toLowerCase().includes(term) ||
        flight.airline.toLowerCase().includes(term) ||
        flight.flightNo.toLowerCase().includes(term)
    );
}
```

### 5. Sort Implementation

**Sort Criteria**:
- Departure time (default)
- Arrival time
- Duration
- Destination (alphabetical)

**Sort Orders**:
- Ascending (default)
- Descending

**Implementation**:
```javascript
function sortFlights(flights, sortBy, order = 'asc') {
    const sorted = [...flights].sort((a, b) => {
        let comparison = 0;
        switch(sortBy) {
            case 'departure':
                comparison = new Date(a.departure) - new Date(b.departure);
                break;
            case 'arrival':
                const arrA = calculateArrival(a);
                const arrB = calculateArrival(b);
                comparison = arrA - arrB;
                break;
            case 'duration':
                comparison = a.duration - b.duration;
                break;
            case 'destination':
                comparison = a.toCity.localeCompare(b.toCity);
                break;
        }
        return order === 'asc' ? comparison : -comparison;
    });
    return sorted;
}
```

### 6. Responsive Design Breakpoints

```css
/* Mobile: < 768px */
@media (max-width: 767px) {
    .card-grid {
        grid-template-columns: 1fr;
    }
}

/* Tablet: 768px - 1024px */
@media (min-width: 768px) and (max-width: 1024px) {
    .card-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Desktop: > 1024px */
@media (min-width: 1025px) {
    .card-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    }
}
```

### 7. Accessibility Implementation

**Keyboard Navigation**:
- Tab order: Search input → Sort controls → Flight cards
- Enter/Space: Activate controls
- Escape: Clear search

**ARIA Labels**:
```html
<input 
    type="search" 
    aria-label="Search flights by destination, airline, or flight number"
    role="searchbox"
>

<select 
    aria-label="Sort flights by"
    aria-controls="flight-grid"
>

<div 
    class="flight-card" 
    role="article" 
    aria-label="Flight from Manila to Tokyo"
>
```

**Focus Management**:
- Visible focus indicators (2px solid outline)
- Focus trap in modal dialogs (if added)
- Skip to main content link

**Semantic HTML**:
```html
<main role="main">
    <section aria-labelledby="international-heading">
        <h2 id="international-heading">International Departures</h2>
        <div class="card-grid" role="list">
            <article class="flight-card" role="listitem">
                <!-- Flight card content -->
            </article>
        </div>
    </section>
</main>
```

### 8. Status Indicator Design

**Status Types and Colors**:
- **On-time**: Green (#28a745) with checkmark icon
- **Delayed**: Orange (#fd7e14) with clock icon + delay duration
- **Cancelled**: Red (#dc3545) with X icon
- **Boarding**: Blue (#007bff) with plane icon

**Implementation**:
```html
<div class="status-indicator status-on-time" role="status" aria-live="polite">
    <span class="status-icon" aria-hidden="true">✓</span>
    <span class="status-text">On Time</span>
</div>
```

### 9. Loading and Error States

**Loading State**:
```html
<div class="loading-container" role="status" aria-live="polite">
    <div class="spinner" aria-hidden="true"></div>
    <p>Loading flight information...</p>
</div>
```

**Error State**:
```html
<div class="error-container" role="alert">
    <h3>Unable to Load Flights</h3>
    <p>We're having trouble loading flight information. Please try again.</p>
    <button onclick="retryLoad()">Retry</button>
</div>
```

**No Results State**:
```html
<div class="no-results" role="status">
    <p>No flights found matching your search.</p>
    <button onclick="clearSearch()">Clear Search</button>
</div>
```

### 10. Performance Optimization

**Image Optimization**:
- Compress images to WebP format with JPEG fallback
- Target size: < 100KB per image
- Dimensions: 640x400px (2x for retina displays)

**Lazy Loading**:
```javascript
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.add('loaded');
            imageObserver.unobserve(img);
        }
    });
});
```

**Debouncing Search**:
```javascript
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

const debouncedSearch = debounce(performSearch, 300);
```

**CSS/JS Minification**:
- Minify CSS files in production
- Minify JavaScript files in production
- Combine files to reduce HTTP requests

## Data Models

### Flight Entity

```php
class Flight {
    public string $id;
    public string $img;
    public string $from;
    public string $to;
    public string $fromCity;
    public string $toCity;
    public string $flightNo;
    public string $airline;
    public string $originTZ;
    public string $destTZ;
    public string $departure;  // ISO 8601 format
    public int $duration;      // minutes
    public string $status;     // enum: on-time, delayed, cancelled, boarding
    public ?int $delayMinutes;
    
    public function getArrivalTime(): DateTime
    public function getFormattedDeparture(): string
    public function getFormattedArrival(): string
    public function getFormattedDuration(): string
    public function isDelayed(): bool
    public function isCancelled(): bool
}
```

### FlightCollection Entity

```php
class FlightCollection {
    private array $flights;
    
    public function __construct(array $flights)
    public function filter(callable $predicate): FlightCollection
    public function sort(string $field, string $order = 'asc'): FlightCollection
    public function search(string $term): FlightCollection
    public function toArray(): array
    public function toJSON(): string
}
```

### UI State Model

```javascript
const UIState = {
    searchTerm: '',
    sortBy: 'departure',
    sortOrder: 'asc',
    isLoading: false,
    error: null,
    internationalFlights: [],
    domesticFlights: [],
    filteredInternational: [],
    filteredDomestic: []
};
```

### Time Display Model

```javascript
class TimeDisplay {
    constructor(datetime, timezone) {
        this.datetime = datetime;
        this.timezone = timezone;
    }
    
    format12Hour() {
        // Returns: "10:30 AM"
    }
    
    formatWithDate() {
        // Returns: "Jan 22, 2026 10:30 AM"
    }
    
    formatWithTimezone() {
        // Returns: "10:30 AM PST"
    }
    
    getTimezoneAbbreviation() {
        // Returns: "PST", "JST", etc.
    }
}
```

### Status Model

```javascript
const FlightStatus = {
    ON_TIME: 'on-time',
    DELAYED: 'delayed',
    CANCELLED: 'cancelled',
    BOARDING: 'boarding'
};

class StatusIndicator {
    constructor(status, delayMinutes = null) {
        this.status = status;
        this.delayMinutes = delayMinutes;
    }
    
    getColor() {
        // Returns appropriate color code
    }
    
    getIcon() {
        // Returns appropriate icon
    }
    
    getText() {
        // Returns status text with delay info if applicable
    }
    
    render() {
        // Returns HTML string
    }
}
```

