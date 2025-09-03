<?php
// Equipment Investment Calculator Configuration

// Supported currencies with symbols
define('CURRENCIES', [
    'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
    'EUR' => ['symbol' => '€', 'name' => 'Euro'],
    'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
    'CAD' => ['symbol' => 'C$', 'name' => 'Canadian Dollar'],
    'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar'],
    'JPY' => ['symbol' => '¥', 'name' => 'Japanese Yen'],
    'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
]);

// Usage frequency options (times per month)
define('USAGE_FREQUENCIES', [
    1 => '1 time per month (12 times/year)',
    2 => '2 times per month (24 times/year)', 
    4 => '4 times per month (48 times/year)',
    8 => '8 times per month (96 times/year)',
    12 => '12 times per month (144 times/year)',
    20 => '20 times per month (240 times/year)',
    30 => '30 times per month (360 times/year)',
]);

// Equipment constants
define('DEPRECIATION_YEARS', 5); // US standard for photography equipment
define('ANNUAL_DEPRECIATION_RATE', 1 / DEPRECIATION_YEARS); // 20% per year
define('MAINTENANCE_RATE', 0.20); // 20% of purchase price over lifetime
define('BREAK_EVEN_THRESHOLD_MONTHS', 12); // 1 year benchmark

?>