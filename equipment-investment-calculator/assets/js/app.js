class EquipmentInvestmentCalculator {
    constructor() {
        this.form = document.getElementById('investment-form');
        this.resultsSection = document.getElementById('results');
        this.currencySelect = document.getElementById('currency');
        this.currencySymbols = document.querySelectorAll('.currency-symbol');
        
        this.initializeEventListeners();
        this.updateCurrencySymbols();
        this.initializeDarkMode();
    }
    
    initializeEventListeners() {
        // Form submission
        this.form.addEventListener('submit', this.handleFormSubmit.bind(this));
        
        // Currency change
        this.currencySelect.addEventListener('change', this.updateCurrencySymbols.bind(this));
        
        // Real-time input validation
        this.addInputValidation('purchase-price', 1, 1000000, 'Purchase price must be between 1 and 1,000,000');
        this.addInputValidation('daily-rental-rate', 1, 10000, 'Daily rental rate must be between 1 and 10,000');
        this.addInputValidation('insurance-annual', 0, 50000, 'Annual insurance must be between 0 and 50,000');
        
        // Dark mode toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', this.toggleDarkMode.bind(this));
            themeToggle.addEventListener('keydown', this.handleThemeToggleKeydown.bind(this));
        }
    }
    
    updateCurrencySymbols() {
        const selectedCurrency = this.currencySelect.value;
        const currencies = {
            'USD': '$', 'EUR': '€', 'GBP': '£', 'CAD': 'C$', 'AUD': 'A$', 'JPY': '¥', 'INR': '₹'
        };
        const symbol = currencies[selectedCurrency] || '$';
        
        this.currencySymbols.forEach(element => {
            element.textContent = symbol;
        });
    }
    
    addInputValidation(inputId, min, max, errorMessage) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        input.addEventListener('input', function() {
            const value = parseFloat(input.value);
            const isValid = !input.value || (value >= min && value <= max);
            
            input.classList.toggle('error', !isValid);
            
            // Remove existing error message
            const existingError = input.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            // Add error message if invalid
            if (!isValid && input.value) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = errorMessage;
                input.parentNode.appendChild(errorDiv);
            }
        });
    }
    
    async handleFormSubmit(event) {
        event.preventDefault();
        
        // Clear previous results
        this.resultsSection.style.display = 'none';
        
        // Collect form data
        const formData = new FormData(this.form);
        formData.append('action', 'calculate');
        
        // Validate required fields
        const requiredFields = ['purchase_price', 'daily_rental_rate', 'usage_per_month'];
        let hasErrors = false;
        
        for (const field of requiredFields) {
            const value = formData.get(field);
            if (!value || parseFloat(value) <= 0) {
                hasErrors = true;
                break;
            }
        }
        
        if (hasErrors) {
            this.showError('Please fill in all required fields with valid values.');
            return;
        }
        
        try {
            // Show loading state
            const submitBtn = this.form.querySelector('.calculate-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Calculating...';
            submitBtn.disabled = true;
            
            // Make request
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // Restore button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            if (data.error) {
                this.showError(data.error);
            } else if (data.success) {
                this.displayResults(data.data);
            } else {
                this.showError('An unexpected error occurred. Please try again.');
            }
            
        } catch (error) {
            console.error('Calculation error:', error);
            this.showError('Network error. Please check your connection and try again.');
            
            // Restore button
            const submitBtn = this.form.querySelector('.calculate-btn');
            submitBtn.textContent = 'Calculate Investment';
            submitBtn.disabled = false;
        }
    }
    
    displayResults(results) {
        const recommendation = results.recommendation;
        const isRentRecommended = recommendation === 'RENT';
        
        const resultsHTML = `
            <div class="recommendation ${isRentRecommended ? 'rent' : 'buy'}">
                <div class="recommendation-badge">
                    <h3>Recommendation: ${recommendation}</h3>
                </div>
                <p class="recommendation-reason">${results.recommendationReason}</p>
            </div>
            
            <div class="financial-breakdown">
                <h4>Financial Breakdown</h4>
                <div class="breakdown-grid">
                    <div class="breakdown-item">
                        <span class="label">Purchase Price:</span>
                        <span class="value">${results.formatted.purchasePrice}</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="label">Daily Rental Rate:</span>
                        <span class="value">${results.formatted.dailyRentalRate}</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="label">Usage per Year:</span>
                        <span class="value">${results.annualUsage} days</span>
                    </div>
                </div>
            </div>
            
            <div class="cost-comparison">
                <h4>Annual Cost Comparison</h4>
                <div class="comparison-grid">
                    <div class="cost-column ${!isRentRecommended ? 'recommended' : ''}">
                        <h5>Ownership Costs</h5>
                        <div class="cost-details">
                            <div class="cost-item">
                                <span>Depreciation (20%/year):</span>
                                <span>${results.formatted.annualDepreciation}</span>
                            </div>
                            <div class="cost-item">
                                <span>Maintenance (${results.formatted.lifetimeMaintenanceCost} ÷ 5 years):</span>
                                <span>${results.formatted.annualMaintenanceCost}</span>
                            </div>
                            ${results.insuranceAnnual > 0 ? `
                            <div class="cost-item">
                                <span>Insurance:</span>
                                <span>${results.formatted.insuranceAnnual}</span>
                            </div>
                            ` : ''}
                            <div class="cost-total">
                                <strong>Total: ${results.formatted.totalAnnualOwnershipCost}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cost-column ${isRentRecommended ? 'recommended' : ''}">
                        <h5>Rental Costs</h5>
                        <div class="cost-details">
                            <div class="cost-item">
                                <span>${results.annualUsage} days × ${results.formatted.dailyRentalRate}:</span>
                                <span>${results.formatted.totalAnnualRentalCost}</span>
                            </div>
                            <div class="cost-total">
                                <strong>Total: ${results.formatted.totalAnnualRentalCost}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="five-year-analysis">
                <h4>5-Year Total Cost Analysis</h4>
                <div class="comparison-grid">
                    <div class="cost-column ${!isRentRecommended ? 'recommended' : ''}">
                        <h5>5-Year Ownership</h5>
                        <div class="cost-details">
                            <div class="cost-item">
                                <span>Equipment Purchase:</span>
                                <span>${results.formatted.purchasePrice}</span>
                            </div>
                            <div class="cost-item">
                                <span>Maintenance (5 years):</span>
                                <span>${results.formatted.total5YearMaintenance}</span>
                            </div>
                            ${results.insuranceAnnual > 0 ? `
                            <div class="cost-item">
                                <span>Insurance (5 years):</span>
                                <span>${results.formatted.total5YearInsurance}</span>
                            </div>
                            ` : ''}
                            <div class="cost-total">
                                <strong>Total: ${results.formatted.total5YearOwnershipCost}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cost-column ${isRentRecommended ? 'recommended' : ''}">
                        <h5>5-Year Rental</h5>
                        <div class="cost-details">
                            <div class="cost-item">
                                <span>5 years × ${results.formatted.totalAnnualRentalCost}:</span>
                                <span>${results.formatted.total5YearRentalCost}</span>
                            </div>
                            <div class="cost-total">
                                <strong>Total: ${results.formatted.total5YearRentalCost}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="five-year-savings">
                    <p><strong>5-Year Savings by ${results.fiveYearSavings > 0 ? 'buying' : 'renting'}: ${results.formatted.fiveYearSavings}</strong></p>
                </div>
            </div>
            
            <div class="break-even-analysis">
                <h4>Break-Even Analysis</h4>
                <div class="break-even-grid">
                    <div class="break-even-item">
                        <span class="metric">${results.breakEvenUsages}</span>
                        <span class="label">rental days to equal purchase price</span>
                    </div>
                    <div class="break-even-item">
                        <span class="metric">${results.breakEvenMonths}</span>
                        <span class="label">months to break even at your usage rate</span>
                    </div>
                    <div class="break-even-item ${results.paysForItselfInOneYear ? 'positive' : 'negative'}">
                        <span class="metric">${results.paysForItselfInOneYear ? 'YES' : 'NO'}</span>
                        <span class="label">pays for itself within 1 year</span>
                    </div>
                </div>
            </div>
            
            <div class="savings-summary">
                <h4>Savings Summary</h4>
                <div class="savings-amount ${results.annualSavings > 0 ? 'positive' : 'negative'}">
                    <span class="amount">${results.formatted.annualSavings}</span>
                    <span class="period">per year</span>
                    <span class="method">${results.annualSavings > 0 ? 'saved by buying' : 'saved by renting'}</span>
                </div>
                <div class="monthly-savings">
                    <small>${results.formatted.monthlySavings} per month</small>
                </div>
            </div>
        `;
        
        this.resultsSection.innerHTML = resultsHTML;
        this.resultsSection.style.display = 'block';
        
        // Smooth scroll to results
        this.resultsSection.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }
    
    showError(message) {
        const errorHTML = `
            <div class="error-message-block">
                <h4>Error</h4>
                <p>${message}</p>
            </div>
        `;
        
        this.resultsSection.innerHTML = errorHTML;
        this.resultsSection.style.display = 'block';
    }
    
    initializeDarkMode() {
        // Check for saved theme preference or default to light mode
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
            themeToggle.setAttribute('aria-pressed', savedTheme === 'dark');
        }
    }
    
    toggleDarkMode() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.textContent = newTheme === 'dark' ? '☀️' : '🌙';
            themeToggle.setAttribute('aria-pressed', newTheme === 'dark');
        }
    }
    
    handleThemeToggleKeydown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            this.toggleDarkMode();
        }
    }
}

// Initialize calculator when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new EquipmentInvestmentCalculator();
});