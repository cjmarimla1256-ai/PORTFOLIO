<?php
/**
 * Flight Data Layer
 * 
 * Contains flight data arrays for international and domestic flights.
 * Each flight follows the Flight Data Model with fields:
 * - id: Unique identifier
 * - img: Image path
 * - from: Origin airport code
 * - to: Destination airport code
 * - fromCity: Origin city name
 * - toCity: Destination city name
 * - flightNo: Flight number
 * - airline: Airline name
 * - originTZ: Origin timezone
 * - destTZ: Destination timezone
 * - departure: Departure datetime (ISO 8601)
 * - duration: Duration in minutes
 * - status: Flight status (on-time, delayed, cancelled, boarding)
 * - delayMinutes: Delay duration if applicable (null if not delayed)
 */

/* INTERNATIONAL FLIGHTS DATA */
$flightsInternational = [
    [
        "id" => "INT001",
        "img" => "img/tokyo.jpg",
        "from" => "MNL",
        "to" => "TKO",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Tokyo, Japan",
        "flightNo" => "PR 2831",
        "airline" => "Philippine Airlines (PAL)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Tokyo",
        "departure" => "2026-01-22 10:00:00",
        "duration" => 240,
        "status" => "on-time",
        "delayMinutes" => null
    ],
    [
        "id" => "INT002",
        "img" => "img/hanoi.jpg",
        "from" => "MNL",
        "to" => "HAN",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Hanoi, Vietnam",
        "flightNo" => "PR 1927",
        "airline" => "Cathay Pacific (CATHAY)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Bangkok",
        "departure" => "2026-01-22 11:30:00",
        "duration" => 210,
        "status" => "delayed",
        "delayMinutes" => 45
    ],
    [
        "id" => "INT003",
        "img" => "img/amsterdam.jpg",
        "from" => "MNL",
        "to" => "AMS",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Amsterdam, Netherlands",
        "flightNo" => "PR 2106",
        "airline" => "Etihad Airways (ETH)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Europe/Amsterdam",
        "departure" => "2026-01-22 08:00:00",
        "duration" => 1020,
        "status" => "boarding",
        "delayMinutes" => null
    ],
    [ 
        "id" => "INT004",
        "img" => "img/geneva.jpg",
        "from" => "MNL",
        "to" => "GVA",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Geneva, Switzerland",
        "flightNo" => "PR 1962",
        "airline" => "Emirates (EK)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Europe/Zurich",
        "departure" => "2026-01-22 08:00:00",
        "duration" => 1300,
        "status" => "on-time",
        "delayMinutes" => null
    ],
    [
        "id" => "INT005",
        "img" => "img/milan.jpg",
        "from" => "MNL",
        "to" => "MXP",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Milan, Italy",
        "flightNo" => "PR 1968",
        "airline" => "Qantas Airlines (QF)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Europe/Rome",
        "departure" => "2026-01-22 08:00:00",
        "duration" => 1030,
        "status" => "cancelled",
        "delayMinutes" => null
    ]
];

/* DOMESTIC FLIGHTS DATA */
$flightsDomestic = [
    [
        "id" => "DOM001",
        "img" => "img/siargao.jpg",
        "from" => "MNL",
        "to" => "IAO",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Siargao, Philippines",
        "flightNo" => "DF 2387",
        "airline" => "Cebu Pacific (CEB)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Manila",
        "departure" => "2026-01-22 08:00:00",
        "duration" => 120,
        "status" => "on-time",
        "delayMinutes" => null
    ],
    [
        "id" => "DOM002",
        "img" => "img/cebu.jpg",
        "from" => "MNL",
        "to" => "CEB",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Cebu, Philippines",
        "flightNo" => "DF 2388",
        "airline" => "Cebu Pacific (CEB)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Manila",
        "departure" => "2026-01-22 09:00:00",
        "duration" => 80,
        "status" => "boarding",
        "delayMinutes" => null
    ],
    [
        "id" => "DOM003",
        "img" => "img/palawan.jpg",
        "from" => "MNL",
        "to" => "PLW",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Palawan, Philippines",
        "flightNo" => "DF 2389",
        "airline" => "Jetstar(JST)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Manila",
        "departure" => "2026-01-22 10:00:00",
        "duration" => 90,
        "status" => "delayed",
        "delayMinutes" => 30
    ],
    [
        "id" => "DOM004",
        "img" => "img/bohol.jpg",
        "from" => "MNL",
        "to" => "BOH",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Bohol, Philippines",
        "flightNo" => "DF 2390",
        "airline" => "Philippine Airlines (PAL)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Manila",
        "departure" => "2026-01-22 10:00:00",
        "duration" => 80,
        "status" => "on-time",
        "delayMinutes" => null
    ],
    [
        "id" => "DOM005",
        "img" => "img/boracay.jpg",
        "from" => "MNL",
        "to" => "BOR",
        "fromCity" => "Manila, Philippines",
        "toCity" => "Boracay, Philippines",
        "flightNo" => "DF 2391",
        "airline" => "Philippine Airlines (PAL)",
        "originTZ" => "Asia/Manila",
        "destTZ" => "Asia/Manila",
        "departure" => "2026-01-22 10:00:00",
        "duration" => 85,
        "status" => "on-time",
        "delayMinutes" => null
    ]
];
