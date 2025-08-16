<?php
require_once 'includes/calculations.php';
require_once 'includes/config.php';

// Handle AJAX requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    http_response_code(405);
    exit('Method not allowed');
}

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
        $converted_data = DepthOfFieldCalculator::convertUnits($dof_data, $unit_system, ConfigurationProvider::getUnitConversions());
        
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

// Unknown action
http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
?>