<?php
require_once 'config.php';

class PrintSizeCalculator {
    
    /**
     * Calculate maximum print sizes for given image dimensions
     * @param int $width_pixels Image width in pixels
     * @param int $height_pixels Image height in pixels
     * @param string $quality_tier Quality tier from config
     * @param string $print_medium Print medium from config
     * @return array Print size calculations
     */
    public static function calculateMaxPrintSize($width_pixels, $height_pixels, $quality_tier = 'standard', $print_medium = 'photo_paper') {
        global $print_quality_tiers, $print_media;
        
        $base_ppi = $print_quality_tiers[$quality_tier]['ppi'];
        $media_multiplier = $print_media[$print_medium]['ppi_multiplier'];
        $effective_ppi = $base_ppi * $media_multiplier;
        
        // Calculate maximum print dimensions
        $max_width_inches = $width_pixels / $effective_ppi;
        $max_height_inches = $height_pixels / $effective_ppi;
        
        return [
            'max_width_inches' => $max_width_inches,
            'max_height_inches' => $max_height_inches,
            'effective_ppi' => $effective_ppi,
            'quality_tier' => $quality_tier,
            'print_medium' => $print_medium,
            'image_megapixels' => round(($width_pixels * $height_pixels) / 1000000, 1)
        ];
    }
    
    /**
     * Calculate required resolution for target print size
     * @param float $target_width_inches Desired print width
     * @param float $target_height_inches Desired print height
     * @param string $quality_tier Quality tier from config
     * @param string $print_medium Print medium from config
     * @return array Required resolution calculations
     */
    public static function calculateRequiredResolution($target_width_inches, $target_height_inches, $quality_tier = 'standard', $print_medium = 'photo_paper') {
        global $print_quality_tiers, $print_media;
        
        $base_ppi = $print_quality_tiers[$quality_tier]['ppi'];
        $media_multiplier = $print_media[$print_medium]['ppi_multiplier'];
        $effective_ppi = $base_ppi * $media_multiplier;
        
        // Calculate required pixel dimensions
        $required_width_pixels = round($target_width_inches * $effective_ppi);
        $required_height_pixels = round($target_height_inches * $effective_ppi);
        $required_megapixels = round(($required_width_pixels * $required_height_pixels) / 1000000, 1);
        
        return [
            'required_width_pixels' => $required_width_pixels,
            'required_height_pixels' => $required_height_pixels,
            'required_megapixels' => $required_megapixels,
            'effective_ppi' => $effective_ppi,
            'target_width_inches' => $target_width_inches,
            'target_height_inches' => $target_height_inches,
            'quality_tier' => $quality_tier,
            'print_medium' => $print_medium
        ];
    }
    
    /**
     * Assess print quality for given image and print size
     * @param int $image_width_pixels Source image width
     * @param int $image_height_pixels Source image height
     * @param float $print_width_inches Print width
     * @param float $print_height_inches Print height
     * @param string $quality_tier Desired quality tier
     * @param string $print_medium Print medium
     * @return array Quality assessment
     */
    public static function assessPrintQuality($image_width_pixels, $image_height_pixels, $print_width_inches, $print_height_inches, $quality_tier = 'standard', $print_medium = 'photo_paper') {
        global $print_quality_tiers, $print_media, $quality_thresholds;
        
        // Calculate actual PPI
        $actual_ppi_width = $image_width_pixels / $print_width_inches;
        $actual_ppi_height = $image_height_pixels / $print_height_inches;
        $actual_ppi = min($actual_ppi_width, $actual_ppi_height); // Use the limiting dimension
        
        // Calculate target PPI
        $base_ppi = $print_quality_tiers[$quality_tier]['ppi'];
        $media_multiplier = $print_media[$print_medium]['ppi_multiplier'];
        $target_ppi = $base_ppi * $media_multiplier;
        
        // Calculate quality ratio
        $quality_ratio = $actual_ppi / $target_ppi;
        
        // Determine quality level
        $quality_level = 'poor';
        if ($quality_ratio >= $quality_thresholds['excellent']) {
            $quality_level = 'excellent';
        } elseif ($quality_ratio >= $quality_thresholds['good']) {
            $quality_level = 'good';
        } elseif ($quality_ratio >= $quality_thresholds['acceptable']) {
            $quality_level = 'acceptable';
        }
        
        return [
            'actual_ppi' => round($actual_ppi, 1),
            'target_ppi' => round($target_ppi, 1),
            'quality_ratio' => $quality_ratio,
            'quality_level' => $quality_level,
            'quality_percentage' => round($quality_ratio * 100, 1),
            'upsampling_needed' => $quality_ratio < 1.0,
            'upsampling_factor' => $quality_ratio < 1.0 ? round(1 / $quality_ratio, 2) : 1.0
        ];
    }
    
    /**
     * Get print size recommendations for multiple quality tiers
     * @param int $width_pixels Image width in pixels
     * @param int $height_pixels Image height in pixels
     * @param string $print_medium Print medium
     * @return array Recommendations for all quality tiers
     */
    public static function getAllQualityRecommendations($width_pixels, $height_pixels, $print_medium = 'photo_paper') {
        global $print_quality_tiers, $common_print_sizes;
        
        $recommendations = [];
        
        foreach ($print_quality_tiers as $tier_key => $tier_info) {
            $max_size = self::calculateMaxPrintSize($width_pixels, $height_pixels, $tier_key, $print_medium);
            
            // Find suitable common print sizes
            $suitable_sizes = [];
            foreach ($common_print_sizes as $size_key => $size_info) {
                list($size_width, $size_height, $size_description) = $size_info;
                
                // Check if this size fits within the maximum dimensions
                if ($size_width <= $max_size['max_width_inches'] && $size_height <= $max_size['max_height_inches']) {
                    $quality_assessment = self::assessPrintQuality(
                        $width_pixels, $height_pixels, 
                        $size_width, $size_height, 
                        $tier_key, $print_medium
                    );
                    
                    $suitable_sizes[] = [
                        'size_key' => $size_key,
                        'width' => $size_width,
                        'height' => $size_height,
                        'description' => $size_description,
                        'quality' => $quality_assessment
                    ];
                }
            }
            
            $recommendations[$tier_key] = [
                'tier_info' => $tier_info,
                'max_size' => $max_size,
                'suitable_common_sizes' => $suitable_sizes
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Calculate optimal viewing distance for given print size and quality
     * @param float $print_width_inches Print width
     * @param float $print_height_inches Print height
     * @param float $actual_ppi Actual PPI of the print
     * @return array Viewing distance recommendations
     */
    public static function calculateViewingDistance($print_width_inches, $print_height_inches, $actual_ppi) {
        // Formula: optimal_distance = (300 / actual_ppi) * 24 inches
        $optimal_distance = (300 / $actual_ppi) * 24;
        
        // Also calculate based on print diagonal for large prints
        $print_diagonal = sqrt($print_width_inches * $print_width_inches + $print_height_inches * $print_height_inches);
        $diagonal_based_distance = $print_diagonal * 1.5; // Rule of thumb: 1.5x diagonal
        
        return [
            'optimal_distance_inches' => round($optimal_distance, 1),
            'optimal_distance_feet' => round($optimal_distance / 12, 1),
            'diagonal_based_distance_inches' => round($diagonal_based_distance, 1),
            'diagonal_based_distance_feet' => round($diagonal_based_distance / 12, 1),
            'recommended_distance_inches' => round(max($optimal_distance, $diagonal_based_distance), 1),
            'recommended_distance_feet' => round(max($optimal_distance, $diagonal_based_distance) / 12, 1)
        ];
    }
    
    /**
     * Get camera recommendations for target megapixels
     * @param float $required_megapixels Required megapixels
     * @return array Camera recommendations
     */
    public static function getCameraRecommendations($required_megapixels) {
        global $camera_resolutions;
        
        $recommendations = [];
        
        foreach ($camera_resolutions as $mp => $info) {
            list($width, $height, $description) = $info;
            
            if ($mp >= $required_megapixels) {
                $recommendations[] = [
                    'megapixels' => $mp,
                    'width' => $width,
                    'height' => $height,
                    'description' => $description,
                    'excess_resolution' => round($mp - $required_megapixels, 1)
                ];
            }
        }
        
        return array_slice($recommendations, 0, 5); // Return top 5 recommendations
    }
    
    /**
     * Validate input parameters
     * @param array $params Input parameters
     * @return array Validation results
     */
    public static function validateInputs($params) {
        $errors = [];
        
        // Validate image dimensions
        if (isset($params['width_pixels'])) {
            if (!is_numeric($params['width_pixels']) || $params['width_pixels'] <= 0) {
                $errors[] = 'Image width must be a positive number';
            } elseif ($params['width_pixels'] < 100 || $params['width_pixels'] > 50000) {
                $errors[] = 'Image width must be between 100 and 50,000 pixels';
            }
        }
        
        if (isset($params['height_pixels'])) {
            if (!is_numeric($params['height_pixels']) || $params['height_pixels'] <= 0) {
                $errors[] = 'Image height must be a positive number';
            } elseif ($params['height_pixels'] < 100 || $params['height_pixels'] > 50000) {
                $errors[] = 'Image height must be between 100 and 50,000 pixels';
            }
        }
        
        // Validate print dimensions
        if (isset($params['print_width'])) {
            if (!is_numeric($params['print_width']) || $params['print_width'] <= 0) {
                $errors[] = 'Print width must be a positive number';
            } elseif ($params['print_width'] < 1 || $params['print_width'] > 100) {
                $errors[] = 'Print width must be between 1 and 100 inches';
            }
        }
        
        if (isset($params['print_height'])) {
            if (!is_numeric($params['print_height']) || $params['print_height'] <= 0) {
                $errors[] = 'Print height must be a positive number';
            } elseif ($params['print_height'] < 1 || $params['print_height'] > 100) {
                $errors[] = 'Print height must be between 1 and 100 inches';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
?>