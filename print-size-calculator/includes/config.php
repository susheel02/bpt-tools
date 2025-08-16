<?php
// Print Size Calculator Configuration

// Print quality tiers with PPI requirements and typical scenarios
$print_quality_tiers = [
    'gallery' => [
        'name' => 'Gallery/Fine Art',
        'ppi' => 300,
        'viewing_distance' => 18, // inches
        'description' => 'Museum quality prints for close inspection and critical viewing',
        'scenarios' => ['Art galleries', 'Exhibitions', 'Fine art sales', 'Portfolio prints'],
        'color' => '#2ecc71'
    ],
    'standard' => [
        'name' => 'Standard Photo',
        'ppi' => 240,
        'viewing_distance' => 24, // inches
        'description' => 'High quality prints for normal viewing distances',
        'scenarios' => ['Family photos', 'Client portraits', 'Photo books', 'Home display'],
        'color' => '#3498db'
    ],
    'casual' => [
        'name' => 'Casual/Family',
        'ppi' => 180,
        'viewing_distance' => 36, // inches
        'description' => 'Good quality for everyday viewing and sharing',
        'scenarios' => ['Social sharing', 'Gifts', 'Everyday prints', 'Photo albums'],
        'color' => '#f39c12'
    ],
    'large_format' => [
        'name' => 'Large Format',
        'ppi' => 120,
        'viewing_distance' => 72, // inches
        'description' => 'Wall displays and large format prints viewed from distance',
        'scenarios' => ['Wall art', 'Large displays', 'Trade shows', 'Office decoration'],
        'color' => '#9b59b6'
    ]
];

// Print media adjustments (multiplier for base PPI)
$print_media = [
    'photo_paper' => [
        'name' => 'Photo Paper (Glossy/Matte)',
        'ppi_multiplier' => 1.0,
        'description' => 'Standard photo paper - sharp detail reproduction',
        'optimal_ppi' => [240, 300]
    ],
    'canvas' => [
        'name' => 'Canvas',
        'ppi_multiplier' => 0.7,
        'description' => 'Textured surface masks lower resolution',
        'optimal_ppi' => [150, 200]
    ],
    'metal' => [
        'name' => 'Metal Prints',
        'ppi_multiplier' => 1.1,
        'description' => 'Ultra-sharp medium, benefits from higher resolution',
        'optimal_ppi' => [240, 320]
    ],
    'fine_art' => [
        'name' => 'Fine Art Paper',
        'ppi_multiplier' => 1.2,
        'description' => 'Premium papers for gallery-quality reproduction',
        'optimal_ppi' => [300, 360]
    ],
    'acrylic' => [
        'name' => 'Acrylic/Plexiglass',
        'ppi_multiplier' => 1.0,
        'description' => 'Sharp, vibrant prints behind acrylic',
        'optimal_ppi' => [240, 300]
    ]
];

// Common print sizes (width x height in inches)
$common_print_sizes = [
    // Standard photo sizes
    '4x6' => [4, 6, 'Standard Photo'],
    '5x7' => [5, 7, 'Standard Photo'],
    '8x10' => [8, 10, 'Standard Photo'],
    '8x12' => [8, 12, 'Standard Photo'],
    '11x14' => [11, 14, 'Large Photo'],
    '12x18' => [12, 18, 'Large Photo'],
    '16x20' => [16, 20, 'Wall Display'],
    '16x24' => [16, 24, 'Wall Display'],
    '20x30' => [20, 30, 'Large Display'],
    '24x36' => [24, 36, 'Poster Size'],
    
    // International A-series (approximate inches)
    'A6' => [4.1, 5.8, 'A6 (105×148mm)'],
    'A5' => [5.8, 8.3, 'A5 (148×210mm)'],
    'A4' => [8.3, 11.7, 'A4 (210×297mm)'],
    'A3' => [11.7, 16.5, 'A3 (297×420mm)'],
    'A2' => [16.5, 23.4, 'A2 (420×594mm)'],
    'A1' => [23.4, 33.1, 'A1 (594×841mm)'],
    
    // Square formats
    '8x8' => [8, 8, 'Square Photo'],
    '12x12' => [12, 12, 'Square Display'],
    '16x16' => [16, 16, 'Square Wall Art'],
    '20x20' => [20, 20, 'Large Square'],
    
    // Panoramic
    '10x30' => [10, 30, 'Panoramic'],
    '12x36' => [12, 36, 'Wide Panoramic'],
    '16x48' => [16, 48, 'Ultra Wide']
];

// Common camera resolutions (megapixels and typical dimensions)
$camera_resolutions = [
    12 => [4000, 3000, 'Entry DSLR/Mirrorless'],
    16 => [4928, 3264, 'Mid-range Camera'],
    20 => [5472, 3648, 'Advanced Camera'],
    24 => [6000, 4000, 'Full Frame Standard'],
    30 => [6720, 4480, 'High Resolution'],
    36 => [7360, 4912, 'High-end Full Frame'],
    42 => [7952, 5304, 'Medium Format Entry'],
    50 => [8688, 5792, 'High Resolution'],
    61 => [9504, 6336, 'Ultra High Resolution'],
    100 => [12000, 8000, 'Medium Format Pro']
];

// Upsampling guidelines
$upsampling_guidelines = [
    'conservative' => [
        'max_percentage' => 150,
        'description' => 'Safe upsampling with minimal quality loss',
        'recommended_for' => 'Critical applications, gallery prints'
    ],
    'moderate' => [
        'max_percentage' => 200,
        'description' => 'Good balance of size increase and quality',
        'recommended_for' => 'Most photography applications'
    ],
    'aggressive' => [
        'max_percentage' => 300,
        'description' => 'Maximum upsampling for specific scenarios',
        'recommended_for' => 'Canvas prints, large viewing distances'
    ]
];

// Viewing distance calculations
function calculateOptimalPPI($viewing_distance_inches) {
    // Formula: PPI = 300 × (24 / viewing_distance)
    // 24 inches is the baseline for 300 PPI
    return round(300 * (24 / $viewing_distance_inches));
}

// Quality assessment thresholds
$quality_thresholds = [
    'excellent' => 1.0,   // 100%+ of recommended PPI
    'good' => 0.8,        // 80%+ of recommended PPI
    'acceptable' => 0.6,  // 60%+ of recommended PPI
    'poor' => 0.4         // Below 60% is poor
];
?>