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
            $purchasePrice = floatval($_POST['purchase_price'] ?? 0);
            $dailyRentalRate = floatval($_POST['daily_rental_rate'] ?? 0);
            $usagePerMonth = intval($_POST['usage_per_month'] ?? 0);
            $insuranceAnnual = floatval($_POST['insurance_annual'] ?? 0);
            $currency = $_POST['currency'] ?? 'USD';
            
            $results = EquipmentInvestmentCalculator::calculate($purchasePrice, $dailyRentalRate, $usagePerMonth, $insuranceAnnual);
            
            if (isset($results['error'])) {
                echo json_encode(['error' => $results['error']]);
                exit;
            }
            
            // Add currency formatting
            $results['currency'] = $currency;
            $results['formatted'] = [
                'purchasePrice' => EquipmentInvestmentCalculator::formatCurrency($results['purchasePrice'], $currency),
                'dailyRentalRate' => EquipmentInvestmentCalculator::formatCurrency($results['dailyRentalRate'], $currency),
                'totalAnnualOwnershipCost' => EquipmentInvestmentCalculator::formatCurrency($results['totalAnnualOwnershipCost'], $currency),
                'totalAnnualRentalCost' => EquipmentInvestmentCalculator::formatCurrency($results['totalAnnualRentalCost'], $currency),
                'annualSavings' => EquipmentInvestmentCalculator::formatCurrency($results['annualSavings'], $currency),
                'monthlySavings' => EquipmentInvestmentCalculator::formatCurrency($results['monthlySavings'], $currency),
                'costPerUsageOwnership' => EquipmentInvestmentCalculator::formatCurrency($results['costPerUsageOwnership'], $currency),
                'costPerUsageRental' => EquipmentInvestmentCalculator::formatCurrency($results['costPerUsageRental'], $currency),
                'insuranceAnnual' => EquipmentInvestmentCalculator::formatCurrency($results['insuranceAnnual'], $currency),
            ];
            
            echo json_encode(['success' => true, 'data' => $results]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Calculation error: ' . $e->getMessage()]);
            exit;
        }
    }
}

// Page variables for Equipment Investment Calculator
$page_title = "Equipment Investment Calculator";
$page_description = "Make smart financial decisions about photography equipment. Calculate whether to rent or buy gear based on usage patterns, depreciation, and hidden costs.";
$css_path = "assets/css/style.css?v=0.0.4";
$base_url = "../";
$show_back_button = true;

// Current page URL for canonical and social sharing
$current_url = "https://www.beyondphototips.com/tools/equipment-investment-calculator/";
$canonical_url = $current_url;

// Social media data
$social_data = [
    'title' => $page_title . ' - BeyondPhotoTips.com',
    'description' => $page_description,
    'url' => $current_url,
    'image' => $current_url . 'assets/images/investment-calculator-social.jpg',
    'image_alt' => 'Equipment Investment Calculator - Smart photography gear decisions'
];

// Social sharing data
$sharing_data = [
    'url' => $current_url,
    'title' => $page_title,
    'text' => 'Make smart financial decisions about photography equipment with this rent vs buy calculator!'
];

// JSON-LD Schema data
$schema_data = [
    'name' => $page_title,
    'description' => $page_description,
    'url' => $current_url,
    'calculator_type' => 'Equipment Investment Calculator',
    'features' => [
        'Rent vs buy analysis for photography equipment',
        'Depreciation and maintenance cost calculations',
        'Break-even analysis with usage patterns',
        'Multi-currency support',
        'Annual and per-usage cost comparisons',
        'Financial recommendations based on usage frequency',
        'Insurance cost integration',
        'Professional financial guidance for gear decisions'
    ],
    'keywords' => [
        'equipment investment calculator',
        'rent vs buy photography equipment',
        'photography gear finance',
        'equipment depreciation',
        'camera rental calculator',
        'lens investment analysis',
        'photography business tools',
        'equipment financing decisions',
        'gear ROI calculator',
        'photography equipment economics'
    ],
    'screenshot' => $current_url . 'assets/images/investment-calculator-screenshot.jpg'
];

// Breadcrumb data
$breadcrumb_data = [
    ['name' => 'BeyondPhotoTips.com', 'url' => 'https://www.beyondphototips.com/'],
    ['name' => 'Photography Tools', 'url' => 'https://www.beyondphototips.com/tools/'],
    ['name' => 'Equipment Investment Calculator', 'url' => $current_url]
];

include '../shared/header.php';
?>

    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" class="theme-toggle" title="Toggle dark/light mode" aria-label="Toggle dark mode" aria-pressed="false">
        🌙
    </button>

    <main class="container">
        <h1>Equipment Investment Calculator</h1>
        
        <!-- Financial Warning -->
        <div class="debt-warning">
            <h3>⚠️ Important Financial Advice</h3>
            <p><strong>Never go into debt to buy camera equipment.</strong> If you need to finance a purchase, renting is always the better financial choice. This calculator helps you make rational decisions based on actual usage and costs.</p>
        </div>
        
        <div class="calculator-wrapper">
            <form id="investment-form" class="calculator-form">
                <div class="form-grid">
                    <!-- Currency Selection -->
                    <div class="input-group full-width">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" required>
                            <?php foreach (CURRENCIES as $code => $data): ?>
                                <option value="<?= htmlspecialchars($code) ?>" <?= $code === 'USD' ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($data['symbol']) ?> <?= htmlspecialchars($data['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Purchase Price -->
                    <div class="input-group">
                        <label for="purchase-price">Equipment Purchase Price</label>
                        <div class="input-with-unit">
                            <span class="currency-symbol">$</span>
                            <input type="number" id="purchase-price" name="purchase_price" min="1" step="0.01" placeholder="3000" required>
                        </div>
                    </div>
                    
                    <!-- Daily Rental Rate -->
                    <div class="input-group">
                        <label for="daily-rental-rate">Daily Rental Rate</label>
                        <div class="input-with-unit">
                            <span class="currency-symbol">$</span>
                            <input type="number" id="daily-rental-rate" name="daily_rental_rate" min="1" step="0.01" placeholder="75" required>
                        </div>
                    </div>
                    
                    <!-- Usage Frequency -->
                    <div class="input-group">
                        <label for="usage-frequency">How often will you use this equipment?</label>
                        <select id="usage-frequency" name="usage_per_month" required>
                            <option value="">Select usage frequency</option>
                            <?php foreach (USAGE_FREQUENCIES as $times => $label): ?>
                                <option value="<?= htmlspecialchars($times) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Insurance (Optional) -->
                    <div class="input-group">
                        <label for="insurance-annual">Annual Insurance Cost <em>(optional)</em></label>
                        <div class="input-with-unit">
                            <span class="currency-symbol">$</span>
                            <input type="number" id="insurance-annual" name="insurance_annual" min="0" step="0.01" placeholder="0">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="calculate-btn">Calculate Investment</button>
            </form>
            
            <!-- Results Section -->
            <div id="results" class="results-section" style="display: none;">
                <!-- Results will be populated by JavaScript -->
            </div>
        </div>

        <section class="how-to-use">
            <h2>How to Use the Equipment Investment Calculator</h2>
            <p>This calculator helps photographers make financially sound decisions about equipment purchases versus rentals. Here's how to use it:</p>
            
            <ol>
                <li><strong>Select your currency:</strong> Choose the currency you'll be working with for all calculations</li>
                <li><strong>Enter purchase price:</strong> The total cost to buy the equipment new</li>
                <li><strong>Enter daily rental rate:</strong> How much it costs to rent this equipment per day</li>
                <li><strong>Choose usage frequency:</strong> How often you realistically expect to use this equipment</li>
                <li><strong>Add insurance costs:</strong> Optional annual insurance if you plan to insure the equipment</li>
                <li><strong>Review recommendation:</strong> Get a clear BUY or RENT recommendation with financial breakdown</li>
            </ol>
            
            <h3>What This Calculator Considers</h3>
            <ul>
                <li><strong>Depreciation:</strong> Equipment loses 20% of its value annually (US standard)</li>
                <li><strong>Maintenance:</strong> 20% of purchase price over the equipment's lifetime</li>
                <li><strong>Insurance:</strong> Annual insurance premiums if applicable</li>
                <li><strong>Opportunity cost:</strong> Money tied up in equipment that could be invested elsewhere</li>
                <li><strong>Break-even analysis:</strong> When ownership becomes more economical than renting</li>
            </ul>
            
            <p><strong>Remember:</strong> This calculator prioritizes financial responsibility. If purchasing requires debt, renting is always recommended regardless of the calculations.</p>
        </section>
    </main>

    <script src="assets/js/app.js?v=0.0.4"></script>

<?php include '../shared/footer.php'; ?>