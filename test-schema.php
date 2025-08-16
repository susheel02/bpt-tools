<?php
/**
 * Test Schema Generation
 * This file tests the schema markup generation to ensure proper JSON-LD output
 */

require_once 'includes/schema-functions.php';

echo "<h1>Schema Markup Test</h1>";

// Test Calculator Schema
echo "<h2>DOF Calculator Schema</h2>";
$test_schema = [
    'name' => 'Depth of Field Calculator',
    'description' => 'Professional depth of field calculator for photographers',
    'url' => 'https://www.beyondphototips.com/tools/dof-calculator/',
    'calculator_type' => 'Depth of Field Calculator',
    'features' => [
        'Multiple sensor format support',
        'Visual SVG diagram',
        'Hyperfocal distance calculations'
    ],
    'keywords' => ['depth of field', 'DOF calculator', 'photography']
];

echo htmlspecialchars(SchemaGenerator::generateCalculatorSchema($test_schema));

// Test Breadcrumb Schema
echo "<h2>Breadcrumb Schema</h2>";
$test_breadcrumbs = [
    ['name' => 'BeyondPhotoTips.com', 'url' => 'https://www.beyondphototips.com/'],
    ['name' => 'Photography Tools', 'url' => 'https://www.beyondphototips.com/tools/'],
    ['name' => 'DOF Calculator', 'url' => 'https://www.beyondphototips.com/tools/dof-calculator/']
];

echo htmlspecialchars(SchemaGenerator::generateBreadcrumbSchema($test_breadcrumbs));

// Test Social Media Meta Tags
echo "<h2>Social Media Meta Tags</h2>";
$test_social = [
    'title' => 'Depth of Field Calculator - BeyondPhotoTips.com',
    'description' => 'Professional depth of field calculator for photographers',
    'url' => 'https://www.beyondphototips.com/tools/dof-calculator/',
    'image' => 'https://www.beyondphototips.com/tools/dof-calculator/assets/images/dof-calculator-social.jpg',
    'image_alt' => 'Depth of Field Calculator'
];

echo htmlspecialchars(SocialMediaGenerator::generateSocialMetaTags($test_social));

// Test Social Sharing Buttons
echo "<h2>Social Sharing Buttons</h2>";
$test_sharing = [
    'url' => 'https://www.beyondphototips.com/tools/dof-calculator/',
    'title' => 'Depth of Field Calculator',
    'text' => 'Check out this professional depth of field calculator!'
];

echo htmlspecialchars(SocialMediaGenerator::generateSharingButtons($test_sharing));

echo "<h2>Validation Notes</h2>";
echo "<ul>";
echo "<li>Validate JSON-LD at: <a href='https://search.google.com/test/rich-results' target='_blank'>Google Rich Results Test</a></li>";
echo "<li>Test social media previews at: <a href='https://developers.facebook.com/tools/debug/' target='_blank'>Facebook Sharing Debugger</a></li>";
echo "<li>Validate Open Graph at: <a href='https://opengraphcheck.com/' target='_blank'>Open Graph Check</a></li>";
echo "<li>Twitter Card Validator: <a href='https://cards-dev.twitter.com/validator' target='_blank'>Twitter Card Validator</a></li>";
echo "</ul>";
?>