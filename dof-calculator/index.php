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
    
    if ($_POST['action'] === 'calculate') {
        try {
            $validation = DepthOfFieldCalculator::validateInputs($_POST);
            
            if (!$validation['valid']) {
                echo json_encode(['error' => implode(', ', $validation['errors'])]);
                exit;
            }
            
            $focal_length = floatval($_POST['focal_length']);
            $aperture = floatval($_POST['aperture']);
            $distance = floatval($_POST['distance']);
            $coc = floatval($_POST['coc']);
            $unit_system = $_POST['unit_system'] ?? 'metric';
            
            $dof_data = DepthOfFieldCalculator::calculateDOF($focal_length, $aperture, $distance, $coc);
            $converted_data = DepthOfFieldCalculator::convertUnits($dof_data, $unit_system);
            
            // Add COC to the response (always in mm)
            $converted_data['coc'] = $coc;
            
            echo json_encode(['success' => true, 'data' => $converted_data]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Calculation error: ' . $e->getMessage()]);
            exit;
        } catch (Error $e) {
            echo json_encode(['error' => 'System error: ' . $e->getMessage()]);
            exit;
        }
    }
}
?>
<?php
// For calculator sub-page, we need custom variables
$page_title = "Depth of Field Calculator";
$page_description = "Professional depth of field calculator for photographers. Calculate DOF for any lens, sensor, and distance combination.";
$css_path = "assets/css/style.css";
$base_url = "../";

// Since we need to output before including header for AJAX, we'll handle header manually
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    include '../shared/header.php';
} else {
    // For AJAX requests, we already returned above
}
?>

<?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode">
        🌙
    </button>

    <main class="container">
        <div class="calculator-wrapper">
            <form id="dof-form" class="calculator-form">
                <div class="form-grid">
                    <!-- Sensor Selection -->
                    <div class="form-group">
                        <label for="sensor-preset">Camera Sensor</label>
                        <select id="sensor-preset" name="sensor_preset" required>
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
                    </div>

                    <!-- Custom Sensor Inputs -->
                    <div id="custom-sensor" class="form-group custom-sensor">
                        <div class="custom-inputs">
                            <div>
                                <label for="sensor-width">Width (mm)</label>
                                <input type="number" id="sensor-width" name="sensor_width" step="0.1" min="0.1">
                            </div>
                            <div>
                                <label for="sensor-height">Height (mm)</label>
                                <input type="number" id="sensor-height" name="sensor_height" step="0.1" min="0.1">
                            </div>
                        </div>
                    </div>

                    <!-- Focal Length -->
                    <div class="form-group">
                        <label for="focal-length">Focal Length (mm)</label>
                        <input type="number" id="focal-length" name="focal_length" step="1" min="1" value="50" required>
                        <div class="presets">
                            <?php foreach ($focal_length_presets as $fl): ?>
                                <button type="button" class="preset-btn" data-value="<?= $fl ?>"><?= $fl ?>mm</button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Aperture -->
                    <div class="form-group">
                        <label for="aperture">Aperture (f-stop)</label>
                        <input type="number" id="aperture" name="aperture" step="0.1" min="0.1" value="8" required>
                        <div class="presets">
                            <?php foreach ($aperture_presets as $ap): ?>
                                <button type="button" class="preset-btn" data-value="<?= $ap ?>">f/<?= $ap ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Distance to Subject -->
                    <div class="form-group">
                        <label for="distance">Distance to Subject</label>
                        <div class="distance-input">
                            <input type="number" id="distance" name="distance" step="0.1" min="0.1" value="2" required>
                            <select id="unit-system" name="unit_system">
                                <option value="metric">meters</option>
                                <option value="imperial">feet</option>
                                <option value="centimeters">centimeters</option>
                                <option value="inches">inches</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="calculate-btn">Calculate Depth of Field</button>
            </form>

            <!-- Results Section -->
            <div id="results" class="results-section" style="display: none;">
                <h2>Depth of Field Results</h2>
                
                <div class="results-grid">
                    <!-- Combined Results with Visual -->
                    <div class="result-card combined-visual-card">
                        <h3>Depth of Field Results</h3>
                        
                        <!-- Visual Diagram -->
                        <div id="dof-visualization" class="dof-visual">
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

    <script src="assets/js/app.js"></script>

<?php include '../shared/footer.php'; ?>
<?php endif; ?>