<?php
/**
 * Flight Card Rendering Function
 * 
 * Generates HTML for a flight card component.
 * Handles DateTime calculations and formatting.
 * 
 * @param array $flight Flight data array containing all flight information
 * @return string Formatted HTML string for the flight card
 */
function renderFlightCard($flight) {
    // Calculate departure and arrival times with timezone handling
    $dep = new DateTime($flight["departure"], new DateTimeZone($flight["originTZ"]));
    $arr = clone $dep;
    $arr->add(new DateInterval("PT{$flight['duration']}M"));
    $arr->setTimezone(new DateTimeZone($flight["destTZ"]));
    
    // Format times with timezone abbreviations
    $depTime = formatTimeWithTimezone($dep, $flight["originTZ"]);
    $arrTime = formatTimeWithTimezone($arr, $flight["destTZ"]);
    
    // Format duration
    $durationFormatted = formatDuration($flight['duration']);
    
    // Generate status indicator HTML
    $statusHTML = renderStatusIndicator($flight['status'], $flight['delayMinutes']);
    
    // Generate timezone difference indicator (if applicable)
    $tzDiffIndicator = renderTimezoneDifferenceIndicatorPHP($flight["originTZ"], $flight["destTZ"]);
    
    // Generate next day indicator (if applicable)
    $nextDayIndicator = renderNextDayIndicatorPHP($dep, $arr);
    
    // Build and return the flight card HTML
    $html = '<article class="flight-card" role="listitem" aria-label="Flight from ' . htmlspecialchars($flight['fromCity']) . ' to ' . htmlspecialchars($flight['toCity']) . '">';
    $html .= '<img data-src="' . htmlspecialchars($flight['img']) . '" alt="' . htmlspecialchars($flight['toCity']) . ' destination" class="lazy-image" src="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 640 400\'%3E%3Crect fill=\'%23e0e0e0\' width=\'640\' height=\'400\'/%3E%3C/svg%3E">';
    $html .= '<div class="card-section">';
    $html .= '<h2>' . htmlspecialchars($flight['from']) . ' → ' . htmlspecialchars($flight['to']) . '</h2>';
    $html .= $statusHTML;
    $html .= '<p>';
    $html .= 'From ' . htmlspecialchars($flight['fromCity']) . ' to ' . htmlspecialchars($flight['toCity']) . '<br>';
    $html .= '<strong>Flight No. ' . htmlspecialchars($flight['flightNo']) . '</strong><br>';
    $html .= htmlspecialchars($flight['airline']) . '<br><br>';
    $html .= '<strong>Departure:</strong> ' . $depTime . '<br>';
    $html .= '<strong>Arrival:</strong> ' . $arrTime . ' ' . $nextDayIndicator . ' ' . $tzDiffIndicator . '<br>';
    $html .= '<strong>Duration:</strong> ' . $durationFormatted;
    $html .= '</p>';
    $html .= '</div>';
    $html .= '</article>';
    
    return $html;
}

/**
 * Format time with timezone abbreviation
 * 
 * @param DateTime $datetime DateTime object
 * @param string $timezone Timezone string
 * @return string Formatted time with timezone (e.g., "10:30 AM PHT")
 */
function formatTimeWithTimezone($datetime, $timezone) {
    $time = $datetime->format('g:i A');
    $tzAbbr = getTimezoneAbbreviationPHP($timezone);
    return $time . ' ' . $tzAbbr;
}

/**
 * Get timezone abbreviation from timezone string
 * 
 * @param string $timezone Timezone string
 * @return string Timezone abbreviation
 */
function getTimezoneAbbreviationPHP($timezone) {
    $timezoneMap = [
        'Asia/Manila' => 'PHT',
        'Asia/Tokyo' => 'JST',
        'Asia/Bangkok' => 'ICT',
        'Europe/Amsterdam' => 'CET',
        'Europe/Zurich' => 'CET',
        'Europe/Rome' => 'CET'
    ];
    
    if (isset($timezoneMap[$timezone])) {
        return $timezoneMap[$timezone];
    }
    
    // Fallback: extract from timezone string
    $parts = explode('/', $timezone);
    return strtoupper(substr(end($parts), 0, 3));
}

/**
 * Format duration in minutes to hours and minutes
 * 
 * @param int $minutes Duration in minutes
 * @return string Formatted duration (e.g., "4h 30m")
 */
function formatDuration($minutes) {
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($hours === 0) {
        return $mins . 'm';
    }
    
    if ($mins === 0) {
        return $hours . 'h';
    }
    
    return $hours . 'h ' . $mins . 'm';
}

/**
 * Generate timezone difference indicator HTML
 * 
 * @param string $originTZ Origin timezone
 * @param string $destTZ Destination timezone
 * @return string HTML string for timezone difference indicator
 */
function renderTimezoneDifferenceIndicatorPHP($originTZ, $destTZ) {
    if ($originTZ === $destTZ) {
        return ''; // No indicator needed for same timezone
    }
    
    // Timezone offset mapping
    $timezoneOffsets = [
        'Asia/Manila' => 8,
        'Asia/Tokyo' => 9,
        'Asia/Bangkok' => 7,
        'Europe/Amsterdam' => 1,
        'Europe/Zurich' => 1,
        'Europe/Rome' => 1
    ];
    
    $originOffset = isset($timezoneOffsets[$originTZ]) ? $timezoneOffsets[$originTZ] : 0;
    $destOffset = isset($timezoneOffsets[$destTZ]) ? $timezoneOffsets[$destTZ] : 0;
    $tzDiff = $destOffset - $originOffset;
    
    $sign = $tzDiff > 0 ? '+' : '';
    $diffText = $sign . $tzDiff . 'h';
    
    return '<span class="timezone-diff" title="Timezone difference">' . $diffText . '</span>';
}

/**
 * Generate next day arrival indicator HTML
 * 
 * @param DateTime $dep Departure DateTime
 * @param DateTime $arr Arrival DateTime
 * @return string HTML string for next day indicator
 */
function renderNextDayIndicatorPHP($dep, $arr) {
    // Check if arrival is on a different date
    if ($dep->format('Y-m-d') === $arr->format('Y-m-d')) {
        return '';
    }
    
    $formattedDate = $arr->format('M j');
    return '<span class="next-day-indicator" title="Arrives next day">(' . $formattedDate . ')</span>';
}

/**
 * Status Indicator Rendering Function
 * 
 * Generates HTML for flight status indicator with appropriate styling.
 * 
 * @param string $status Flight status (on-time, delayed, cancelled, boarding)
 * @param int|null $delayMinutes Delay duration in minutes (null if not delayed)
 * @return string Formatted HTML string for the status indicator
 */
function renderStatusIndicator($status, $delayMinutes = null) {
    $statusClass = 'status-' . $status;
    $statusText = ucfirst(str_replace('-', ' ', $status));
    
    // Determine icon based on status
    $icon = '';
    switch($status) {
        case 'on-time':
            $icon = '✓';
            break;
        case 'delayed':
            $icon = '🕐';
            break;
        case 'cancelled':
            $icon = '✕';
            break;
        case 'boarding':
            $icon = '✈';
            break;
    }
    
    // Add delay information if applicable
    if ($status === 'delayed' && $delayMinutes !== null) {
        $statusText .= ' (' . $delayMinutes . ' min)';
    }
    
    $html = '<div class="status-indicator ' . $statusClass . '" role="status" aria-live="polite">';
    $html .= '<span class="status-icon" aria-hidden="true">' . $icon . '</span>';
    $html .= '<span class="status-text">' . htmlspecialchars($statusText) . '</span>';
    $html .= '</div>';
    
    return $html;
}
