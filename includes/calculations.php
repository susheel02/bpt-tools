<?php
require_once 'config.php';

class DepthOfFieldCalculator {
    
    /**
     * Calculate depth of field
     * @param float $focal_length Focal length in mm
     * @param float $aperture F-stop (f/2.8 = 2.8)
     * @param float $distance Distance to subject in meters
     * @param float $coc Circle of confusion in mm
     * @return array DOF calculations
     */
    public static function calculateDOF($focal_length, $aperture, $distance, $coc) {
        // Convert distance to mm for calculations
        $distance_mm = $distance * 1000;
        
        // Calculate hyperfocal distance in mm
        $hyperfocal = ($focal_length * $focal_length) / ($aperture * $coc) + $focal_length;
        
        // Calculate near and far focus distances in mm
        $near_distance = ($hyperfocal * $distance_mm) / ($hyperfocal + $distance_mm - $focal_length);
        $far_distance = ($hyperfocal * $distance_mm) / ($hyperfocal - $distance_mm + $focal_length);
        
        // Handle case where far distance goes to infinity
        if ($distance_mm >= ($hyperfocal - $focal_length)) {
            $far_distance = INF;
        }
        
        // Convert back to meters
        $near_distance_m = $near_distance / 1000;
        $far_distance_m = is_infinite($far_distance) ? INF : $far_distance / 1000;
        $hyperfocal_m = $hyperfocal / 1000;
        
        // Calculate DOF in front and behind subject
        $dof_front = $distance - $near_distance_m;
        $dof_behind = is_infinite($far_distance_m) ? INF : $far_distance_m - $distance;
        $total_dof = is_infinite($dof_behind) ? INF : $dof_front + $dof_behind;
        
        return [
            'near_distance' => $near_distance_m,
            'far_distance' => $far_distance_m,
            'hyperfocal_distance' => $hyperfocal_m,
            'dof_front' => $dof_front,
            'dof_behind' => $dof_behind,
            'total_dof' => $total_dof,
            'subject_distance' => $distance
        ];
    }
    
    /**
     * Calculate hyperfocal distance
     * @param float $focal_length Focal length in mm
     * @param float $aperture F-stop
     * @param float $coc Circle of confusion in mm
     * @return float Hyperfocal distance in meters
     */
    public static function calculateHyperfocal($focal_length, $aperture, $coc) {
        $hyperfocal_mm = ($focal_length * $focal_length) / ($aperture * $coc) + $focal_length;
        return $hyperfocal_mm / 1000;
    }
    
    /**
     * Convert distance values to specified units
     * @param array $dof_data DOF calculation results
     * @param string $unit_system 'metric' or 'imperial'
     * @return array Converted DOF data
     */
    public static function convertUnits($dof_data, $unit_system = 'metric') {
        global $unit_conversions;
        
        if (!isset($unit_conversions[$unit_system])) {
            return $dof_data;
        }
        
        $factor = $unit_conversions[$unit_system]['distance_factor'];
        $unit = $unit_conversions[$unit_system]['display_unit'];
        
        $converted = [];
        foreach ($dof_data as $key => $value) {
            if (is_infinite($value)) {
                $converted[$key] = 'infinity';
            } else {
                $converted[$key] = $value * $factor;
            }
        }
        
        $converted['unit'] = $unit;
        return $converted;
    }
    
    /**
     * Prepare data for JSON encoding by handling infinity values
     * @param array $data Data array that may contain infinity values
     * @return array JSON-safe data array
     */
    public static function prepareForJSON($data) {
        $prepared = [];
        foreach ($data as $key => $value) {
            if (is_infinite($value)) {
                $prepared[$key] = 'infinity';
            } elseif (is_nan($value)) {
                $prepared[$key] = null;
            } else {
                $prepared[$key] = $value;
            }
        }
        return $prepared;
    }
    
    /**
     * Format distance for display
     * @param float $distance Distance value
     * @param string $unit Unit suffix
     * @param int $decimals Number of decimal places
     * @return string Formatted distance
     */
    public static function formatDistance($distance, $unit = 'm', $decimals = 2) {
        if (is_infinite($distance)) {
            return '∞';
        }
        
        return number_format($distance, $decimals) . $unit;
    }
    
    /**
     * Validate input parameters
     * @param array $params Input parameters
     * @return array Validation results
     */
    public static function validateInputs($params) {
        $errors = [];
        
        if (!isset($params['focal_length']) || !is_numeric($params['focal_length']) || $params['focal_length'] <= 0) {
            $errors[] = 'Focal length must be a positive number';
        } elseif ($params['focal_length'] < 1 || $params['focal_length'] > 2000) {
            $errors[] = 'Focal length must be between 1mm and 2000mm';
        }
        
        if (!isset($params['aperture']) || !is_numeric($params['aperture']) || $params['aperture'] <= 0) {
            $errors[] = 'Aperture must be a positive number';
        } elseif ($params['aperture'] < 0.5 || $params['aperture'] > 64) {
            $errors[] = 'Aperture must be between f/0.5 and f/64';
        }
        
        if (!isset($params['distance']) || !is_numeric($params['distance']) || $params['distance'] <= 0) {
            $errors[] = 'Distance must be a positive number';
        } elseif ($params['distance'] < 0.01 || $params['distance'] > 10000) {
            $errors[] = 'Distance must be between 0.01m and 10000m';
        }
        
        if (!isset($params['coc']) || !is_numeric($params['coc']) || $params['coc'] <= 0) {
            $errors[] = 'Circle of confusion must be a positive number';
        } elseif ($params['coc'] < 0.001 || $params['coc'] > 1) {
            $errors[] = 'Circle of confusion must be between 0.001mm and 1mm';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
?>