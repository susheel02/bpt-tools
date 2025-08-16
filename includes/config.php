<?php
// Depth of Field Calculator Configuration

// Sensor presets with width, height in mm, and circle of confusion
$sensor_presets = [
    'full_frame' => [
        'name' => 'Full Frame (35mm)',
        'width' => 36,
        'height' => 24,
        'coc' => 0.029 // Circle of confusion in mm
    ],
    'aps_c_canon' => [
        'name' => 'APS-C Canon',
        'width' => 22.3,
        'height' => 14.9,
        'coc' => 0.019
    ],
    'aps_c_nikon' => [
        'name' => 'APS-C Nikon/Sony',
        'width' => 23.5,
        'height' => 15.6,
        'coc' => 0.020
    ],
    'micro_43' => [
        'name' => 'Micro Four Thirds',
        'width' => 17.3,
        'height' => 13,
        'coc' => 0.015
    ],
    'fuji_gfx' => [
        'name' => 'Fujifilm GFX (Medium Format)',
        'width' => 43.8,
        'height' => 32.9,
        'coc' => 0.037
    ],
    'hasselblad_x' => [
        'name' => 'Hasselblad X System',
        'width' => 43.8,
        'height' => 32.9,
        'coc' => 0.037
    ],
    'hasselblad_h' => [
        'name' => 'Hasselblad H System',
        'width' => 53.4,
        'height' => 40,
        'coc' => 0.045
    ],
    'large_format_4x5' => [
        'name' => '4x5 Large Format',
        'width' => 102,
        'height' => 127,
        'coc' => 0.146
    ]
];

// Unit conversion factors
$unit_conversions = [
    'metric' => [
        'name' => 'Metric (meters)',
        'distance_factor' => 1, // Base unit
        'display_unit' => 'm'
    ],
    'imperial' => [
        'name' => 'Imperial (feet)',
        'distance_factor' => 3.28084, // meters to feet
        'display_unit' => 'ft'
    ],
    'centimeters' => [
        'name' => 'Centimeters',
        'distance_factor' => 100, // meters to centimeters
        'display_unit' => 'cm'
    ],
    'inches' => [
        'name' => 'Inches',
        'distance_factor' => 39.3701, // meters to inches
        'display_unit' => 'in'
    ]
];

// Common aperture values
$aperture_presets = [
    1.4, 1.8, 2, 2.8, 4, 5.6, 8, 11, 16, 22, 32
];

// Common focal lengths
$focal_length_presets = [
    14, 16, 20, 24, 28, 35, 50, 85, 100, 135, 200, 300, 400, 500, 600
];
?>