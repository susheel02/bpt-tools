<?php
require_once 'includes/config.php';
?>
<?php
// Page variables for DOF Calculator
$page_title = "Depth of Field Calculator";
$page_description = "Professional depth of field calculator for photographers. Calculate DOF for any lens, sensor, and distance combination with visual diagrams.";
$css_path = "assets/css/style.css?v=0.0.3";
$base_url = "../";
$show_back_button = true;
$manifest_path = "manifest.json";

// Current page URL for canonical and social sharing
$current_url = "https://www.beyondphototips.com/tools/dof-calculator/";
$canonical_url = $current_url;

// Social media data
$social_data = [
    'title' => $page_title . ' - BeyondPhotoTips.com',
    'description' => $page_description,
    'url' => $current_url,
    'image' => $current_url . 'assets/images/dof-calculator-social.jpg',
    'image_alt' => 'Depth of Field Calculator - Professional photography tool'
];

// Social sharing data
$sharing_data = [
    'url' => $current_url,
    'title' => $page_title,
    'text' => 'Check out this professional depth of field calculator for photographers!'
];

// JSON-LD Schema data
$schema_data = [
    'name' => $page_title,
    'description' => $page_description,
    'url' => $current_url,
    'calculator_type' => 'Depth of Field Calculator',
    'features' => [
        'Multiple sensor format support (Full Frame, APS-C, Micro 4/3, Medium Format)',
        'Visual SVG diagram showing focus range',
        'Hyperfocal distance calculations',
        'Multiple units: meters, feet, centimeters, inches',
        'Save/bookmark functionality',
        'Dark/light mode toggle',
        'Mobile-responsive design'
    ],
    'keywords' => [
        'depth of field',
        'DOF calculator',
        'photography calculator',
        'lens calculator',
        'hyperfocal distance',
        'circle of confusion',
        'photography tools',
        'camera sensor',
        'aperture calculator'
    ],
    'screenshot' => $current_url . 'assets/images/dof-calculator-screenshot.jpg'
];

// Breadcrumb data
$breadcrumb_data = [
    ['name' => 'BeyondPhotoTips.com', 'url' => 'https://www.beyondphototips.com/'],
    ['name' => 'Photography Tools', 'url' => 'https://www.beyondphototips.com/tools/'],
    ['name' => 'Depth of Field Calculator', 'url' => $current_url]
];

// Include header for page display
include '../shared/header.php';
?>

    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode" aria-label="Toggle dark mode" aria-pressed="false">
        🌙
    </button>

    <main class="container">
        <div class="calculator-wrapper">
            <form id="dof-form" class="calculator-form">
                <div class="form-grid">
                    <!-- Sensor Selection -->
                    <div class="form-group">
                        <label for="sensor-preset">Camera Sensor</label>
                        <select id="sensor-preset" name="sensor_preset" required aria-required="true" aria-describedby="sensor-help">
                            <option value="">Select sensor type...</option>
                            <?php foreach ($sensor_presets as $key => $sensor): ?>
                                <option value="<?= $key ?>" 
                                        data-width="<?= $sensor['width'] ?>" 
                                        data-height="<?= $sensor['height'] ?>" 
                                        data-coc="<?= $sensor['coc'] ?>"
                                        <?= $key === 'full_frame' ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sensor['name']) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="custom">Custom sensor dimensions</option>
                        </select>
                        <small id="sensor-help" class="form-help">Select your camera's sensor size for accurate circle of confusion</small>
                    </div>

                    <!-- Custom Sensor Inputs -->
                    <div id="custom-sensor" class="form-group custom-sensor">
                        <div class="custom-inputs">
                            <div>
                                <label for="sensor-width">Width (mm)</label>
                                <input type="number" id="sensor-width" name="sensor_width" step="0.1" min="0.1" aria-describedby="custom-sensor-help">
                            </div>
                            <div>
                                <label for="sensor-height">Height (mm)</label>
                                <input type="number" id="sensor-height" name="sensor_height" step="0.1" min="0.1" aria-describedby="custom-sensor-help">
                            </div>
                        </div>
                        <small id="custom-sensor-help" class="form-help">Enter custom sensor dimensions in millimeters</small>
                    </div>

                    <!-- Focal Length -->
                    <div class="form-group">
                        <label for="focal-length">Focal Length (mm)</label>
                        <input type="number" id="focal-length" name="focal_length" step="1" min="1" value="50" required aria-required="true" aria-describedby="focal-help">
                        <div class="presets" role="group" aria-label="Focal length presets">
                            <?php foreach ($focal_length_presets as $fl): ?>
                                <button type="button" class="preset-btn" data-value="<?= $fl ?>" aria-label="Set focal length to <?= $fl ?>mm" tabindex="0"><?= $fl ?>mm</button>
                            <?php endforeach; ?>
                        </div>
                        <small id="focal-help" class="form-help">Lens focal length in millimeters (1-2000mm)</small>
                    </div>

                    <!-- Aperture -->
                    <div class="form-group">
                        <label for="aperture">Aperture (f-stop)</label>
                        <input type="number" id="aperture" name="aperture" step="0.1" min="0.1" value="8" required aria-required="true" aria-describedby="aperture-help">
                        <div class="presets" role="group" aria-label="Aperture presets">
                            <?php foreach ($aperture_presets as $ap): ?>
                                <button type="button" class="preset-btn" data-value="<?= $ap ?>" aria-label="Set aperture to f/<?= $ap ?>" tabindex="0">f/<?= $ap ?></button>
                            <?php endforeach; ?>
                        </div>
                        <small id="aperture-help" class="form-help">Camera aperture setting (f/0.5 to f/64)</small>
                    </div>

                    <!-- Distance to Subject -->
                    <div class="form-group">
                        <label for="distance">Distance to Subject</label>
                        <div class="distance-input">
                            <input type="number" id="distance" name="distance" step="0.1" min="0.1" value="2" required aria-required="true" aria-describedby="distance-help">
                            <select id="unit-system" name="unit_system" aria-label="Unit system">
                                <option value="metric">meters</option>
                                <option value="imperial">feet</option>
                                <option value="centimeters">centimeters</option>
                                <option value="inches">inches</option>
                            </select>
                        </div>
                        <small id="distance-help" class="form-help">Distance from camera to subject (minimum 0.1)</small>
                    </div>
                </div>

                <button type="submit" class="calculate-btn">Calculate Depth of Field</button>
            </form>

            <!-- Results Section -->
            <div id="results" class="results-section" style="display: none;" role="region" aria-label="Calculation results" aria-live="polite">
                <h2>Depth of Field Results</h2>
                
                <div class="results-grid">
                    <!-- Combined Results with Visual -->
                    <div class="result-card combined-visual-card">
                        <h3>Depth of Field Results</h3>
                        
                        <!-- Visual Diagram -->
                        <div id="dof-visualization" class="dof-visual" role="img" aria-label="Depth of field visualization diagram">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <!-- Three-Column Data Grid -->
                        <div class="focus-data-grid">
                            <div class="focus-range-section">
                                <h4>Focus Range</h4>
                                <div class="data-items">
                                    <div class="result-item">
                                        <label>Near Distance:</label>
                                        <span id="near-distance">-</span>
                                    </div>
                                    <div class="result-item">
                                        <label>Far Distance:</label>
                                        <span id="far-distance">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="dof-section">
                                <h4>Depth of Field</h4>
                                <div class="data-items">
                                    <div class="result-item">
                                        <label>In Front:</label>
                                        <span id="dof-front">-</span>
                                    </div>
                                    <div class="result-item">
                                        <label>Behind:</label>
                                        <span id="dof-behind">-</span>
                                    </div>
                                    <div class="result-item total">
                                        <label>Total DOF:</label>
                                        <span id="total-dof">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hyperfocal-section">
                                <h4>Hyperfocal Distance</h4>
                                <div class="data-items">
                                    <div class="result-item hyperfocal-main">
                                        <span id="hyperfocal-distance" class="hyperfocal-value">-</span>
                                        <small class="hyperfocal-help">Focus at this distance for maximum DOF from half this distance to infinity</small>
                                    </div>
                                    <div class="result-item">
                                        <label>Circle of confusion:</label>
                                        <span id="circle-of-confusion">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save/Bookmark -->
                <div class="save-section">
                    <button id="save-calculation" class="save-btn">Save This Calculation</button>
                    <button id="show-saved" class="save-btn secondary">View Saved Calculations</button>
                </div>
            </div>

            <!-- Saved Calculations -->
            <div id="saved-calculations" class="saved-section" style="display: none;">
                <h2>Saved Calculations</h2>
                <div id="saved-list" class="saved-list">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/polyfills.js"></script>
    <script src="assets/js/app.js"></script>

<?php include '../shared/footer.php'; ?>