<?php
require_once 'includes/calculations.php';
require_once 'includes/config.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Clear any output buffers to prevent HTML from interfering with JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'calculate_max_print') {
        try {
            $validation = PrintSizeCalculator::validateInputs($_POST);
            
            if (!$validation['valid']) {
                echo json_encode(['error' => implode(', ', $validation['errors'])]);
                exit;
            }
            
            $width_pixels = intval($_POST['width_pixels']);
            $height_pixels = intval($_POST['height_pixels']);
            $print_medium = $_POST['print_medium'] ?? 'photo_paper';
            
            $recommendations = PrintSizeCalculator::getAllQualityRecommendations($width_pixels, $height_pixels, $print_medium);
            
            echo json_encode(['success' => true, 'data' => $recommendations]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Calculation error: ' . $e->getMessage()]);
            exit;
        }
    }
    
    if ($_POST['action'] === 'calculate_required_resolution') {
        try {
            $validation = PrintSizeCalculator::validateInputs($_POST);
            
            if (!$validation['valid']) {
                echo json_encode(['error' => implode(', ', $validation['errors'])]);
                exit;
            }
            
            $print_width = floatval($_POST['print_width']);
            $print_height = floatval($_POST['print_height']);
            $quality_tier = $_POST['quality_tier'] ?? 'standard';
            $print_medium = $_POST['print_medium'] ?? 'photo_paper';
            
            $required_resolution = PrintSizeCalculator::calculateRequiredResolution($print_width, $print_height, $quality_tier, $print_medium);
            $camera_recommendations = PrintSizeCalculator::getCameraRecommendations($required_resolution['required_megapixels']);
            
            $response_data = [
                'required_resolution' => $required_resolution,
                'camera_recommendations' => $camera_recommendations
            ];
            
            echo json_encode(['success' => true, 'data' => $response_data]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Calculation error: ' . $e->getMessage()]);
            exit;
        }
    }
    
    if ($_POST['action'] === 'assess_print_quality') {
        try {
            $validation = PrintSizeCalculator::validateInputs($_POST);
            
            if (!$validation['valid']) {
                echo json_encode(['error' => implode(', ', $validation['errors'])]);
                exit;
            }
            
            $image_width = intval($_POST['image_width']);
            $image_height = intval($_POST['image_height']);
            $print_width = floatval($_POST['print_width']);
            $print_height = floatval($_POST['print_height']);
            $quality_tier = $_POST['quality_tier'] ?? 'standard';
            $print_medium = $_POST['print_medium'] ?? 'photo_paper';
            
            $quality_assessment = PrintSizeCalculator::assessPrintQuality(
                $image_width, $image_height,
                $print_width, $print_height,
                $quality_tier, $print_medium
            );
            
            $viewing_distance = PrintSizeCalculator::calculateViewingDistance(
                $print_width, $print_height, $quality_assessment['actual_ppi']
            );
            
            $response_data = [
                'quality_assessment' => $quality_assessment,
                'viewing_distance' => $viewing_distance
            ];
            
            echo json_encode(['success' => true, 'data' => $response_data]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Calculation error: ' . $e->getMessage()]);
            exit;
        }
    }
}

// Page variables for Print Size Calculator
$page_title = "Print Size Calculator";
$page_description = "Calculate optimal print sizes and resolution requirements for different viewing distances and print qualities. Essential tool for photographers planning prints.";
$css_path = "assets/css/print-calculator.css?v=0.0.6";
$base_url = "../";
$show_back_button = true;

// Current page URL for canonical and social sharing
$current_url = "https://www.beyondphototips.com/tools/print-size-calculator/";
$canonical_url = $current_url;

// Social media data
$social_data = [
    'title' => $page_title . ' - BeyondPhotoTips.com',
    'description' => $page_description,
    'url' => $current_url,
    'image' => $current_url . 'assets/images/print-calculator-social.jpg',
    'image_alt' => 'Print Size Calculator - Professional photography tool'
];

// Social sharing data
$sharing_data = [
    'url' => $current_url,
    'title' => $page_title,
    'text' => 'Check out this professional print size calculator for photographers!'
];

// JSON-LD Schema data
$schema_data = [
    'name' => $page_title,
    'description' => $page_description,
    'url' => $current_url,
    'calculator_type' => 'Print Size Calculator',
    'features' => [
        'Calculate optimal print sizes from image resolution',
        'Determine required megapixels for target print sizes',
        'Print quality recommendations based on viewing distance',
        'Support for different print media (photo paper, canvas, metal, fine art)',
        'PPI/DPI education and guidance',
        'Quality tiers: Gallery/Fine Art, Standard Photo, Casual, Large Format',
        'Real-world scenario recommendations',
        'Camera recommendation system'
    ],
    'keywords' => [
        'print size calculator',
        'print resolution',
        'PPI calculator',
        'DPI calculator',
        'photography printing',
        'print quality',
        'viewing distance',
        'photography tools',
        'megapixel calculator',
        'photo printing guide'
    ],
    'screenshot' => $current_url . 'assets/images/print-calculator-screenshot.jpg'
];

// Breadcrumb data
$breadcrumb_data = [
    ['name' => 'BeyondPhotoTips.com', 'url' => 'https://www.beyondphototips.com/'],
    ['name' => 'Photography Tools', 'url' => 'https://www.beyondphototips.com/tools/'],
    ['name' => 'Print Size Calculator', 'url' => $current_url]
];

include '../shared/header.php';
?>

    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode" aria-label="Toggle dark mode" aria-pressed="false">
        🌙
    </button>

    <main class="container">
        <div class="calculator-wrapper">
            <!-- Mode Toggle -->
            <div class="mode-toggle">
                <button id="mode-max-print" class="active">What can I print?</button>
                <button id="mode-required-resolution">What resolution do I need?</button>
            </div>

            <!-- Mode 1: What can I print? -->
            <div id="calculator-max-print" class="calculator-mode active">
                <div class="input-section">
                    <h3>Image Information</h3>
                    <form id="max-print-form">
                        <div class="input-grid">
                            <div class="input-group">
                                <label for="image-source">Image Source</label>
                                <select id="image-source" name="image_source">
                                    <option value="dimensions">Enter Dimensions</option>
                                    <option value="megapixels">Camera Megapixels</option>
                                </select>
                            </div>

                            <!-- Image Dimensions Input -->
                            <div id="dimensions-input" class="input-group">
                                <label for="width-pixels">Width (pixels)</label>
                                <input type="number" id="width-pixels" name="width_pixels" min="100" max="50000" value="6000" required>
                            </div>

                            <div id="dimensions-input-2" class="input-group">
                                <label for="height-pixels">Height (pixels)</label>
                                <input type="number" id="height-pixels" name="height_pixels" min="100" max="50000" value="4000" required>
                            </div>

                            <!-- Megapixels Input -->
                            <div id="megapixels-input" class="input-group megapixel-input" style="display: none;">
                                <label for="camera-megapixels">Camera Megapixels</label>
                                <input type="number" id="camera-megapixels" name="camera_megapixels" min="1" max="200" step="0.1" value="24">
                                <div class="camera-presets">
                                    <?php foreach ($camera_resolutions as $mp => $info): ?>
                                        <button type="button" class="camera-preset-btn" data-mp="<?= $mp ?>" data-width="<?= $info[0] ?>" data-height="<?= $info[1] ?>"><?= $mp ?>MP</button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="print-medium-max">Print Medium 
                                    <span class="tooltip">ℹ️
                                        <span class="tooltiptext">Different print media have different resolution requirements. Canvas can use lower PPI due to texture, while fine art paper benefits from higher resolution.</span>
                                    </span>
                                </label>
                                <select id="print-medium-max" name="print_medium">
                                    <?php foreach ($print_media as $key => $medium): ?>
                                        <option value="<?= $key ?>"><?= htmlspecialchars($medium['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="calculate-btn">Calculate Maximum Print Sizes</button>
                    </form>
                </div>

                <!-- Results will be inserted here -->
                <div id="max-print-results" class="results-section" style="display: none;"></div>
            </div>

            <!-- Mode 2: What resolution do I need? -->
            <div id="calculator-required-resolution" class="calculator-mode">
                <div class="input-section">
                    <h3>Print Requirements</h3>
                    <form id="required-resolution-form">
                        <div class="input-grid">
                            <div class="input-group">
                                <label for="target-width">Print Width (inches)</label>
                                <input type="number" id="target-width" name="print_width" min="1" max="100" step="0.1" value="16" required>
                            </div>

                            <div class="input-group">
                                <label for="target-height">Print Height (inches)</label>
                                <input type="number" id="target-height" name="print_height" min="1" max="100" step="0.1" value="20" required>
                            </div>

                            <div class="input-group">
                                <label for="quality-tier">Print Quality 
                                    <span class="tooltip">ℹ️
                                        <span class="tooltiptext">Quality tiers are based on viewing distance and intended use. Gallery prints need higher resolution for close inspection, while large format prints are viewed from farther away.</span>
                                    </span>
                                </label>
                                <select id="quality-tier" name="quality_tier" required>
                                    <?php foreach ($print_quality_tiers as $key => $tier): ?>
                                        <option value="<?= $key ?>" <?= $key === 'standard' ? 'selected' : '' ?>><?= htmlspecialchars($tier['name']) ?> (<?= $tier['ppi'] ?> PPI)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="print-medium-req">Print Medium</label>
                                <select id="print-medium-req" name="print_medium">
                                    <?php foreach ($print_media as $key => $medium): ?>
                                        <option value="<?= $key ?>"><?= htmlspecialchars($medium['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Common Print Size Presets -->
                        <div class="input-group">
                            <label>Common Print Sizes</label>
                            <div class="print-size-presets">
                                <?php 
                                $popular_sizes = ['4x6', '5x7', '8x10', '11x14', '16x20', '20x30', 'A4', 'A3', 'A2'];
                                foreach ($popular_sizes as $size_key): 
                                    if (isset($common_print_sizes[$size_key])):
                                        list($width, $height, $description) = $common_print_sizes[$size_key];
                                ?>
                                    <div class="print-size-preset" data-width="<?= $width ?>" data-height="<?= $height ?>">
                                        <div><?= $size_key ?></div>
                                        <small><?= $width ?>"×<?= $height ?>"</small>
                                    </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>

                        <button type="submit" class="calculate-btn">Calculate Required Resolution</button>
                    </form>
                </div>

                <!-- Results will be inserted here -->
                <div id="required-resolution-results" class="results-section" style="display: none;"></div>
            </div>
        </div>
    </main>

    <script src="assets/js/print-calculator.js?v=0.0.3"></script>

<?php include '../shared/footer.php'; ?>