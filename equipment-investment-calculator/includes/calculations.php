<?php
require_once 'config.php';

class EquipmentInvestmentCalculator {
    
    public static function calculate($purchasePrice, $dailyRentalRate, $usagePerMonth, $insuranceAnnual = 0) {
        // Input validation
        if ($purchasePrice <= 0 || $dailyRentalRate <= 0 || $usagePerMonth <= 0) {
            return ['error' => 'All values must be greater than zero'];
        }
        
        // Calculate annual usage
        $annualUsage = $usagePerMonth * 12;
        
        // Calculate ownership costs
        $annualDepreciation = $purchasePrice * ANNUAL_DEPRECIATION_RATE;
        $lifetimeMaintenanceCost = $purchasePrice * MAINTENANCE_RATE;
        $annualMaintenanceCost = $lifetimeMaintenanceCost / DEPRECIATION_YEARS;
        
        // Total annual ownership cost
        $totalAnnualOwnershipCost = $annualDepreciation + $annualMaintenanceCost + $insuranceAnnual;
        
        // 5-year ownership costs
        $total5YearDepreciation = $purchasePrice; // Full purchase price over 5 years
        $total5YearMaintenance = $lifetimeMaintenanceCost;
        $total5YearInsurance = $insuranceAnnual * DEPRECIATION_YEARS;
        $total5YearOwnershipCost = $purchasePrice + $total5YearMaintenance + $total5YearInsurance;
        
        // Calculate rental costs
        $totalAnnualRentalCost = $dailyRentalRate * $annualUsage;
        $total5YearRentalCost = $totalAnnualRentalCost * DEPRECIATION_YEARS;
        
        // Break-even calculations (when rental costs equal purchase price + maintenance)
        $breakEvenCost = $purchasePrice + $lifetimeMaintenanceCost + $total5YearInsurance;
        $breakEvenUsages = ceil($breakEvenCost / $dailyRentalRate);
        $breakEvenMonths = ceil($breakEvenUsages / $usagePerMonth);
        
        // Calculate cost per usage for ownership (including all costs)
        $costPerUsageOwnership = $totalAnnualOwnershipCost / $annualUsage;
        $costPerUsageRental = $dailyRentalRate;
        
        // Annual savings calculation
        $annualSavings = $totalAnnualRentalCost - $totalAnnualOwnershipCost;
        
        // 5-year savings calculation
        $fiveYearSavings = $total5YearRentalCost - $total5YearOwnershipCost;
        
        // Recommendation logic
        $recommendation = self::getRecommendation($breakEvenMonths, $annualSavings);
        
        // 1-year benchmark
        $paysForItselfInOneYear = $breakEvenMonths <= BREAK_EVEN_THRESHOLD_MONTHS;
        
        return [
            'purchasePrice' => $purchasePrice,
            'dailyRentalRate' => $dailyRentalRate,
            'usagePerMonth' => $usagePerMonth,
            'annualUsage' => $annualUsage,
            'insuranceAnnual' => $insuranceAnnual,
            
            // Ownership costs breakdown
            'annualDepreciation' => $annualDepreciation,
            'annualMaintenanceCost' => $annualMaintenanceCost,
            'totalAnnualOwnershipCost' => $totalAnnualOwnershipCost,
            'lifetimeMaintenanceCost' => $lifetimeMaintenanceCost,
            
            // 5-year ownership costs
            'total5YearDepreciation' => $total5YearDepreciation,
            'total5YearMaintenance' => $total5YearMaintenance,
            'total5YearInsurance' => $total5YearInsurance,
            'total5YearOwnershipCost' => $total5YearOwnershipCost,
            
            // Rental costs
            'totalAnnualRentalCost' => $totalAnnualRentalCost,
            'total5YearRentalCost' => $total5YearRentalCost,
            
            // Break-even analysis
            'breakEvenUsages' => $breakEvenUsages,
            'breakEvenMonths' => $breakEvenMonths,
            'paysForItselfInOneYear' => $paysForItselfInOneYear,
            
            // Cost comparison
            'costPerUsageOwnership' => $costPerUsageOwnership,
            'costPerUsageRental' => $costPerUsageRental,
            'annualSavings' => $annualSavings,
            'monthlySavings' => $annualSavings / 12,
            'fiveYearSavings' => $fiveYearSavings,
            
            // Recommendation
            'recommendation' => $recommendation,
            'recommendationReason' => self::getRecommendationReason($recommendation, $breakEvenMonths, $annualSavings)
        ];
    }
    
    private static function getRecommendation($breakEvenMonths, $annualSavings) {
        // If equipment pays for itself within 1 year, recommend buying
        if ($breakEvenMonths <= BREAK_EVEN_THRESHOLD_MONTHS) {
            return 'BUY';
        }
        
        // If renting saves money annually, recommend renting
        if ($annualSavings < 0) {
            return 'RENT';
        }
        
        // Otherwise, recommend buying (long-term savings even if >1 year payback)
        return 'BUY';
    }
    
    private static function getRecommendationReason($recommendation, $breakEvenMonths, $annualSavings) {
        if ($recommendation === 'BUY') {
            if ($breakEvenMonths <= BREAK_EVEN_THRESHOLD_MONTHS) {
                return "This equipment will pay for itself within {$breakEvenMonths} months of usage.";
            } else {
                $savings = abs($annualSavings);
                return "While payback takes {$breakEvenMonths} months, you'll save money long-term with annual savings of " . number_format($savings, 2) . ".";
            }
        } else {
            $savings = abs($annualSavings);
            return "Renting will save you " . number_format($savings, 2) . " annually based on your usage pattern.";
        }
    }
    
    public static function formatCurrency($amount, $currency = 'USD') {
        $symbol = CURRENCIES[$currency]['symbol'];
        return $symbol . number_format(abs($amount), 2);
    }
    
    public static function getCurrencySymbol($currency) {
        return CURRENCIES[$currency]['symbol'] ?? '$';
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'calculate') {
    header('Content-Type: application/json');
    
    $purchasePrice = floatval($_POST['purchase_price'] ?? 0);
    $dailyRentalRate = floatval($_POST['daily_rental_rate'] ?? 0);
    $usagePerMonth = intval($_POST['usage_per_month'] ?? 0);
    $insuranceAnnual = floatval($_POST['insurance_annual'] ?? 0);
    $currency = $_POST['currency'] ?? 'USD';
    
    $results = EquipmentInvestmentCalculator::calculate($purchasePrice, $dailyRentalRate, $usagePerMonth, $insuranceAnnual);
    
    if (isset($results['error'])) {
        http_response_code(400);
        echo json_encode($results);
        exit;
    }
    
    // Add currency formatting
    $results['currency'] = $currency;
    $results['formatted'] = [
        'purchasePrice' => EquipmentInvestmentCalculator::formatCurrency($results['purchasePrice'], $currency),
        'dailyRentalRate' => EquipmentInvestmentCalculator::formatCurrency($results['dailyRentalRate'], $currency),
        'totalAnnualOwnershipCost' => EquipmentInvestmentCalculator::formatCurrency($results['totalAnnualOwnershipCost'], $currency),
        'totalAnnualRentalCost' => EquipmentInvestmentCalculator::formatCurrency($results['totalAnnualRentalCost'], $currency),
        'total5YearOwnershipCost' => EquipmentInvestmentCalculator::formatCurrency($results['total5YearOwnershipCost'], $currency),
        'total5YearRentalCost' => EquipmentInvestmentCalculator::formatCurrency($results['total5YearRentalCost'], $currency),
        'annualSavings' => EquipmentInvestmentCalculator::formatCurrency($results['annualSavings'], $currency),
        'monthlySavings' => EquipmentInvestmentCalculator::formatCurrency($results['monthlySavings'], $currency),
        'fiveYearSavings' => EquipmentInvestmentCalculator::formatCurrency($results['fiveYearSavings'], $currency),
        'costPerUsageOwnership' => EquipmentInvestmentCalculator::formatCurrency($results['costPerUsageOwnership'], $currency),
        'costPerUsageRental' => EquipmentInvestmentCalculator::formatCurrency($results['costPerUsageRental'], $currency),
        'insuranceAnnual' => EquipmentInvestmentCalculator::formatCurrency($results['insuranceAnnual'], $currency),
        'annualDepreciation' => EquipmentInvestmentCalculator::formatCurrency($results['annualDepreciation'], $currency),
        'annualMaintenanceCost' => EquipmentInvestmentCalculator::formatCurrency($results['annualMaintenanceCost'], $currency),
        'lifetimeMaintenanceCost' => EquipmentInvestmentCalculator::formatCurrency($results['lifetimeMaintenanceCost'], $currency),
        'total5YearMaintenance' => EquipmentInvestmentCalculator::formatCurrency($results['total5YearMaintenance'], $currency),
        'total5YearInsurance' => EquipmentInvestmentCalculator::formatCurrency($results['total5YearInsurance'], $currency),
    ];
    
    echo json_encode(['success' => true, 'data' => $results]);
    exit;
}
?>